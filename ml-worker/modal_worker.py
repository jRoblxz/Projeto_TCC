# modal_worker.py
# Deploy: modal deploy modal_worker.py
# Docs:   https://modal.com/docs

import modal
import os
import json
import requests
import tempfile
from pathlib import Path

# ── Imagem Docker com todas as dependências ───────────────────────────────────
image = (
    modal.Image.debian_slim(python_version="3.11")
    .apt_install("ffmpeg", "libgl1", "libglib2.0-0", "git")
    .pip_install(
        "ultralytics",
        "supervision",
        "scikit-learn",
        "opencv-python-headless",
        "google-cloud-storage",
        "fastapi[standard]",
        "requests",
    )
    .run_commands(
        "pip install -q git+https://github.com/roboflow/sports.git"
    )
)

app = modal.App("football-tracker", image=image)

# ── Volume para guardar os modelos .pt (evita re-download a cada job) ─────────
model_volume = modal.Volume.from_name("football-models", create_if_missing=True)

# ── Secrets do Google Cloud e Laravel ────────────────────────────────────────
# Configure em: https://modal.com/secrets
# Chaves necessárias:
#   GCS_BUCKET_NAME        → nome do bucket no Google Cloud Storage
#   GCS_CREDENTIALS_JSON   → conteúdo do service account JSON (string)
#   LARAVEL_WEBHOOK_URL    → URL do endpoint webhook no Laravel
#   LARAVEL_WEBHOOK_SECRET → token secreto para validar o webhook
secrets = [
    modal.Secret.from_name("tcc-football"),
]


