<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem as LeagueFilesystem;
use Masbug\Flysystem\GoogleDriveAdapter;
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleService;
use Illuminate\Filesystem\FilesystemAdapter;

class GoogleDriveServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Storage::extend('google', function ($app, $config) {
            $client = new GoogleClient();
            
            // Use service account JSON file
            if (isset($config['clientJson']) && !empty($config['clientJson'])) {
                $client->setAuthConfig($config['clientJson']);
            } else {
                throw new \Exception('Google Drive clientJson is not configured.');
            }
            
            $client->setScopes([GoogleService::DRIVE]);
            $client->setAccessType('offline');
            
            $service = new GoogleService($client);
            
            $options = [];
            if (isset($config['folderId']) && !empty($config['folderId'])) {
                $options['root'] = $config['folderId'];
            }
            
            $adapter = new GoogleDriveAdapter($service, $config['folderId'] ?? 'root', $options);
            
            $driver = new LeagueFilesystem($adapter, $config);
            
            // Return Laravel's FilesystemAdapter instead of League's Filesystem
            return new FilesystemAdapter($driver, $adapter, $config);
        });
    }
}