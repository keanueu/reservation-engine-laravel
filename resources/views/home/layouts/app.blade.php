<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('LOGO-FINAL.png') }}" type="image/x-icon">
    <title>{{ config('app.name') }} — Luxury Beach & Boat Resort</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* ── Design Tokens ── */
        :root {
            --brand:       #964B00;
            --brand-dark:  #6b3500;
            --brand-light: #bf6b1a;
            --white:       #ffffff;
            --off-white:   #faf9f7;
            --gray-50:     #f9fafb;
            --gray-100:    #f3f4f6;
            --gray-200:    #e5e7eb;
            --gray-500:    #6b7280;
            --gray-700:    #374151;
            --gray-900:    #111827;
            --shadow-sm:   0 1px 3px rgba(0,0,0,0.08);
            --shadow-md:   0 4px 16px rgba(0,0,0,0.10);
            --shadow-lg:   0 8px 32px rgba(0,0,0,0.13);
            --shadow-xl:   0 20px 60px rgba(0,0,0,0.16);
        }

        /* ── Global resets ── */
        *, *::before, *::after { border-radius: 0 !important; box-sizing: border-box; }
        .loader-spinner { border-radius: 9999px !important; }
        [x-cloak] { display: none !important; }
        html { scroll-behavior: smooth; }

        /* ── Typography ── */
        body { font-family: 'Manrope', sans-serif; background: var(--off-white); color: var(--gray-900); }
        .font-playfair { font-family: 'Playfair Display', serif !important; }
        h1, h2, h3 { font-family: 'Playfair Display', serif; }
        input, select, textarea, button { font-family: 'Manrope', sans-serif; }

        /* ── Scroll-reveal ── */
        [data-reveal] { opacity: 0; transform: translateY(32px); transition: opacity .65s ease, transform .65s ease; }
        [data-reveal].revealed { opacity: 1; transform: translateY(0); }
        [data-reveal-delay="1"] { transition-delay: .1s; }
        [data-reveal-delay="2"] { transition-delay: .2s; }
        [data-reveal-delay="3"] { transition-delay: .3s; }

        /* ── Skeleton loader ── */
        .skeleton { background: linear-gradient(90deg, #e5e7eb 25%, #f3f4f6 50%, #e5e7eb 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; }
        @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

        /* ── Primary button ── */
        .btn-primary { background: var(--brand); color: #fff; transition: background .2s, transform .15s, box-shadow .2s; }
        .btn-primary:hover { background: var(--brand-dark); transform: translateY(-1px); box-shadow: 0 6px 20px rgba(150,75,0,.3); }

        /* ── Outline button ── */
        .btn-outline { background: transparent; color: var(--brand); border: 2px solid var(--brand); transition: all .2s; }
        .btn-outline:hover { background: var(--brand); color: #fff; }

        /* ── Card ── */
        .card { background: #fff; border: 1px solid var(--gray-200); transition: box-shadow .25s, transform .25s; overflow: hidden; }
        .card:hover { box-shadow: var(--shadow-xl); transform: translateY(-4px); }

        /* ── Glass ── */
        .glass { background: rgba(255,255,255,0.12); backdrop-filter: blur(16px) saturate(180%); -webkit-backdrop-filter: blur(16px) saturate(180%); border: 1px solid rgba(255,255,255,0.2); }

        /* ── Toast ── */
        .toast { position: fixed; bottom: 24px; right: 24px; z-index: 9999; background: var(--gray-900); color: #fff; padding: 12px 20px; font-size: .875rem; font-weight: 600; box-shadow: var(--shadow-lg); transform: translateY(80px); opacity: 0; transition: all .3s ease; }
        .toast.show { transform: translateY(0); opacity: 1; }

        /* ── Animated wave ── */
        @keyframes waveMove { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }
        .wave-animate { animation: waveMove 12s linear infinite; }

        /* ── Image lazy fade ── */
        img.lazy { opacity: 0; transition: opacity .4s ease; }
        img.lazy.loaded { opacity: 1; }
    </style>
</head>
<body>
    @include('home.partials.loader')
    <div id="main-content" class="opacity-0 transition-opacity duration-500">
        @include('home.universal-modal')
        @include('home.modals.guest-limit')
        @include('home.partials.nav')
        @include('home.partials.universal-form')
        @include('partials.my-bookings-modal')
        @include('partials.booking-extension-generic-modal')

        @if(session('payment_returned') || request()->query('from_payment') == '1')
            <div id="payment-return-banner" class="fixed right-4 top-24 z-50 md:top-32">
                <button id="payment-return-btn" class="btn-primary px-5 py-2.5 text-sm font-semibold shadow-lg">
                    ✓ Payment received — Refresh
                </button>
            </div>
            <script>
                (function(){
                    var btn = document.getElementById('payment-return-btn');
                    if(!btn) return;
                    btn.addEventListener('click', function(){ try{ window.location.reload(); }catch(e){ window.location.href='/'; } });
                    setTimeout(function(){ var el=document.getElementById('payment-return-banner'); if(el) el.remove(); }, 8000);
                })();
            </script>
        @endif

        @yield('content')
        @include('home.partials.footer')
    </div>

    <script src="/js/home.js"></script>
    <script src="/js/chatbot.js"></script>
    <script src="/js/room-tab.js"></script>
    <script src="/js/room-cart.js"></script>
    <script src="/js/weather.js"></script>
    <script src="/js/alert.js"></script>
    <script src="/js/alert-v2.js"></script>
    <script src="/js/amenities.js"></script>
    <script src="/js/contact.js"></script>
    <script src="/js/index.js"></script>
    <script src="/js/booking-extension.js"></script>
    <script src="/js/refund.js"></script>
    <script src="/js/my-bookings.js"></script>
    <script src="/js/remove-modal.js"></script>
    <script src="/js/hero.js"></script>
    <script src="/js/nav.js"></script>
    <script>
        // Scroll-reveal
        document.addEventListener('DOMContentLoaded', () => {
            const obs = new IntersectionObserver((entries) => {
                entries.forEach(e => { if(e.isIntersecting){ e.target.classList.add('revealed'); obs.unobserve(e.target); } });
            }, { threshold: 0.1 });
            document.querySelectorAll('[data-reveal]').forEach(el => obs.observe(el));

            // Lazy images
            const imgObs = new IntersectionObserver((entries) => {
                entries.forEach(e => {
                    if(e.isIntersecting){
                        const img = e.target;
                        if(img.dataset.src){ img.src = img.dataset.src; img.classList.add('loaded'); imgObs.unobserve(img); }
                    }
                });
            });
            document.querySelectorAll('img.lazy').forEach(img => imgObs.observe(img));
        });
    </script>
</body>
</html>
