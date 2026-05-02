<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Images;

class RoomController extends Controller
{
    public function create()
    {
        return view('frontdesk.create_room');
    }

    public function index()
    {
        return view('frontdesk.view_room', ['datas' => Room::all()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric',
            'accommodates'=> 'required|string|max:255',
            'room_type'   => 'required|string',
            'images'      => 'nullable|array',
            'images.*'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $room = Room::create([
            'room_name'   => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'accommodates'=> $request->accommodates,
            'terms'       => $request->terms,
            'room_type'   => $request->room_type,
            'beds'        => $request->beds,
            'amenities'   => $request->amenities,
            'check_in'    => $request->check_in,
            'check_out'   => $request->check_out,
        ]);

        $this->storeImages($request, $room->id);

        return redirect()->back()->with('success', 'Room added successfully!');
    }

    public function edit(int $id)
    {
        return view('frontdesk.update_room', ['data' => Room::findOrFail($id)]);
    }

    public function update(Request $request, int $id)
    {
        $room = Room::findOrFail($id);

        $room->update([
            'room_name'   => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'accommodates'=> $request->accommodates,
            'terms'       => $request->terms,
            'room_type'   => $request->room_type,
            'beds'        => $request->beds,
            'amenities'   => $request->amenities,
            'check_in'    => $request->check_in,
            'check_out'   => $request->check_out,
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name  = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('room'), $name);

            if ($room->image && file_exists(public_path('room/' . $room->image))) {
                unlink(public_path('room/' . $room->image));
            }
            $room->image = $name;
            $room->save();
        }

        $this->storeImages($request, $room->id);

        return redirect('view_room')->with('message', 'Room updated successfully!');
    }

    public function destroy(int $id)
    {
        Room::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Room deleted.');
    }

    private function storeImages(Request $request, int $roomId): void
    {
        if (!$request->hasFile('images')) {
            return;
        }
        foreach ($request->file('images') as $file) {
            if (!$file->isValid()) continue;
            $name = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('room'), $name);
            Images::create(['image' => $name, 'room_id' => $roomId]);
        }
    }
}
