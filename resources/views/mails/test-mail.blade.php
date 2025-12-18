<div>
    <h2>{{ $mailMessage }}</h2>

    <h3>Daftar Produk dengan Stok kurang dari 5</h3>
    <ul>
        @forelse ($stokhampirhabis as $produk)
            <li>
                <strong>{{ $produk->nama_produk }}</strong> — Stok: {{ $produk->stok }}
            </li>
        @empty
            <li>Tidak ada produk yang stoknya kurang dari 5.</li>
        @endforelse
    </ul>
</div>
