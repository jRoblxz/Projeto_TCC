<?php

namespace App\Http\Controllers;

use App\Models\VideoJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        $gcsPath  = "videos/input/{$uuid}.mp4";

        // Sobe o vídeo para o Google Cloud Storage
        Storage::disk('gcs')->put($gcsPath, file_get_contents($file->getRealPath()));

        // Cria o registro do job
        $job = VideoJob::create([
            'user_id'           => Auth::id(),
            'status'            => 'pending',
            'original_filename' => $file->getClientOriginalName(),
            'input_gcs_path'    => $gcsPath,
        ]);

        // Dispara o worker no Modal (não bloqueia)
        Http::timeout(10)->post(config('services.modal.trigger_url'), [
            'job_id'           => $job->id,
            'input_gcs_path'   => $gcsPath,
            'ball_model_gcs'   => 'models/ball.pt',
            'player_model_gcs' => 'models/player.pt',
            'field_model_gcs'  => 'models/field.pt',
        ]);

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