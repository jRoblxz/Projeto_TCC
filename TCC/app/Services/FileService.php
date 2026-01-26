<?php

namespace App\Services;

use Google\Cloud\Storage\StorageClient;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class FileService
{
    protected $storage;
    protected $bucketName;

    public function __construct()
    {
        $this->bucketName = env('GOOGLE_CLOUD_STORAGE_BUCKET');
        
        $this->storage = new StorageClient([
            'projectId' => env('GOOGLE_CLOUD_PROJECT_ID'),
            'keyFilePath' => env('GOOGLE_CLOUD_KEY_FILE'), // Certifique-se que o .env aponta para o JSON correto
        ]);
    }

    public function upload(UploadedFile $file, string $folder = 'user'): ?string
    {
        try {
            $bucket = $this->storage->bucket($this->bucketName);
            $fileName = $folder . '/' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $bucket->upload(
                file_get_contents($file->getRealPath()),
                ['name' => $fileName]
            );

            return $fileName;
        } catch (\Exception $e) {
            Log::error("Erro no Upload GCS: " . $e->getMessage());
            throw $e; // Relança para quem chamou tratar
        }
    }

    public function delete(?string $path): void
    {
        if (!$path) return;

        try {
            $bucket = $this->storage->bucket($this->bucketName);
            $object = $bucket->object($path);
            
            if ($object->exists()) {
                $object->delete();
            }
        } catch (\Exception $e) {
            Log::warning("Erro ao deletar arquivo GCS: " . $e->getMessage());
        }
    }
}