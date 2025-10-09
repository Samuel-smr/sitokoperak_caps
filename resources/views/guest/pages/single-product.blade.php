@extends('guest.layouts.main')

@section('title', $produk->nama_produk)

@push('styles')
    {{-- Memuat file CSS khusus untuk halaman ini --}}
    <link rel="stylesheet" href="{{ asset('assets/css/detail-product.css') }}">
@endpush

@section('content')
    {{-- Breadcrumb Navigation --}}
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <nav aria-label="breadcrumb" class="product-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('guest-katalog') }}">Katalog</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $produk->nama_produk }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    {{-- Product Detail Section --}}
    <section class="section" id="product">
        <div class="container">
            <div class="row">
                {{-- Kolom Kiri: Galeri Gambar Produk --}}
                <div class="col-lg-6">
                    <div class="gallery-wrapper">
                        {{-- Gambar Utama --}}
                        <div class="main-image-container mb-3">
                            @if($produk->fotoProduk->isNotEmpty())
                                <img src="{{ asset('storage/' . $produk->fotoProduk->first()->file_foto_produk) }}" alt="{{ $produk->nama_produk }}" id="mainProductImage" class="img-fluid">
                            @else
                                <img src="{{ asset('assets/images/produk-default.jpg') }}" alt="Produk Default" id="mainProductImage" class="img-fluid">
                            @endif
                        </div>
                        {{-- Thumbnail Gambar --}}
                        <div class="thumbnail-container">
                            @foreach ($produk->fotoProduk as $foto)
                                <div class="thumbnail-item">
                                    <img src="{{ asset('storage/' . $foto->file_foto_produk) }}" alt="Thumbnail {{ $produk->nama_produk }}" class="img-fluid" onclick="changeMainImage(this)">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Informasi Produk --}}
                <div class="col-lg-6">
                    {{-- GANTI SELURUH ISI DARI <div class="right-content"> DENGAN KODE INI --}}
                    <div class="right-content">

                        {{-- BARIS 1: JUDUL PRODUK DAN IKON AKSI --}}
                        <div class="product-header">
                            <h2 class="product-title">{{ $produk->nama_produk }}</h2>
                            <div class="action-icons">
                                <button class="btn btn-icon" id="copyLinkBtn" title="Bagikan Tautan">
                                    <i class="fa fa-share-alt"></i>
                                </button>
                            </div>
                        </div>

                        <div class="rating-stock-wrapper">
                            <div class="rating-wrapper">
                                <div class="stars-custom">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </div>
                            </div>
                            <span class="stock-status">IN STOCK</span>
                        </div>
                        <span class="price">Rp {{ number_format($produk->harga, 0, ',', '.') }}</span>
                        @php
                            $usaha = $produk->usaha->first();
                        @endphp

                        <div class="usaha-info">
                            @if ($usaha)
                                <a href="{{ route('guest-detail-usaha', ['usaha' => $usaha, 'from_product' => $produk->slug]) }}" class="usaha-link">
                                    <img src="{{ asset('assets/images/kategori-default.jpg') }}" alt="Logo Usaha" class="usaha-avatar">
                                    <div class="usaha-details">
                                        <span class="usaha-name">{{ $usaha->nama_usaha }}</span>
                                        <span class="usaha-spesialisasi">{{ $usaha->deskripsi_usaha ?? 'Kerajinan Perak Kotagede' }}</span>
                                    </div>
                                </a>
                                <div class="social-links">
                                    <a href="#" target="_blank" class="social-icon email" title="Email"><i class="fa fa-envelope"></i></a>
                                    <a href="https://wa.me/" target="_blank" class="social-icon whatsapp" title="WhatsApp"><i class="fa fa-phone"></i></a>
                                    <a href="#" target="_blank" class="social-icon instagram" title="Instagram"><i class="fa fa-instagram"></i></a>
                                    <a href="#" target="_blank" class="social-icon tiktok" title="TikTok"><i class="fa fa-tiktok"></i></a>
                                    <a href="#" target="_blank" class="social-icon shopee" title="Shopee">
                                        <img src="{{ asset('assets/images/shopee-icon.png') }}" alt="Shopee">
                                    </a>
                                    <a href="#" target="_blank" class="social-icon tokped" title="Tokopedia">
                                        <img src="{{ asset('assets/images/tokopedia-icon.png') }}" alt="Tokopedia">
                                    </a>
                                </div>
                            @else
                                <p class="text-muted">Informasi usaha tidak tersedia.</p>
                            @endif
                        </div>

                        {{-- DESKRIPSI DAN DETAIL PRODUK --}}
                        <div class="product-details">
                            <h5>Detail</h5>
                            <p>{{ $produk->deskripsi }}</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

<section class="products">
    <div class="container">
        <div class="section-heading">
            <h2>Produk Terkait</h2>
        </div>
    </div>
    <div class="container">
        <div class="row">
            @foreach ($randomProduks as $produk)
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="product-item">
                        <a href="{{ route('guest-singleProduct', $produk->slug) }}">
                            <div class="thumb">
                                <img src="{{ asset('storage/' . optional($produk->fotoProduk->first())->file_foto_produk) }}"
                                    alt="{{ $produk->nama_produk }}"
                                    onerror="this.onerror=null;this.src='{{ asset('images/produk-default.jpg') }}';">
                            </div>
                            <div class="down-content">
                                <h4>{{ $produk->nama_produk }}</h4>
                                <span class="product-price">Rp {{ number_format($produk->harga, 0, ',', '.') }}</span>
                                <ul class="stars">
                                    @for ($i = 0; $i < 5; $i++)
                                        <li><i class="fa fa-star"></i></li>
                                    @endfor
                                </ul>
                                <p class="product-reviews">20 Reviews</p>
                            </div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="col-lg-12">
            <div class="text-center mt-5">
                <a href="{{ route('guest-katalog') }}" class="see-all-button btn">Lihat Semua</a>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Fungsi untuk mengubah gambar utama saat thumbnail diklik
    function changeMainImage(thumbnail) {
        const mainImage = document.getElementById('mainProductImage');
        mainImage.src = thumbnail.src;
    }

    // --- TAMBAHKAN KODE BARU DI BAWAH INI ---
    document.addEventListener('DOMContentLoaded', function() {
        const copyBtn = document.getElementById('copyLinkBtn');

        if (copyBtn) {
            copyBtn.addEventListener('click', function() {
                // Ambil URL halaman saat ini
                const urlToCopy = window.location.href;

                // Gunakan Clipboard API untuk menyalin URL
                navigator.clipboard.writeText(urlToCopy).then(function() {
                    // Beri tahu pengguna bahwa link berhasil disalin
                    const icon = copyBtn.querySelector('i');
                    const originalIconClass = icon.className;

                    // Ganti ikon menjadi centang
                    icon.className = 'fa fa-check';
                    copyBtn.disabled = true; // Nonaktifkan tombol sejenak

                    // Kembalikan ikon seperti semula setelah 2 detik
                    setTimeout(function() {
                        icon.className = originalIconClass;
                        copyBtn.disabled = false;
                    }, 2000);

                }).catch(function(err) {
                    console.error('Gagal menyalin tautan: ', err);
                    // Cadangan jika browser tidak mendukung atau gagal
                    alert('Gagal menyalin tautan ke clipboard.');
                });
            });
        }
    });
</script>
@endpush