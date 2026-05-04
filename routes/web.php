<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\BoatBookingController;
use App\Http\Controllers\Auth\RegistrationOtpController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RoomAvailabilityController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BoatController;
use App\Http\Controllers\FrontdeskController;
use App\Models\BoatBooking;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\WeatherAlertController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AIChatController;
use App\Http\Controllers\BookingExtensionController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\CartPageController;
use App\Http\Controllers\BookingWizardController;
use App\Http\Controllers\CollectionsController;
use App\Http\Controllers\HeroSearchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymongoWebhookController;

Route::get('/dashboard', function () {
    return redirect('/home');
})->name('dashboard');


// frontdesk routes
Route::middleware(['auth'])->get('/frontdesk/home', [FrontdeskController::class, 'index'])->name('frontdesk.home');
Route::get('/frontdesk/boat_bookings', function () {
    $boatBookings = BoatBooking::with('boat')
        ->orderBy('booking_date', 'desc')
        ->get();

    return view('frontdesk.boat_bookings', compact('boatBookings'));
});
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [AdminController::class, 'index']);
    Route::get('/create_room', [RoomController::class, 'create']);
    Route::post('/add_room', [RoomController::class, 'store']);
    Route::get('/view_room', [RoomController::class, 'index']);
    Route::get('/view_boat', [BoatController::class, 'index']);
    // Frontdesk settings (booking & refund percentages)
    Route::get('/frontdesk/settings', [SettingsController::class, 'index'])->name('frontdesk.settings');
    Route::post('/frontdesk/settings', [SettingsController::class, 'update'])->name('frontdesk.settings.update');
    Route::get('/create_boat', [BoatController::class, 'create']);
    Route::post('/add_boat', [BoatController::class, 'store'])->name('add_boat');
    Route::get('/update_boat/{id}', [BoatController::class, 'edit']);
    Route::post('/edit_boat/{id}', [BoatController::class, 'update']);
    Route::get('/delete_boat/{id}', [BoatController::class, 'destroy']);
    Route::get('/delete_room/{id}', [RoomController::class, 'destroy']);
    Route::get('/update_room/{id}', [RoomController::class, 'edit']);
    Route::post('/edit_room/{id}', [RoomController::class, 'update']);
    Route::get('/images_pages', [AdminController::class, 'images_pages']);
    Route::post('/upload_images', [AdminController::class, 'upload_images']);
    Route::get('/delete_images/{id}', [AdminController::class, 'delete_images']);
    Route::get('/all_messages', [AdminController::class, 'all_messages']);
    Route::get('/notifications/contacts', [AdminController::class, 'notificationsContacts']);
    Route::get('/notifications/messages', [AdminController::class, 'notificationsMessages']);
    Route::get('/notifications/unread-users', [AdminController::class, 'notificationsUnreadUsers']);
    Route::get('/delete_message/{id}', [AdminController::class, 'delete_message']);
    Route::get('/bookings', [AdminController::class, 'bookings']);
    Route::post('/admin/bookings/{id}/set-actual-times', [AdminController::class, 'setActualTimes'])->name('admin.bookings.set_actual_times');
    Route::get('/delete_booking/{id}', [AdminController::class, 'delete_booking']);
    Route::get('/approve_booking/{id}', [AdminController::class, 'approve_booking']);
    Route::get('/reject_booking/{id}', [AdminController::class, 'reject_booking']);
    Route::post('/email/{id}', [AdminController::class, 'email']);
    Route::get('/send_booking_email/{id}', [AdminController::class, 'send_booking_email']);
    Route::get('/send_email/{id}', [AdminController::class, 'send_email']);
    Route::post('/booking_email/{id}', [AdminController::class, 'booking_email']);
    Route::get('/room_details/{id}', [HomeController::class, 'room_details']);
    Route::get('/boat_details/{id}', [HomeController::class, 'boat_details']);
});
// Use AdminController for check-in/out actions (methods implemented there)
Route::get('/bookings/check-in/{id}', [AdminController::class, 'checkIn'])->name('bookings.checkIn');
Route::get('/bookings/check-out/{id}', [AdminController::class, 'checkOut'])->name('bookings.checkOut');

