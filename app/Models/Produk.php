<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Produk extends Model
{
    protected $table = 'produk';

    protected $fillable = [
        'kode_produk',
        'kategori_produk_id',
        'pengerajin_id',
        'nama_produk',
        'slug',
        'deskripsi',
        'harga',
        'stok',
        'gambar',
        'status', // pending / approved / rejected
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($produk) {
            $produk->slug = self::generateUniqueSlug($produk->nama_produk);
        });

        static::updating(function ($produk) {
            // kalau nama berubah, update slug dan tetap unik
            if ($produk->isDirty('nama_produk')) {
                $produk->slug = self::generateUniqueSlug($produk->nama_produk, $produk->id);
            }
        });
    }

    private static function generateUniqueSlug(string $nama, ?int $ignoreId = null): string
    {
        $base = Str::slug($nama);
        $slug = $base;

        $query = self::where('slug', $slug);
        if ($ignoreId) $query->where('id', '!=', $ignoreId);

        $count = $query->count();
        $i = 2;

        while ($count > 0) {
            $slug = $base . '-' . $i;

            $query = self::where('slug', $slug);
            if ($ignoreId) $query->where('id', '!=', $ignoreId);

            $count = $query->count();
            $i++;
        }

        return $slug;
    }

    public function kategoriProduk()
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_produk_id');
    }

    public function fotoProduk()
    {
        return $this->hasMany(FotoProduk::class, 'produk_id');
    }

    public function usaha()
    {
        return $this->belongsToMany(Usaha::class, 'usaha_produk', 'produk_id', 'usaha_id');
    }

    public function pengerajin()
    {
        return $this->belongsTo(Pengerajin::class, 'pengerajin_id');
    }
}
