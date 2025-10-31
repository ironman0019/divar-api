<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\City;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with('city');

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('mobile', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Filter by admin status
        if ($request->filled('is_admin')) {
            $query->where('is_admin', $request->is_admin);
        }

        // Filter by city
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $query->orderBy($sortBy, $sortDirection);

        $users = $query->paginate(15)->withQueryString();

        // Get cities for filter
        $cities = City::select('id', 'name')->get();

        return view('admin.users.index', compact('users', 'cities'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $cities = City::select('id', 'name')->get();
        return view('admin.users.create', compact('cities'));
    }

    /**
     * Store a newly created user
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        // Hash password
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Set defaults
        $data['is_active'] = $request->has('is_active') ? (bool) $request->is_active : false;
        
        // Handle is_admin separately since it's guarded (cannot be mass-assigned)
        $isAdmin = $request->has('is_admin') ? (bool) $request->is_admin : false;
        
        $user = User::create($data);
        $user->is_admin = $isAdmin;
        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'کاربر با موفقیت ایجاد شد.');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load('city');
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $cities = City::select('id', 'name')->get();
        return view('admin.users.edit', compact('user', 'cities'));
    }

    /**
     * Update the specified user
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        // Hash password if provided
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Set statuses
        $data['is_active'] = $request->has('is_active') ? (bool) $request->is_active : false;
        
        // Handle is_admin separately since it's guarded (cannot be mass-assigned)
        $isAdmin = $request->has('is_admin') ? (bool) $request->is_admin : false;
        
        $user->update($data);
        $user->is_admin = $isAdmin;
        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'کاربر با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        $authUserId = auth()->id();
        if ($authUserId && $user->id === $authUserId) {
            return redirect()->back()
                ->with('error', 'شما نمی‌توانید حساب کاربری خود را حذف کنید.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'کاربر با موفقیت حذف شد.');
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'فعال' : 'غیرفعال';

        return redirect()->back()
            ->with('success', "کاربر {$status} شد.");
    }

    /**
     * Toggle user admin status
     */
    public function toggleAdmin(User $user)
    {
        // Prevent removing admin from yourself
        $authUserId = auth()->id();
        if ($authUserId && $user->id === $authUserId) {
            return redirect()->back()
                ->with('error', 'شما نمی‌توانید دسترسی ادمین خود را تغییر دهید.');
        }

        // Update is_admin directly since it's guarded (cannot be mass-assigned)
        $user->is_admin = !$user->is_admin;
        $user->save();

        $status = $user->is_admin ? 'ادمین' : 'کاربر عادی';

        return redirect()->back()
            ->with('success', "کاربر به {$status} تغییر یافت.");
    }
}

