<?php

namespace App\Http\Controllers\Api;

use App\Models\VideoJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class VideoJobController extends Controller
{
    /**
     * Upload do vídeo e disparo do processamento.
     * POST /api/video-jobs
     */
    public function store(Request $request)
    {
        $request->validate([
            'video' => 'required|file|mimetypes:video/mp4,video/avi,video/quicktime|max:512000', // 500MB
        ]);

        $file     = $request->file('video');
        $uuid     = Str::uuid();
        $filename = "{$uuid}.mp4";
        $gcsPath  = "videos/input/{$filename}";

        try {
            // 1. Mudamos para putFileAs (Otimizado, não trava a memória)
            // Ele tenta salvar e retorna o caminho se der certo, ou false se falhar
            $uploadSucesso = Storage::disk('gcs_videos')->putFileAs('videos/input', $file, $filename);

            // 2. A Trava: Se o Laravel retornar false, ele para aqui e te avisa
            if (!$uploadSucesso) {
                return response()->json([
                    'message' => 'Falha silenciosa: O Laravel tentou enviar, mas o Google Cloud recusou. Verifique se a variável GOOGLE_CLOUD_STORAGE_VIDEO_BUCKET está certa no .env!'
                ], 500);
            }

        } catch (\Exception $e) {
            // 3. O Debugger: Se o Google Cloud cuspir um erro (403, 404), vai aparecer no seu React
            return response()->json([
                'message' => 'Erro bloqueante do Google Cloud: ' . $e->getMessage()
            ], 500);
        }

        // Se o código chegou até aqui, é porque o VÍDEO ESTÁ NO BUCKET! Uhul!
        // Agora sim podemos criar o Job e chamar a IA do Modal.
        $job = VideoJob::create([
            'user_id'           => Auth::id(),
            'status'            => 'pending',
            'original_filename' => $file->getClientOriginalName(),
            'input_gcs_path'    => $gcsPath,
        ]);

        try {
            // Dispara o worker no Modal
            Http::timeout(10)->post(config('services.modal.trigger_url'), [
                'job_id'           => $job->id,
                'input_gcs_path'   => $gcsPath,
                'ball_model_gcs'   => 'models/ball.pt',
                'player_model_gcs' => 'models/player.pt',
                'field_model_gcs'  => 'models/field.pt',
            ]);
        } catch (\Exception $e) {
             // Caso o Modal esteja fora do ar ou a URL esteja errada
             return response()->json([
                'message' => 'Vídeo salvo, mas erro ao chamar a IA (Modal): ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'job_id' => $job->id,
            'status' => $job->status,
        ], 201);
    }

    /**
     * Status do job (React faz polling aqui).
     * GET /api/video-jobs/{id}
     */
    public function show(VideoJob $videoJob)
    {
        // Garante que o job pertence ao usuário autenticado
        abort_unless($videoJob->user_id === Auth::id(), 403);

        return response()->json([
            'id'          => $videoJob->id,
            'status'      => $videoJob->status,
            'video_url'   => $videoJob->output_video_url,
            'csv_url'     => $videoJob->output_csv_url,
            'total_frames'=> $videoJob->total_frames,
            'error'       => $videoJob->error_message,
            'created_at'  => $videoJob->created_at,
            'finished_at' => $videoJob->processing_finished_at,
        ]);
    }

    /**
     * Lista os jobs do usuário autenticado.
     * GET /api/video-jobs
     */
    public function index()
    {
        $jobs = VideoJob::where('user_id', Auth::id())
            ->latest()
            ->get(['id','status','original_filename','output_video_url','output_csv_url','created_at']);

        return response()->json($jobs);
    }
}