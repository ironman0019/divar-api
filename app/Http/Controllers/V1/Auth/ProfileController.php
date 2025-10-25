<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    use HttpResponse;

    public function getProfile()
    {
        $user = auth('api')->user();

        return $this->success([
            'user' => $user
        ], "Profile retrieved successfully");
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|max:255|email',
            'city_id' => 'nullable|numeric|exists:cities,id'
        ]);

        $user = auth('api')->user();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->city_id = $request->city_id;
        $user->save();

        return $this->success([
            'user' => $user->fresh()
        ], "Profile updated successfully");
    }
}
