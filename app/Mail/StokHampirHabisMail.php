<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StokHampirHabisMail extends Mailable
{
    use Queueable, SerializesModels;

    public $produk;

    /**
     * Create a new message instance.
     */
    public function __construct($produk)
    {
        $this->produk = $produk;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Peringatan: Stok Produk Hampir Habis')
                    ->view('emails.stok_hampir_habis')
                    ->with([
                        'produk' => $this->produk
                    ]);
    }
}
