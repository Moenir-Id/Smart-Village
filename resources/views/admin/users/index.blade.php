@extends('layouts.admin')
@section('title', 'Pengguna')
@section('content')

<style>
  /* Mobile: card list instead of table */
  .usr-tbl-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}
  .usr-cards{display:none;flex-direction:column;gap:0}
  .usr-card{display:flex;align-items:center;gap:12px;padding:13px 16px;border-bottom:1px solid var(--border)}
  .usr-card:last-child{border-bottom:none}
  @media(max-width:640px){
    .usr-tbl-wrap{display:none}
    .usr-cards{display:flex}
  }
</style>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px">
  <h2 style="font-size:19px;font-weight:800;margin:0;letter-spacing:-.02em">Pengguna</h2>
  <a href="{{ route('admin.users.create') }}" class="btn btn-p btn-sm">
    <i class="bi bi-plus-lg"></i> Tambah
  </a>
</div>

<div class="card">
  <div class="ch"><i class="bi bi-people-fill"></i> Daftar Pengguna</div>

  {{-- Desktop table --}}
  <div class="usr-tbl-wrap">
    <table class="tbl">
      <thead>
        <tr><th>Nama</th><th>Email</th><th>Role</th><th style="text-align:right">Aksi</th></tr>
      </thead>
      <tbody>
        @forelse($users as $u)
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:10px">
              <div style="width:32px;height:32px;border-radius:50%;background:var(--p-md);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:var(--p);flex-shrink:0">
                {{ strtoupper(substr($u->name,0,2)) }}
              </div>
              <span style="font-weight:600">{{ $u->name }}</span>
            </div>
          </td>
          <td style="color:var(--subtle)">{{ $u->email }}</td>
          <td>
            @php $rc=match($u->role){'admin'=>['#dbeafe','#1e40af'],'petugas'=>['#dcfce7','#166534'],default=>['#f3f4f6','#374151']}; @endphp
            <span class="bdg" style="background:{{ $rc[0] }};color:{{ $rc[1] }};text-transform:uppercase;font-size:10.5px;letter-spacing:.07em">{{ $u->role }}</span>
          </td>
          <td>
            <div style="display:flex;gap:6px;justify-content:flex-end">
              <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-ol btn-sm"><i class="bi bi-pencil"></i> Edit</a>
              @if($u->id !== auth()->id())
              <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Hapus pengguna ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm" style="background:#fef2f2;color:#dc2626;border:1.5px solid #fecaca"><i class="bi bi-trash"></i></button>
              </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center;padding:48px;color:var(--subtle)">Belum ada pengguna.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Mobile card list --}}
  <div class="usr-cards">
    @forelse($users as $u)
    @php $rc=match($u->role){'admin'=>['#dbeafe','#1e40af'],'petugas'=>['#dcfce7','#166534'],default=>['#f3f4f6','#374151']}; @endphp
    <div class="usr-card">
      <div style="width:40px;height:40px;border-radius:50%;background:var(--p-md);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:var(--p);flex-shrink:0">
        {{ strtoupper(substr($u->name,0,2)) }}
      </div>
      <div style="flex:1;min-width:0">
        <div style="font-weight:700;font-size:13.5px">{{ $u->name }}</div>
        <div style="font-size:12px;color:var(--subtle);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $u->email }}</div>
        <span class="bdg" style="background:{{ $rc[0] }};color:{{ $rc[1] }};text-transform:uppercase;font-size:10px;letter-spacing:.06em;margin-top:4px">{{ $u->role }}</span>
      </div>
      <div style="display:flex;flex-direction:column;gap:6px;flex-shrink:0">
        <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-ol btn-sm"><i class="bi bi-pencil"></i></a>
        @if($u->id !== auth()->id())
        <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Hapus pengguna ini?')">
          @csrf @method('DELETE')
          <button class="btn btn-sm" style="background:#fef2f2;color:#dc2626;border:1.5px solid #fecaca"><i class="bi bi-trash"></i></button>
        </form>
        @endif
      </div>
    </div>
    @empty
    <div style="text-align:center;padding:48px;color:var(--subtle)">
      <i class="bi bi-people" style="font-size:32px;display:block;margin-bottom:10px;opacity:.25"></i>
      <div style="font-weight:600;margin-bottom:4px">Belum ada pengguna</div>
    </div>
    @endforelse
  </div>
</div>
@endsection
