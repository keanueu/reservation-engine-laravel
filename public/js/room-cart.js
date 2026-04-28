// Room Cart - Add to Cart functionality
function addRoomToCart(roomId) {
    const startDate = document.getElementById('startDate')?.value;
    const endDate = document.getElementById('endDate')?.value;
    const checkinTime = document.getElementById('room_checkin_time')?.value;
    const checkoutTime = document.getElementById('room_checkout_time')?.value;
    const adults = document.getElementById('adults')?.value;
    const children = document.getElementById('children')?.value;

    // Validate inputs
    if (!startDate || !endDate) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Dates',
            text: 'Please select check-in and check-out dates.',
            confirmButtonColor: '#964B00'
        });
        return;
    }

    if (!checkinTime || !checkoutTime) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Times',
            text: 'Please select check-in and check-out times.',
            confirmButtonColor: '#964B00'
        });
        return;
    }

    // Show loading
    Swal.fire({
        title: 'Adding to cart...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Get CSRF token
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Submit to cart
    fetch(`/add-to-cart/${roomId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            startDate: startDate,
            endDate: endDate,
            start_time: checkinTime,
            end_time: checkoutTime,
            adults: parseInt(adults),
            children: parseInt(children)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Update cart summary
            if (data.cart_html) {
                const cartSummary = document.getElementById('cart-summary');
                if (cartSummary) {
                    cartSummary.innerHTML = data.cart_html;
                }
            }

            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Added to Cart!',
                text: 'Room has been successfully added to your cart.',
                confirmButtonColor: '#964B00',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            // Show error message
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Failed to add room to cart.',
                confirmButtonColor: '#964B00'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while adding the room to cart.',
            confirmButtonColor: '#964B00'
        });
    });
}
