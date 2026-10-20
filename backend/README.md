# SummitGear API (Laravel 12)

RESTful API untuk rental alat pendakian. Dokumentasi lengkap ada di [README utama](../README.md).

## Menjalankan tanpa Docker (Laragon)

```bash
composer install
cp .env.example .env
php artisan key:generate
# buat database "summitgear" di MySQL Laragon, lalu:
php artisan migrate --seed
php artisan serve
```

## Testing

```bash
php artisan test
```

Test memakai SQLite in-memory (lihat `phpunit.xml`), jadi tidak butuh MySQL.

## Struktur penting

| Path | Isi |
|---|---|
| `routes/api.php` | Semua endpoint `/api/*` |
| `app/Http/Controllers/Api` | `AuthController`, `GearController`, `HealthController` |
| `app/Http/Requests` | Validasi input (gagal → 422) |
| `app/Http/Resources` | Format respons JSON |
| `app/Http/Middleware/ForceJsonResponse.php` | Memaksa respons JSON (401 tidak jadi redirect) |
| `bootstrap/app.php` | Registrasi route, middleware, penanganan exception JSON |
| `docker/Caddyfile` | Konfigurasi FrankenPHP (port 8000, access log) |
| `docker/entrypoint.sh` | Migrasi, seeding, cache, lalu menjalankan FrankenPHP |
| `postman/` | Postman Collection |
