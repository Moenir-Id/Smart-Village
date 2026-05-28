@extends('layouts.admin')
@section('title', isset($jenisSurat) ? 'Edit Jenis Surat' : 'Tambah Jenis Surat')
@section('content')

<div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap">
  <a href="{{ route('admin.jenis-surat.index') }}" class="btn btn-ol btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
  <h2 style="font-size:19px;font-weight:800;margin:0;letter-spacing:-.02em">
    {{ isset($jenisSurat) ? 'Edit' : 'Tambah' }} Jenis Surat
  </h2>
</div>

<div class="card" style="max-width:580px">
  <div class="cb" style="padding:20px">
    <form method="POST" action="{{ isset($jenisSurat) ? route('admin.jenis-surat.update', $jenisSurat) : route('admin.jenis-surat.store') }}">
      @csrf
      @if(isset($jenisSurat)) @method('PUT') @endif

      <div style="margin-bottom:16px">
        <label class="fl">Nama Jenis Surat <span style="color:#ef4444">*</span></label>
        <input type="text" name="nama" value="{{ old('nama', $jenisSurat->nama ?? '') }}"
               class="fc @error('nama') is-invalid @enderror" required>
        @error('nama')<div class="fi">{{ $message }}</div>@enderror
      </div>

      <div style="margin-bottom:16px">
        <label class="fl">Urutan</label>
        <input type="number" name="urutan" value="{{ old('urutan', $jenisSurat->urutan ?? 0) }}"
               class="fc" min="0" style="max-width:120px">
      </div>

      <div style="margin-bottom:16px">
        <label class="fl">Persyaratan <span style="color:var(--subtle);font-weight:400;font-size:12px">(opsional)</span></label>
        <textarea name="persyaratan" rows="3" class="fc"
                  placeholder="Daftar persyaratan…">{{ old('persyaratan', $jenisSurat->persyaratan ?? '') }}</textarea>
      </div>

      <div style="margin-bottom:24px">
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:13px 15px;background:var(--bg);border-radius:9px;border:1.5px solid var(--border)">
          <input type="checkbox" name="aktif" value="1"
                 {{ old('aktif', $jenisSurat->aktif ?? true) ? 'checked' : '' }}
                 style="width:17px;height:17px;accent-color:var(--p);cursor:pointer">
          <div>
            <div style="font-size:13.5px;font-weight:600">Aktif</div>
            <div style="font-size:11.5px;color:var(--subtle);margin-top:1px">Tampilkan jenis surat ini di halaman warga</div>
          </div>
        </label>
      </div>

      <button type="submit" class="btn btn-p"><i class="bi bi-check-lg"></i> Simpan</button>
    </form>
  </div>
</div>
@endsection
