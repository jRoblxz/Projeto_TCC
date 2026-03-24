<?php

namespace App\Http\Controllers\Api;

use App\Models\VideoJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Recebe callbacks do Modal.
     * POST /api/webhook/modal
     */
    public function modal(Request $request)
    {
        // Valida o secret
        $secret = $request->header('X-Webhook-Secret');
        if ($secret !== config('services.modal.webhook_secret')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $jobId  = $request->input('job_id');
        $status = $request->input('status');
        $job    = VideoJob::find($jobId);

        if (!$job) {
            return response()->json(['error' => 'Job not found'], 404);
        }

        match ($status) {
            'processing' => $job->update([
                'status'                 => 'processing',
                'processing_started_at'  => now(),
            ]),
            'done' => $job->update([
                'status'                  => 'done',
                'output_video_url'        => $request->input('video_url'),
                'output_csv_url'          => $request->input('csv_url'),
                'total_frames'            => $request->input('total_frames'),
                'processing_finished_at'  => now(),
            ]),
            'failed' => $job->update([
                'status'        => 'failed',
                'error_message' => $request->input('error'),
            ]),
            default => Log::warning("Webhook status desconhecido: {$status}")
        };

        return response()->json(['ok' => true]);
    }
}