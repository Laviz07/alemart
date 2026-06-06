-- =====================================
-- DATABASE ALEMART
-- =====================================

DROP DATABASE IF EXISTS alemart_db;
CREATE DATABASE alemart_db;
USE alemart_db;

-- =====================================
-- USERS
-- =====================================

CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','kasir') NOT NULL
);

-- =====================================
-- KATEGORI
-- =====================================

CREATE TABLE kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE
);

-- =====================================
-- SUPPLIER
-- =====================================

CREATE TABLE supplier (
    id_supplier INT AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(100) NOT NULL,
    alamat TEXT,
    no_telp VARCHAR(15)
);

-- =====================================
-- PRODUK
-- =====================================

CREATE TABLE produk (
    id_produk INT AUTO_INCREMENT PRIMARY KEY,

    id_kategori INT NOT NULL,

    nama_produk VARCHAR(100) NOT NULL,

    harga_beli DECIMAL(10,2) NOT NULL,
    harga_jual DECIMAL(10,2) NOT NULL,

    stok INT NOT NULL DEFAULT 0,

    satuan ENUM(
        'pcs',
        'bungkus',
        'botol',
        'kaleng',
        'kg',
        'gram',
        'liter',
        'ml',
        'pack',
        'sak',
        'butir'
    ) NOT NULL,

    foto_produk VARCHAR(255),

    FOREIGN KEY (id_kategori)
    REFERENCES kategori(id_kategori)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
);

-- =====================================
-- PEMBELIAN
-- =====================================

CREATE TABLE pembelian (
    id_pembelian INT AUTO_INCREMENT PRIMARY KEY,

    id_supplier INT NOT NULL,
    id_user INT NOT NULL,

    tanggal_pembelian DATETIME NOT NULL,

    total_pembelian DECIMAL(12,2) NOT NULL DEFAULT 0,

    FOREIGN KEY (id_supplier)
    REFERENCES supplier(id_supplier)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

    FOREIGN KEY (id_user)
    REFERENCES users(id_user)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
);

-- =====================================
-- DETAIL PEMBELIAN
-- =====================================

CREATE TABLE detail_pembelian (
    id_detail_pembelian INT AUTO_INCREMENT PRIMARY KEY,

    id_pembelian INT NOT NULL,
    id_produk INT NOT NULL,

    jumlah INT NOT NULL,

    harga_beli DECIMAL(10,2) NOT NULL,

    subtotal DECIMAL(12,2) NOT NULL,

    no_batch VARCHAR(50),

    expired DATE,

    FOREIGN KEY (id_pembelian)
    REFERENCES pembelian(id_pembelian)
    ON UPDATE CASCADE
    ON DELETE CASCADE,

    FOREIGN KEY (id_produk)
    REFERENCES produk(id_produk)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
);

-- =====================================
-- TRANSAKSI
-- =====================================

CREATE TABLE transaksi (
    id_transaksi INT AUTO_INCREMENT PRIMARY KEY,

    id_user INT NOT NULL,

    tanggal_transaksi DATETIME NOT NULL,

    total_harga DECIMAL(12,2) NOT NULL,

    bayar DECIMAL(12,2) NOT NULL,

    kembalian DECIMAL(12,2) NOT NULL,

    FOREIGN KEY (id_user)
    REFERENCES users(id_user)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
);

-- =====================================
-- DETAIL TRANSAKSI
-- =====================================

CREATE TABLE detail_transaksi (
    id_detail_transaksi INT AUTO_INCREMENT PRIMARY KEY,

    id_transaksi INT NOT NULL,
    id_produk INT NOT NULL,

    jumlah INT NOT NULL,

    harga_jual DECIMAL(10,2) NOT NULL,

    subtotal DECIMAL(12,2) NOT NULL,

    FOREIGN KEY (id_transaksi)
    REFERENCES transaksi(id_transaksi)
    ON UPDATE CASCADE
    ON DELETE CASCADE,

    FOREIGN KEY (id_produk)
    REFERENCES produk(id_produk)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
);

-- =====================================
-- DUMMY DATA USERS
-- =====================================

INSERT INTO users
(nama, username, password, role)
VALUES
(
'Administrator',
'admin',
'$2a$12$IQ6dBxjqt/YkBiA1RuRoGenFvg9FE3z2nR5P783esIUqXTPVMtWv2', 
-- admin123
'admin'
),

(
'Kasir 1',
'kasir1',
'$2a$12$P4k12cmdJdIQePeHo4d/6eEwxTjyJqOP8xeRgXGxvNfwx10H9Gidy',
-- kasir123
'kasir'
),

(
'Kasir 2',
'kasir2',
'$2a$12$lq5ZT1z5GPxyDjlNjQsgXeAZQsU50YEeXMPIjMC9Se3jjTfFOYQLW', 
-- kasir234
'kasir'
);

-- =====================================
-- DUMMY DATA KATEGORI
-- =====================================

INSERT INTO kategori (nama_kategori)
VALUES
('Makanan'),
('Minuman'),
('Sembako'),
('Snack'),
('Perawatan');

-- =====================================
-- DUMMY DATA SUPPLIER
-- =====================================

INSERT INTO supplier
(nama_supplier, alamat, no_telp)
VALUES
(
'PT Indofood',
'Jakarta',
'081111111111'
),

(
'PT Mayora',
'Tangerang',
'082222222222'
),

(
'PT Wings Food',
'Surabaya',
'083333333333'
),

(
'PT Aqua Golden',
'Bekasi',
'084444444444'
),

(
'CV Sumber Jaya',
'Karawang',
'085555555555'
);

-- =====================================
-- DUMMY DATA PRODUK
-- =====================================

INSERT INTO produk
(
id_kategori,
nama_produk,
harga_beli,
harga_jual,
stok,
satuan,
foto_produk
)
VALUES

(
1,
'Indomie Goreng',
2500,
3500,
100,
'bungkus',
'indomie.jpg'
),

(
1,
'Mie Sedaap Soto',
2400,
3400,
80,
'bungkus',
'miesedaap.jpg'
),

(
2,
'Aqua 600ml',
2500,
4000,
120,
'botol',
'aqua.jpg'
),

(
2,
'Teh Botol Sosro',
3500,
5000,
75,
'botol',
'tehbotol.jpg'
),

(
4,
'Chitato Sapi Panggang',
7000,
10000,
50,
'bungkus',
'chitato.jpg'
);

-- =====================================
-- CONTOH DATA PEMBELIAN
-- =====================================

INSERT INTO pembelian
(
id_supplier,
id_user,
tanggal_pembelian,
total_pembelian
)
VALUES
(
1,
1,
NOW(),
548000
);

INSERT INTO detail_pembelian
(
id_pembelian,
id_produk,
jumlah,
harga_beli,
subtotal,
no_batch,
expired
)
VALUES
(
1,
1,
100,
2500,
250000,
'IND-001',
'2027-01-01'
),

(
1,
2,
80,
2400,
192000,
'MSD-001',
'2027-02-01'
),

(
1,
3,
40,
2650,
106000,
'AQ-001',
'2027-06-01'
);

-- =====================================
-- CONTOH DATA TRANSAKSI
-- =====================================

INSERT INTO transaksi
(
id_user,
tanggal_transaksi,
total_harga,
bayar,
kembalian
)
VALUES
(
2,
NOW(),
11500,
15000,
3500
);

INSERT INTO detail_transaksi
(
id_transaksi,
id_produk,
jumlah,
harga_jual,
subtotal
)
VALUES
(
1,
1,
1,
3500,
3500
),

(
1,
3,
2,
4000,
8000
);