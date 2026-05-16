<?php
// Replace with your PayMongo Secret Key
$secretKey = "sk_test_VpBtNLRwG5esis6uWd5xmxGs";

// Collect form data
$amount = $_POST['amount'] * 100; // Convert to centavo


// Define the data payload for creating a Payment Link
$data = [
    "data" => [
        "attributes" => [
            "amount" => $amount,
            "currency" => "PHP",
            "description" => "Sample Description",
            "remarks" => "Sample Remarks",
            "checkout_url" => "https://pm.link/org-sBNv7gWdxikVStjWLa5zEfBt/test/Yxj6GJs"
        ]
    ]
];

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.paymongo.com/v1/links");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Basic " . base64_encode($secretKey . ":")
]);

// curl request
$result = curl_exec($ch);
curl_close($ch);

// response decode
$response = json_decode($result, true);

// checker for payment link
if (isset($response['data']['attributes']['checkout_url'])) {
    // Show an intermediate page with a safe "Proceed to Pay" button that opens
    // the PayMongo checkout in a new tab and a "Return to Home" button.
    $checkoutUrl = $response['data']['attributes']['checkout_url'];

    // Allow an optional return URL to be passed through (best-effort).
    // If provided via POST (e.g. return_to=/paymongo/success.php), append it to the
    // checkout URL as a query parameter so that if PayMongo preserves it on redirect
    // we can use it. This is best-effort — some gateways ignore extra query params.
    $returnTo = isset($_POST['return_to']) ? trim($_POST['return_to']) : '/';
    if ($returnTo) {
        $sep = (strpos($checkoutUrl, '?') === false) ? '?' : '&';
        $checkoutUrl .= $sep . 'return_to=' . urlencode($returnTo);
    }
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Proceed to Payment</title>
        <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        <style>
            body{font-family:'Raleway',ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial;display:flex;align-items:center;justify-content:center;height:100vh;background:#f7fafc}
            .card{background:#fff;padding:28px;border-radius:8px;box-shadow:0 6px 18px rgba(0,0,0,0.08);text-align:center;width:360px}
            .btn{display:inline-block;margin:8px;padding:10px 16px;border-radius:6px;border:none;cursor:pointer;text-decoration:none}
            .btn-pay{background:#059669;color:#fff}
            .btn-home{background:#e5e7eb;color:#111}
            .muted{font-size:13px;color:#6b7280;margin-top:10px}
        </style>
    </head>
    <body>
        <div class="card">
            <h2 style="margin-bottom:6px">Complete Your Payment</h2>
            <p class="muted">Amount: PHP <?php echo number_format($amount/100,2); ?></p>
            <div style="margin-top:16px">
                <a class="btn btn-pay" href="<?php echo htmlspecialchars($checkoutUrl); ?>" target="_blank" rel="noopener noreferrer">Proceed to Pay</a>
                <a class="btn btn-home" href="/">Return to Home</a>
            </div>
            <p class="muted">If the checkout did not open, <a href="<?php echo htmlspecialchars($checkoutUrl); ?>" target="_blank">click here</a> to open it.</p>
        </div>
        <script>
            // Attempt to open the checkout in a new tab automatically (best-effort).
            try {
                window.open(<?php echo json_encode($checkoutUrl); ?>, '_blank', 'noopener');
            } catch (e) {
                // ignore pop-up blocking; user can click the button
            }
        </script>
    </body>
    </html>
    <?php
    exit();
} else {
    // error message
    echo "Error creating payment link: " . print_r($response, true);
}
