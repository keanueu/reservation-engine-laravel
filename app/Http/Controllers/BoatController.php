<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boat;

class BoatController extends Controller
{
    public function create()
    {
        return view('frontdesk.create_boat');
    }

    public function index()
    {
        return view('frontdesk.view_boat', ['boats' => Boat::all()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'price'      => 'required|numeric',
            'capacity'   => 'required|integer',
            'status'     => 'required|string',
            'start_time' => 'nullable',
            'end_time'   => 'nullable',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description'=> 'nullable|string',
        ]);

        $data = $request->only(['name', 'description', 'price', 'capacity', 'status', 'start_time', 'end_time']);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name  = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('boats'), $name);
            $data['image'] = $name;
        }

        Boat::create($data);

        return redirect()->back()->with('success', 'Boat added successfully!');
    }

    public function edit(int $id)
    {
        return view('frontdesk.update_boat', ['boat' => Boat::findOrFail($id)]);
    }

    public function update(Request $request, int $id)
    {
        $boat = Boat::findOrFail($id);

        $boat->fill($request->only(['name', 'description', 'price', 'capacity', 'status', 'start_time', 'end_time']));

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name  = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('boats'), $name);

            if ($boat->image && file_exists(public_path('boats/' . $boat->image))) {
                unlink(public_path('boats/' . $boat->image));
            }
            $boat->image = $name;
        }

        $boat->save();

        return redirect('view_boat')->with('message', 'Boat updated successfully!');
    }

    public function destroy(int $id)
    {
        Boat::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Boat deleted.');
    }
}
