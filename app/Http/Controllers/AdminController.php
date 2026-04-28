<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Boat;
use App\Models\BoatBooking;
use App\Models\Images;
use App\Models\Contact;
use App\Models\Message;
use App\Models\CalamityStatus;
use Illuminate\Support\Str;
use App\Notifications\SendEmailNotification;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;

class AdminController extends Controller
{

  // user condition logging in
  public function index()
  {
    if (Auth::id()) {
      $usertype = Auth()->user()->usertype;
      \Log::info('Dashboard usertype: ' . ($usertype ?? 'none') . ', email: ' . (Auth()->user()->email ?? 'none'));

      if ($usertype == 'user') {
        $rooms = Room::with(['discounts.images', 'images'])->get();
        $boats = Boat::all();
        $image = Images::all();
        return view('home.index', compact('rooms', 'boats', 'image'));
      } else if ($usertype == 'admin') {
        return $this->dashboard();
      } else if ($usertype == 'frontdesk') {
        $today = Carbon::today();

        // frontdesk arrival-
        $todaysArrivals = Booking::with('room')
          ->whereDate('start_date', $today)
          ->where('status', 'approve')
          ->get();
        $todaysCheckInsCount = $todaysArrivals->count();

        // frontdesk departure
        $todaysDepartures = Booking::with('room')
          ->where(function ($q) use ($today) {
            $q->whereDate('end_date', $today)
              ->whereIn('status', ['checked-in', 'checked-out']);
          })
          ->orWhere(function ($q) use ($today) {
            $q->where('status', 'checked-out')
              ->whereDate('updated_at', $today);
          })
          ->get();
        $todaysCheckOutsCount = $todaysDepartures->count();

        // In House Guests
        $inHouseGuests = Booking::with('room')
          ->where('status', 'checked-in')
          ->whereDate('start_date', '<=', $today)
          ->whereDate('end_date', '>=', $today)
          ->get();
        $inHouseGuestsCount = $inHouseGuests->count();

        // Room available
        $totalRooms = Room::count();
        $occupiedRoomIds = $inHouseGuests->pluck('room_id')->toArray();
        $availableRoomsCount = Room::whereNotIn('id', $occupiedRoomIds)->count();

        // Totals
        $roomsCount = Room::count();
        $boatsCount = Boat::count();
        $bookingsCount = Booking::count();

        $rooms = Room::all();
        $boats = Boat::all();
        $bookings = Booking::with('room')->get();
        $boatBookings = BoatBooking::with('boat')->get();
        $images = Images::all();

        $todaysBoatTrips = BoatBooking::with('boat')
          ->whereDate('booking_date', $today)
          ->get();

        // pass all the data on the frontdesk dashboard
        return view('frontdesk.index', compact(
          'todaysCheckInsCount',
          'todaysCheckOutsCount',
          'inHouseGuestsCount',
          'availableRoomsCount',
          'roomsCount',
          'boatsCount',
          'bookingsCount',
          'rooms',
          'boats',
          'bookings',
          'boatBookings',
          'images',
          'todaysArrivals',
          'todaysDepartures',
          'inHouseGuests',
          'todaysBoatTrips'
        ));
      } else {
        return redirect()->back();
      }
    }
  }


  // home page all rooms
  public function home()
  {
    $rooms = Room::with(['discounts.images', 'images'])->get();
    $boats = Boat::all();
    $image = Images::all();
    return view('home.index', compact('rooms', 'boats', 'image'));
  }

