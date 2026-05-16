@extends('home.layouts.app')

@section('content')

<div class="relative w-full h-[35vh] md:h-[45vh]">
    <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80"
        alt="Luxury Beach Resort" class="absolute inset-0 object-cover w-full h-full">
    <div class="relative z-10 flex items-end justify-center w-full h-full bg-black bg-opacity-50 px-4 pb-12 md:pb-16">
        <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl text-white text-center font-medium ">
            My bookings
        </h1>
    </div>
</div>

<div class="min-h-screen bg-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Page Header --}}
        <div class="mb-8" data-reveal>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium mb-4 section-label">Your reservations</p>
                    <h2 class="text-4xl font-medium text-black">My bookings</h2>
                    <p class="text-base text-black mt-3 leading-relaxed">Manage your reservations and booking history</p>
                </div>
                <a href="javascript:void(0)" onclick="openBookingModal()" 
                   class="btn-primary px-6 py-3 text-sm">
                    New booking
                </a>
            </div>
        </div>

        @auth
            {{-- Booking Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8" data-reveal data-reveal-delay="1">
                <div class="bg-white p-6 shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-black">Total bookings</p>
                            <p class="text-3xl text-black mt-1" id="total-bookings">--</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-black">Confirmed</p>
                            <p class="text-3xl text-black mt-1" id="confirmed-bookings">--</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-black">Upcoming</p>
                            <p class="text-3xl text-black mt-1" id="upcoming-bookings">--</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter Tabs --}}
            <div class="mb-6" data-reveal data-reveal-delay="2">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <button class="booking-filter-tab active border-b-2 border-[#964B00] py-2 px-1 text-sm text-[#964B00]" data-filter="all">
                            All bookings
                        </button>
                        <button class="booking-filter-tab border-b-2 border-transparent py-2 px-1 text-sm text-black hover:text-black hover:border-gray-300" data-filter="upcoming">
                            Upcoming
                        </button>
                        <button class="booking-filter-tab border-b-2 border-transparent py-2 px-1 text-sm text-black hover:text-black hover:border-gray-300" data-filter="completed">
                            Completed
                        </button>
                        <button class="booking-filter-tab border-b-2 border-transparent py-2 px-1 text-sm text-black hover:text-black hover:border-gray-300" data-filter="cancelled">
                            Cancelled
                        </button>
                    </nav>
                </div>
            </div>

            {{-- Bookings List --}}
            <div class="space-y-6" data-reveal data-reveal-delay="3">
                <div id="bookings-container">
                    {{-- Loading State --}}
                    <div id="bookings-loading" class="text-center py-12">
                        <div class="inline-block animate-spin h-8 w-8 border-4 border-gray-300 border-t-[#964B00]"></div>
                        <p class="text-black mt-4">Loading your bookings...</p>
                    </div>

                    {{-- Empty State --}}
                    <div id="bookings-empty" class="hidden text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h3 class="mt-4 text-xl text-black">No bookings found</h3>
                        <p class="mt-3 text-base text-black">You haven't made any bookings yet.</p>
                        <div class="mt-6">
                            <a href="javascript:void(0)" onclick="openBookingModal()" class="btn-primary px-6 py-3 text-sm">
                                Make your first booking
                            </a>
                        </div>
                    </div>

                    {{-- Bookings List --}}
                    <div id="bookings-list" class="hidden space-y-4">
                        {{-- Booking items will be populated by JavaScript --}}
                    </div>
                </div>
            </div>

        @else
            {{-- Guest State --}}
            <div class="text-center py-16" data-reveal>
                <svg class="mx-auto h-16 w-16 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <h3 class="mt-6 text-2xl text-black">Sign in to view your bookings</h3>
                <p class="mt-3 text-base text-black leading-relaxed">Access your reservation history and manage upcoming stays.</p>
                <div class="mt-8 flex justify-center gap-4">
                    <a href="{{ route('login') }}" class="btn-primary px-6 py-3 text-sm">
                        Sign in
                    </a>
                    <a href="{{ route('register') }}" class="btn-outline px-6 py-3 text-sm">
                        Create account
                    </a>
                </div>
            </div>
        @endauth
    </div>
</div>

{{-- Booking Details Modal --}}
<div id="booking-details-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeBookingModal()"></div>
        
        <div class="inline-block align-bottom bg-white text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div class="bg-white px-6 pt-6 pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg text-black">Booking details</h3>
                    <button onclick="closeBookingModal()" class="text-white hover:text-black">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div id="booking-details-content">
                    {{-- Content will be populated by JavaScript --}}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @auth
        loadBookings();
        setupFilterTabs();
    @endauth
});

@auth
function loadBookings() {
    fetch('/api/my-bookings', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to load bookings');
        }
        return response.json();
    })
    .then(data => {
        updateBookingStats(data);
        displayBookings(data.bookings || []);
    })
    .catch(error => {
        console.error('Error loading bookings:', error);
        document.getElementById('bookings-loading').classList.add('hidden');
        document.getElementById('bookings-empty').classList.remove('hidden');
    });
}

