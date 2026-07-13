<?php

namespace App\Http\Controllers;

use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return match (true) {
            $user->hasRole(RoleName::SuperAdmin->value) => $this->admin(),
            $user->hasRole(RoleName::Alumni->value) => view('dashboard.alumni'),
            $user->hasRole(RoleName::Student->value) => view('dashboard.student'),
            $user->hasRole(RoleName::Faculty->value) => view('dashboard.faculty'),
            default => throw new HttpException(403, 'Your account has no role assigned yet. Contact an administrator.'),
        };
    }

    private function admin(): View
    {
        return view('dashboard.admin', [
            'totalUsers' => User::count(),
            'totalAlumni' => User::role(RoleName::Alumni->value)->count(),
            'totalStudents' => User::role(RoleName::Student->value)->count(),
            'totalFaculty' => User::role(RoleName::Faculty->value)->count(),
        ]);
    }
}
