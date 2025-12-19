@extends('adminlte::page')

@section('title', 'Produk Pending')

@section('content_header')
    <h1>Produk Menunggu ACC</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- ===================== PENDING TABLE ===================== --}}
    @if($produks->isEmpty())
        <div class="alert alert-info">Tidak ada produk pending.</div>
    @else
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Kode</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th style="width:320px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produks as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p->nama_produk }}</td>
                                <td>{{ $p->kode_produk }}</td>
                                <td>{{ optional($p->kategoriProduk)->nama_kategori ?? '-' }}</td>
                                <td>{{ $p->harga }}</td>
                                <td>{{ $p->stok }}</td>
                                <td class="d-flex gap-2 flex-wrap">

                                    {{-- ACC --}}
                                    <form action="{{ route('admin.produk-approve', $p->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-success btn-sm" type="submit">ACC</button>
                                    </form>

                                    {{-- Tolak --}}
                                    <form action="{{ route('admin.produk-reject', $p->id) }}" method="POST"
                                          onsubmit="return confirm('Yakin tolak produk ini?')">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-warning btn-sm" type="submit">Tolak</button>
                                    </form>

                                    {{-- Edit (Modal) --}}
                                    <button
                                        type="button"
                                        class="btn btn-primary btn-sm btn-edit-produk"
                                        data-toggle="modal"
                                        data-target="#modalEditProduk"
                                        data-id="{{ $p->id }}"
                                        data-kode="{{ $p->kode_produk }}"
                                        data-kategori="{{ $p->kategori_produk_id }}"
                                        data-nama="{{ $p->nama_produk }}"
                                        data-deskripsi="{{ $p->deskripsi }}"
                                        data-harga="{{ $p->harga }}"
                                        data-stok="{{ $p->stok }}"
                                    >
                                        Edit
                                    </button>

                                    {{-- Delete --}}

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ===================== REJECTED TABLE ===================== --}}
    <hr class="my-4">
    <h4 class="mb-3">Produk Ditolak</h4>

    @if($rejectedProduks->isEmpty())
        <div class="alert alert-info">Tidak ada produk ditolak.</div>
    @else
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Kode</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th style="width:260px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rejectedProduks as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p->nama_produk }}</td>
                                <td>{{ $p->kode_produk }}</td>
                                <td>{{ optional($p->kategoriProduk)->nama_kategori ?? '-' }}</td>
                                <td>{{ $p->harga }}</td>
                                <td>{{ $p->stok }}</td>
                                <td class="d-flex gap-2 flex-wrap">

                                    {{-- ACC --}}
                                    <form action="{{ route('admin.produk-approve', $p->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-success btn-sm" type="submit">ACC</button>
                                    </form>

                                    {{-- Edit (Modal) --}}
                                    <button
                                        type="button"
                                        class="btn btn-primary btn-sm btn-edit-produk"
                                        data-toggle="modal"
                                        data-target="#modalEditProduk"
                                        data-id="{{ $p->id }}"
                                        data-kode="{{ $p->kode_produk }}"
                                        data-kategori="{{ $p->kategori_produk_id }}"
                                        data-nama="{{ $p->nama_produk }}"
                                        data-deskripsi="{{ $p->deskripsi }}"
                                        data-harga="{{ $p->harga }}"
                                        data-stok="{{ $p->stok }}"
                                    >
                                        Edit
                                    </button>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.produk-destroy', $p->id) }}" method="POST"
                                          onsubmit="return confirm('Yakin hapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                                    </form>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ===================== MODAL EDIT ===================== --}}
    <div class="modal fade" id="modalEditProduk" tabindex="-1" role="dialog" aria-labelledby="modalEditProdukLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <form id="formEditProduk" method="POST">
          @csrf
          @method('PUT')

          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalEditProdukLabel">Edit Produk</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <div class="modal-body">
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label>Kode Produk</label>
                  <input type="text" class="form-control" name="kode_produk" id="edit_kode_produk" required>
                </div>

                <div class="form-group col-md-6">
                  <label>Kategori</label>
                  <select class="form-control" name="kategori_produk_id" id="edit_kategori_produk_id" required>
                    @foreach($kategoriProduks as $k)
                      <option value="{{ $k->id }}">
                        {{ $k->nama_kategori ?? $k->nama ?? 'Kategori' }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" class="form-control" name="nama_produk" id="edit_nama_produk" required>
              </div>

              <div class="form-group">
                <label>Deskripsi</label>
                <textarea class="form-control" name="deskripsi" id="edit_deskripsi" rows="3" required></textarea>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label>Harga</label>
                  <input type="number" class="form-control" name="harga" id="edit_harga" required>
                </div>

                <div class="form-group col-md-6">
                  <label>Stok</label>
                  <input type="number" class="form-control" name="stok" id="edit_stok" required>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
          </div>
        </form>
      </div>
    </div>
@stop

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.btn-edit-produk');
    const form = document.getElementById('formEditProduk');

    buttons.forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;

            // set action form edit ke route update (sesuai routes kamu: admin/produk/update/{id})
            form.action = `{{ url('admin/produk/update') }}/${id}`;

            document.getElementById('edit_kode_produk').value = this.dataset.kode || '';
            document.getElementById('edit_kategori_produk_id').value = this.dataset.kategori || '';
            document.getElementById('edit_nama_produk').value = this.dataset.nama || '';
            document.getElementById('edit_deskripsi').value = this.dataset.deskripsi || '';
            document.getElementById('edit_harga').value = this.dataset.harga || 0;
            document.getElementById('edit_stok').value = this.dataset.stok || 0;
        });
    });
});
</script>
@endpush
