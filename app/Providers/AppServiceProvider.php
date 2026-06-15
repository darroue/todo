<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Inertia\ResponseFactory;
use League\Flysystem\Filesystem;
use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter;
use League\Flysystem\GoogleCloudStorage\PortableVisibilityHandler;

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
        $this->configureInertiaToast();
    }

    /**
     * Register the `Inertia::toast()` macro for flashing toast notifications.
     */
    protected function configureInertiaToast(): void
    {
        ResponseFactory::macro('toast', function (string $message, string $type = 'success', ?array $action = null): ResponseFactory {
            /** @var ResponseFactory $this */
            $payload = [
                'type' => $type,
                'message' => $message,
            ];

            if ($action !== null) {
                $payload['action'] = $action;
            }

            return $this->flash('toast', $payload);
        });
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
                '',
                null,
                PortableVisibilityHandler::NO_PREDEFINED_VISIBILITY,
            );

            return new class(new Filesystem($adapter, $config), $adapter, $config) extends FilesystemAdapter
            {
                public function url($path): string
                {
                    return rtrim($this->config['url'], '/').'/'.ltrim($path, '/');
                }
            };
        });
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        JsonResource::withoutWrapping();

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
