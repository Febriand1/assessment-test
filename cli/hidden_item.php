<?php

// Definisikan grid
$grid = [
    ['#', '#', '#', '#', '#', '#', '#', '#'], // Baris 0
    ['#', '.', '.', '.', '.', '.', '.', '#'], // Baris 1
    ['#', '.', '#', '#', '#', '.', '.', '#'], // Baris 2
    ['#', '.', '.', '.', '#', '.', '#', '#'], // Baris 3
    ['#', 'X', '#', '.', '.', '.', '.', '#'], // Baris 4 (X ada di kolom 1)
    ['#', '#', '#', '#', '#', '#', '#', '#'], // Baris 5
];

// Tentukan posisi awal X (Baris 4, Kolom 1)
$startX = 1;
$startY = 4;

echo "GRID AWAL:\n";
foreach ($grid as $y => $row) {
    foreach ($row as $x => $char) {
        echo $char;
    }
    echo "\n";
}

// Mengambil input dari user lewat CLI menggunakan readline()
$inputA = (int) readline("Masukkan langkah ke Utara/Atas (A): ");
$inputB = (int) readline("Masukkan langkah ke Timur/Kanan (B): ");
$inputC = (int) readline("Masukkan langkah ke Selatan/Bawah (C): ");

// Masukkan input ke array pergerakan (Vektor)
$movements = [
    ['direction' => 'UTARA',   'steps' => $inputA, 'dx' => 0, 'dy' => -1],
    ['direction' => 'TIMUR',   'steps' => $inputB, 'dx' => 1, 'dy' => 0],
    ['direction' => 'SELATAN', 'steps' => $inputC, 'dx' => 0, 'dy' => 1]
];

$currentX = $startX;
$currentY = $startY;
$isValidPath = true;

// Jalankan simulasi step by step
foreach ($movements as $move) {
    for ($i = 0; $i < $move['steps']; $i++) {
        $currentX += $move['dx'];
        $currentY += $move['dy'];

        // Cek jika koordinat keluar dari batas grid atau menabrak '#'
        if (!isset($grid[$currentY][$currentX]) || $grid[$currentY][$currentX] === '#') {
            $isValidPath = false;
            break 2; // Keluar dari kedua looping (for dan foreach)
        }
    }
}

// Tampilkan Hasil Akhir
if (!$isValidPath) {
    echo "\n[ERROR] Jalur tidak valid! Karakter menabrak rintangan atau keluar batas map.\n";
    exit(1);
}

echo "\nProbable Item Locations:\n";
echo "- (X: {$currentX}, Y: {$currentY})\n";

// Tampilkan peta visual dengan simbol '$'
echo "\nVisual Grid Map:\n";
foreach ($grid as $y => $row) {
    foreach ($row as $x => $char) {
        // Jika ini adalah koordinat akhir, ganti karakternya jadi '$'
        if ($x === $currentX && $y === $currentY) {
            echo '$';
        } else {
            echo $char;
        }
    }
    echo "\n";
}
