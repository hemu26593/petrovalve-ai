<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $stats = [
            'total_users' => User::count(),
            'admins'      => User::role('admin')->count(),
            'managers'    => User::role('manager')->count(),
            'viewers'     => User::role('viewer')->count(),
        ];

        return view('dashboard', compact('stats'));
    }
}
