<?php

namespace App\Providers;

use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter;
use Illuminate\Filesystem\FilesystemAdapter; 

class GCSServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Storage::extend('gcs', function ($app, $config) {
            
            // Validação: verifica se as chaves necessárias existem
            if (!isset($config['project_id']) || !isset($config['bucket'])) {
                throw new \Exception('Configuração GCS incompleta: project_id e bucket são obrigatórios');
            }
            
            // Monta as opções do StorageClient
            $clientOptions = [
                'projectId' => $config['project_id'],
            ];
            
            // Adiciona keyFilePath apenas se existir na configuração
            if (isset($config['key_file']) && !empty($config['key_file'])) {
                $clientOptions['keyFilePath'] = $config['key_file'];
            }
            
            $client = new StorageClient($clientOptions);

            $bucket = $client->bucket($config['bucket']);

            // CRÍTICO: Apenas 2 parâmetros para evitar ACLs
            $adapter = new GoogleCloudStorageAdapter(
                $bucket,
                $config['path_prefix'] ?? ''
            );

            $driver = new Filesystem($adapter);

            $config['url'] = 'https://storage.googleapis.com/' . $config['bucket'];

            return new FilesystemAdapter($driver, $adapter, $config);
        });
    }
}