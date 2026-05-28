@extends('layouts.admin')
@section('title','Laporan Bulanan')
@section('content')

<style>
  .lap-grid{display:grid;grid-template-columns:1.6fr 1fr;gap:16px;margin-bottom:20px}
  .lap-header-actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
  @media(max-width:700px){
    .lap-grid{grid-template-columns:1fr}
    .lap-header-actions{width:100%}
    .lap-header-actions .btn{flex:1;justify-content:center;font-size:12px;padding:7px 10px}
    .lap-header-actions input[type=month]{flex:1}
  }
  /* Stats: 5→3→2 cols */
  @media(max-width:600px){.lap-sg{grid-template-columns:repeat(3,1fr)!important}}
  @media(max-width:360px){.lap-sg{grid-template-columns:repeat(2,1fr)!important}}

  /* Mobile table: hide non-essential cols */
  @media(max-width:560px){
    .lap-tbl th:nth-child(3),.lap-tbl td:nth-child(3),
    .lap-tbl th:nth-child(5),.lap-tbl td:nth-child(5),
    .lap-tbl th:nth-child(6),.lap-tbl td:nth-child(6){display:none}
  }
</style>

<div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px">
  <div>
    <h2 style="font-size:19px;font-weight:800;margin:0;letter-spacing:-.02em">Laporan Permohonan</h2>
    <p style="font-size:12.5px;color:var(--subtle);margin:3px 0 0">{{ \Carbon\Carbon::parse($bulan.'-01')->translatedFormat('F Y') }}</p>
  </div>
  <div class="lap-header-actions">
    <form method="GET">
      <input type="month" name="bulan" value="{{ $bulan }}" class="fc" style="width:auto" onchange="this.form.submit()">
    </form>
    <a href="{{ route('admin.laporan.csv', ['bulan'=>$bulan]) }}" class="btn btn-ol btn-sm">
      <i class="bi bi-filetype-csv"></i> CSV
    </a>
    <a href="{{ route('admin.laporan.pdf', ['bulan'=>$bulan]) }}" target="_blank"
       style="display:inline-flex;align-items:center;gap:6px;background:var(--p);color:#fff;border:none;padding:7px 14px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none">
      <i class="bi bi-file-earmark-pdf-fill"></i> PDF
    </a>
  </div>
</div>

<div class="sg lap-sg" style="grid-template-columns:repeat(5,1fr);margin-bottom:20px">
  <div class="sc cp"><div class="sn">{{ $stats['total'] }}</div><div class="sl">Total</div></div>
  <div class="sc cy"><div class="sn">{{ $stats['pending'] }}</div><div class="sl">Menunggu</div></div>
  <div class="sc cb2"><div class="sn">{{ $stats['diproses'] }}</div><div class="sl">Diproses</div></div>
  <div class="sc cg"><div class="sn">{{ $stats['selesai'] }}</div><div class="sl">Selesai</div></div>
  <div class="sc cr"><div class="sn">{{ $stats['ditolak'] }}</div><div class="sl">Ditolak</div></div>
</div>

<div class="lap-grid">
  <div class="card">
    <div class="ch"><i class="bi bi-bar-chart-fill"></i> Pengajuan Harian</div>
    <div class="cb" style="padding:16px">
      <div style="display:flex;align-items:flex-end;gap:3px;height:100px;overflow:hidden">
        @php $max = $harian->max() ?: 1; @endphp
        @for($d=1;$d<=31;$d++)
          @php $tgl=str_pad($d,2,'0',STR_PAD_LEFT);$val=$harian->get($tgl,0); @endphp
          <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px">
            <div style="width:100%;background:{{ $val>0?'var(--p)':'#edf0f5' }};border-radius:2px 2px 0 0;height:{{ $val>0?round($val/$max*100):2 }}%;min-height:2px" title="{{ $d }}: {{ $val }}"></div>
            @if($d%7===0)<div style="font-size:8px;color:var(--subtle)">{{ $d }}</div>@else<div style="height:10px"></div>@endif
          </div>
        @endfor
      </div>
      @if($avgSla)
      <div style="margin-top:12px;padding:10px 12px;background:#f0fdf4;border-radius:8px;border:1px solid #bbf7d0;font-size:12.5px;color:#166534">
        <i class="bi bi-clock"></i> Rata-rata penyelesaian: <strong>{{ round($avgSla,1) }} jam</strong>
      </div>
      @endif
    </div>
  </div>

  <div class="card">
    <div class="ch"><i class="bi bi-collection-fill"></i> Per Jenis Surat</div>
    <div class="cb" style="padding:16px">
      @forelse($perJenis as $nama => $data)
      <div style="margin-bottom:14px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
          <span style="font-size:12.5px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:70%">{{ $nama }}</span>
          <span style="font-size:11.5px;color:var(--subtle)">{{ $data['selesai'] }}/{{ $data['total'] }}</span>
        </div>
        <div style="height:6px;background:#edf0f5;border-radius:3px;overflow:hidden">
          <div style="height:100%;background:var(--p);width:{{ $stats['total']>0?round($data['total']/$stats['total']*100):0 }}%;border-radius:3px"></div>
        </div>
      </div>
      @empty
      <p style="text-align:center;color:var(--subtle);font-size:13px;padding:20px 0">Belum ada data</p>
      @endforelse
    </div>
  </div>
</div>

<div class="card">
  <div class="ch"><i class="bi bi-table"></i> Daftar Permohonan ({{ $stats['total'] }})</div>
  <div class="tbl-wrap">
    <table class="tbl lap-tbl">
      <thead>
        <tr><th>Kode</th><th>Nama</th><th>Jenis Surat</th><th>Status</th><th>Masuk</th><th>Selesai</th></tr>
      </thead>
      <tbody>
        @forelse($permohonan as $p)
        <tr>
          <td><a href="{{ route('admin.antrean.show',$p) }}" class="mono" style="font-weight:700;color:var(--p);text-decoration:none">{{ $p->kode_unik }}</a></td>
          <td>
            <div style="font-weight:600">{{ $p->nama_lengkap }}</div>
            <div style="font-size:11.5px;color:var(--subtle)">{{ $p->nik }}</div>
          </td>
          <td style="color:var(--subtle)">{{ $p->jenisSurat->nama ?? '-' }}</td>
          <td><span class="bdg bdg-{{ $p->status }}">{{ $p->statusLabel() }}</span></td>
          <td style="color:var(--subtle);font-size:12px;font-family:'DM Mono',monospace">{{ $p->created_at->format('d/m H:i') }}</td>
          <td style="color:var(--subtle);font-size:12px;font-family:'DM Mono',monospace">{{ $p->processed_at?->format('d/m H:i') ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;color:var(--subtle);padding:40px">Tidak ada permohonan bulan ini</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection
