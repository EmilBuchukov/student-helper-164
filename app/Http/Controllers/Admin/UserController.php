<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $users = User::query()
            ->with('schoolClass')
            ->orderBy('name')
            ->paginate(25)
            ->through(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value,
                'class_name' => $user->schoolClass?->name,
                'is_active' => $user->is_active,
            ]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }
}
