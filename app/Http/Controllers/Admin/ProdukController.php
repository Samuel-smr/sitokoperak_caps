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
        $dataProduk = Produk::where('status', 'approved')->latest()->get();

        return view('admin.produk.index-produk', [
            'produks' => $dataProduk
        ]);
    }

    // ✅ halaman produk pending + rejected (dalam 1 view)
    public function pending()
    {
        $pendingProduk  = Produk::where('status', 'pending')->latest()->get();
        $rejectedProduk = Produk::where('status', 'rejected')->latest()->get();

        return view('admin.produk.produk-pending', [
            'produks' => $pendingProduk,
            'rejectedProduks' => $rejectedProduk,
            'kategoriProduks' => KategoriProduk::all(), // untuk dropdown modal edit
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
        $data['slug'] = Str::slug($data['nama_produk']);
        $data['status'] = 'approved';

        Produk::create($data);

        return redirect()->route('admin.produk-index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    // ✅ penting: JANGAN set status approved di update (biar pending/rejected tidak auto-approved saat edit)
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

        Produk::where('id', $id)->update($data);

        return back()->with('success', 'Data Produk berhasil diupdate.');
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }
}
