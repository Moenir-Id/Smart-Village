<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permohonan;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total'    => Permohonan::count(),
            'pending'  => Permohonan::where('status', 'pending')->count(),
            'diproses' => Permohonan::where('status', 'diproses')->count(),
            'selesai'  => Permohonan::where('status', 'selesai')->count(),
            'ditolak'  => Permohonan::where('status', 'ditolak')->count(),
            'overdue'  => Permohonan::overdueSla()->count(),
        ];

        // Tren 7 hari terakhir
        $tren = Permohonan::select(
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // Permohonan terbaru
        $terbaru = Permohonan::with('jenisSurat')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'tren', 'terbaru'));
    }
}
