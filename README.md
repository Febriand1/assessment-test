# Assessment Test

1. **Task 1: Online Store (Flash Sale Concurrency Handling)**
2. **Task 2: Hidden Item (CLI Coordinate Prediction Game)**

## Struktur Folder Project

```bash
├── cli/
│   └── hidden_item.php      # Aplikasi CLI untuk Task 2 (Hidden Item Game)
├── db/
│   ├── database.php         # Class Database, Koneksi PDO MySQL, dan Fungsi Core
│   └── migrate.php          # Script CLI untuk inisialisasi / migration awal MySQL
├── public/
│   └── api.php              # API Endpoint utama untuk Task 1 (Online Store)
├── tests/
│   └── concurrency_test.php # Automated Functional Test untuk simulasi Flash Sale
├── db.sql                   # Hasil export / dump database MySQL
└── README.md                # Dokumentasi Project
```

## Task 1: Online Store (Flash Sale Concurrency)

- Run Migration Database

```bash
php db/migrate.php
```

- Run Server

```bash
php -S localhost:8080 -t public
```

- Run Functional Concurrency Test

```bash
php tests/concurrency_test.php
```

### Hasil

```bash
[SUCCESS] Request #1: Order #1 berhasil dibuat untuk Pembeli Ke-1.
[SUCCESS] Request #2: Order #2 berhasil dibuat untuk Pembeli Ke-2.
[SUCCESS] Request #3: Order #3 berhasil dibuat untuk Pembeli Ke-3.
[SUCCESS] Request #4: Order #4 berhasil dibuat untuk Pembeli Ke-4.
[SUCCESS] Request #5: Order #5 berhasil dibuat untuk Pembeli Ke-5.
[SUCCESS] Request #6: Order #6 berhasil dibuat untuk Pembeli Ke-6.
[SUCCESS] Request #7: Order #7 berhasil dibuat untuk Pembeli Ke-7.
[SUCCESS] Request #8: Order #8 berhasil dibuat untuk Pembeli Ke-8.
[SUCCESS] Request #9: Order #9 berhasil dibuat untuk Pembeli Ke-9.
[SUCCESS] Request #10: Order #10 berhasil dibuat untuk Pembeli Ke-10.
[FAILED]  Request #11: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #12: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #13: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #14: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #15: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #16: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #17: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #18: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #19: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #20: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #21: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #22: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #23: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #24: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #25: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #26: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #27: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #28: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #29: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #30: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #31: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #32: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #33: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #34: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #35: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #36: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #37: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #38: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #39: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #40: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #41: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #42: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #43: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #44: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #45: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #46: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #47: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #48: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #49: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0
[FAILED]  Request #50: HTTP 422 - Stok habis: Super Gaming Laptop RTX 5090. Available: 0

Total Concurrent Requests : 50
Successful Orders (201)   : 10
Conflict/OOS Orders (422) : 40
Network Drop/Timeout (0)  : 0
```

## Task 2: Hidden Item Game (CLI)

- Run Game Hidden Item CLI

```bash
php cli/hidden_item.php
```

### Hasil

```bash
GRID AWAL:
########
#......#
#.###..#
#...#.##
#X#....#
########
Masukkan langkah ke Utara/Atas (A): 1
Masukkan langkah ke Timur/Kanan (B): 2
Masukkan langkah ke Selatan/Bawah (C): 1

Probable Item Locations:
- (X: 3, Y: 4)

Visual Grid Map:
########
#......#
#.###..#
#...#.##
#X#$...#
########
```
