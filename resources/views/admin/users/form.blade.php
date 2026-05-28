@extends('layouts.admin')
@section('title', isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna')
@section('content')

<style>
  /* Role info cards: 3 col desktop → stacked mobile */
  .role-info{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:10px}
  @media(max-width:480px){.role-info{grid-template-columns:1fr}}
</style>

<div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap">
  <a href="{{ route('admin.users.index') }}" class="btn btn-ol btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
  <h2 style="font-size:19px;font-weight:800;margin:0;letter-spacing:-.02em">
    {{ isset($user) ? 'Edit' : 'Tambah' }} Pengguna
  </h2>
</div>

<div class="card" style="max-width:520px">
  <div class="ch"><i class="bi bi-person-fill"></i> {{ isset($user) ? 'Edit' : 'Data' }} Pengguna</div>
  <div class="cb" style="padding:20px">
    <form method="POST" action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}">
      @csrf
      @if(isset($user)) @method('PUT') @endif

      <div style="margin-bottom:16px">
        <label class="fl">Nama <span style="color:#ef4444">*</span></label>
        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}"
               class="fc @error('name') is-invalid @enderror" placeholder="Nama lengkap" required>
        @error('name')<div class="fi">{{ $message }}</div>@enderror
      </div>

      <div style="margin-bottom:16px">
        <label class="fl">Email <span style="color:#ef4444">*</span></label>
        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
               class="fc @error('email') is-invalid @enderror" placeholder="nama@desa.id" required>
        @error('email')<div class="fi">{{ $message }}</div>@enderror
      </div>

      <div style="margin-bottom:16px">
        <label class="fl">
          Password
          @if(isset($user))
            <span style="color:var(--subtle);font-weight:400;font-size:11.5px">(kosongkan jika tidak diganti)</span>
          @else
            <span style="color:#ef4444">*</span>
          @endif
        </label>
        <input type="password" name="password"
               class="fc @error('password') is-invalid @enderror"
               placeholder="••••••••"
               {{ isset($user) ? '' : 'required' }}>
        @error('password')<div class="fi">{{ $message }}</div>@enderror
      </div>

      <div style="margin-bottom:24px">
        <label class="fl">Role <span style="color:#ef4444">*</span></label>
        <select name="role" class="fc @error('role') is-invalid @enderror" required>
          <option value="admin"   {{ old('role', $user->role ?? '') === 'admin'   ? 'selected' : '' }}>Admin</option>
          <option value="petugas" {{ old('role', $user->role ?? '') === 'petugas' ? 'selected' : '' }}>Petugas</option>
          <option value="viewer"  {{ old('role', $user->role ?? '') === 'viewer'  ? 'selected' : '' }}>Viewer</option>
        </select>
        @error('role')<div class="fi">{{ $message }}</div>@enderror
        <div class="role-info">
          @foreach(['admin'=>['Akses penuh ke semua fitur','#dbeafe','#1e40af'],'petugas'=>['Kelola antrean surat','#dcfce7','#166534'],'viewer'=>['Hanya lihat data','#f3f4f6','#374151']] as $r=>[$desc,$bg,$clr])
          <div style="padding:10px 12px;background:{{ $bg }};border-radius:8px">
            <div style="font-size:11px;font-weight:700;color:{{ $clr }};text-transform:uppercase;letter-spacing:.06em;margin-bottom:2px">{{ ucfirst($r) }}</div>
            <div style="font-size:11px;color:{{ $clr }};opacity:.75">{{ $desc }}</div>
          </div>
          @endforeach
        </div>
      </div>

      <div style="display:flex;gap:8px;flex-wrap:wrap">
        <button type="submit" class="btn btn-p"><i class="bi bi-check-lg"></i> Simpan</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-ol">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
