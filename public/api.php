<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Menangani preflight request dari browser (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../db/database.php';

// Memastikan struktur database selalu tersedia sebelum request diproses
try {
    Database::migrate();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database migration failed: " . $e->getMessage()]);
    exit;
}

// Mengambil path endpoint yang diminta
$pathInfo = $_SERVER['PATH_INFO'] ?? '';

if (empty($pathInfo)) {
    $uri = $_SERVER['REQUEST_URI'];

    // Fallback untuk server yang tidak menyediakan PATH_INFO
    if (strpos($uri, 'api.php') !== false) {
        $parts = explode('api.php', $uri);
        $pathInfo = $parts[1] ?? '';
    }
}

$pathInfo = rtrim(explode('?', $pathInfo)[0], '/');

// Routing endpoint
switch ($pathInfo) {

    // GET /products
    case '/products':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $db = Database::getConnection();

                // Mengambil seluruh data produk
                $stmt = $db->query("SELECT * FROM products");
                $products = $stmt->fetchAll();

                echo json_encode($products);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => $e->getMessage()]);
            }
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Method Not Allowed"]);
        }
        break;

    // POST /orders
    case '/orders':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Membaca payload JSON dari request
            $input = json_decode(file_get_contents('php://input'), true);

            // Validasi payload utama
            if (!$input || empty($input['customer_name']) || empty($input['items'])) {
                http_response_code(400);
                echo json_encode([
                    "error" => "Invalid payload structure. Required: customer_name, items"
                ]);
                exit;
            }

            $customerName = $input['customer_name'];
            $items = $input['items'];

            try {
                $db = Database::getConnection();

                // Memulai transaksi agar seluruh proses atomik
                // Jika ada error di tengah, semua perubahan akan dibatalkan
                $db->exec('START TRANSACTION');

                $totalPrice = 0.0;
                $orderItemsToInsert = [];

                // Validasi seluruh item pesanan terlebih dahulu
                foreach ($items as $item) {

                    $productId = $item['product_id'] ?? null;
                    $quantity = $item['quantity'] ?? null;

                    if (!$productId || !$quantity || $quantity <= 0) {
                        throw new Exception(
                            "Invalid item: product_id and valid quantity required",
                            400
                        );
                    }

                    // Mengambil data produk terbaru dari database
                    // Hold produk di DB agar request concurrency mengantre dengan aman
                    $stmt = $db->prepare("SELECT * FROM products WHERE id = ? FOR UPDATE");
                    $stmt->execute([$productId]);
                    $product = $stmt->fetch();

                    if (!$product) {
                        throw new Exception("Product with ID $productId not found", 404);
                    }

                    // Memastikan stok masih mencukupi
                    if ($product['inventory'] < $quantity) {
                        throw new Exception(
                            "Stok habis: " .
                                $product['name'] .
                                ". Available: " .
                                $product['inventory'],
                            422
                        );
                    }

                    // Menghitung total harga pesanan
                    $itemPrice = $product['price'];
                    $totalPrice += $itemPrice * $quantity;

                    // Menyimpan hasil validasi untuk diproses setelahnya
                    $orderItemsToInsert[] = [
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'price' => $itemPrice,
                        'new_inventory' => $product['inventory'] - $quantity
                    ];
                }

                // Membuat data order utama
                $stmt = $db->prepare("
                    INSERT INTO orders (customer_name, total_price)
                    VALUES (?, ?)
                ");

                $stmt->execute([
                    $customerName,
                    $totalPrice
                ]);

                $orderId = $db->lastInsertId();

                // Menyimpan item pesanan sekaligus mengurangi stok produk
                $insertItemStmt = $db->prepare("
                    INSERT INTO order_items
                    (order_id, product_id, quantity, price)
                    VALUES (?, ?, ?, ?)
                ");

                $updateInventoryStmt = $db->prepare("
                    UPDATE products
                    SET inventory = ?
                    WHERE id = ?
                ");

                // Proses penyimpanan item dan update stok
                foreach ($orderItemsToInsert as $oItem) {
                    $insertItemStmt->execute([
                        $orderId,
                        $oItem['product_id'],
                        $oItem['quantity'],
                        $oItem['price']
                    ]);

                    $updateInventoryStmt->execute([
                        $oItem['new_inventory'],
                        $oItem['product_id']
                    ]);
                }

                // Seluruh proses berhasil
                $db->exec('COMMIT');

                http_response_code(201);

                echo json_encode([
                    "message" => "Order placed successfully",
                    "order_id" => $orderId,
                    "total_price" => $totalPrice
                ]);
            } catch (PDOException $e) {

                // Membatalkan seluruh perubahan jika terjadi error database
                if (isset($db)) {
                    try {
                        $db->exec('ROLLBACK');
                    } catch (Exception $ex) {
                    }
                }

                // Menangkap error khusus terkait antrean/lock database
                if (
                    $e->getCode() === 'HY000' && (
                        strpos($e->getMessage(), 'Lock wait timeout exceeded') !== false
                    )
                ) {
                    http_response_code(409);

                    echo json_encode([
                        "error" => "System busy handling transaction queue. Please try again."
                    ]);
                } else {
                    http_response_code(500);

                    echo json_encode([
                        "error" => "Database error: " . $e->getMessage()
                    ]);
                }
            } catch (Exception $e) {

                // Rollback jika validasi atau proses bisnis gagal
                if (isset($db)) {
                    try {
                        $db->exec('ROLLBACK');
                    } catch (Exception $ex) {
                    }
                }

                $code = $e->getCode();
                $httpCode = ($code >= 400 && $code <= 500) ? $code : 400;

                http_response_code($httpCode);

                echo json_encode([
                    "error" => $e->getMessage()
                ]);
            }
        } else {

            http_response_code(405);

            echo json_encode([
                "error" => "Method Not Allowed"
            ]);
        }

        break;

    // POST /reset
    case '/reset':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {

                // Mengembalikan database ke kondisi awal
                Database::migrate();
                Database::seed();

                echo json_encode([
                    "message" => "Database reset and seeded successfully"
                ]);
            } catch (Exception $e) {

                http_response_code(500);

                echo json_encode([
                    "error" => "Reset failed: " . $e->getMessage()
                ]);
            }
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Method Not Allowed"]);
        }
        break;

    // Endpoint tidak ditemukan
    default:
        http_response_code(404);
        echo json_encode([
            "error" => "Endpoint not found: " . $pathInfo
        ]);
        break;
}
