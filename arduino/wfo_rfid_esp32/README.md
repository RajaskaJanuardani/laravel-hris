# Integrasi ESP32 RFID WFO.id

## Library Arduino IDE

Install library berikut lewat Library Manager:

- MFRC522
- LiquidCrystal I2C
- ArduinoJson

Board yang dipakai: ESP32.

## Koneksi Pin

RC522:

- SDA/SS ke GPIO 5
- SCK ke GPIO 18
- MOSI ke GPIO 23
- MISO ke GPIO 19
- RST ke GPIO 27
- 3.3V ke 3.3V
- GND ke GND

LCD I2C:

- SDA ke GPIO 21
- SCL ke GPIO 22
- VCC ke 5V atau 3.3V sesuai modul
- GND ke GND

## URL Laravel

ESP32 tidak bisa memakai `127.0.0.1`, karena itu menunjuk ke ESP32 sendiri.
Gunakan IP laptop/server Laravel, contoh:

```cpp
const char* LARAVEL_SCAN_URL = "http://192.168.1.10:8000/api/rfid/scan";
```

Jalankan Laravel agar bisa diakses perangkat lain:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## Alur LCD

- Scan sebelum jam 08:00: `Absen belum` / `di mulai`
- Scan masuk setelah jam 17:00 tanpa absen masuk: `Absen belum` / `di mulai`
- Scan berhasil masuk: LCD menampilkan `Masuk berhasil` dan nama karyawan
- Scan berhasil pulang: LCD menampilkan `Pulang berhasil` dan nama karyawan
- Kartu tidak terdaftar: LCD menampilkan `Kartu tidak` / `terdaftar`
