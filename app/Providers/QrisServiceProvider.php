<?php

namespace App\Providers;

use App\Models\Pengaturan;
use App\Services\Qris\GatewayQrisProvider;
use App\Services\Qris\ManualQrisProvider;
use App\Services\Qris\QrisProvider;
use Illuminate\Support\ServiceProvider;

class QrisServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Dipilih saat dipakai (bukan singleton) supaya perubahan di Pengaturan langsung berlaku.
        $this->app->bind(QrisProvider::class, function ($app) {
            $provider = config('qris.provider') ?: Pengaturan::ambil('qris_provider', 'manual');

            return $provider === 'gateway'
                ? $app->make(GatewayQrisProvider::class)
                : $app->make(ManualQrisProvider::class);
        });
    }
}
