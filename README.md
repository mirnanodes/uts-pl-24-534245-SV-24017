# SummitGear — Rental Alat Pendakian

UTS Konstruksi dan Evolusi Perangkat Lunak (SVPL261501), Gasal 2026/2027
**Mirna — NIM 24/534245/SV/24017**

SummitGear adalah sistem manajemen katalog rental alat pendakian:

- **Backend:** RESTful API Laravel 12 + Sanctum, dijalankan dengan **FrankenPHP**
- **Frontend:** Vue 3 + Vite + Axios, disajikan oleh Nginx
- **Database:** MySQL 8.4

Seluruh stack berjalan dengan Docker Compose. CI/CD memakai GitHub Actions dan GitHub Container Registry.

[![CI/CD](https://github.com/mirnanodes/uts-pl-24-534245-SV-24017/actions/workflows/ci.yml/badge.svg)](https://github.com/mirnanodes/uts-pl-24-534245-SV-24017/actions/workflows/ci.yml)

---

## Struktur repository

```
.
├── .github/workflows/ci.yml   # test-backend, build-frontend, deploy (GHCR)
├── backend/                   # Laravel 12 API
│   ├── app/
│   │   ├── Enums/             # GearCategory, GearCondition
│   │   ├── Http/Controllers/Api/
│   │   ├── Http/Middleware/   # ForceJsonResponse
│   │   ├── Http/Requests/     # LoginRequest, GearRequest (validasi → 422)
│   │   ├── Http/Resources/    # GearResource, UserResource
│   │   └── Models/            # User, Gear
│   ├── database/              # migrations, factories, seeders
│   ├── docker/                # Caddyfile (FrankenPHP) + entrypoint.sh
│   ├── postman/               # Postman Collection
│   ├── tests/                 # Feature & Unit tests
│   └── Dockerfile
├── frontend/                  # Vue 3 SPA
│   ├── src/
│   │   ├── api/http.js        # instance Axios + interceptor token
│   │   ├── services/          # authService, gearService
│   │   ├── composables/       # useAuth, useFlash, useGearOptions
│   │   ├── router/            # route guard
│   │   ├── components/        # GearForm, PaginationBar, ...
│   │   └── views/             # Login, Daftar, Form
│   ├── nginx/                 # template konfigurasi Nginx (proxy /api)
│   └── Dockerfile             # multi-stage: Node → Nginx
├── docker-compose.yml
└── .env.example
```

## Menjalankan dengan Docker (satu perintah)

```bash
cp .env.example .env          # lalu isi APP_KEY, DB_PASSWORD, ADMIN_PASSWORD
docker compose up -d --build
docker compose ps
```

| Layanan | URL |
|---|---|
| Aplikasi Vue | http://localhost:8080 |
| API (langsung ke FrankenPHP) | http://localhost:8000/api |
| API (lewat proxy Nginx) | http://localhost:8080/api |

Login memakai `ADMIN_EMAIL` / `ADMIN_PASSWORD` dari `.env`.

Data MySQL disimpan di named volume `db_data`, jadi tetap ada setelah `docker compose down` lalu `up -d`. Perintah `down -v` akan menghapus data ini.

## Alur data

```
Browser (Vue + Axios)
   │  GET /api/gears   Authorization: Bearer <token>
   ▼
frontend (Nginx :80)  ── proxy_pass ──►  backend (FrankenPHP :8000, Laravel)
                                             │  Eloquent (DB_HOST=db)
                                             ▼
                                         db (MySQL :3306, volume db_data)
```

## Endpoint API

| Method | Endpoint | Auth | Keterangan |
|---|---|---|---|
| GET | `/api/health` | – | Status API, database, dan SAPI (`frankenphp`) |
| POST | `/api/login` | – | 200 + token · 422 bila validasi/kredensial salah |
| GET | `/api/me` | Bearer | Profil pengguna |
| POST | `/api/logout` | Bearer | Mencabut token yang sedang dipakai |
| GET | `/api/gear-options` | Bearer | Pilihan kategori & kondisi |
| GET | `/api/gears?search=&category=&page=&per_page=` | Bearer | Daftar (paginasi) |
| GET | `/api/gears/{id}` | Bearer | Detail · 404 bila tidak ada |
| POST | `/api/gears` | Bearer | **201** · 422 bila validasi gagal |
| PUT | `/api/gears/{id}` | Bearer | 200 · 422 |
| DELETE | `/api/gears/{id}` | Bearer | 200 |

Tanpa token, semua endpoint ber-Bearer menjawab **401** `{"message":"Unauthenticated."}`.

## Pengujian

```bash
cd backend
composer install
php artisan test
```

Postman: impor `backend/postman/SummitGear.postman_collection.json`, isi variabel `admin_password`, lalu jalankan dengan Collection Runner.

## CI/CD

| Event | test-backend | build-frontend | deploy |
|---|:---:|:---:|:---:|
| Push ke branch fitur / `dev` | ✅ | ✅ | ⏭️ dilewati |
| Pull Request ke `dev` / `main` | ✅ | ✅ | ⏭️ dilewati |
| Push ke `main` | ✅ | ✅ | ✅ |

Job `deploy` melakukan tiga hal:

1. Build image backend & frontend.
2. Push ke `ghcr.io` dengan tag **commit SHA**.
3. Menjalankan `docker compose` memakai image tersebut. Rahasia (`APP_KEY`, `DB_PASSWORD`, `ADMIN_PASSWORD`) diambil dari GitHub Secrets. Setelah itu dilakukan smoke test `/api/health` dan login.

## Teknologi

Laravel 12 · Sanctum 4 · PHP 8.3 · FrankenPHP 1.13 · MySQL 8.4 · Vue 3.5 · Vue Router · Axios · Vite · Nginx 1.30 · Docker Compose · GitHub Actions · GHCR
