<?php
// Styled landing page shown after PayMongo/GCash payment.
// Provides a clear confirmation, large CTA, and an automatic redirect back to merchant.
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Payment Received</title>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root{--green:#34d399;--bg:#ffffff;--muted:#6b7280}
        html,body{height:100%;margin:0;font-family:'Raleway',ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial;background:#f3f4f6}
        .center{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
        .card{background:var(--bg);padding:32px;border-radius:10px;box-shadow:0 10px 30px rgba(2,6,23,0.08);text-align:center;max-width:640px;width:100%}
        .check{width:96px;height:96px;border-radius:9999px;background:linear-gradient(180deg,var(--green),#10b981);display:inline-flex;align-items:center;justify-content:center;margin:0 auto 18px}
        .check svg{width:48px;height:48px;filter:drop-shadow(0 6px 12px rgba(16,185,129,0.18))}
        h1{margin:0 0 8px;font-size:20px;color:#111827}
        p.lead{margin:0 0 18px;color:var(--muted)}
        .cta{display:block;background:var(--green);color:#fff;padding:14px 18px;border-radius:8px;text-decoration:none;font-weight:600;margin:12px 0}
        .countdown{font-size:13px;color:var(--muted);margin-top:8px}
        .note{font-size:13px;color:#9ca3af;margin-top:8px}
    </style>
</head>
<body>
    <div class="center">
        <div class="card" role="status" aria-live="polite">
            <div class="check" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M20 7L9 18l-5-5" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1>Payment received!</h1>
            <p class="lead">An automated receipt will be sent to your email.</p>

            <a id="redirectBtn" class="cta" href="#">Redirect back to merchant</a>

            <div class="countdown">You will be redirected in <span id="count">4</span> second(s)!</div>
            <div class="note">If the button doesn't work, please copy and paste your browser's current URL into the merchant's return form.</div>
        </div>
    </div>

    <script>
        (function(){
            var seconds = 4;
            var el = document.getElementById('count');
            var btn = document.getElementById('redirectBtn');

            // Read optional return_to param from this page's URL (fallback to '/')
            var params = new URLSearchParams(window.location.search);
            var target = params.get('return_to') || '/';

            function redirectToMerchant() {
                try {
                    if (window.opener && !window.opener.closed) {
                        // try to update the merchant (opener) and close this window
                        window.opener.location.href = target;
                        window.close();
                        return;
                    }
                } catch (e) {
                    // ignore cross-origin access errors and fall back
                }
                // fallback: navigate current window to target
                window.location.href = target;
            }

            var interval = setInterval(function(){
                seconds -= 1;
                if(el) el.textContent = seconds;
                if(seconds <= 0){
                    clearInterval(interval);
                    redirectToMerchant();
                }
            },1000);

            btn.addEventListener('click', function(e){
                e.preventDefault();
                redirectToMerchant();
            });
        })();
    </script>
</body>
</html>