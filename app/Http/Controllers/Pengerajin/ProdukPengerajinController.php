<?php

namespace App\Http\Controllers\Pengerajin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Auth;
use App\Models\UsahaPengerajin;;
use App\Models\UsahaProduk;
use App\Models\KategoriProduk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\StokHampirHabisMail;

class ProdukPengerajinController extends Controller
{

    public function produk()
    {
        $pengerajin = Auth::user()->pengerajin;

        $pengerajinId = $pengerajin->id;

           // Cari usaha yang dimiliki oleh pengerajin
        $usahaId = UsahaPengerajin::where('pengerajin_id', $pengerajinId)
            ->value('usaha_id');

        if (!$usahaId) {
            return view('pengerajin.dashboard', ['produk' => collect()]);
        }

        // Cari semua produk dalam usaha tersebut
        $produkIds = UsahaProduk::where('usaha_id', $usahaId)->pluck('produk_id');

        $produk = Produk::whereIn('id', $produkIds)->get();
        return view('pengerajin.produk.produk',compact('produk'));
    }
    public function produk_all()
    {
        // Ambil semua produk dari tabel produk
        $produk = Produk::all();

        return view('pengerajin.produk_all.produk_all', [
            'title' => 'Semua Produk',
            'produk' => $produk
        ]);
    }

        // CREATE
    public function create()
    {
        $kategori = KategoriProduk::all();
        return view('pengerajin.produk.create_produk', compact('kategori'));
    }

    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required',
            'kode_produk' => 'required',
            'harga'       => 'required|numeric',
            'stok'        => 'required|numeric',
            'gambar'        => 'required|image|max:2048',
            'deskripsi'   => 'required',
            'slug'        => 'required',
            'kategori_produk_id' => 'required'
        ]);

        $data = $request->except('gambar');
        $data['pengerajin_id'] = Auth::user()->pengerajin->id;

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('produk', 'public');
        }

        // Insert ke tabel produk dan ambil instance
        $produk = Produk::create($data); // <-- $produk sekarang sudah ada

        // Insert ke tabel usaha_produk
        DB::table('usaha_produk')->insert([
            'usaha_id'   => Auth::user()->pengerajin->id,
            'produk_id'  => $produk->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('pengerajin.produk')->with('success', 'Produk berhasil ditambahkan');
    }


    // EDIT
    public function edit($id)
    {
        $produk   = Produk::findOrFail($id);
        $kategori = KategoriProduk::all();

        return view('pengerajin.produk.edit_produk', compact('produk', 'kategori'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        $request->validate([
            'nama_produk' => 'required',
            'kode_produk' => 'required',
            'harga'       => 'required|numeric',
            'stok'        => 'required|numeric',
            'gambar'      => 'nullable|image|max:2048',
            'deskripsi'   => 'required',
            'slug'   => 'required',
            'kategori_produk_id' => 'required'
        ]);

        $data = $request->except('gambar');

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('produk', 'public');
        }

        $produk->update($data);

        return redirect()->route('pengerajin.produk')->with('success', 'Produk berhasil diperbarui');
    }

    // DELETE
    public function delete($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->delete();

        return redirect()->route('pengerajin.produk')->with('success', 'Produk berhasil dihapus');
    }


    // cek stok hampir habis
    public function cekStokHampirHabis()
    {
        $batas = 5; // stok minimal
        $produk = Produk::where('stok', '<=', $batas)->get();

        foreach ($produk as $p) {
            Mail::to('samuelriguntoro@gmail.com')->send(new StokHampirHabisMail($p));
        }

        return 'Email peringatan stok telah dikirim.';
    }
}
