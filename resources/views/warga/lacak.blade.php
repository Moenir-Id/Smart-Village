@extends('layouts.warga')
@section('title', 'Lacak Surat')
@section('content')

<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:60px 20px;text-align:center">
  <div style="width:56px;height:56px;border:3px solid var(--border);border-top-color:var(--p);border-radius:50%;animation:sp .7s linear infinite;margin-bottom:18px"></div>
  <div style="font-size:14px;font-weight:600;color:var(--text);margin-bottom:4px">Mengalihkan…</div>
  <div style="font-size:12.5px;color:var(--subtle)">Memuat status permohonan Anda</div>
</div>

@endsection
@push('scripts')
<script>
  window.location.href = '/?lacak={{ $kode }}';
</script>
@endpush
