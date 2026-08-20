<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AdminLog::with('causer')->latest();

        if ($request->filled('admin')) {
            $query->where('causer_id', $request->integer('admin'))->where('causer_type', User::class);
        }

        if ($request->filled('q')) {
            $query->where('description', 'like', '%'.$request->query('q').'%');
        }

        $logs = $query->paginate(25)->withQueryString();

        $admins = User::whereHas('role', fn ($q) => $q->where('slug', 'admin'))->orderBy('name')->get();

        return view('admin.activity-log.index', compact('logs', 'admins'));
    }
}
