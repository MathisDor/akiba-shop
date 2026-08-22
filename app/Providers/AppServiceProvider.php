<?php

namespace App\Providers;

use App\Services\CartService;
use App\Services\Payments\EbillingPaymentGateway;
use App\Services\Payments\ManualPaymentGateway;
use App\Services\Payments\PaymentGatewayInterface;
use Illuminate\Filesystem\LocalFilesystemAdapter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Local\LocalFilesystemAdapter as LocalAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\Visibility;
use League\MimeTypeDetection\ExtensionMimeTypeDetector;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CartService::class);
        $this->app->bind(PaymentGatewayInterface::class, function ($app) {
            $configured = config('services.ebilling.username') && config('services.ebilling.shared_key');

            return $configured ? $app->make(EbillingPaymentGateway::class) : $app->make(ManualPaymentGateway::class);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        if (! extension_loaded('fileinfo')) {
            $this->registerLocalDriverWithoutFileinfo();
        }
    }

    /**
     * Some shared hosts (e.g. CloudStore Africa) don't ship ext-fileinfo,
     * which Flysystem's local adapter needs for MIME detection and which
     * otherwise throws on every request touching the local/public disk.
     * Reimplements FilesystemManager::createLocalDriver() with an
     * extension-based MIME detector instead. Self-resolves once the host
     * installs the extension, since the check above will then be false.
     */
    private function registerLocalDriverWithoutFileinfo(): void
    {
        Storage::extend('local', function ($app, array $config) {
            $visibility = PortableVisibilityConverter::fromArray(
                $config['permissions'] ?? [],
                $config['directory_visibility'] ?? $config['visibility'] ?? Visibility::PRIVATE
            );

            $links = ($config['links'] ?? null) === 'skip'
                ? LocalAdapter::SKIP_LINKS
                : LocalAdapter::DISALLOW_LINKS;

            $adapter = new LocalAdapter(
                $config['root'], $visibility, $config['lock'] ?? LOCK_EX, $links,
                new ExtensionMimeTypeDetector(),
            );

            $flysystem = new Flysystem($adapter, Arr::only($config, [
                'directory_visibility', 'disable_asserts', 'retain_visibility', 'temporary_url', 'url', 'visibility',
            ]));

            return (new LocalFilesystemAdapter($flysystem, $adapter, $config))
                ->shouldServeSignedUrls($config['serve'] ?? false, fn () => $app['url']);
        });
    }
}
