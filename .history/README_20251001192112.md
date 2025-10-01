# Obayan.Core Server

> Backend server & API untuk **Obayan** — platform pesantren modern terintegrasi (SIAKAD, absensi, keuangan, web, modul operasional).

> Situs utama: [https://obayan.id](https://obayan.id) ([obayan.id](https://obayan.id/))

---

## Fitur Utama

- Manajemen pengguna & autentikasi (roles, permission, audit log)
- Santri / siswa / kelas / struktur akademik
- Absensi real-time & sesi (terintegrasi dengan perangkat RFID / fingerprint)
- Modul izin / kunjungan / pelanggaran
- Modul keuangan: tagihan (SPP, syahriyah), item tagihan, status pembayaran
- CMS Web: berita, halaman statis, media upload
- Modul operasional: agenda, pengumuman
- API untuk aplikasi mobile & frontend
- Logging, audit, dan keamanan (RBAC, logging, JSONB metadata)

---

## Prasyarat

- PHP ≥ 8.1
- Composer
- Database: PostgreSQL (direkomendasikan) / MySQL (dengan penyesuaian)
- Node.js & npm (untuk bagian frontend / asset build jika ada)
- Ekstensi PHP: `pdo`, `pgsql` (atau `mysqli`), `mbstring`, `openssl`, `json`

---

## Struktur Direktori (contoh)

```

.

├── app/

├── bootstrap/

├── config/

├── database/

│   ├── migrations/

│   ├── seeders/

│   └── factories/

├── public/

├── resources/

│   ├── views/

│   └── assets/

├── routes/

│   ├── api.php

│   └── web.php

├── src/ (opsional jika modul core / services terpisah)

├── storage/

└── tests/

```

---

## Instalasi

1. Clone repo

   ```bash

   git clone https://github.com/your-org/obayan.core.git

   cd obayan.core

   ```
2. Install dependencies

   ```bash

   composer install

   ```
3. Setup file lingkungan

   ```bash

   cp .env.example .env

   ```

   Edit `.env` sesuai kebutuhan:

   ```env

   APP_NAME=ObayanCore

   APP_ENV=local

   APP_KEY=base64:...

   APP_DEBUG=true

   APP_URL=http://localhost:8000


   DB_CONNECTION=pgsql

   DB_HOST=127.0.0.1

   DB_PORT=5432

   DB_DATABASE=obayan_core

   DB_USERNAME=...

   DB_PASSWORD=...


   # Optional: konfigurasi queue, redis, mail, etc

   QUEUE_CONNECTION=database

   ```
4. Generate key aplikasi

   ```bash

   php artisan key:generate

   ```
5. Jalankan migrasi & seeder (opsional)

   ```bash

   php artisan migrate

   php artisan db:seed

   ```
6. (Opsional) Swagger / dokumentasi API

   Jika kamu menggunakan package dokumentasi, generate docs-nya:

   ```bash

   php artisan l5-swagger:generate

   ```
7. Jalankan server

   ```bash

   php artisan serve

   ```

---

## Konfigurasi Queue / Background Jobs

Proyek ini mendukung queue (jobs, email, notifikasi). Misalnya:

```bash

phpartisanqueue:work--tries=3

```

Pastikan queue driver (database, redis, etc) sudah diset di `.env`:

```env

QUEUE_CONNECTION=database

```

---

## API & Autentikasi

- Semua API berada pada prefix `/api/v1`
- Gunakan token / JWT / Sanctum (atau mekanisme auth yang kamu pilih)
- Response format: JSON standar dengan struktur:

  ```json

  {

    "status": "success"|"error",

    "data": {...},

    "message": "..."

  }

  ```
- Endpoints utama (contoh):

  | Domain | Endpoint | Fungsi |

  |---|---|---|

  | `/api/v1/auth/login` | POST | login user |

  | `/api/v1/users` | GET / POST / PUT / DELETE | manajemen user |

  | `/api/v1/santri` | CRUD | data santri |

  | `/api/v1/attendance` | GET / POST | list & input absensi |

  | `/api/v1/izin` | CRUD | izin keluar / sakit |

  | `/api/v1/news` | CRUD / List | berita & halaman web |

  | `/api/v1/bills` | CRUD / list | tagihan santri |

Pastikan kamu buat dokumentasi (Swagger / Postman) agar frontend / mobile bisa integrasi lancar.

---

## Testing

Gunakan PHPUnit / Pest untuk testing unit / feature:

```bash

phpartisantest

```

---

## Deployment / Produksi

- Gunakan environment `.env.production` dan set `APP_ENV=production`
- Aktifkan caching config / route / view:

  ```bash

  php artisan config:cache

  php artisan route:cache

  php artisan view:cache

  ```
- Setup supervisor / systemd untuk queue / schedule (cron).

  Contoh supervisor config (Ubuntu):

  ```

  [program:obayan-queue]

  process_name=%(program_name)s_%(process_num)02d

  command=php /path/to/artisan queue:work --sleep=3 --tries=3

  numprocs=1

  autostart=true

  autorestart=true

  stderr_logfile=/var/log/obayan/queue.err.log

  stdout_logfile=/var/log/obayan/queue.out.log

  ```
- Setup cron untuk scheduler:

  ```cron

  * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1

  ```
- Pastikan backup database rutin & konfigurasi SSL / HTTPS.

---

## Kontribusi

Kami menyambut kontribusi dari kamu! Berikut panduan singkat:

1. Fork repository
2. Buat branch fitur: `feature/nama-fitur`
3. Tambahkan test (unit / feature)
4. Pastikan `composer fix` / `php-cs-fixer` sesuai style (jika ada)
5. Pull request dengan deskripsi fitur / bug fix

---

## Catatan / Roadmap ke Depan

- Integrasi ToriID (RFID / Fingerprint) secara real-time
- Modul CBT / ujian / penjadwalan
- Modul inventory / aset
- Integrasi dengan kanal pembayaran (QRIS, VA, midtrans, dsb)
- Dashboard & laporan visual
- Multi-tenant / multi-campus support
- API eksternal & Webhook

---

## Referensi & Lisensi

- Obayan (platform resmi): [https://obayan.id](https://obayan.id) ([obayan.id](https://obayan.id/))
- Lisensi: MIT / (atau jenis lisensi yang kamu pilih) — sertakan file `LICENSE`
- Dokumen API (Swagger / Postman) → (tambah link atau path di proyek kamu)
