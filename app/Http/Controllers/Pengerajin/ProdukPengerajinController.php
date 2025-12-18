<?php

namespace App\Http\Controllers\Pengerajin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;
use Illuminate\Support\Facades\Auth;
use App\Models\UsahaPengerajin;
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

        if (!$pengerajin) {
            return view('pengerajin.dashboard', ['produk' => collect()]);
        }

        $pengerajinId = $pengerajin->id;

        // ambil usaha_id milik pengerajin
        $usahaId = UsahaPengerajin::where('pengerajin_id', $pengerajinId)->value('usaha_id');

        if (!$usahaId) {
            return view('pengerajin.dashboard', ['produk' => collect()]);
        }

        // ambil semua produk_id dalam usaha tersebut
        $produkIds = UsahaProduk::where('usaha_id', $usahaId)->pluck('produk_id');

        // ✅ hanya tampilkan produk yang sudah di-ACC admin
        $produk = Produk::whereIn('id', $produkIds)
            ->where('status', 'approved')
            ->latest()
            ->get();

        return view('pengerajin.produk.produk', compact('produk'));
    }

    public function produk_all()
    {
        // ✅ hanya tampilkan produk yang sudah di-ACC admin
        $produk = Produk::where('status', 'approved')->latest()->get();

        return view('pengerajin.produk_all.produk_all', [
            'title' => 'Semua Produk',
            'produk' => $produk
        ]);
    }

    public function create()
    {
        $kategori = KategoriProduk::all();
        return view('pengerajin.produk.create_produk', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required',
            'kode_produk' => 'required',
            'harga' => 'required|numeric',
            'stok' => 'required|numeric',
            'gambar' => 'required|image|max:2048',
            'deskripsi' => 'required',
            'slug' => 'required',
            'kategori_produk_id' => 'required'
        ]);

        $pengerajin = Auth::user()->pengerajin;
        if (!$pengerajin) {
            return back()->with('error', 'Akun kamu belum terhubung ke data pengerajin.');
        }

        $pengerajinId = $pengerajin->id;

        // ambil usaha_id milik pengerajin
        $usahaId = UsahaPengerajin::where('pengerajin_id', $pengerajinId)->value('usaha_id');
        if (!$usahaId) {
            return back()->with('error', 'Kamu belum terdaftar pada usaha manapun. Hubungi admin.');
        }

        $data = $request->except('gambar');
        $data['pengerajin_id'] = $pengerajinId;

        // ✅ produk baru = pending (belum tampil di daftar pengerajin)
        $data['status'] = 'pending';

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('produk', 'public');
        }

        DB::transaction(function () use ($data, $usahaId) {
            $produk = Produk::create($data);

            DB::table('usaha_produk')->insert([
                'usaha_id' => $usahaId,
                'produk_id' => $produk->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        });

        return redirect()->route('pengerajin.produk')
            ->with('success', 'Produk berhasil diajukan dan menunggu ACC admin.');
    }

    public function edit($id)
    {
        $produk = Produk::findOrFail($id);
        $kategori = KategoriProduk::all();

        return view('pengerajin.produk.edit_produk', compact('produk', 'kategori'));
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        $request->validate([
            'nama_produk' => 'required',
            'kode_produk' => 'required',
            'harga' => 'required|numeric',
            'stok' => 'required|numeric',
            'gambar' => 'nullable|image|max:2048',
            'deskripsi' => 'required',
            'slug' => 'required',
            'kategori_produk_id' => 'required'
        ]);

        $data = $request->except('gambar');

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('produk', 'public');
        }

        // ✅ kalau pengerajin edit → balik pending lagi (harus ACC ulang)
        // Kalau kamu TIDAK mau ACC ulang saat edit, hapus baris ini:
        $data['status'] = 'pending';

        $produk->update($data);

        return redirect()->route('pengerajin.produk')
            ->with('success', 'Produk berhasil diperbarui dan menunggu ACC admin.');
    }

    public function delete($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->delete();

        return redirect()->route('pengerajin.produk')
            ->with('success', 'Produk berhasil dihapus.');
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
