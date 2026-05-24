# HRIS RFID (Laravel 10)

Sistem HRIS sederhana untuk absensi RFID (ESP32 + RC522) dan payroll, dibangun dengan Laravel 10 + MySQL + Bootstrap 5.

## Fitur Utama

- Multi role: `admin` dan `employee`
- Absensi RFID via REST API: ESP32 kirim UID ke endpoint Laravel
- Monitoring scan RFID (log)
- Cuti/Izin (pengajuan employee, approval admin)
- Approval lembur (admin) dan tampilan lembur di portal employee
- Payroll sederhana + slip gaji
- Laporan + export Excel/PDF (admin)
- Profil employee (edit telepon, alamat, foto; email readonly)

## Requirement

- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Node.js + npm (untuk Vite build)

## Setup Lokal

1. Install dependency:

```bash
composer install
npm install
```

2. Buat environment file:

```bash
cp .env.example .env
php artisan key:generate
```

3. Atur koneksi DB di `.env`:

- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

4. Migrasi + seeding:

```bash
php artisan migrate --seed
```

5. Storage link (untuk foto profile):

```bash
php artisan storage:link
```

6. Jalankan aplikasi:

```bash
npm run dev
php artisan serve
```

## Akun Demo (Seeder)

- Admin:
  - Email: `admin@hris.test`
  - Password: `password`
- Employee (contoh):
  - Email: `employee@hris.test`
  - Password: `password`

## Integrasi ESP32 (RFID)

Endpoint scan:

- `POST /api/rfid/scan`

Payload minimal:

```json
{
  "uid": "D1E2F3A4",
  "device_name": "ESP32-01",
  "token": "YOUR_DEVICE_TOKEN"
}
```

Token device (opsional):

- Set `RFID_DEVICE_TOKEN` di `.env`
- Jika diisi, request ke `/api/rfid/scan` harus mengirim `token` yang sama

## Catatan

- Project ini menggunakan 2 role (`admin`, `employee`). Jabatan employee dibedakan dengan `job_role` (`staff`, `mandor`) untuk kebutuhan statistik/tampilan.
