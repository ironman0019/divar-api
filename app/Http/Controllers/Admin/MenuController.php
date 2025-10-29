<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Http\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    protected $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menu::with('parent')
            ->orderBy('position')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parentMenus = Menu::whereNull('parent_id')->get();
        return view('admin.menus.create', compact('parentMenus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'nullable|string|max:255',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'position' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'status' => 'boolean'
        ]);

        $data = $request->all();

        // Handle icon upload
        if ($request->hasFile('icon')) {
            $data['icon'] = $this->imageUploadService->uploadImage($request->file('icon'));
        }

        // Set status default to true if not provided
        $data['status'] = $request->has('status') ? (bool) $request->status : true;

        Menu::create($data);

        return redirect()->route('admin.menus.index')
            ->with('success', 'منو با موفقیت ایجاد شد.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        $menu->load('parent', 'children');
        return view('admin.menus.show', compact('menu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        $parentMenus = Menu::whereNull('parent_id')
            ->where('id', '!=', $menu->id)
            ->get();
        
        return view('admin.menus.edit', compact('menu', 'parentMenus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'nullable|string|max:255',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'position' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'status' => 'boolean'
        ]);

        $data = $request->all();

        // Handle icon upload
        if ($request->hasFile('icon')) {
            // Delete old icon if exists
            if ($menu->icon) {
                $this->imageUploadService->removeImage($menu->icon);
            }
            $data['icon'] = $this->imageUploadService->uploadImage($request->file('icon'));
        }

        // Set status
        $data['status'] = $request->has('status') ? (bool) $request->status : false;

        $menu->update($data);

        return redirect()->route('admin.menus.index')
            ->with('success', 'منو با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        $menu->delete();

        return redirect()->route('admin.menus.index')
            ->with('success', 'منو با موفقیت حذف شد.');
    }

    /**
     * Toggle menu status.
     */
    public function toggleStatus(Menu $menu)
    {
        $menu->update(['status' => !$menu->status]);

        $status = $menu->status ? 'فعال' : 'غیرفعال';
        
        return redirect()->back()
            ->with('success', "منو {$status} شد.");
    }
}