<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendTestEmail;
use App\Models\Produk;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {

            $stokhampirhabis = Produk::where('stok', '<', 5)->get();

            if ($stokhampirhabis->isEmpty()) {
                return;
            }

            $message = "Daftar produk yang stoknya hampir habis.";

            Mail::to('samuelriguntoro@gmail.com')
                ->send(new SendTestEmail($message, $stokhampirhabis));

        })->everyFiveMinutes();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
