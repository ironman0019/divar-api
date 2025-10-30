<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Http\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
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
        $settings = Setting::all();
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.settings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'keywords' => 'nullable|string|max:500'
        ]);

        $data = $request->all();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $data['logo'] = $this->imageUploadService->uploadImage($request->file('logo'));
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $data['favicon'] = $this->imageUploadService->uploadImage($request->file('favicon'));
        }

        Setting::create($data);

        return redirect()->route('admin.settings.index')
            ->with('success', 'تنظیمات با موفقیت ایجاد شد.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Setting $setting)
    {
        return view('admin.settings.show', compact('setting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Setting $setting)
    {
        return view('admin.settings.edit', compact('setting'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Setting $setting)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'keywords' => 'nullable|string|max:500'
        ]);

        $data = $request->all();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($setting->logo) {
                $this->imageUploadService->removeImage($setting->logo);
            }
            $data['logo'] = $this->imageUploadService->uploadImage($request->file('logo'));
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            // Delete old favicon if exists
            if ($setting->favicon) {
                $this->imageUploadService->removeImage($setting->favicon);
            }
            $data['favicon'] = $this->imageUploadService->uploadImage($request->file('favicon'));
        }

        $setting->update($data);

        return redirect()->route('admin.settings.index')
            ->with('success', 'تنظیمات با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setting $setting)
    {
        // Delete images if exist
        if ($setting->logo) {
            $this->imageUploadService->removeImage($setting->logo);
        }
        if ($setting->favicon) {
            $this->imageUploadService->removeImage($setting->favicon);
        }

        $setting->delete();

        return redirect()->route('admin.settings.index')
            ->with('success', 'تنظیمات با موفقیت حذف شد.');
    }

    /**
     * Show general settings form (main settings page)
     */
    public function general()
    {
        $setting = Setting::getFirst();
        return view('admin.settings.general', compact('setting'));
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'keywords' => 'nullable|string|max:500'
        ]);

        $data = $request->all();

        // Get existing setting or create new one
        $setting = Setting::first();
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($setting && $setting->logo) {
                $this->imageUploadService->removeImage($setting->logo);
            }
            $data['logo'] = $this->imageUploadService->uploadImage($request->file('logo'));
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            // Delete old favicon if exists
            if ($setting && $setting->favicon) {
                $this->imageUploadService->removeImage($setting->favicon);
            }
            $data['favicon'] = $this->imageUploadService->uploadImage($request->file('favicon'));
        }

        // Update existing record or create new one
        if ($setting) {
            $setting->update($data);
        } else {
            Setting::create($data);
        }

        return redirect()->route('admin.settings.general')
            ->with('success', 'تنظیمات عمومی با موفقیت به‌روزرسانی شد.');
    }
}
