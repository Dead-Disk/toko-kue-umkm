<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminActivityLogController extends Controller
{
    /**
     * Tampilkan halaman log aktivitas pengguna (Owner & Staff).
     * Khusus untuk Owner/Admin toko.
     */
    public function index(Request $request): View
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('role')) {
            $query->where('user_role', $request->input('role'));
        }

        if ($request->filled('action')) {
            $query->where('action', strtolower($request->input('action')));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date')) {
            $date = $request->input('date');
            if ($date === 'today') {
                $query->whereDate('created_at', today());
            } elseif ($date === 'week') {
                $query->where('created_at', '>=', now()->subDays(7));
            } elseif ($date === 'month') {
                $query->where('created_at', '>=', now()->subDays(30));
            } else {
                $query->whereDate('created_at', $date);
            }
        }

        $logs = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::whereDate('created_at', today())->count(),
            'owner' => ActivityLog::whereIn('user_role', ['admin', 'owner'])->count(),
            'staff' => ActivityLog::where('user_role', 'staff')->count(),
        ];

        return view('admin.logs.index', [
            'logs' => $logs,
            'stats' => $stats,
            'activeRole' => $request->input('role', ''),
            'activeAction' => $request->input('action', ''),
            'activeDate' => $request->input('date', ''),
            'search' => $request->input('search', ''),
        ]);
    }
}