function updateBookingStats(data) {
    document.getElementById('total-bookings').textContent = data.stats?.total || 0;
    document.getElementById('confirmed-bookings').textContent = data.stats?.confirmed || 0;
    document.getElementById('upcoming-bookings').textContent = data.stats?.upcoming || 0;
}

function displayBookings(bookings) {
    const container = document.getElementById('bookings-container');
    const loading = document.getElementById('bookings-loading');
    const empty = document.getElementById('bookings-empty');
    const list = document.getElementById('bookings-list');

    loading.classList.add('hidden');

    if (bookings.length === 0) {
        empty.classList.remove('hidden');
        list.classList.add('hidden');
        return;
    }

    empty.classList.add('hidden');
    list.classList.remove('hidden');
    list.innerHTML = '';

    bookings.forEach(booking => {
        const bookingCard = createBookingCard(booking);
        list.appendChild(bookingCard);
    });
}

function createBookingCard(booking) {
    const card = document.createElement('div');
    card.className = 'booking-card bg-white border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200';
    card.dataset.status = booking.status?.toLowerCase() || 'pending';
    
    const statusColors = {
        confirmed: 'bg-green-100 text-green-800',
        pending: 'bg-yellow-100 text-yellow-800',
        cancelled: 'bg-red-100 text-red-800',
        completed: 'bg-gray-100 text-black'
    };

    card.innerHTML = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-[#964B00] text-white flex items-center justify-center text-lg">
                        ${booking.room_name ? booking.room_name.charAt(0) : 'B'}
                    </div>
                    <div>
                        <h3 class="text-lg text-black">${booking.room_name || booking.boat_name || 'Booking'}</h3>
                        <p class="text-sm text-black mt-0.5">Booking #${booking.id}</p>
                    </div>
                </div>
                <span class="px-3 py-1 text-sm ${statusColors[booking.status?.toLowerCase()] || statusColors.pending}">
                    ${booking.status || 'Pending'}
                </span>
            </div>

            ${booking.refund_status ? `
                <div class="mb-4 p-3 bg-gray-50 border-l-4 ${booking.refund_status === 'refunded' ? 'border-green-500' : 'border-amber-500'}">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium  ${booking.refund_status === 'refunded' ? 'text-green-700' : 'text-amber-700'}">
                            ${booking.refund_status === 'refunded' ? 'Refund Processed' : 'Refund ' + booking.refund_status}
                        </span>
                        ${booking.refund_amount ? `<span class="text-sm font-medium text-black">₱${parseFloat(booking.refund_amount).toLocaleString()}</span>` : ''}
                    </div>
                </div>
            ` : ''}
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <p class="text-sm text-black mb-1.5">Check-in</p>
                    <p class="text-sm text-black">${formatDate(booking.checkin_date)} ${booking.checkin_time || ''}</p>
                </div>
                <div>
                    <p class="text-sm text-black mb-1.5">Check-out</p>
                    <p class="text-sm text-black">${formatDate(booking.checkout_date)} ${booking.checkout_time || ''}</p>
                </div>
                <div>
                    <p class="text-sm text-black mb-1.5">Total amount</p>
                    <p class="text-base text-black">₱${parseFloat(booking.total_price || 0).toLocaleString()}</p>
                </div>
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4 text-sm text-black">
                    <span>${booking.adults || 0} Adults</span>
                    ${booking.children ? `<span>${booking.children} Children</span>` : ''}
                    <span>Created ${formatDate(booking.created_at)}</span>
                </div>
                <div class="flex items-center gap-2">
                    ${booking.status?.toLowerCase() === 'confirmed' && !booking.group_has_refund ? `
                        <button onclick="toggleExtensionForm(${booking.id})" 
                                class="text-blue-600 hover:text-blue-800 text-sm">
                            Request extension
                        </button>
                    ` : ''}
                    ${['confirmed', 'pending'].includes(booking.status?.toLowerCase()) && !booking.group_has_refund ? `
                        <button onclick="toggleRefundForm(${booking.id})" 
                                class="text-red-600 hover:text-red-800 text-sm">
                            Request refund
                        </button>
                    ` : ''}
                    <button onclick="viewBookingDetails(${booking.id})" 
                            class="text-[#964B00] hover:text-[#7a3c00] text-sm">
                        View details
                    </button>
                </div>
            </div>
            
            <!-- Extension Form (Collapsible) -->
            <div id="extension-form-${booking.id}" class="hidden mt-4 pt-4 border-t border-gray-200">
                <h4 class="text-sm text-black mb-3">Request extension</h4>
                <form onsubmit="handleExtensionSubmit(event, ${booking.id})" class="space-y-3">
                    <div>
                        <label class="block text-sm text-black mb-1">Additional hours</label>
                        <select name="hours" required class="w-full border border-gray-300 px-3 py-2 text-sm">
                            <option value="1">1 Hour</option>
                            <option value="2">2 Hours</option>
                            <option value="5">5 Hours</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-black mb-1">Payment method</label>
                        <select name="payment_method" required class="w-full border border-gray-300 px-3 py-2 text-sm">
                            <option value="">Select payment method</option>
                            <option value="online">Pay Online (PayMongo)</option>
                            <option value="frontdesk">Pay at Front Desk</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="toggleExtensionForm(${booking.id})" 
                                class="px-4 py-2 text-sm border border-gray-300 text-black hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm bg-[#964B00] text-white hover:bg-[#7a3c00]">
                            Submit request
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Refund Form (Collapsible) -->
            <div id="refund-form-${booking.id}" class="hidden mt-4 pt-4 border-t border-gray-200">
                <h4 class="text-sm text-black mb-3">Request refund</h4>
                <form onsubmit="handleRefundSubmit(event, ${booking.id})" class="space-y-3">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div>
                        <label class="block text-sm text-black mb-1">Amount (optional)</label>
                        <input type="number" step="0.01" name="amount" 
                               class="w-full border border-gray-300 px-3 py-2 text-sm" 
                               placeholder="Leave blank for full refund">
                    </div>
                    <div>
                        <label class="block text-sm text-black mb-1">Reason (optional)</label>
                        <textarea name="reason" rows="3" 
                                  class="w-full border border-gray-300 px-3 py-2 text-sm" 
                                  placeholder="Reason for refund request"></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="toggleRefundForm(${booking.id})" 
                                class="px-4 py-2 text-sm border border-gray-300 text-black hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm bg-red-600 text-white hover:bg-red-700">
                            Submit request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    return card;
}

function setupFilterTabs() {
    const tabs = document.querySelectorAll('.booking-filter-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Update active tab
            tabs.forEach(t => {
                t.classList.remove('active', 'border-[#964B00]', 'text-[#964B00]');
                t.classList.add('border-transparent', 'text-black');
            });
            this.classList.add('active', 'border-[#964B00]', 'text-[#964B00]');
            this.classList.remove('border-transparent', 'text-black');
            
            // Filter bookings
            const filter = this.dataset.filter;
            filterBookings(filter);
        });
    });
}

