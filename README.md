## START PROJECT AND/OR SETUP

JIKALAU INGIN MENGGUNAKAN PROYEK INI DARI DRIVE INI ATAU MENARIK DARI GITHUB DAN TANPA HARUS MANUAL IMPORT DATABASE

- Jalankan ini satu-persatu
```
composer install
composer dump-autoload
composer update
```

- Lalu pindah direktori ke folder projek
```
cd Cdc-Project-Iqbal
```

- Membuat/Create database 'cdc_information_system' yang masih kosong di MySQL dengan cara
-- Nyalakan Apache dan MySQL di XAMPP
-- Buka http://localhost/phpmyadmin
-- Tekan 'New'
-- Ketik '*cdc_information_system*' di kolom 'Database Name'
-- Tekan 'Create'

- Duplicate file .env.example, lalu yang di duplicate tersebut ubah ekstensi menjadi '.env' saja (hapus '.example' nya)

- Lalu jalankan 
```
php artisan migrate:fresh 
// atau
php artisan migrate --seed
```

- Jikalau ada error Mengenai Package atau 'require' lakukan ini:
```
composer require laravel/sanctum
composer require rap2hpoutre/fast-excel
composer require barryvdh/laravel-dompdf
composer require spatie/image-optimizer
composer require phpunit/phpunit --dev
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```
```
// Lanjut lagi ke bagian ini jikalau tidak ada error package:

php artisan key:generate
php artisan storage:link
php artisan config:cache
php artisan view:cache
php artisan route:cache
php artisan config:clear
php artisan view:clear
php artisan cache:clear
php artisan route:clear
```

- Silahkan di coba run:
```
php artisan serve
```

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
