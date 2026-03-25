<?php

namespace App\Http\Controllers\Api;

use App\Models\VideoJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
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
        $request->validate([
            'titulo'            => 'nullable|string|max:255', // NOVO: Aceita o título da análise
            'gcs_path'          => 'required|string',
            'original_filename' => 'nullable|string', 
        ]);

        $gcsPath = $request->input('gcs_path');
        $originalFilename = $request->input('original_filename', 'video_upload.mp4');
        
        // Se o React não mandar título, usa o nome original do arquivo como fallback
        $titulo = $request->input('titulo') ?: $originalFilename; 

        $job = VideoJob::create([
            'user_id'           => Auth::id(),
            'status'            => 'pending',
            'titulo'            => $titulo, // NOVO: Salva o título no banco
            'original_filename' => $originalFilename,
            'input_gcs_path'    => $gcsPath,
        ]);

        try {
            // Dispara o worker no Modal (INTOCADO E SEGURO)
            Http::timeout(10)->post(config('services.modal.trigger_url'), [
                'job_id'           => $job->id,
                'input_gcs_path'   => $gcsPath,
                'ball_model_gcs'   => 'models/ball.pt',
                'player_model_gcs' => 'models/player.pt',
                'field_model_gcs'  => 'models/field.pt',
            ]);
        } catch (\Exception $e) {
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
        abort_unless($videoJob->user_id === Auth::id(), 403);

        return response()->json([
            'id'          => $videoJob->id,
            'titulo'      => $videoJob->titulo, // NOVO: Devolve o título para o React
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
     * Lista os jobs do usuário autenticado (Agora Paginado para a Galeria).
     * GET /api/video-jobs
     */
    public function index()
    {
        // NOVO: Usa Paginate em vez de Get() para montar os Cards na tela de galeria
        $jobs = VideoJob::where('user_id', Auth::id())
            ->latest()
            ->paginate(12, ['id', 'titulo', 'status', 'original_filename', 'output_video_url', 'output_csv_url', 'created_at']);

        return response()->json($jobs);
    }

    /**
     * NOVO: Atualiza o título da análise.
     * PUT/PATCH /api/video-jobs/{id}
     */
    public function update(Request $request, VideoJob $videoJob)
    {
        abort_unless($videoJob->user_id === Auth::id(), 403);

        $request->validate([
            'titulo' => 'required|string|max:255',
        ]);

        $videoJob->update([
            'titulo' => $request->titulo,
        ]);

        return response()->json(['message' => 'Nome da análise atualizado com sucesso!', 'job' => $videoJob]);
    }

    /**
     * NOVO: Exclui a análise e limpa o Google Cloud Storage.
     * DELETE /api/video-jobs/{id}
     */
    public function destroy(VideoJob $videoJob)
    {
        abort_unless($videoJob->user_id === Auth::id(), 403);

        try {
            // Conecta no GCS usando as variáveis de VÍDEO (evita apagar coisas erradas)
            $storage = new StorageClient([
                'keyFilePath' => env('GOOGLE_CLOUD_KEY_FILE'),
                'projectId'   => env('GOOGLE_CLOUD_PROJECT_ID', 'projetotcc-478522')
            ]);
            
            $bucketName = env('GOOGLE_CLOUD_STORAGE_VIDEO_BUCKET', 'videos-tcc');
            $bucket = $storage->bucket($bucketName);
            $prefix = "https://storage.googleapis.com/{$bucketName}/";

            // 1. Apaga o vídeo bruto de entrada (se existir)
            if ($videoJob->input_gcs_path) {
                $object = $bucket->object($videoJob->input_gcs_path);
                if ($object->exists()) $object->delete();
            }

            // 2. Apaga o vídeo final processado pela IA
            if ($videoJob->output_video_url) {
                $path = str_replace($prefix, '', $videoJob->output_video_url);
                $object = $bucket->object($path);
                if ($object->exists()) $object->delete();
            }

            // 3. Apaga a planilha CSV
            if ($videoJob->output_csv_url) {
                $path = str_replace($prefix, '', $videoJob->output_csv_url);
                $object = $bucket->object($path);
                if ($object->exists()) $object->delete();
            }

            // Finalmente apaga do Banco de Dados
            $videoJob->delete();

        } catch (\Exception $e) {
            return response()->json(['message' => 'Análise excluída do BD, mas erro ao limpar o bucket: ' . $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Análise e arquivos excluídos com sucesso!']);
    }

    /**
     * Gera URL Assinada (INTOCADO E SEGURO)
     */
    public function getUploadUrl(Request $request) 
    {
        $uuid = Str::uuid();
        $gcsPath = "videos/input/{$uuid}.mp4";

        try {
            $storage = new StorageClient([
                'keyFilePath' => env('GOOGLE_CLOUD_KEY_FILE'),
                'projectId'   => env('GOOGLE_CLOUD_PROJECT_ID', 'projetotcc-478522')
            ]);
            
            $bucket = $storage->bucket(env('GOOGLE_CLOUD_STORAGE_VIDEO_BUCKET', 'videos-tcc'));
            $object = $bucket->object($gcsPath);

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