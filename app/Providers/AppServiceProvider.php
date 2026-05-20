<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use League\Flysystem\Filesystem;
use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureGcs();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureGcs(): void
    {
        Storage::extend('gcs', function (Application $app, array $config) {
            $keyFile = json_decode($config['credentials'], true);
            $keyFile['private_key'] = str_replace('\n', "\n", $keyFile['private_key']);

            $storageClient = new StorageClient([
                'keyFile' => $keyFile,
                'projectId' => $config['project_id'],
            ]);

            $adapter = new GoogleCloudStorageAdapter(
                $storageClient->bucket($config['bucket']),
            );

            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config,
            );
        });
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