// Frontdesk: mark deposit paid manually
Route::post('/bookings/{id}/mark-deposit-paid', [AdminController::class, 'markDepositPaid'])->name('bookings.markDepositPaid');
Route::post('/boat-bookings/{id}/mark-deposit-paid', [AdminController::class, 'markBoatDepositPaid'])->name('boat_bookings.markDepositPaid');


/*
|--------------------------------------------------------------------------
| HOME ROUTES
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\RoomPricingController;

// Public real-time availability check (used by room detail widget — no auth required)
Route::get('/check-room-availability', [RoomAvailabilityController::class, 'check'])->name('room.availability.check');
Route::get('/check-boat-availability', [CartController::class, 'checkBoatAvailability'])->name('boat.availability.check');

// Public dynamic pricing endpoint (used by room detail widget)
Route::get('/room-pricing', [RoomPricingController::class, 'calculate'])->name('room.pricing');

// PayMongo webhook — CSRF exempt because it is excluded via withoutMiddleware
// This is the /webhooks/paymongo alias (the canonical endpoint is POST /api/paymongo/webhook)
Route::post('/webhooks/paymongo', [PaymongoWebhookController::class, 'handle'])
    ->name('webhooks.paymongo')
    ->withoutMiddleware([
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        'web',
    ]);
Route::post('/add_booking/{id}', [HomeController::class, 'add_booking']);
Route::post('/add_boat_booking/{id}', [HomeController::class, 'add_boat_booking']);


// user routes
Route::get('/', [AdminController::class, 'home']);

// Hero search form submissions
Route::post('/search/stay', [HeroSearchController::class, 'searchStay'])->name('search.stay');
Route::post('/search/sail', [HeroSearchController::class, 'searchSail'])->name('search.sail');
// Weather proxy endpoints (use server-side to hide API key and enable caching)
Route::get('/weather/current', [WeatherController::class, 'current']);
Route::get('/weather/forecast', [WeatherController::class, 'forecast']);

Route::middleware(['auth'])->group(function () {
    Route::get('/home/rooms', [PageController::class, 'home_rooms']);
    // Custom profile page (renamed to avoid conflict with Jetstream's profile.show)
    Route::get('/profile', [ProfileController::class, 'show'])->name('user.profile');
    // User bookings (personal view for guests to see their bookings and request extensions)
    Route::get('/home/bookings', [PageController::class, 'home_bookings'])->name('home.bookings');
    // My Bookings page (dedicated page instead of modal)
    Route::get('/my-bookings', [PageController::class, 'my_bookings_page'])->name('my.bookings');
    // API: return authenticated user's bookings as JSON for the My Bookings page
    Route::get('/api/my-bookings', [PageController::class, 'api_my_bookings'])->name('api.my_bookings');

    // Alerts API and admin store
    Route::post('/admin/alerts', [AlertController::class, 'store'])->middleware('auth')->name('admin.alerts.store');
    Route::get('/admin/alerts', [AlertController::class, 'index'])->middleware('auth')->name('admin.alerts.index');

    Route::get('/home/alerts', [PageController::class, 'home_alerts']);
    Route::get('/home/boat', [PageController::class, 'home_boat']);
    // Dedicated cart page (replaces /home/selectroom)
    Route::get('/cart', [CartPageController::class, 'index'])->name('cart.show');
    Route::get('/home/checkout', [PageController::class, 'home_checkout']);
    Route::get('/checkout/{room_id}', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/add-boat-to-cart/{boat_id}', [CartController::class, 'addBoatToCart']);
    Route::get('/remove-boat-from-cart/{boat_id}', [CartController::class, 'removeBoatFromCart']);
    Route::post('/add-to-cart/{room_id}', [CartController::class, 'add'])->name('cart.add');
    Route::get('/remove-from-cart/{room_id}', [CartController::class, 'remove'])->name('cart.remove');

    // Booking & Availability
    Route::get('/check_availability', [AdminController::class, 'checkAvailability'])->name('check_availability');
    Route::post('/add_boat_booking/{id}', [BoatBookingController::class, 'add_boat_booking']);
    Route::get('/delete_boat_booking/{id}', [BoatBookingController::class, 'delete_boat_booking']);
    Route::get('/approve_boat_booking/{id}', [BoatBookingController::class, 'approve_boat_booking']);
    Route::get('/reject_boat_booking/{id}', [BoatBookingController::class, 'reject_boat_booking']);
    Route::get('/send_boat_booking_email/{id}', [BoatBookingController::class, 'showSendBoatBookingEmail']);
    Route::post('/boat_booking_email/{id}', [BoatBookingController::class, 'sendBoatBookingEmail']);
    Route::post('/check-room-availability', [RoomAvailabilityController::class, 'check']);
    Route::post('/check-boat-availability', [CartController::class, 'checkBoatAvailability']);

});

Route::get('/home/contact', [PageController::class, 'home_contact']);
Route::get('/home/amenities', [PageController::class, 'home_amenities']);


//otp
Route::get('/register/otp', [RegistrationOtpController::class, 'form'])->name('registration.otp.form');
Route::post('/register/otp', [RegistrationOtpController::class, 'verify'])->name('registration.otp.verify');
Route::post('/register/otp/resend', [RegistrationOtpController::class, 'resend'])->name('registration.otp.resend');


//payment method
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');

// Booking Wizard (3-step pages replacing the modal)
Route::middleware(['auth'])->group(function () {
    Route::get('/booking/dates',  [BookingWizardController::class, 'stepDates'])->name('booking.dates');
    Route::post('/booking/dates', [BookingWizardController::class, 'postDates'])->name('booking.dates.post');
    Route::get('/booking/guests',  [BookingWizardController::class, 'stepGuests'])->name('booking.guests');
    Route::post('/booking/guests', [BookingWizardController::class, 'postGuests'])->name('booking.guests.post');
    Route::get('/booking/review',  [BookingWizardController::class, 'stepReview'])->name('booking.review');
    Route::post('/booking/review', [BookingWizardController::class, 'postReview'])->name('booking.review.post');
});
Route::get('/bookings/{booking}/pay', [PaymentController::class, 'payForBooking'])->name('bookings.pay');
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
Route::get('/booking/success', [PaymentController::class, 'bookingSuccess'])->name('booking.success');

// Booking extension endpoints (minimal implementation)
Route::middleware(['auth'])->post('/bookings/{booking}/extension', [BookingExtensionController::class, 'store'])->name('bookings.extension.store');
// Admin approve (frontdesk) - mark as paid and extend booking
Route::middleware(['auth'])->post('/admin/bookings/extensions/{id}/approve', [BookingExtensionController::class, 'approve'])->name('admin.bookings.extension.approve');
// Payment webhook for extensions (gateway will call this)
Route::post('/booking-extensions/webhook', [BookingExtensionController::class, 'webhook'])->name('booking_extensions.webhook');

// Wrapper route to pay for an extension using existing PaymentController flow
Route::middleware(['auth'])->get('/booking-extensions/{id}/pay', [BookingExtensionController::class, 'pay'])->name('booking_extensions.pay');

// Refund routes
// User requests a refund for a paid booking
Route::middleware(['auth'])->post('/bookings/{id}/request-refund', [BookingController::class, 'requestRefund'])->name('bookings.requestRefund');

// Frontdesk: approve/reject refund (mark refunded in DB). These require auth.
Route::middleware(['auth'])->post('/admin/bookings/{id}/refund/approve', [BookingController::class, 'adminApproveRefund'])->name('admin.bookings.refund.approve');
Route::middleware(['auth'])->post('/admin/bookings/{id}/refund/reject', [BookingController::class, 'adminRejectRefund'])->name('admin.bookings.refund.reject');

// Admin API: return pending online extension statuses (for frontdesk polling)
Route::middleware(['auth'])->get('/admin/api/pending-extensions', [BookingExtensionController::class, 'pendingExtensions'])->name('admin.api.pending_extensions');
// Admin API: refresh single extension status by querying PayMongo (frontdesk may call this)
Route::middleware(['auth'])->get('/admin/api/extensions/{id}/refresh', [BookingExtensionController::class, 'refresh'])->name('admin.api.extensions.refresh');
// Admin API: get single extension
Route::middleware(['auth'])->get('/admin/api/extensions/{id}', [BookingExtensionController::class, 'show'])->name('admin.api.extensions.show');



/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
// Collections / Promotions pages
Route::get('/collections/xmas', [CollectionsController::class, 'xmas'])->name('collections.xmas');

Route::middleware(['auth'])->group(function () {

    Route::get('/admin/settings', [AdminController::class, 'settingsIndex'])->name('admin.settings.index');
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/export-sales', [AdminController::class, 'exportSales'])->name('admin.export.sales');
    Route::get('/admin/api/recent-bookings', [AdminController::class, 'recentBookings'])->name('admin.api.recent_bookings');
    // Admin management routes
    Route::get('/admin/users', [AdminController::class, 'usersIndex'])->name('admin.users.index');
    Route::get('/admin/users/create', [AdminController::class, 'usersCreate'])->name('admin.users.create');
    Route::get('/admin/users/{user}/edit', [AdminController::class, 'usersEdit'])->name('admin.users.edit');
    Route::delete('/admin/users/{user}', [AdminController::class, 'usersDestroy'])->name('admin.users.destroy');
    Route::post('/admin/users', [AdminController::class, 'usersStore'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [AdminController::class, 'usersUpdate'])->name('admin.users.update');
    Route::get('/search', [AdminController::class, 'search'])->name('search');
    Route::prefix('admin/api')->group(function () {
        // Chat routes
        Route::get('/sessions', [ChatController::class, 'sessions']);
        Route::get('/session/{session_id}', [ChatController::class, 'sessionMessages']);
        Route::post('/session/{session_id}/reply', [ChatController::class, 'reply']);


    });

    // Admin discounts (promotions)
    Route::get('/admin/discounts', [DiscountController::class, 'index'])->name('admin.discounts.index');
    Route::get('/admin/discounts/create', [DiscountController::class, 'create'])->name('admin.discounts.create');
    Route::post('/admin/discounts', [DiscountController::class, 'store'])->name('admin.discounts.store');
    Route::get('/admin/discounts/{discount}/edit', [DiscountController::class, 'edit'])->name('admin.discounts.edit');
    Route::put('/admin/discounts/{discount}', [DiscountController::class, 'update'])->name('admin.discounts.update');
    Route::delete('/admin/discounts/{discount}', [DiscountController::class, 'destroy'])->name('admin.discounts.delete');
});

Route::get('/admin/chat', [ChatController::class, 'index']);

// chatbot
Route::middleware(['auth'])->group(function () {

    Route::prefix('chat')->group(function () {
        Route::get('/fetch', [ChatbotController::class, 'fetchMessages']);
        Route::post('/send', [ChatbotController::class, 'sendMessage']);
        Route::post('/quick-reply', [ChatbotController::class, 'quickReply']);
        // Chat helper endpoints for quick replies
        Route::get('/rooms', [AdminController::class, 'chatRooms']);
        Route::get('/amenities', [AdminController::class, 'chatAmenities']);
        Route::get('/contact-info', [AdminController::class, 'chatContactInfo']);
    });

    Route::get('/calamity', [AdminController::class, 'calamityIndex'])->name('admin.calamity.index');

    // Form submissions
    Route::post('/calamity/status', [AdminController::class, 'updateStatus'])->name('admin.calamity.update_status');
    Route::post('/calamity/item/add', [AdminController::class, 'addItem'])->name('admin.calamity.add_item');
    Route::post('/calamity/item/{id}/toggle', [AdminController::class, 'toggleItem'])->name('admin.calamity.toggle_item');
    Route::delete('/calamity/item/{id}/delete', [AdminController::class, 'deleteItem'])->name('admin.calamity.delete_item');

});

Route::get('/preparedness-hub', [AdminController::class, 'showPublicHub'])->name('public.hub');
Route::get('/check-typhoon-status', [WeatherAlertController::class, 'checkTyphoonStatus']);
// email direct route
Route::get('/alert-status', function () {
    return redirect('/');
});


Route::middleware(['auth'])->group(function () {

    // GET all notifications
    Route::get('/user/notifications', function () {
        return auth()->user()->unreadNotifications;
    });

    // Mark as read
    Route::post('/user/notifications/{id}/read', function ($id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return response()->json(['status' => 'ok']);
    });

});


Route::post('/ai/chat', [AIChatController::class, 'chat']);
// web.php

 Route::get('/room_detailsv2/{id}', [HomeController::class, 'room_detailsv2']);


 Route::get('/health', function () {
    return response('OK', 200);
});