<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarouselItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarouselController extends Controller
{
    private function checkAccess()
    {
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Akses tidak diizinkan.');
        }

        if (
            !$user->hasRole('Super Admin') &&
            !$user->hasRole('Atasan PPID Pelaksana') &&
            !$user->hasRole('PPID Pelaksana') &&
            !$user->hasRole('PPID Pelaksana / Pembantu Bappeda') &&
            !$user->hasRole('Desk Layanan') &&
            !$user->hasRole('Petugas Pelayanan Informasi / Desk Layanan')
        ) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola Banner Carousel.');
        }
    }

    public function index(Request $request)
    {
        $this->checkAccess();

        $carousels = CarouselItem::orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalSlides = $carousels->count();
        $activeSlides = $carousels->where('is_active', true)->count();
        $inactiveSlides = $totalSlides - $activeSlides;

        return view('admin.carousel.index', compact('carousels', 'totalSlides', 'activeSlides', 'inactiveSlides'));
    }

    public function create()
    {
        $this->checkAccess();

        $nextOrder = (CarouselItem::max('order') ?? 0) + 1;
        return view('admin.carousel.create', compact('nextOrder'));
    }

    public function store(Request $request)
    {
        $this->checkAccess();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:1000',
            'badge_text' => 'nullable|string|max:100',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|string|max:255',
            'button_target' => 'required|in:_self,_blank',
            'order' => 'required|integer|min:0',
            'is_active' => 'nullable',
        ], [
            'title.required' => 'Judul slide wajib diisi.',
            'image.required' => 'Gambar banner wajib diunggah.',
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        $imagePath = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imagePath = $file->store('carousels', 'public');
        }

        CarouselItem::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'badge_text' => $validated['badge_text'] ?? null,
            'image_path' => $imagePath,
            'button_text' => $validated['button_text'] ?? null,
            'button_url' => $validated['button_url'] ?? null,
            'button_target' => $validated['button_target'] ?? '_self',
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.carousel.index')
            ->with('success', 'Slide banner carousel berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $this->checkAccess();

        $carousel = CarouselItem::findOrFail($id);
        return view('admin.carousel.edit', compact('carousel'));
    }

    public function update(Request $request, $id)
    {
        $this->checkAccess();

        $carousel = CarouselItem::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:1000',
            'badge_text' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|string|max:255',
            'button_target' => 'required|in:_self,_blank',
            'order' => 'required|integer|min:0',
            'is_active' => 'nullable',
        ], [
            'title.required' => 'Judul slide wajib diisi.',
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        $dataToUpdate = [
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'badge_text' => $validated['badge_text'] ?? null,
            'button_text' => $validated['button_text'] ?? null,
            'button_url' => $validated['button_url'] ?? null,
            'button_target' => $validated['button_target'] ?? '_self',
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('image')) {
            // Delete old file if stored in public storage
            if ($carousel->image_path && Storage::disk('public')->exists($carousel->image_path)) {
                Storage::disk('public')->delete($carousel->image_path);
            }

            $file = $request->file('image');
            $dataToUpdate['image_path'] = $file->store('carousels', 'public');
        }

        $carousel->update($dataToUpdate);

        return redirect()->route('admin.carousel.index')
            ->with('success', 'Slide banner carousel berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->checkAccess();

        $carousel = CarouselItem::findOrFail($id);

        if ($carousel->image_path && Storage::disk('public')->exists($carousel->image_path)) {
            Storage::disk('public')->delete($carousel->image_path);
        }

        $carousel->delete();

        return redirect()->route('admin.carousel.index')
            ->with('success', 'Slide banner carousel berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        $this->checkAccess();

        $carousel = CarouselItem::findOrFail($id);
        $carousel->is_active = !$carousel->is_active;
        $carousel->save();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $carousel->is_active,
                'message' => 'Status banner berhasil diubah.',
            ]);
        }

        return redirect()->back()->with('success', 'Status publikasi banner berhasil diperbarui.');
    }
}
