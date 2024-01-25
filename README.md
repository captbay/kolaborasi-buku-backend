# KOLABORASI PENERBITAN BUKU - BACKEND LARAVEL

Project Tugas Akhir I Putu Agestya Pramana / 200710994 dengan membuat aplikasi pembuatan buku dengan konsep berkolaborasi lalu buku yang sudah jadi diterbitkan dan dijual di platform ini juga. User juga bisa mengajukan penerbitan untuk bukunya pribadi.

## Features API (DONE)

-   Login
-   Register
-   Logout

(kalo mau custom emails untuk verif dan reset password cek dokumentasi laravel)
https://laravel.com/docs/10.x/passwords ||
https://laravel.com/docs/10.x/verification

-   Verifikasi Email Register
-   Kirim Ulang Verifikasi Email
-   Email Lupa Password
-   Reset Password
-   Ganti Password

## Installation

Menyiapkan instalasi composer:

```sh
composer install
```

Salin file `.env.example` menjadi `.env` pada root folder dan lakukan konfiguasi sesuai local dev pada local laptop.

Note:
Untuk store file menggunakan AWS S3 (jika tidak memiliki akses bisa bertanya kepada mentor)

Membersihkan config dan cache setelah perubahan .env:

```sh
php artisan config:cache
```

Generate key app laravel:

```sh
php artisan key:generate
```

Melakukan migrate database pertama kali:

```sh
php artisan migrate
```

Melakukan migrate database pertama kali dengan seeder:

```sh
php artisan migrate --seed
```

Menjalankan Laravel:

```sh
php artisan serve
```
