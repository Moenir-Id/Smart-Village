<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permohonan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends Controller
{
    public function stats(): JsonResponse
    {
        return response()->json([
            'total'    => Permohonan::count(),
            'pending'  => Permohonan::where('status', 'pending')->count(),
            'diproses' => Permohonan::where('status', 'diproses')->count(),
            'selesai'  => Permohonan::where('status', 'selesai')->count(),
            'ditolak'  => Permohonan::where('status', 'ditolak')->count(),
            'overdue'  => Permohonan::overdueSla()->count(),
            'tren'     => Permohonan::select(DB::raw('DATE(created_at) as tanggal'), DB::raw('COUNT(*) as total'))
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('tanggal')->orderBy('tanggal')->get(),
        ]);
    }
}
