<?php

namespace App\Http\Controllers\Api;

use App\Models\VideoJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Google\Cloud\Storage\StorageClient;

class VideoJobController extends Controller
{
    /**
     * Upload do vídeo e disparo do processamento.
     * POST /api/video-jobs
     */
    public function store(Request $request)
    {
        // 1. Agora validamos apenas os textos que o React envia
        $request->validate([
            'gcs_path'          => 'required|string',
            'original_filename' => 'nullable|string', 
        ]);

        $gcsPath = $request->input('gcs_path');
        $originalFilename = $request->input('original_filename', 'video_upload.mp4');

        // 2. O VÍDEO JÁ ESTÁ NO BUCKET! Uhul! 
        // Criamos o Job diretamente no banco.
        $job = VideoJob::create([
            'user_id'           => Auth::id(),
            'status'            => 'pending',
            'original_filename' => $originalFilename,
            'input_gcs_path'    => $gcsPath,
        ]);

        try {
            // 3. Dispara o worker no Modal
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
                'message' => 'Vídeo salvo no Google, mas erro ao chamar a IA (Modal): ' . $e->getMessage()
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

    public function getUploadUrl(Request $request) {
        $uuid = Str::uuid();
        $gcsPath = "videos/input/{$uuid}.mp4";

        try {
            $storage = new StorageClient([
                'keyFilePath' => env('GOOGLE_CLOUD_KEY_FILE'),
                'projectId'   => env('GOOGLE_CLOUD_PROJECT_ID', 'projetotcc-478522')
            ]);
            
            $bucket = $storage->bucket(env('GOOGLE_CLOUD_STORAGE_VIDEO_BUCKET', 'videos-tcc'));
            $object = $bucket->object($gcsPath);

            // Gera um link seguro válido por 15 minutos para fazer o PUT do vídeo
            $url = $object->signedUrl(
                new \DateTime('+15 minutes'),
                [
                    'method' => 'PUT',
                    'contentType' => 'video/mp4'
                ]
            );

            return response()->json([
                'upload_url' => $url,
                'gcs_path' => $gcsPath
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao gerar link: ' . $e->getMessage()], 500);
        }
    }
}