<?php

namespace App\Http\Controllers\Super;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'user'  => 'nullable|integer',
            'group' => 'nullable|string|max:20',
            'from'  => 'nullable|date',
            'to'    => 'nullable|date',
            'q'     => 'nullable|string|max:100',
        ]);

        $logs = ActivityLog::with('user')
            ->when($filters['user'] ?? null, fn ($q, $u) => $q->where('user_id', $u))
            ->when(isset(ActivityLog::GROUPS[$filters['group'] ?? '']), fn ($q) => $q->where('action', 'like', $filters['group'] . '.%'))
            ->when($filters['from'] ?? null, fn ($q, $d) => $q->where('created_at', '>=', Carbon::parse($d)->startOfDay()))
            ->when($filters['to'] ?? null, fn ($q, $d) => $q->where('created_at', '<=', Carbon::parse($d)->endOfDay()))
            ->when($filters['q'] ?? null, fn ($q, $t) => $q->where('description', 'like', "%{$t}%"))
            ->latest('created_at')->latest('id')
            ->paginate(50)->withQueryString();

        $staff = User::whereIn('role', User::STAFF)->orderBy('nom')->get(['id', 'nom', 'role']);

        return view('super.activity', compact('logs', 'staff', 'filters'));
    }
}
