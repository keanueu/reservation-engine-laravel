@extends('frontdesk.layouts.app')
@section('content')
    <div class="p-6">
        <h2 class="text-2xl font-bold mb-4">Manage Images for {{ $room->room_name }}</h2>

        {{-- Upload form --}}
        <form action="{{ route('rooms.images.store', $room->id) }}" method="POST" enctype="multipart/form-data"
            class="mb-6">
            @csrf
            <input type="file" name="image" class="mb-2">
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Upload</button>
        </form>

        {{-- Existing images --}}
        <div class="grid grid-cols-3 gap-4">
            @foreach($room->images as $img)
                <div class="relative">
                    <img src="{{ asset('room_images/' . $img->image) }}" class="w-full h-40 object-cover rounded">
                    <form action="{{ route('rooms.images.destroy', $img->id) }}" method="POST" class="absolute top-2 right-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
@endsection
