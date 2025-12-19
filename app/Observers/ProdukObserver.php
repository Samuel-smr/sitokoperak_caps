<?php

namespace App\Observers;

use App\Models\Produk;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendTestEmail;

class ProdukObserver
{
    /**
     * Handle the Produk "created" event.
     */
    public function created(Produk $produk): void
    {
        //
    }

    /**
     * Handle the Produk "updated" event.
     */
    public function updated(Produk $produk)
    {
        // cek perubahan stok
        if ($produk->isDirty('stok')) {

            // ambil produk stok < 5
            $produkMenipis = Produk::where('stok', '<', 5)->get();

            if ($produkMenipis->isNotEmpty()) {

                $message = "Ada produk yang stoknya hampir habis.";

                Mail::to('samuelriguntoro@gmail.com')
                    ->send(new SendTestEmail($message, $produkMenipis));
            }
        }
    }

    /**
     * Handle the Produk "deleted" event.
     */
    public function deleted(Produk $produk): void
    {
        //
    }

    /**
     * Handle the Produk "restored" event.
     */
    public function restored(Produk $produk): void
    {
        //
    }

    /**
     * Handle the Produk "force deleted" event.
     */
    public function forceDeleted(Produk $produk): void
    {
        //
    }
}
