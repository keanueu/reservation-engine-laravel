<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Discount;
use App\Models\DiscountImage;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::with(['images','rooms'])->orderBy('id','desc')->get();
        return view('admin.discounts.index', compact('discounts'));
    }

    public function create()
    {
        $rooms = Room::orderBy('room_name')->get();
        return view('admin.discounts.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'slug' => 'nullable|string|max:160',
            'type' => 'nullable|string|max:50',
            'amount' => 'required|numeric',
            'amount_type' => 'required|in:percent,fixed',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'combinable' => 'nullable|boolean',
            'active' => 'nullable|boolean',
            'rooms' => 'nullable|array',
            'images.*' => 'nullable|image|max:5120'
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['combinable'] = $request->has('combinable') ? 1 : 0;
        $data['active'] = $request->has('active') ? 1 : 0;

        DB::beginTransaction();
        try {
            $discount = Discount::create($data);

            // attach rooms
            if (!empty($data['rooms'])) {
                $discount->rooms()->sync($data['rooms']);
            }

            // handle images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $name = time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('images/promotions'), $name);
                    $discount->images()->create(['filename' => $name]);
                }
            }

            DB::commit();
            return redirect()->route('admin.discounts.index')->with('success', 'Discount created');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit(Discount $discount)
    {
        $rooms = Room::orderBy('room_name')->get();
        $discount->load(['images','rooms']);
        return view('admin.discounts.edit', compact('discount','rooms'));
    }

    public function update(Request $request, Discount $discount)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'slug' => 'nullable|string|max:160',
            'type' => 'nullable|string|max:50',
            'amount' => 'required|numeric',
            'amount_type' => 'required|in:percent,fixed',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'combinable' => 'nullable|boolean',
            'active' => 'nullable|boolean',
            'rooms' => 'nullable|array',
            'images.*' => 'nullable|image|max:5120'
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $data['combinable'] = $request->has('combinable') ? 1 : 0;
        $data['active'] = $request->has('active') ? 1 : 0;

        DB::beginTransaction();
        try {
            $discount->update($data);

            // sync rooms
            $discount->rooms()->sync($data['rooms'] ?? []);

            // handle new images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $name = time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('images/promotions'), $name);
                    $discount->images()->create(['filename' => $name]);
                }
            }

            DB::commit();
            return redirect()->route('admin.discounts.index')->with('success', 'Discount updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Discount $discount)
    {
        // delete images files
        foreach ($discount->images as $img) {
            $path = public_path('images/promotions/' . $img->filename);
            if (file_exists($path)) {
                @unlink($path);
            }
        }
        $discount->images()->delete();
        $discount->rooms()->detach();
        $discount->delete();
        return redirect()->route('admin.discounts.index')->with('success', 'Discount deleted');
    }
}
