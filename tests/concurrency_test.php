<?php

// Reset database lewat API agar stok kembali jadi 10
echo "Resetting database state... ";
$ch = curl_init("http://localhost:8080/api.php/reset");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_exec($ch);
curl_close($ch);
echo "Done.\n\n";

// Siapkan 50 request sekaligus
$totalRequests = 50;
$mh = curl_multi_init();
$curlArray = [];

for ($i = 1; $i <= $totalRequests; $i++) {
    $curlArray[$i] = curl_init("http://localhost:8080/api.php/orders");

    // Payload order untuk produk ID 1 (Stoknya cuma ada 10)
    $payload = json_encode([
        "customer_name" => "Pembeli Ke-" . $i,
        "items" => [
            ["product_id" => 1, "quantity" => 1]
        ]
    ]);

    curl_setopt($curlArray[$i], CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curlArray[$i], CURLOPT_POST, true);
    curl_setopt($curlArray[$i], CURLOPT_POSTFIELDS, $payload);
    curl_setopt($curlArray[$i], CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    // Masukkan ke dalam handler multi-curl
    curl_multi_add_handle($mh, $curlArray[$i]);
}

// Eksekusi seluruh request secara bersamaan
$running = null;
do {
    curl_multi_exec($mh, $running);
} while ($running > 0);

// Ambil hasil response dari setiap request
$successCount = 0;
$failCount = 0;
$httpZeroCount = 0;

for ($i = 1; $i <= $totalRequests; $i++) {
    $httpCode = curl_getinfo($curlArray[$i], CURLINFO_HTTP_CODE);
    $responseBody = curl_multi_getcontent($curlArray[$i]);
    $responseData = json_decode($responseBody, true);

    // Format log per baris request
    if ($httpCode === 201) {
        $successCount++;
        $orderId = $responseData['order_id'] ?? '?';
        echo "[SUCCESS] Request #$i: Order #$orderId berhasil dibuat untuk Pembeli Ke-$i.\n";
    } elseif ($httpCode === 422 || $httpCode === 409) {
        $failCount++;
        $errorMessage = $responseData['error'] ?? 'Stok Habis / Sistem Sibuk';
        echo "[FAILED]  Request #$i: HTTP $httpCode - $errorMessage\n";
    } elseif ($httpCode === 0) {
        $httpZeroCount++;
        echo "[ERROR]   Request #$i: HTTP 0 - Koneksi terputus ke server.\n";
    } else {
        echo "[UNKNOWN] Request #$i: HTTP $httpCode - $responseBody\n";
    }

    curl_multi_remove_handle($mh, $curlArray[$i]);
    curl_close($curlArray[$i]);
}

curl_multi_close($mh);

echo "\n";
echo "Total Concurrent Requests : $totalRequests\n";
echo "Successful Orders (201)   : $successCount\n";
echo "Conflict/OOS Orders (422) : $failCount\n";
echo "Network Drop/Timeout (0)  : $httpZeroCount\n";