  // admin dashboard
  public function dashboard()
  {
    $currentMonthStart = Carbon::now()->startOfMonth();
    $currentMonthEnd = Carbon::now()->endOfMonth();
    $lastMonthStart = Carbon::now()->subMonthNoOverflow()->startOfMonth();
    $lastMonthEnd = Carbon::now()->subMonthNoOverflow()->endOfMonth();
    $chartStartDate = Carbon::today()->subDays(29);

    $datas = Booking::with('room')->latest()->take(5)->get();
    $boatBookings = BoatBooking::with('boat')->latest()->take(5)->get();

    // Totals
    $totalRoomBookings = Booking::count();
    $totalBoatBookings = BoatBooking::count();
    $totalBookings = $totalRoomBookings + $totalBoatBookings;

    // Revenue (collected amounts — use paid_amount when available)
    $totalRoomRevenue = Booking::sum('paid_amount');
    $totalBoatRevenue = BoatBooking::sum('paid_amount');
    $totalRevenue = ($totalRoomRevenue ?? 0) + ($totalBoatRevenue ?? 0);

    // Revenue growth (vs last month)
    // Use paid_at for revenue by month (actual collected amounts)
    $currentRevenue = Booking::whereBetween('paid_at', [$currentMonthStart, $currentMonthEnd])->sum('paid_amount')
      + BoatBooking::whereBetween('paid_at', [$currentMonthStart, $currentMonthEnd])->sum('paid_amount');

    $lastMonthRevenue = Booking::whereBetween('paid_at', [$lastMonthStart, $lastMonthEnd])->sum('paid_amount')
      + BoatBooking::whereBetween('paid_at', [$lastMonthStart, $lastMonthEnd])->sum('paid_amount');

    $revenueGrowth = $lastMonthRevenue > 0
      ? (($currentRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100
      : 0;

    // Guest growth (vs last month)
    $currentCustomers = User::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count();
    $lastMonthCustomers = User::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();

    $customerGrowth = $lastMonthCustomers > 0
      ? (($currentCustomers - $lastMonthCustomers) / $lastMonthCustomers) * 100
      : 0;

    // Occupancy Rate (rooms + boats)
    $totalRooms = Room::count();
    $totalBoats = Boat::count();
    $totalUnits = $totalRooms + $totalBoats;

    $bookedRooms = Booking::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count();
    $bookedBoats = BoatBooking::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count();
    $bookedUnits = $bookedRooms + $bookedBoats;

    $occupancyRate = $totalUnits > 0 ? ($bookedUnits / $totalUnits) * 100 : 0;

    // Occupancy growth vs last month
    $lastMonthBookedRooms = Booking::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
    $lastMonthBookedBoats = BoatBooking::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
    $lastMonthBookedUnits = $lastMonthBookedRooms + $lastMonthBookedBoats;

    $occupancyGrowth = $lastMonthBookedUnits > 0
      ? (($bookedUnits - $lastMonthBookedUnits) / $lastMonthBookedUnits) * 100
      : 0;

    // Bookings for last 30 days (for Chart.js)
    $labels = [];
    $roomChartData = [];
    $boatChartData = [];

    $roomCountsByDate = Booking::selectRaw('DATE(created_at) as booking_date, COUNT(*) as total')
      ->whereDate('created_at', '>=', $chartStartDate)
      ->groupBy(\DB::raw('DATE(created_at)'))
      ->pluck('total', 'booking_date');
    $boatCountsByDate = BoatBooking::selectRaw('DATE(created_at) as booking_date, COUNT(*) as total')
      ->whereDate('created_at', '>=', $chartStartDate)
      ->groupBy(\DB::raw('DATE(created_at)'))
      ->pluck('total', 'booking_date');

    for ($i = 29; $i >= 0; $i--) {
      $date = Carbon::today()->subDays($i);
      $dateKey = $date->toDateString();
      $labels[] = $date->format('M d'); // e.g., Oct 01

      $roomCount = (int) ($roomCountsByDate[$dateKey] ?? 0);
      $boatCount = (int) ($boatCountsByDate[$dateKey] ?? 0);

      $roomChartData[] = $roomCount;
      $boatChartData[] = $boatCount;
    }

    //  Pass everything to the view
    return view('admin.dashboard', compact(
      'datas',
      'boatBookings',
      'totalBookings',
      'totalRoomBookings',
      'totalBoatBookings',
      'totalRevenue',
      'revenueGrowth',
      'currentCustomers',
      'customerGrowth',
      'occupancyRate',
      'occupancyGrowth',
      'labels',
      'roomChartData',
      'boatChartData'
    ));
  }

  /**
   * Export combined sales (room + boat bookings) to an Excel file.
   *
   * Optional query params: from, to (YYYY-MM-DD) to filter by created_at.
   */
  public function exportSales(Request $request)
  {
    $from = $request->get('from');
    $to = $request->get('to');

    $roomQuery = Booking::with('room');
    $boatQuery = BoatBooking::with('boat');

    if ($from) {
      $roomQuery->whereDate('created_at', '>=', $from);
      $boatQuery->whereDate('created_at', '>=', $from);
    }
    if ($to) {
      $roomQuery->whereDate('created_at', '<=', $to);
      $boatQuery->whereDate('created_at', '<=', $to);
    }

    $rooms = $roomQuery->get();
    $boats = $boatQuery->get();

    $rows = collect();

    foreach ($rooms as $r) {
      $rows->push([
        'Room',
        $r->id,
        $r->name,
        $r->email,
        $r->start_date ?? '',
        $r->end_date ?? '',
        optional($r->room)->room_name ?? '',
        $r->total_amount ?? 0,
        $r->paid_amount ?? 0,
        $r->payment_status ?? '',
        $r->paid_at ?? '',
        $r->created_at ?? '',
      ]);
    }

    foreach ($boats as $b) {
      $rows->push([
        'Boat',
        $b->id,
        $b->name,
        $b->email,
        $b->booking_date ?? '',
        '',
        optional($b->boat)->name ?? '',
        $b->total_amount ?? 0,
        $b->paid_amount ?? 0,
        $b->payment_status ?? '',
        $b->paid_at ?? '',
        $b->created_at ?? '',
      ]);
    }

    // sort by created_at desc if present
    $rows = $rows->sortByDesc(function ($r) {
      return $r[11] ?? null;
    })->values();

    // Use the SalesExport and download
    return Excel::download(new SalesExport(collect($rows)), 'sales_export_' . now()->format('Ymd_His') . '.xlsx');
  }




  //create room
  public function create_room()
  {
    return view('frontdesk.create_room');
  }

  //view room
  public function view_room()
  {
    $datas = Room::all();
    return view('frontdesk.view_room', compact('datas'));
  }

  //delete room
  public function delete_room($id)
  {
    $data = Room::find($id);
    $data->delete();
    return redirect()->back()->with('Room deleted');
  }

  // Show the update form
  public function update_room($id)
  {
    $data = Room::findOrFail($id);
    return view('frontdesk.update_room', compact('data'));
  }

  //add room
  public function add_room(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'price' => 'required|numeric',
      'accommodates' => 'required|string|max:255',
      'room_type' => 'required|string',
      'images' => 'nullable|array',
      'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $room = new Room;
    $room->room_name = $request->name;
    $room->description = $request->description;
    $room->price = $request->price;
    $room->accommodates = $request->accommodates;
    $room->terms = $request->terms;
    $room->room_type = $request->room_type;
    $room->beds = $request->beds;
    $room->amenities = $request->amenities;
    $room->check_in = $request->check_in;
    $room->check_out = $request->check_out;

    // handle multiple images (if provided)
    if ($request->hasFile('images')) {
      foreach ($request->file('images') as $image) {
        if (!$image->isValid())
          continue;
        $imagename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('room'), $imagename);
        // create Images record and link to room after room is saved
        // collect names to attach after save
        $savedImages[] = $imagename;
      }
    }

    $room->save();

    // persist image records (if any)
    if (!empty($savedImages)) {
      foreach ($savedImages as $name) {
        \App\Models\Images::create([
          'image' => $name,
          'room_id' => $room->id,
        ]);
      }
    }

    return redirect()->back()->with('success', 'Room added successfully!');
  }

  // FIXED
  public function bookings()
  {
    $datas = Booking::with('room')->get(); // only room booking
    $boatBookings = BoatBooking::with('boat')->get(); // add boat bookings
    return view('frontdesk.booking', compact('datas', 'boatBookings'));
  }

  // Mark deposit as paid for a room booking (manual reconciliation / cash)
  public function markDepositPaid($id)
  {
    $booking = Booking::findOrFail($id);
  $depositPercent = (float) \App\Models\Setting::get('deposit_percentage', config('booking.deposit_percentage', 50));
    $depositAmount = ($booking->total_amount ?? 0) * ($depositPercent / 100);

    $booking->paid_amount = $depositAmount;
    $booking->paid_at = now();
    $booking->payment_status = 'paid';
    $booking->save();

    return redirect()->back()->with('success', 'Deposit marked as paid.');
  }

  // Mark deposit as paid for a boat booking
  public function markBoatDepositPaid($id)
  {
    $booking = BoatBooking::findOrFail($id);
  $depositPercent = (float) \App\Models\Setting::get('deposit_percentage', config('booking.deposit_percentage', 50));
    $depositAmount = ($booking->total_amount ?? 0) * ($depositPercent / 100);

    $booking->paid_amount = $depositAmount;
    $booking->paid_at = now();
    $booking->payment_status = 'paid';
    $booking->save();

    return redirect()->back()->with('success', 'Boat deposit marked as paid.');
  }

  //delete booking
  public function delete_booking($id)
  {
    $data = Booking::find($id);
    $data->delete();
    return redirect()->back();
  }

  //approve
  public function approve_booking($id)
  {
    $booking = Booking::find($id);
    $booking->status = 'approve';
    $booking->save();
    return redirect()->back();
  }

  //reject
  public function reject_booking($id)
  {
    $booking = Booking::find($id);
    $booking->status = 'rejected';
    $booking->save();
    return redirect()->back();
  }

  //images (lahat ng images for pages)
  public function images_pages()
  {
    $images = Images::all();
    return view('frontdesk.images_pages', compact('images'));
  }

  public function upload_images(Request $request)
  {
    $data = new Images;
    $image = $request->image;

    if ($image) {
      $imagename = time() . '.' . $image->getClientOriginalExtension();
      $request->image->move('images', $imagename);
      $data->image = $imagename;
      $data->save();
      return redirect()->back();
    }
  }

  public function delete_images($id)
  {
    $image = Images::find($id);
    $image->delete();
    return redirect()->back();
  }

  public function all_messages()
  {
    $contacts = Contact::all();
    return view('frontdesk.all_messages', compact('contacts'));
  }

  public function delete_message($id)
  {
    $contact = Contact::find($id);
    if ($contact) {
      $contact->delete();
    }
    return redirect()->back()->with('message', 'Message deleted successfully');
  }

  /**
   * Return recent contact messages for frontdesk notification dropdown.
   */
  public function notificationsContacts()
  {
    $contacts = Contact::orderBy('created_at', 'desc')->take(5)->get();

    $data = $contacts->map(function ($c) {
      return [
        'id' => $c->id,
        'name' => $c->name,
        'email' => $c->email,
        'message' => Str::limit($c->message, 80),
        'created_at' => $c->created_at->toDateTimeString(),
      ];
    });

    return response()->json($data);
  }

  /**
   * Return recent chat/messages for admin notification dropdown.
   */
  public function notificationsMessages()
  {
    // Return recent messages that matter to admins:
    // - messages sent by users (sender = 'user' or sender NULL)
    // - acknowledgements or messages flagged requires_admin = true (so staff see pending requests)
    // Exclude messages that were sent by admins themselves.
    $messages = Message::with('user')
      ->where(function ($q) {
        $q->where('sender', 'user')
          ->orWhereNull('sender')
          ->orWhere('requires_admin', true);
      })
      ->where(function ($q) {
        $q->whereNull('sender')->orWhere('sender', '<>', 'admin');
      })
      ->orderBy('created_at', 'desc')
      ->take(5)
      ->get();

    $data = $messages->map(function ($m) {
      $name = $m->user ? $m->user->name : ($m->sender ?? 'Chat');
      $email = $m->user ? ($m->user->email ?? null) : ($m->meta['email'] ?? null);

      return [
        'id' => $m->id,
        'name' => $name,
        'email' => $email,
        'message' => Str::limit($m->message, 80),
        'created_at' => $m->created_at->toDateTimeString(),
      ];
    });

    return response()->json($data);
  }

  /**
   * Return the number of unique chat sessions/users that currently have messages
   * requiring admin attention. This is used to show a compact admin chat bubble badge.
   */
  public function notificationsUnreadUsers()
  {
    $baseQuery = Message::query()
      ->where(function ($q) {
        $q->where('sender', 'user')
          ->orWhereNull('sender')
          ->orWhere('requires_admin', true);
      })
      ->where(function ($q) {
        $q->whereNull('sender')->orWhere('sender', '<>', 'admin');
      });

    $distinctSessions = (clone $baseQuery)
      ->whereNotNull('session_id')
      ->where('session_id', '<>', '')
      ->distinct('session_id')
      ->count('session_id');

    $distinctUsersWithoutSession = (clone $baseQuery)
      ->where(function ($q) {
        $q->whereNull('session_id')->orWhere('session_id', '');
      })
      ->whereNotNull('user_id')
      ->distinct('user_id')
      ->count('user_id');

    $messagesWithoutSessionOrUser = (clone $baseQuery)
      ->where(function ($q) {
        $q->whereNull('session_id')->orWhere('session_id', '');
      })
      ->whereNull('user_id')
      ->count();

    return response()->json([
      'count' => $distinctSessions + $distinctUsersWithoutSession + $messagesWithoutSessionOrUser,
    ]);
  }

  public function send_email($id)
  {
    $email = Contact::findOrFail($id);
    return view('frontdesk.send_email', compact('email'));
  }

  public function email(Request $request, $id)
  {
    $email = Contact::find($id);

    $details = [
      'greeting' => $request->greeting,
      'body' => $request->body,
      'action_text' => $request->action_text,
      'action_url' => $request->action_url,
      'endline' => $request->endline,
    ];

    Notification::send($email, new SendEmailNotification($details));

    return redirect()->back();
  }

  public function booking_email(Request $request, $id)
  {
    $booking = Booking::find($id);

    if (!$booking) {
      abort(404, 'Booking not found');
    }

    

    $details = [
      'greeting' => $request->greeting,
      'body' => $request->body,
      'action_text' => $request->action_text,
      'action_url' => $request->action_url,
      'endline' => $request->endline,
    ];

    Notification::route('mail', $booking->email)
      ->notify(new SendEmailNotification($details));

    return redirect()->back()->with('success', 'Email sent successfully!');
  }

  public function send_booking_email($id)
  {
    $booking = Booking::findOrFail($id);
    return view('frontdesk.send_booking_email', compact('booking'));
  }

  public function edit_room(Request $request, $id)
  {
    $room = Room::findOrFail($id);

    $room->room_name = $request->name;
    $room->description = $request->description;
    $room->price = $request->price;
    $room->accommodates = $request->accommodates;
    $room->terms = $request->terms;
    $room->room_type = $request->room_type;
    $room->beds = $request->beds;
    $room->amenities = $request->amenities;
    $room->check_in = $request->check_in;
    $room->check_out = $request->check_out;

    if ($request->hasFile('image')) {
      $image = $request->file('image');
      $imagename = time() . '.' . $image->getClientOriginalExtension();
      $image->move('room', $imagename);

      if ($room->image && file_exists(public_path('room/' . $room->image))) {
        unlink(public_path('room/' . $room->image));
      }

      $room->image = $imagename;
    }

    // handle additional multiple images upload (non-destructive: appends new images)
    if ($request->hasFile('images')) {
      foreach ($request->file('images') as $imageFile) {
        if (!$imageFile->isValid())
          continue;
        $iname = time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
        $imageFile->move(public_path('room'), $iname);
        \App\Models\Images::create([
          'image' => $iname,
          'room_id' => $room->id,
        ]);
      }
    }

    $room->save();

    return redirect('view_room')->with('message', 'Room updated successfully!');
  }

  //create boat
  public function create_boat()
  {
    return view('frontdesk.create_boat');
  }

  // add boat
  public function add_boat(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'price' => 'required|numeric',
      'capacity' => 'required|integer',
      'status' => 'required|string',
      'start_time' => 'nullable',
      'end_time' => 'nullable',
      'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
      'description' => 'nullable|string',
    ]);

