<?php

class Database
{
    private static ?PDO $pdo = null;

    // Konfigurasi Database MySQL
    private static string $host = '127.0.0.1';
    private static string $dbName = 'db_test';
    private static string $username = 'root';
    private static string $password = '';
    private static string $charset = 'utf8mb4';

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            try {
                // Koneksi awal ke MySQL tanpa dbname
                $dsnTanpaDb = "mysql:host=" . self::$host . ";charset=" . self::$charset;
                $pdoAwal = new PDO($dsnTanpaDb, self::$username, self::$password);

                // Buat database secara otomatis jika belum ada
                $pdoAwal->exec("CREATE DATABASE IF NOT EXISTS `" . self::$dbName . "`");

                // Koneksi ke database yang sudah dibuat
                $dsnResmi = "mysql:host=" . self::$host . ";dbname=" . self::$dbName . ";charset=" . self::$charset;
                self::$pdo = new PDO($dsnResmi, self::$username, self::$password);

                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

                // Tambahkan pengaturan untuk mendukung transaksi dan locking
                self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            } catch (PDOException $e) {
                die("Koneksi Database Gagal: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    public static function migrate(): void
    {
        $db = self::getConnection();

        // Create Products Table
        // Gunakan InnoDB agar mendukung transaksi dan locking
        $db->exec("CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            inventory INT NOT NULL,
            CONSTRAINT chk_inventory CHECK(inventory >= 0)
        ) ENGINE=InnoDB;");

        // Create Orders Table
        $db->exec("CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_name VARCHAR(255) NOT NULL,
            total_price DECIMAL(10, 2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;");

        // Create Order Items Table
        $db->exec("CREATE TABLE IF NOT EXISTS order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id)
        ) ENGINE=InnoDB;");
    }

    public static function seed(): void
    {
        $db = self::getConnection();

        // Clear table dan reset auto_increment pakai TRUNCATE
        // Matikan sementara foreign key check agar tidak error saat truncate
        $db->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $db->exec("TRUNCATE TABLE order_items");
        $db->exec("TRUNCATE TABLE orders");
        $db->exec("TRUNCATE TABLE products");
        $db->exec("SET FOREIGN_KEY_CHECKS = 1;");

        // Seed initial product for Flash Sale
        $stmt = $db->prepare("INSERT INTO products (name, price, inventory) VALUES (?, ?, ?)");
        $stmt->execute(['Super Gaming Laptop RTX 5090', 999.00, 10]);
        $stmt->execute(['Wireless Gaming Mouse', 49.99, 100]);
        $stmt->execute(['Mechanical Keyboard', 89.99, 50]);
    }
}
