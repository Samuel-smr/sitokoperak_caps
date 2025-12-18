<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendTestEmail;
use App\Models\Produk;

Schedule::call(function () {

    $stokhampirhabis = Produk::where('stok', '<', 5)->get();

    if ($stokhampirhabis->isEmpty()) {
        return;
    }

    $message = "Daftar produk yang stoknya hampir habis.";

    Mail::to('samuelriguntoro@gmail.com')
        ->send(new SendTestEmail($message, $stokhampirhabis));

})->everyFiveMinutes();
