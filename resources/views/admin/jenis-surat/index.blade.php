@extends('layouts.admin')
@section('title', 'Jenis Surat')
@section('content')

<style>
  .js-tbl-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}
  .js-cards{display:none;flex-direction:column;gap:0}
  .js-card{display:flex;align-items:center;gap:12px;padding:13px 16px;border-bottom:1px solid var(--border)}
  .js-card:last-child{border-bottom:none}
  @media(max-width:600px){
    .js-tbl-wrap{display:none}
    .js-cards{display:flex}
  }
</style>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px">
  <h2 style="font-size:19px;font-weight:800;margin:0;letter-spacing:-.02em">Jenis Surat</h2>
  <a href="{{ route('admin.jenis-surat.create') }}" class="btn btn-p btn-sm"><i class="bi bi-plus-lg"></i> Tambah</a>
</div>

<div class="card">
  {{-- Desktop table --}}
  <div class="js-tbl-wrap">
    <table class="tbl">
      <thead>
        <tr><th>No</th><th>Nama</th><th>Template</th><th>Status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        @forelse($jenisSurat as $js)
        <tr>
          <td class="mono" style="color:var(--subtle)">{{ $js->urutan }}</td>
          <td style="font-weight:600">{{ $js->nama }}</td>
          <td>
            @if($js->hasTemplate())
              <span class="bdg" style="background:#dcfce7;color:#166534"><i class="bi bi-check-lg"></i> Ada</span>
            @else
              <span class="bdg bdg-gray">Belum</span>
            @endif
          </td>
          <td><span class="bdg {{ $js->aktif ? 'bdg-selesai' : 'bdg-gray' }}">{{ $js->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="{{ route('admin.jenis-surat.edit', $js) }}" class="btn btn-ol btn-sm">Edit</a>
              <form method="POST" action="{{ route('admin.jenis-surat.destroy', $js) }}" onsubmit="return confirm('Hapus jenis surat ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm" style="background:#fef2f2;color:#dc2626;border:1.5px solid #fecaca">Hapus</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center;color:var(--subtle);padding:40px">Belum ada jenis surat.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Mobile card list --}}
  <div class="js-cards">
    @forelse($jenisSurat as $js)
    <div class="js-card">
      <div style="width:36px;height:36px;border-radius:9px;background:var(--p-lt);display:flex;align-items:center;justify-content:center;font-size:14px;color:var(--p);flex-shrink:0">
        <i class="bi bi-collection-fill"></i>
      </div>
      <div style="flex:1;min-width:0">
        <div style="font-weight:700;font-size:13.5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $js->nama }}</div>
        <div style="display:flex;gap:6px;margin-top:4px;flex-wrap:wrap">
          <span class="bdg {{ $js->aktif ? 'bdg-selesai' : 'bdg-gray' }}" style="font-size:10px">{{ $js->aktif ? 'Aktif' : 'Nonaktif' }}</span>
          @if($js->hasTemplate())
            <span class="bdg" style="background:#dcfce7;color:#166534;font-size:10px"><i class="bi bi-check-lg"></i> Template</span>
          @else
            <span class="bdg bdg-gray" style="font-size:10px">Belum ada template</span>
          @endif
        </div>
      </div>
      <div style="display:flex;flex-direction:column;gap:6px;flex-shrink:0">
        <a href="{{ route('admin.jenis-surat.edit', $js) }}" class="btn btn-ol btn-sm"><i class="bi bi-pencil"></i></a>
        <form method="POST" action="{{ route('admin.jenis-surat.destroy', $js) }}" onsubmit="return confirm('Hapus jenis surat ini?')">
          @csrf @method('DELETE')
          <button class="btn btn-sm" style="background:#fef2f2;color:#dc2626;border:1.5px solid #fecaca"><i class="bi bi-trash"></i></button>
        </form>
      </div>
    </div>
    @empty
    <div style="text-align:center;padding:40px;color:var(--subtle)">Belum ada jenis surat.</div>
    @endforelse
  </div>
</div>
@endsection
