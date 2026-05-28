@extends('layouts.admin')
@section('title', 'Audit Log')
@section('content')

<style>
  /* Desktop: table */
  .al-tbl-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}
  /* Mobile: card list */
  .al-cards{display:none;flex-direction:column;gap:0}
  .al-card{padding:13px 16px;border-bottom:1px solid var(--border)}
  .al-card:last-child{border-bottom:none}
  @media(max-width:640px){
    .al-tbl-wrap{display:none}
    .al-cards{display:flex}
  }
</style>

<div style="margin-bottom:20px">
  <h2 style="font-size:19px;font-weight:800;margin:0;letter-spacing:-.02em">Audit Log</h2>
</div>

<div class="card">
  {{-- Desktop table --}}
  <div class="al-tbl-wrap">
    <table class="tbl">
      <thead>
        <tr><th>Waktu</th><th>Pengguna</th><th>Aksi</th><th>Model</th><th>IP</th></tr>
      </thead>
      <tbody>
        @forelse($logs as $log)
        <tr>
          <td class="mono" style="font-size:12px;color:var(--subtle)">{{ $log->created_at->format('d/m/Y H:i') }}</td>
          <td style="font-weight:500">{{ $log->user?->name ?? 'Sistem' }}</td>
          <td><span class="bdg bdg-gray" style="font-size:11px">{{ $log->action }}</span></td>
          <td style="color:var(--subtle);font-size:12px">{{ $log->model_type }} <span class="mono">#{{ $log->model_id }}</span></td>
          <td class="mono" style="font-size:11.5px;color:var(--subtle)">{{ $log->ip_address }}</td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center;color:var(--subtle);padding:40px">Belum ada log.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Mobile card list --}}
  <div class="al-cards">
    @forelse($logs as $log)
    <div class="al-card">
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap">
        <span class="bdg bdg-gray" style="font-size:10.5px">{{ $log->action }}</span>
        <span class="mono" style="font-size:11px;color:var(--subtle)">{{ $log->created_at->format('d/m/Y H:i') }}</span>
      </div>
      <div style="font-weight:600;font-size:13px">{{ $log->user?->name ?? 'Sistem' }}</div>
      <div style="font-size:11.5px;color:var(--subtle);margin-top:2px">
        {{ $log->model_type }} <span class="mono">#{{ $log->model_id }}</span>
        · <span class="mono">{{ $log->ip_address }}</span>
      </div>
    </div>
    @empty
    <div style="text-align:center;padding:40px;color:var(--subtle)">Belum ada log.</div>
    @endforelse
  </div>

  <div style="padding:14px 18px;border-top:1px solid var(--border);background:#fafbfe;border-radius:0 0 var(--r) var(--r)">
    {{ $logs->links() }}
  </div>
</div>
@endsection