    $boat = new Boat;
    $boat->name = $request->name;
    $boat->description = $request->description;
    $boat->price = $request->price;
    $boat->capacity = $request->capacity;
    $boat->status = $request->status;
    $boat->start_time = $request->start_time;
    $boat->end_time = $request->end_time;

    if ($request->hasFile('image')) {
      $image = $request->file('image');
      $imagename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
      $image->move(public_path('boats'), $imagename);
      $boat->image = $imagename;
    }

    $boat->save();

    return redirect()->back()->with('success', 'Boat added successfully!');
  }

  public function view_boat()
  {
    $boats = Boat::all();
    return view('frontdesk.view_boat', compact('boats'));
  }

  public function delete_boat($id)
  {
    $boat = Boat::find($id);
    $boat->delete();
    return redirect()->back()->with('Boat deleted');
  }

  public function update_boat($id)
  {
    $boat = Boat::findOrFail($id);
    return view('frontdesk.update_boat', compact('boat'));
  }

  public function edit_boat(Request $request, $id)
  {
    $boat = Boat::findOrFail($id);

    $boat->name = $request->name;
    $boat->description = $request->description;
    $boat->price = $request->price;
    $boat->capacity = $request->capacity;
    $boat->status = $request->status;
    $boat->start_time = $request->start_time;
    $boat->end_time = $request->end_time;

    if ($request->hasFile('image')) {
      $image = $request->file('image');
      $imagename = time() . '.' . $image->getClientOriginalExtension();
      $image->move(public_path('boats'), $imagename);

      if ($boat->image && file_exists(public_path('boats/' . $boat->image))) {
        unlink(public_path('boats/' . $boat->image));
      }

      $boat->image = $imagename;
    }

    $boat->save();

    return redirect('view_boat')->with('message', 'Boat updated successfully!');

  }

  // Check room availability (basic filtering example)
  public function checkAvailability(Request $request)
  {
    $rooms = Room::query();
    // Example filter: by room_type
    if ($request->has('room_type')) {
      $rooms->where('room_type', $request->room_type);
    }
    if ($request->has('accommodates')) {
      $rooms->where('accommodates', $request->accommodates);
    }
    // Add more filters as needed
    $filteredRooms = $rooms->get();

    $boats = Boat::all();
    $image = Images::all();
    return view('home.index', [
      'rooms' => $filteredRooms,
      'boats' => $boats,
      'image' => $image,
    ]);
  }



  // List all users
  public function usersIndex()
  {
    $users = User::paginate(10);
    return view('admin.users.index', compact('users'));
  }

  // Show create form
  public function usersCreate()
  {
    return view('admin.users.create');
  }

  // Store new user
  public function usersStore(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|email|unique:users',
      'password' => 'required|min:6|confirmed',
      'usertype' => 'required|in:user,frontdesk,admin',
    ]);

    User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
      'usertype' => $request->usertype,
    ]);

    return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
  }

  // Show edit form
  public function usersEdit(User $user)
  {
    return view('admin.users.edit', compact('user'));
  }

  // Update user
  public function usersUpdate(Request $request, User $user)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|email|unique:users,email,' . $user->id,
      'usertype' => 'required|in:user,frontdesk,admin',
    ]);

    $user->update($request->only(['name', 'email', 'usertype']));

    return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
  }

  // Delete user
  public function usersDestroy(User $user)
  {
    $user->delete();

    return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
  }

  public function settingsIndex()
  {
    return view('admin.settings');
  }

  public function search(Request $request)
  {
    $query = $request->get('q');

    if (strlen($query) < 2) {
      return response()->json([
        'bookings' => [],
        'guests' => [],
      ]);
    }

    $bookings = Booking::where('name', 'like', "%{$query}%")
      ->orWhere('email', 'like', "%{$query}%")
      ->limit(5)
      ->get(['id', 'name', 'email']);

    $guests = User::where('name', 'like', "%{$query}%")
      ->limit(5)
      ->get(['id', 'name']);

    return response()->json([
      'bookings' => $bookings,
      'guests' => $guests,
    ]);
  }

  public function checkIn($id)
  {
    $booking = Booking::findOrFail($id);
    $booking->status = 'checked-in';
    $booking->actual_checkin_at = now();
    // ensure scheduled_checkin_at exists (fallback to start_date + default 13:00)
    if (!$booking->scheduled_checkin_at && $booking->start_date) {
      $booking->scheduled_checkin_at = Carbon::parse($booking->start_date . ' 13:00')->toDateTimeString();
    }
    $booking->save();

    return redirect()->back()->with('success', 'Guest checked in!');
  }

  public function checkOut($id)
  {
    $booking = Booking::findOrFail($id);
    $booking->status = 'checked-out';
    $booking->actual_checkout_at = now();
    // ensure scheduled_checkout_at exists (fallback to end_date + default 11:00)
    if (!$booking->scheduled_checkout_at && $booking->end_date) {
      $booking->scheduled_checkout_at = Carbon::parse($booking->end_date . ' 11:00')->toDateTimeString();
    }
    $booking->save();

    return redirect()->back()->with('success', 'Guest checked out!');
  }

  /**
   * Allow frontdesk/admin to set actual check-in / check-out datetimes manually.
   */
  public function setActualTimes(Request $request, $id)
  {
    $booking = Booking::findOrFail($id);
    $request->validate([
      'actual_checkin_at' => 'nullable|date',
      'actual_checkout_at' => 'nullable|date',
    ]);

    if ($request->filled('actual_checkin_at')) {
      $booking->actual_checkin_at = $request->input('actual_checkin_at');
      $booking->status = $booking->status === 'checked-out' ? $booking->status : 'checked-in';
    }
    if ($request->filled('actual_checkout_at')) {
      $booking->actual_checkout_at = $request->input('actual_checkout_at');
      $booking->status = 'checked-out';
    }

    $booking->save();

    return redirect()->back()->with('success', 'Actual times updated.');
  }

  /**
   * Return a structured list of rooms for chatbot quick reply.
   */
  public function chatRooms()
  {
    // Return only name and price for quick chat listing
    $rooms = Room::all();

    $data = $rooms->map(function ($r) {
      return [
        'id' => $r->id,
        'name' => $r->room_name ?? $r->name ?? 'Room ' . $r->id,
        'price' => $r->price ?? null,
      ];
    });

    return response()->json(['rooms' => $data]);
  }

  /**
   * Return a deduplicated list of amenities used across rooms for chatbot quick reply.
   */
  public function chatAmenities()
  {
    // Gather amenities stored on rooms (comma-separated) and merge with
    // static amenities listed on the site's Amenities page.
    $roomAmenities = Room::pluck('amenities')
      ->filter()
      ->map(function ($a) {
        return array_map('trim', explode(',', $a));
      })
      ->flatten()
      ->unique()
      ->values()
      ->all();

    // Amenities rendered in resources/views/home/amenity/service.blade.php —
    // include these as well so the chatbot lists the same items.
    $pageAmenities = [
      'Pristine Beach',
      'Round-the-Clock Security',
      'Beachfront Cabanas & Shade',
      'Private Units with Kitchens',
      'No Corkage Fee',
      'Pet-Friendly (24/7)',
      'Guaranteed Power (backup generator)',
      'Gated Parking',
      '24/7 Guest Assistance',
    ];

    $amenities = collect($roomAmenities)->merge($pageAmenities)->unique()->values()->all();

    return response()->json(['amenities' => $amenities]);
  }

  /**
   * Return contact info for frontdesk used by chatbot quick reply.
   */
  public function chatContactInfo()
  {
    // Hard-coded for now to match site content; adjust if you store these in config or DB.
    $contact = [
      'phone' => '(0123) 456-7890',
      'email' => 'info@hotelcabanas.com',
    ];

    return response()->json($contact);
  }


  public function calamityIndex()
  {
    // 1. Get the single global status (e.g., 'Normal', 'Alert', 'Evacuate')
    // We assume CalamityStatus is a model that always contains a single row/document
    $currentStatus = CalamityStatus::first() ?? new CalamityStatus(['status' => 'Normal']);


    // Render the calamity management index view located at resources/views/admin/calamity/index.blade.php
    return view('admin.calamity.index', [
      'currentStatus' => $currentStatus->status,

    ]);
  }

  /**
   * Update the global resort safety status.
   * @param Request $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function updateStatus(Request $request)
  {
    $request->validate([
      'status' => 'required|in:Normal,Alert,Evacuate',
    ]);

    // Find the single status record or create it if it doesn't exist
    $statusRecord = CalamityStatus::first() ?? new CalamityStatus();
    $statusRecord->status = $request->input('status');
    $statusRecord->save();

    return redirect()->route('admin.calamity.index')->with('success', 'Global safety status updated to ' . $statusRecord->status . '.');
  }




  /**
   * Return recent bookings as JSON for admin dashboard live refresh.
   */
  public function recentBookings()
  {
    $bookings = Booking::with('room')
      ->latest()
      ->take(10)
      ->get()
      ->map(function ($b) {
        return [
          'id' => $b->id,
          'name' => $b->name,
          'status' => $b->status,
          'payment_status' => $b->payment_status,
          'total_amount' => $b->total_amount,
          'paid_amount' => $b->paid_amount,
          'paid_at' => $b->paid_at ? $b->paid_at->toDateTimeString() : null,
          'updated_at' => $b->updated_at ? $b->updated_at->toDateTimeString() : null,
        ];
      });

    return response()->json(['bookings' => $bookings]);
  }
}

