<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Mail;
use App\Models\Produk;
use App\Mail\StokHampirHabisMail;

Schedule::call(function () {

    $batasStok = 5;

    // cek perubahan stok
    $adaPerubahanStok = Produk::whereColumn('stok', '!=', 'last_checked_stock')
        ->whereNotNull('last_checked_stock')
        ->exists();

    if ($adaPerubahanStok) {

        // ambil semua produk stok < 5
        $produkMenipis = Produk::where('stok', '<', $batasStok)->get();

        if ($produkMenipis->isNotEmpty()) {
            Mail::to('samuelriguntoro@gmail.com')
                ->send(new StokHampirHabisMail($produkMenipis));
        }

        // update snapshot stok
        Produk::query()->update([
            'last_checked_stock' => DB::raw('stok')
        ]);
    }

})->everyFiveMinutes();

