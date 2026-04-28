<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Discount;
use Illuminate\Support\Facades\Schema;

class CollectionsController extends Controller
{
    /**
     * Display the Xmas Sale collection page.
     */
    public function xmas()
    {
        // load the discount by slug if the column exists, otherwise fall back to matching by name
        $query = Discount::with(['images', 'rooms.images'])->where('active', 1);

        if (Schema::hasColumn('discounts', 'slug')) {
            $query->where('slug', 'xmas-sale');
        } else {
            // fall back to the human-readable name if slug isn't available
            $query->where('name', 'Xmas Sale');
        }

        $discount = $query->firstOrFail();

        $rooms = $discount->rooms;

        return view('collections.xmas', compact('discount', 'rooms'));
    }
}
