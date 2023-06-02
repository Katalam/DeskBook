<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->get();

        return inertia('Users/Index', [
            'users' => UserResource::collection($users),
        ]);
    }
}