@app.function(
    gpu="T4",                    # GPU gratuita no Modal (tier free)
    timeout=3600,                # 1 hora máximo por job
    secrets=secrets,
    volumes={"/models": model_volume},
    memory=8192,
)
def process_video(
    job_id: int,
    input_gcs_path: str,         # ex: "videos/input/uuid_video.mp4"
    ball_model_gcs: str,         # ex: "models/ball.pt"
    player_model_gcs: str,       # ex: "models/player.pt"
    field_model_gcs: str,        # ex: "models/field.pt"
):
    """
    Processa um vídeo de futebol:
    1. Baixa vídeo e modelos do GCS
    2. Roda pipeline (ball + player + field + homografia)
    3. Sobe vídeo anotado + CSV para o GCS
    4. Notifica o Laravel via webhook
    """
    import numpy as np
    import cv2
    import csv
    import supervision as sv
    from sklearn.cluster import KMeans
    from ultralytics import YOLO
    from google.cloud import storage
    from datetime import timedelta
    from sports.configs.soccer import SoccerPitchConfiguration
    from sports.annotators.soccer import draw_pitch, draw_points_on_pitch
    from sports.common.view import ViewTransformer

    # ── GCS client ────────────────────────────────────────────────────────────
    creds_json = os.environ["GCS_CREDENTIALS_JSON"]
    bucket_name = os.environ["GCS_BUCKET_NAME"]

    import google.auth
    from google.oauth2 import service_account
    creds_dict = json.loads(creds_json)
    credentials = service_account.Credentials.from_service_account_info(creds_dict)
    gcs_client = storage.Client(credentials=credentials)
    bucket = gcs_client.bucket(bucket_name)

    def gcs_download(gcs_path: str, local_path: str):
        bucket.blob(gcs_path).download_to_filename(local_path)
        print(f"Downloaded: {gcs_path} → {local_path}")

    def gcs_upload(local_path: str, gcs_path: str) -> str:
        blob = bucket.blob(gcs_path)
        blob.upload_from_filename(local_path)
        blob.make_public()
        print(f"Uploaded: {local_path} → {gcs_path}")
        return blob.public_url

    # ── Notifica Laravel ──────────────────────────────────────────────────────
    def notify_laravel(status: str, payload: dict = {}):
        url    = os.environ["LARAVEL_WEBHOOK_URL"]
        secret = os.environ["LARAVEL_WEBHOOK_SECRET"]
        try:
            requests.post(url, json={
                "job_id": job_id,
                "status": status,
                **payload,
            }, headers={"X-Webhook-Secret": secret}, timeout=10)
        except Exception as e:
            print(f"Webhook error: {e}")

    # ── Baixa arquivos ────────────────────────────────────────────────────────
    with tempfile.TemporaryDirectory() as tmpdir:
        tmpdir = Path(tmpdir)

        notify_laravel("processing")

        video_in  = str(tmpdir / "input.mp4")
        video_out = str(tmpdir / "output.mp4")
        csv_out   = str(tmpdir / "tracking.csv")

        gcs_download(input_gcs_path, video_in)

        # Modelos — usa volume para cache entre jobs
        def get_model(gcs_path: str, name: str) -> str:
            local = f"/models/{name}"
            if not Path(local).exists():
                gcs_download(gcs_path, local)
            return local

        ball_pt   = get_model(ball_model_gcs,   "ball.pt")
        player_pt = get_model(player_model_gcs, "player.pt")
        field_pt  = get_model(field_model_gcs,  "field.pt")

        # ── Carrega modelos ───────────────────────────────────────────────────
        ball_model   = YOLO(ball_pt)
        player_model = YOLO(player_pt)
        field_model  = YOLO(field_pt)

        # ── Configurações ─────────────────────────────────────────────────────
        BALL_CONF, BALL_IOU     = 0.15, 0.3
        PLAYER_CONF, PLAYER_IOU = 0.4,  0.9
        FIELD_CONF              = 0.15
        GOALKEEPER_CLASS        = 0
        PLAYER_CLASS            = 1
        PLAYER_CLASS_IDS        = [0, 1]
        TEAM_NAMES              = {0: "TIME A", 1: "TIME B"}
        GK_COLOR_HEX            = "#FFD700"
        TEAM_COLORS             = {"TIME A": "#0000FF", "TIME B": "#FF0000"}
        FIELD_KP_NAMES = [
            "TRC","TR18ML","TR6ML","TL6ML","TL18ML","TLC",
            "TR6MC","TL6MC","PL","TR18MC","TRArc","TLArc",
            "TL18MC","RML","RMC","LMC","LML","BR18MC",
            "BRArc","BLArc","BL18MC","PR","BR6MC","BL6MC",
            "BRC","BR18ML","BR6ML","BL6ML","BL18ML","BLC",
            "CL","CR"
        ]
        CONFIG        = SoccerPitchConfiguration()
        PITCH_SCALE   = 0.065
        PITCH_PADDING = 20
        MINI_W, MINI_H   = 400, 260
        MINI_MARGIN      = 16
        MINI_ALPHA       = 0.88
        MIN_KP           = 4

        # ── TeamAssigner ──────────────────────────────────────────────────────
        class TeamAssigner:
            def __init__(self):
                self.player_team_dict = {}
                self.kmeans = None

            def _field_mask(self, roi_hsv):
                lo = np.array([35, 40, 40])
                hi = np.array([85, 255, 255])
                return cv2.inRange(roi_hsv, lo, hi) == 0

            def _shirt_color(self, frame, bbox):
                x1,y1,x2,y2 = map(int, bbox)
                x1,y1 = max(0,x1), max(0,y1)
                x2,y2 = min(frame.shape[1],x2), min(frame.shape[0],y2)
                crop = frame[y1:y2, x1:x2]
                if crop.size == 0: return np.array([0,0])
                top = crop[:crop.shape[0]//2,:]
                if top.size == 0: return np.array([0,0])
                h,w = top.shape[:2]
                roi = top[max(0,h//2-int(h*.2)):h//2+int(h*.2),
                          max(0,w//2-int(w*.15)):w//2+int(w*.15)]
                if roi.size == 0: roi = top
                lab  = cv2.cvtColor(roi, cv2.COLOR_RGB2LAB)
                mask = self._field_mask(cv2.cvtColor(roi, cv2.COLOR_RGB2HSV))
                a = lab[:,:,1][mask] if np.any(mask) else lab[:,:,1].flatten()
                b = lab[:,:,2][mask] if np.any(mask) else lab[:,:,2].flatten()
                return np.array([np.median(a), np.median(b)])

            def calibrate(self, frames, dets_list):
                colors = []
                for frame, dets in zip(frames, dets_list):
                    for bbox in dets.xyxy:
                        colors.append(self._shirt_color(frame, bbox))
                if len(colors) < 4: return
                X = np.array(colors)
                km = KMeans(n_clusters=2, init='k-means++', n_init=10, random_state=42)
                km.fit(X)
                self.kmeans = km

            def get_team(self, frame, bbox, tracker_id):
                if tracker_id in self.player_team_dict:
                    return self.player_team_dict[tracker_id]
                if self.kmeans is None: return 0
                color = self._shirt_color(frame, bbox)
                team  = int(self.kmeans.predict(color.reshape(1,-1))[0])
                self.player_team_dict[tracker_id] = team
                return team

            def reset(self): self.player_team_dict = {}

        # ── Mini-campo ────────────────────────────────────────────────────────
        FIELD_TEMPLATE = draw_pitch(
            config=CONFIG,
            background_color=sv.Color.from_hex('#1a7a1a'),
            line_color=sv.Color.WHITE,
            padding=PITCH_PADDING,
            scale=PITCH_SCALE,
        )

        def compute_transformer(kp_dict):
            src_pts, dst_pts = [], []
            for cls_id, (cx, cy) in kp_dict.items():
                if cls_id < len(CONFIG.vertices):
                    src_pts.append([cx, cy])
                    dst_pts.append(CONFIG.vertices[cls_id])
            if len(src_pts) < MIN_KP: return None
            try:
                return ViewTransformer(
                    source=np.array(src_pts, dtype=np.float32),
                    target=np.array(dst_pts, dtype=np.float32),
                )
            except Exception: return None

        def draw_minimap(frame, player_positions, ball_pos, transformer):
            mini = FIELD_TEMPLATE.copy()
            if transformer is not None:
                try:
                    team_a, team_b, gks, balls = [], [], [], []
                    for (px, py, team_id, tid, is_gk) in player_positions:
                        pt = transformer.transform_points(np.array([[px,py]], dtype=np.float32))
                        if is_gk: gks.append(pt[0])
                        elif team_id == 0: team_a.append(pt[0])
                        else: team_b.append(pt[0])
                    if ball_pos is not None:
                        pt = transformer.transform_points(np.array([ball_pos], dtype=np.float32))
                        balls.append(pt[0])
                    kw = dict(config=CONFIG, padding=PITCH_PADDING, scale=PITCH_SCALE, pitch=mini)
                    if team_a:
                        mini = draw_points_on_pitch(xy=np.array(team_a), face_color=sv.Color.from_hex('0000FF'), edge_color=sv.Color.WHITE, radius=10, **kw)
                        kw["pitch"] = mini
                    if team_b:
                        mini = draw_points_on_pitch(xy=np.array(team_b), face_color=sv.Color.from_hex('FF0000'), edge_color=sv.Color.WHITE, radius=10, **kw)
                        kw["pitch"] = mini
                    if gks:
                        mini = draw_points_on_pitch(xy=np.array(gks), face_color=sv.Color.from_hex('FFD700'), edge_color=sv.Color.BLACK, radius=10, **kw)
                        kw["pitch"] = mini
                    if balls:
                        mini = draw_points_on_pitch(xy=np.array(balls), face_color=sv.Color.WHITE, edge_color=sv.Color.BLACK, radius=7, **kw)
                except Exception: pass
            mini = cv2.resize(mini, (MINI_W, MINI_H))
            cv2.rectangle(mini, (0,0), (MINI_W-1,MINI_H-1), (255,255,255), 2)
            fh, fw = frame.shape[:2]
            x0, y0 = MINI_MARGIN, fh - MINI_H - MINI_MARGIN
            if y0 >= 0 and x0 + MINI_W <= fw:
                roi = frame[y0:y0+MINI_H, x0:x0+MINI_W]
                frame[y0:y0+MINI_H, x0:x0+MINI_W] = cv2.addWeighted(mini, MINI_ALPHA, roi, 1-MINI_ALPHA, 0)
            return frame

        # ── Calibração ────────────────────────────────────────────────────────
        team_assigner = TeamAssigner()
        frames_calib, dets_calib = [], []
        for i, frame in enumerate(sv.get_video_frames_generator(video_in)):
            if i >= 30: break
            result = player_model.predict(frame, conf=PLAYER_CONF, iou=PLAYER_IOU, agnostic_nms=True, verbose=False)[0]
            dets = sv.Detections.from_ultralytics(result)
            dets = dets[dets.class_id == PLAYER_CLASS]
            if len(dets) > 0:
                frames_calib.append(frame)
                dets_calib.append(dets)
        if frames_calib:
            team_assigner.calibrate(frames_calib, dets_calib)

        # ── Pipeline principal ────────────────────────────────────────────────
        _cap = cv2.VideoCapture(video_in)
        video_fps = _cap.get(cv2.CAP_PROP_FPS) or 30.0
        _cap.release()

        tracker          = sv.ByteTrack()
        last_transformer = None
        csv_rows         = []

        palette_teams = sv.ColorPalette.from_hex([TEAM_COLORS["TIME A"], TEAM_COLORS["TIME B"]])
        gk_palette    = sv.ColorPalette.from_hex([GK_COLOR_HEX, GK_COLOR_HEX])
        box_players   = sv.BoxAnnotator(color=palette_teams, thickness=2, color_lookup=sv.ColorLookup.INDEX)
        lbl_players   = sv.LabelAnnotator(color=palette_teams, color_lookup=sv.ColorLookup.INDEX, text_scale=0.4)
        box_gk        = sv.BoxAnnotator(color=gk_palette, thickness=2, color_lookup=sv.ColorLookup.INDEX)
        lbl_gk        = sv.LabelAnnotator(color=gk_palette, color_lookup=sv.ColorLookup.INDEX, text_scale=0.4)

        def callback(frame: np.ndarray, index: int) -> np.ndarray:
            nonlocal last_transformer
            annotated = frame.copy()
            n_kp = 0

            # Campo
            try:
                field_result = field_model.predict(frame, conf=FIELD_CONF, verbose=False)[0]
                field_dets   = sv.Detections.from_ultralytics(field_result)
                kp_dict = {}
                for bbox, cls_id, conf in zip(field_dets.xyxy, field_dets.class_id, field_dets.confidence):
                    cx, cy = int((bbox[0]+bbox[2])/2), int((bbox[1]+bbox[3])/2)
                    cid = int(cls_id)
                    if cid not in kp_dict or conf > kp_dict[cid][2]:
                        kp_dict[cid] = (cx, cy, float(conf))
                kp_clean = {k: v[:2] for k, v in kp_dict.items()}
                n_kp = len(kp_clean)
                if n_kp >= MIN_KP:
                    new_t = compute_transformer(kp_clean)
                    if new_t is not None:
                        last_transformer = new_t
            except Exception: pass

            # Jogadores
            player_positions = []
            try:
                p_result = player_model.predict(frame, conf=PLAYER_CONF, iou=PLAYER_IOU, agnostic_nms=True, verbose=False)[0]
                all_dets = sv.Detections.from_ultralytics(p_result)
                all_dets = all_dets[np.isin(all_dets.class_id, PLAYER_CLASS_IDS)]
                all_dets = tracker.update_with_detections(all_dets)
                if len(all_dets) > 0:
                    dets_gk = all_dets[all_dets.class_id == GOALKEEPER_CLASS]
                    dets_pl = all_dets[all_dets.class_id == PLAYER_CLASS]
                    if len(dets_pl) > 0:
                        team_ids = np.array([team_assigner.get_team(frame, bbox, tid) for bbox, tid in zip(dets_pl.xyxy, dets_pl.tracker_id)])
                        labels_pl = [f"#{tid} {TEAM_NAMES[team_assigner.get_team(frame, bbox, tid)]}" for bbox, tid in zip(dets_pl.xyxy, dets_pl.tracker_id)]
                        annotated = box_players.annotate(annotated, dets_pl, custom_color_lookup=team_ids)
                        annotated = lbl_players.annotate(annotated, dets_pl, labels=labels_pl, custom_color_lookup=team_ids)
                        for bbox, tid, team_id in zip(dets_pl.xyxy, dets_pl.tracker_id, team_ids):
                            cx, cy = int((bbox[0]+bbox[2])/2), int((bbox[1]+bbox[3])/2)
                            player_positions.append((cx, cy, int(team_id), int(tid), False))
                    if len(dets_gk) > 0:
                        labels_gk = [f"#{tid} GK" for tid in dets_gk.tracker_id]
                        gk_idx    = np.zeros(len(dets_gk), dtype=int)
                        annotated = box_gk.annotate(annotated, dets_gk, custom_color_lookup=gk_idx)
                        annotated = lbl_gk.annotate(annotated, dets_gk, labels=labels_gk, custom_color_lookup=gk_idx)
                        for bbox, tid in zip(dets_gk.xyxy, dets_gk.tracker_id):
                            cx, cy = int((bbox[0]+bbox[2])/2), int((bbox[1]+bbox[3])/2)
                            player_positions.append((cx, cy, 0, int(tid), True))
            except Exception: pass

            # Bola
            ball_pos = None
            try:
                b_result  = ball_model.predict(frame, conf=BALL_CONF, iou=BALL_IOU, verbose=False)[0]
                ball_dets = sv.Detections.from_ultralytics(b_result)
                if len(ball_dets) > 0:
                    best = int(np.argmax(ball_dets.confidence))
                    bbox = ball_dets.xyxy[best]
                    cx, cy = int((bbox[0]+bbox[2])/2), int((bbox[1]+bbox[3])/2)
                    ball_pos = (cx, cy)
                    cv2.circle(annotated, (cx,cy), 7, (255,255,255), -1)
                    cv2.circle(annotated, (cx,cy), 7, (0,0,0), 2)
                    cv2.putText(annotated, "ball", (cx+9,cy-9), cv2.FONT_HERSHEY_SIMPLEX, 0.45, (255,255,255), 1, cv2.LINE_AA)
            except Exception: pass

            # HUD
            count_a = sum(1 for p in player_positions if not p[4] and p[2]==0)
            count_b = sum(1 for p in player_positions if not p[4] and p[2]==1)
            cv2.rectangle(annotated,(10,10),(230,90),(0,0,0),-1)
            cv2.rectangle(annotated,(10,10),(230,90),(255,255,255),1)
            cv2.putText(annotated,f"TIME A: {count_a}",(18,35),cv2.FONT_HERSHEY_SIMPLEX,0.65,(255,80,80),2)
            cv2.putText(annotated,f"TIME B: {count_b}",(18,62),cv2.FONT_HERSHEY_SIMPLEX,0.65,(80,80,255),2)
            cv2.putText(annotated,f"KP: {n_kp}/32",(18,84),cv2.FONT_HERSHEY_SIMPLEX,0.4,(200,200,200),1)

            # Mini-campo
            annotated = draw_minimap(annotated, player_positions, ball_pos, last_transformer)

            # CSV
            timestamp = str(timedelta(seconds=index/video_fps))[:-3]
            for (px, py, team_id, tid, is_gk) in player_positions:
                rx, ry = None, None
                if last_transformer is not None:
                    try:
                        pt = last_transformer.transform_points(np.array([[px,py]], dtype=np.float32))
                        rx = round(float(pt[0][0])/100, 2)
                        ry = round(float(pt[0][1])/100, 2)
                    except Exception: pass
                csv_rows.append({"frame":index,"timestamp":timestamp,"type":"goalkeeper" if is_gk else "player","id":tid,"team":"GK" if is_gk else TEAM_NAMES[team_id],"pixel_cx":px,"pixel_cy":py,"field_x_m":rx,"field_y_m":ry})
            if ball_pos is not None:
                brx, bry = None, None
                if last_transformer is not None:
                    try:
                        pt = last_transformer.transform_points(np.array([ball_pos], dtype=np.float32))
                        brx = round(float(pt[0][0])/100, 2)
                        bry = round(float(pt[0][1])/100, 2)
                    except Exception: pass
                csv_rows.append({"frame":index,"timestamp":timestamp,"type":"ball","id":0,"team":None,"pixel_cx":ball_pos[0],"pixel_cy":ball_pos[1],"field_x_m":brx,"field_y_m":bry})

            return annotated

        sv.process_video(source_path=video_in, target_path=video_out, callback=callback, show_progress=True)

        # Comprime vídeo
        video_compressed = str(tmpdir / "output_compressed.mp4")
        os.system(f"ffmpeg -y -loglevel error -i {video_out} -vcodec libx264 -crf 26 {video_compressed}")

        # Salva CSV
        import csv as csvlib
        fieldnames = ["frame","timestamp","type","id","team","pixel_cx","pixel_cy","field_x_m","field_y_m"]
        with open(csv_out, "w", newline="", encoding="utf-8") as f:
            writer = csvlib.DictWriter(f, fieldnames=fieldnames)
            writer.writeheader()
            writer.writerows(csv_rows)

        # Upload para GCS
        base = f"videos/output/{job_id}"
        video_url = gcs_upload(video_compressed, f"{base}/annotated.mp4")
        csv_url   = gcs_upload(csv_out,          f"{base}/tracking.csv")

        # Notifica Laravel com sucesso
        notify_laravel("done", {
            "video_url": video_url,
            "csv_url":   csv_url,
            "total_frames": len(csv_rows),
        })

        print(f"Job {job_id} concluído!")


@app.function(secrets=secrets)
@modal.fastapi_endpoint(method="POST")
def trigger(body: dict):
    """
    Endpoint HTTP que o Laravel chama para disparar o processamento.
    Body esperado:
    {
        "job_id": 123,
        "input_gcs_path": "videos/input/uuid.mp4",
        "ball_model_gcs": "models/ball.pt",
        "player_model_gcs": "models/player.pt",
        "field_model_gcs": "models/field.pt"
    }
    """
    secret = os.environ.get("LARAVEL_WEBHOOK_SECRET", "")
    # Dispara processamento em background (não bloqueia o response)
    process_video.spawn(
        job_id=body["job_id"],
        input_gcs_path=body["input_gcs_path"],
        ball_model_gcs=body.get("ball_model_gcs",   "models/ball.pt"),
        player_model_gcs=body.get("player_model_gcs", "models/player.pt"),
        field_model_gcs=body.get("field_model_gcs",  "models/field.pt"),
    )
    return {"status": "queued", "job_id": body["job_id"]}