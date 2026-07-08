<?php

require_once __DIR__ . '/database.php';

try {
    echo "1. Migration... ";
    Database::migrate();
    echo "SUKSES!\n";

    echo "2. Seeding... ";
    Database::seed();
    echo "SUKSES!\n";

    echo "\n[INFO] Database ready\n";
} catch (Exception $e) {
    echo "\n[ERROR] Proses gagal: " . $e->getMessage() . "\n";
    exit(1);
}
