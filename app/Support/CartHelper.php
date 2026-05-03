<?php

namespace App\Support;

use App\Models\Room;
use App\Models\Boat;
use App\Models\Setting;
use Illuminate\Support\Facades\View;

class CartHelper
{
    /**
     * Compute the grand total from a raw cart array.
     */
    public static function total(array $cart): float
    {
        $total = 0.0;
        foreach ($cart as $item) {
            if (isset($item['room_id'])) {
                $total += $item['line_total'] ?? (($item['unit_price'] ?? 0) * ($item['nights'] ?? 1));
            } elseif (isset($item['boat_id'])) {
                $total += $item['price'] ?? 0;
            }
        }
        return $total;
    }

    /**
     * Build the cartRooms / cartBoats collections used by every cart partial.
     */
    public static function buildCollections(array $cart): array
    {
        $cartRooms = collect();
        $cartBoats = collect();

        foreach ($cart as $item) {
            if (isset($item['room_id'])) {
                $room = Room::find($item['room_id']);
                if ($room) {
                    $room->cart_data = $item;
                    $cartRooms->push($room);
                }
            } elseif (isset($item['boat_id'])) {
                $boat = Boat::find($item['boat_id']);
                if ($boat) {
                    $boat->cart_data = $item;
                    $cartBoats->push($boat);
                }
            }
        }

        return [$cartRooms, $cartBoats];
    }

    /**
     * Render a cart partial and return the HTML string.
     */
    public static function renderPartial(string $partial, array $cart): string
    {
        [$cartRooms, $cartBoats] = self::buildCollections($cart);
        $total          = self::total($cart);
        $depositPercent = (float) Setting::get('deposit_percentage', config('booking.deposit_percentage', 50));
        $deposit        = $total * ($depositPercent / 100);

        return View::make($partial, compact('cartRooms', 'cartBoats', 'total', 'deposit'))->render();
    }

    /**
     * Detect which cart partial to render based on request headers / referer.
     */
    public static function detectPartial(): string
    {
        $referer = request()->headers->get('referer', '');

        if (request()->hasHeader('X-From-Checkout') || str_contains($referer, '/home/checkout')) {
            return 'home.partials.checkout-price-details';
        }
        if (request()->hasHeader('X-From-RoomCart') || str_contains($referer, '/home/roomcart')) {
            return 'home.partials.cart-checkout';
        }
        return 'home.partials.cart-summary';
    }
}
