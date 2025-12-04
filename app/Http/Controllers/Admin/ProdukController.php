<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\KategoriProduk;
use Illuminate\Support\Str;

class ProdukController extends Controller
{
    public function index()
    {
        // ✅ hanya produk yang sudah di-ACC (approved) tampil di list utama admin
        $dataProduk = Produk::where('status', 'approved')->latest()->get();

        return view('admin.produk.index-produk', [
            'produks' => $dataProduk
        ]);
    }

    // ✅ halaman khusus untuk produk pending (menunggu ACC)
    public function pending()
    {
        $dataProduk = Produk::where('status', 'pending')->latest()->get();

        return view('admin.produk.produk-pending', [
            'produks' => $dataProduk
        ]);
    }

    // ✅ admin ACC produk
    public function approve($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->update(['status' => 'approved']);

        return back()->with('success', 'Produk berhasil di-ACC.');
    }

    // ✅ admin tolak produk
    public function reject($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->update(['status' => 'rejected']);

        return back()->with('success', 'Produk berhasil ditolak.');
    }

    public function create()
    {
        $kategoriProduks = KategoriProduk::all();

        return view('admin.produk.create-produk', [
            'kategoriProduks' => $kategoriProduks
        ]);
    }

    public function edit($id)
    {
        $kategoriProduks = KategoriProduk::all();
        $produk = Produk::findOrFail($id);

        return view('admin.produk.edit-produk', [
            'kategoriProduks' => $kategoriProduks,
            'produk' => $produk
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_produk' => 'required|string',
            'kategori_produk_id' => 'required|exists:kategori_produk,id',
            'nama_produk' => 'required|string',
            'deskripsi' => 'required|string',
            'harga' => 'required|integer',
            'stok' => 'required|integer',
        ]);

        $data = $request->all();

        // ✅ slug otomatis
        $data['slug'] = Str::slug($data['nama_produk']);

        // ✅ produk yang dibuat admin langsung approved
        $data['status'] = 'approved';

        Produk::create($data);

        return redirect()->route('admin.produk-index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'kode_produk' => 'required|string',
            'kategori_produk_id' => 'required|exists:kategori_produk,id',
            'nama_produk' => 'required|string',
            'deskripsi' => 'required|string',
            'harga' => 'required|integer',
            'stok' => 'required|integer',
        ]);

        $data['slug'] = Str::slug($data['nama_produk']);

        // ✅ jaga-jaga: kalau admin edit, tetap approved
        $data['status'] = 'approved';

        Produk::where('id', $id)->update($data);

        return redirect()->route('admin.produk-index')
            ->with('success', 'Data Produk berhasil diupdate.');
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->delete();

        return redirect()->route('admin.produk-index')
            ->with('success', 'Data Produk berhasil dihapus.');
    }
}
