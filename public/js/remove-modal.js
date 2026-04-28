$(document).ready(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // --- Modal Animations & Styles (CSS/Tailwind classes are assumed to exist) ---
    const MODAL_FADE_DURATION = 300; // in milliseconds

    // Universal modal helpers that use the shared modal in `resources/views/home/layouts/app.blade.php`
    function hideModal() {
        const modalContainer = $('#app-modal');

        // 1. Smooth fade out animation
        modalContainer.removeClass('opacity-100').addClass('opacity-0');

        // 2. Wait for the animation to complete, then hide and clean up
        setTimeout(() => {
            $('#modal-title').text('');
            $('#modal-message').text('');
            $('#modal-buttons').empty();
            // Remove 'flex' and add 'hidden' to truly hide it after fade out
            modalContainer.addClass('hidden').removeClass('flex');
        }, MODAL_FADE_DURATION);
    }

    function showModal(title, message, type) {
        const modalContainer = $('#app-modal');

        // Clean up before showing
        $('#modal-title').text(title || '');
        $('#modal-message').text(message || '');

        const buttons = $('#modal-buttons');
        buttons.empty();

        // Standard Modal Button
        const ok = $('<button>')
            .addClass('px-6 py-2 bg-orange-600 text-sm text-white rounded')
            .text('Close')
            .on('click', hideModal);

        buttons.append(ok);

        // Responsive & Centering Styling (These styles make it full screen and centered on all devices)
        modalContainer.css({
            'position': 'fixed',
            'top': '0',
            'left': '0',
            'width': '100%',
            'height': '100%',
            'zIndex': '50', // High z-index to overlay everything
            'backgroundColor': 'rgba(0, 0, 0, 0.5)', // Dark overlay
            'alignItems': 'center',
            'justifyContent': 'center',
            'transition': `opacity ${MODAL_FADE_DURATION}ms ease-in-out` // Add transition for fade effect
        });

        // 1. Show the modal container (make it visible, but transparent)
        modalContainer.removeClass('hidden opacity-100').addClass('flex opacity-0');

        // 2. Trigger the smooth fade-in after a slight delay to ensure the 'hidden' class is removed
        setTimeout(() => {
            modalContainer.addClass('opacity-100').removeClass('opacity-0');
        }, 10); // Small delay to allow browser to register the visibility change
    }

    function showConfirmModal(message, onConfirm, title = 'Confirm') {
        const modalContainer = $('#app-modal');

        // Clean up before showing
        $('#modal-title').text(title);
        $('#modal-message').text(message || '');

        const buttons = $('#modal-buttons');
        buttons.empty();

        // Cancel Button
        const cancel = $('<button>')
            .addClass('px-4 py-2 bg-gray-200 mr-2 rounded')
            .text('Cancel')
            .on('click', hideModal);

        // Confirm Button
        const confirm = $('<button>')
            .addClass('px-4 py-2 bg-red-600 text-white rounded')
            .text('Yes, Remove')
            .on('click', function () {
                hideModal();
                try {
                    if (typeof onConfirm === 'function') onConfirm();
                } catch (e) { console.error(e); }
            });

        buttons.append(cancel).append(confirm);

        // Responsive & Centering Styling (Same as showModal)
        modalContainer.css({
            'position': 'fixed',
            'top': '0',
            'left': '0',
            'width': '100%',
            'height': '100%',
            'zIndex': '50',
            'backgroundColor': 'rgba(0, 0, 0, 0.5)',
            'alignItems': 'center',
            'justifyContent': 'center',
            'transition': `opacity ${MODAL_FADE_DURATION}ms ease-in-out`
        });

        // 1. Show the modal container (make it visible, but transparent)
        modalContainer.removeClass('hidden opacity-100').addClass('flex opacity-0');

        // 2. Trigger the smooth fade-in after a slight delay
        setTimeout(() => {
            modalContainer.addClass('opacity-100').removeClass('opacity-0');
        }, 10);
    }

    /* ============================================================
        BOOK NOW BUTTON HANDLER
    ============================================================ */
    $('.book-now-btn').click(function (e) {
        e.preventDefault();

        let btn = $(this);
        let btnText = btn.find('.btn-text');
        let loader = btn.find('.btn-loader');
        let token = $('meta[name="csrf-token"]').attr('content');

        // Disable button + show loader
        btn.prop('disabled', true)
            .addClass('opacity-70 cursor-not-allowed');
        btnText.text('CHECKING...');
        loader.removeClass('hidden');

        /* ============================================================
            ROOM BOOKING
        ============================================================ */
        if (btn.data('room-id')) {

            let roomId = btn.data('room-id');
            let startDate = $('#startDate').val();
            let endDate = $('#endDate').val();
            let adults = parseInt($('#adults').val() || 0);
            let children = parseInt($('#children').val() || 0);
            let maxGuests = parseInt(btn.data('max-guests') || 13);

            // Guest validation
            if ((adults + children) > maxGuests) {
                resetButton();
                return showModal("Guest Limit Exceeded", "Guest count exceeds room capacity.", "warning");
            }

            let checkinTime = $('#room_checkin_time').val() || null;
            let checkoutTime = $('#room_checkout_time').val() || null;

            $.ajax({
                url: '/check-room-availability',
                method: 'POST',
                data: {
                    room_id: roomId,
                    start_date: startDate,
                    end_date: endDate,
                    start_time: checkinTime,
                    end_time: checkoutTime,
                    _token: token
                },
                success: function (resp) {

                    if (resp.available) {
                        btnText.text('ADDING...');

                        $.ajax({
                            url: '/add-to-cart/' + roomId,
                            method: 'POST',
                            data: {
                                startDate: startDate,
                                endDate: endDate,
                                start_time: checkinTime,
                                end_time: checkoutTime,
                                adults: adults,
                                children: children,
                                _token: token
                            },
                            success: function (response) {
                                $('#cart-summary').html(response.cart_html);

                                btnText.text('ADDED ✓');
                                btn.removeClass('bg-black').addClass('bg-green-600 text-white');

                                setTimeout(() => resetButton(), 1200);

                                showModal("Room Added", "The room has been successfully added to your cart.", "success");
                            },
                            error: function (xhr) {
                                resetButton();
                                showModal("Error", xhr.responseJSON?.message || "Failed to add room.", "error");
                            }
                        });

                    } else {
                        resetButton();
                        showModal("Unavailable", resp.message || "This room is already booked.", "warning");
                    }
                },
                error: function () {
                    resetButton();
                    showModal("Missing Details", "Please fill in check-in and check-out details.", "warning");
                }
            });
        }


        /* ============================================================
            BOAT BOOKING
        ============================================================ */
        else if (btn.data('boat-id')) {

            let boatId = btn.data('boat-id');
            let bookingDate = $('#booking_date').val();
            let guests = parseInt($('#guests').val() || 0);
            let startTime = $('#start_time').val();
            let endTime = $('#end_time').val();

            // Validation
            if (!bookingDate || !guests || !startTime || !endTime) {
                resetButton();
                return showModal("Missing Information", "Please complete the boat booking form.", "warning");
            }

            if (guests > 7) {
                resetButton();
                return showModal("Over Capacity", "Guest count exceeds boat capacity.", "warning");
            }

            $.ajax({
                url: '/check-boat-availability',
                method: 'POST',
                data: {
                    boat_id: boatId,
                    booking_date: bookingDate,
                    start_time: startTime,
                    end_time: endTime,
                    guests: guests,
                    _token: token
                },
                success: function (resp) {

                    if (resp.available) {
                        btnText.text('ADDING...');

                        $.ajax({
                            url: '/add-boat-to-cart/' + boatId,
                            method: 'POST',
                            data: {
                                booking_date: bookingDate,
                                start_time: startTime,
                                end_time: endTime,
                                guests: guests,
                                _token: token
                            },
                            success: function (response) {
                                $('#cart-summary').html(response.cart_html);

                                btnText.text('ADDED ✓');
                                btn.removeClass('bg-black').addClass('bg-green-600 text-white');

                                setTimeout(() => resetButton(), 1200);

                                showModal("Boat Added", "The boat has been successfully added to your cart.", "success");
                            },
                            error: function (xhr) {
                                resetButton();
                                showModal("Error", xhr.responseJSON?.message || "Failed to add boat.", "error");
                            }
                        });

                    } else {
                        resetButton();
                        showModal("Unavailable", resp.message || "Boat is already booked for this time.", "warning");
                    }
                },
                error: function () {
                    resetButton();
                    showModal("Error", "Failed checking boat availability.", "warning");
                }
            });
        }


        /* ============================================================
            RESET BUTTON FUNCTION
        ============================================================ */
        function resetButton() {
            btnText.text('BOOK NOW');
            btn.prop('disabled', false)
                .removeClass('opacity-70 cursor-not-allowed');
            loader.addClass('hidden');
        }
    });


    /* ============================================================
        REMOVE ROOM FROM CHECKOUT
    ============================================================ */
    $(document).on('click', '.remove-room-btn', function (e) {
        e.preventDefault();

        let roomId = $(this).data('room-id');

        showConfirmModal("Are you sure you want to remove this room?", function () {

            $.ajax({
                url: '/remove-from-cart/' + roomId,
                method: 'GET',
                headers: { 'X-From-Checkout': '1' },
                success: function () {
                    window.location.reload();
                },
                error: function () {
                    showModal("Error", "Failed to remove room. Please try again.", "error");
                }
            });
        });
    });


    /* ============================================================
        REMOVE BOAT FROM CHECKOUT
    ============================================================ */
    $(document).on('click', '.remove-boat-btn', function (e) {
        e.preventDefault();

        let boatId = $(this).data('boat-id');

        showConfirmModal("Are you sure you want to remove this boat?", function () {

            $.ajax({
                url: '/remove-boat-from-cart/' + boatId,
                method: 'GET',
                headers: { 'X-From-Checkout': '1' },
                success: function () {
                    window.location.reload();
                },
                error: function () {
                    showModal("Error", "Failed to remove boat.", "error");
                }
            });
        });
    });

});