function filterBookings(filter) {
    const cards = document.querySelectorAll('.booking-card');
    cards.forEach(card => {
        if (filter === 'all') {
            card.style.display = 'block';
        } else {
            const status = card.dataset.status;
            if (filter === 'upcoming' && ['confirmed', 'pending'].includes(status)) {
                card.style.display = 'block';
            } else if (filter === status) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        }
    });
}

function viewBookingDetails(bookingId) {
    // Implementation for viewing booking details
    document.getElementById('booking-details-modal').classList.remove('hidden');
    // Load and display booking details
}

function closeBookingModal() {
    document.getElementById('booking-details-modal').classList.add('hidden');
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
}

function showError(message) {
    // Simple error display - you can enhance this
    alert(message);
}

function toggleExtensionForm(bookingId) {
    const form = document.getElementById('extension-form-' + bookingId);
    const refundForm = document.getElementById('refund-form-' + bookingId);
    if (form) {
        form.classList.toggle('hidden');
        if (refundForm && !refundForm.classList.contains('hidden')) {
            refundForm.classList.add('hidden');
        }
    }
}

function toggleRefundForm(bookingId) {
    const form = document.getElementById('refund-form-' + bookingId);
    const extensionForm = document.getElementById('extension-form-' + bookingId);
    if (form) {
        form.classList.toggle('hidden');
        if (extensionForm && !extensionForm.classList.contains('hidden')) {
            extensionForm.classList.add('hidden');
        }
    }
}

async function handleExtensionSubmit(event, bookingId) {
    event.preventDefault();
    const form = event.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Processing...';
    
    try {
        const formData = new FormData(form);
        const data = {
            hours: formData.get('hours'),
            payment_method: formData.get('payment_method')
        };
        
        const response = await fetch(`/bookings/${bookingId}/extension`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (!response.ok) {
            throw new Error(result.error || result.message || 'Failed to request extension');
        }
        
        if (result.pay_url) {
            window.location.href = result.pay_url;
        } else {
            alert('Extension requested successfully via Front Desk payment.');
            window.location.reload();
        }
    } catch (error) {
        console.error('Extension error:', error);
        alert(error.message);
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
}

async function handleRefundSubmit(event, bookingId) {
    event.preventDefault();
    const form = event.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    if (!confirm('Are you sure you want to request a refund? This will cancel your booking immediately.')) {
        return;
    }
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Processing...';
    
    try {
        const formData = new FormData(form);
        const data = {
            amount: formData.get('amount'),
            reason: formData.get('reason')
        };
        
        const response = await fetch(`/bookings/${bookingId}/request-refund`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (!response.ok) {
            throw new Error(result.error || result.message || 'Failed to request refund');
        }
        
        alert(result.message || 'Refund request submitted successfully. Your booking is now cancelled.');
        window.location.reload();
    } catch (error) {
        console.error('Refund error:', error);
        alert(error.message);
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
}
@endauth
</script>
@endsection

