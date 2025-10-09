<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\KategoriProduk;
use App\Models\Produk;
use App\Models\Usaha;
use App\Models\FotoProduk;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $produks = Produk::with('kategoriProduk', 'fotoProduk')->get();
        return view('guest.pages.index', [
            'produks' => $produks,
        ]);
    }

    public function productsByCategory($slug)
    {
        $kategori = KategoriProduk::where('slug', $slug)->firstOrFail();
        $produks = Produk::where('kategori_produk_id', $kategori->id)->get();

        return view('guest.pages.products', [
            'kategori' => $kategori,
            'produks' => $produks,
        ]);
    }

    public function katalog(Request $request)
{
    $query = Produk::with('kategoriProduk', 'fotoProduk');

    if ($request->filled('kategori')) {
        $query->whereHas('kategoriProduk', function ($q) use ($request) {
            $q->where('slug', $request->kategori);
        });
    }

    if ($request->filled('min_harga')) {
        $query->where('harga', '>=', $request->min_harga);
    }
    if ($request->filled('max_harga')) {
        $query->where('harga', '<=', $request->max_harga);
    }

    if ($request->filled('urutkan')) {
        if ($request->urutkan == 'terlaris') {
            $query->orderBy('jumlah_terjual', 'desc');
        }
        else {
            $query->latest(); 
        }
    } else {
        $query->latest();
    }

    $produks = $query->paginate(12)->appends($request->query());

    $kategoris = KategoriProduk::all();

    return view('guest.pages.katalog', [
        'produks' => $produks,
        'kategoris' => $kategoris,
    ]);
}

    public function singleProduct($slug)
    {
        $produk = Produk::where('slug', $slug)->firstOrFail();
        return view('guest.pages.single-product',[
            'produk' => $produk,
        ]);
    }

    public function detailUsaha(Request $request, Usaha $usaha)
    {
        $usaha->load('pengerajins', 'produks');
        $previousProduct = null;

        if ($request->has('from_product')) {
            $previousProduct = Produk::where('slug', $request->from_product)->first();
        }
            
        return view('guest.pages.detail-usaha', [
            'usaha' => $usaha,
            'produks' => $usaha->produks,
            'previousProduct' => $previousProduct, 
        ]);
    }

    public function about()
    {
        return view('guest.pages.about');
    }
    public function contact()
    {
        return view('guest.pages.contact');
    }
}
