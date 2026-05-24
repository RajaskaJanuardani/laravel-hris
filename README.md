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
#include <WiFi.h>
#include <HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <ArduinoJson.h>

#define SS_PIN 5
#define RST_PIN 27

const char* WIFI_SSID = "ganti";
const char* WIFI_PASSWORD = "ganti_password_wifi";

const char* SERVER_URL = "http://ip_kamu/api/rfid/scan";
const char* DEVICE_NAME = "ESP32-RC522-01";
const char* DEVICE_TOKEN = ""; // isi kalau nanti RFID_DEVICE_TOKEN dipakai di Laravel

MFRC522 rfid(SS_PIN, RST_PIN);
LiquidCrystal_I2C lcd(0x27, 16, 2);

String lastUid = "";
unsigned long lastScanAt = 0;
const unsigned long scanCooldown = 3000;

void setup() {
  Serial.begin(115200);

  Wire.begin(21, 22);
  lcd.init();
  lcd.backlight();

  SPI.begin(18, 19, 23, SS_PIN);
  rfid.PCD_Init();

  lcd.setCursor(0, 0);
  lcd.print("HRIS RFID");
  lcd.setCursor(0, 1);
  lcd.print("Connecting...");

  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("WiFi Connected");
  lcd.setCursor(0, 1);
  lcd.print(WiFi.localIP());

  delay(1500);
  showReady();
}

void loop() {
  if (!rfid.PICC_IsNewCardPresent()) return;
  if (!rfid.PICC_ReadCardSerial()) return;

  String uid = getUid();

  if (uid == lastUid && millis() - lastScanAt < scanCooldown) {
    rfid.PICC_HaltA();
    rfid.PCD_StopCrypto1();
    return;
  }

  lastUid = uid;
  lastScanAt = millis();

  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("UID:");
  lcd.setCursor(0, 1);
  lcd.print(uid);

  Serial.println("UID: " + uid);

  sendToLaravel(uid);

  rfid.PICC_HaltA();
  rfid.PCD_StopCrypto1();

  delay(1500);
  showReady();
}

String getUid() {
  String uid = "";

  for (byte i = 0; i < rfid.uid.size; i++) {
    if (rfid.uid.uidByte[i] < 0x10) uid += "0";
    uid += String(rfid.uid.uidByte[i], HEX);
  }

  uid.toUpperCase();
  return uid;
}

void sendToLaravel(String uid) {
  if (WiFi.status() != WL_CONNECTED) {
    lcd.clear();
    lcd.print("WiFi Error");
    return;
  }

  HTTPClient http;
  http.begin(SERVER_URL);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  String payload = "uid=" + uid;
  payload += "&device_name=" + String(DEVICE_NAME);
  payload += "&source=esp32";

  if (String(DEVICE_TOKEN).length() > 0) {
    payload += "&token=" + String(DEVICE_TOKEN);
  }

  int httpCode = http.POST(payload);
  String response = http.getString();

  Serial.println("HTTP: " + String(httpCode));
  Serial.println(response);

  lcd.clear();

  if (httpCode == 200) {
    StaticJsonDocument<256> doc;
    DeserializationError error = deserializeJson(doc, response);

    if (!error) {
      bool ok = doc["ok"];
      const char* message = doc["message"] | "OK";
      const char* employee = doc["employee"] | "";

      lcd.setCursor(0, 0);
      lcd.print(ok ? "Berhasil" : "Gagal");

      lcd.setCursor(0, 1);
      if (strlen(employee) > 0) {
        lcd.print(employee);
      } else {
        lcd.print(message);
      }
    } else {
      lcd.print("Response Error");
    }
  } else {
    lcd.setCursor(0, 0);
    lcd.print("Server Error");
    lcd.setCursor(0, 1);
    lcd.print("HTTP ");
    lcd.print(httpCode);
  }

  http.end();
}

void showReady() {
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Tempel Kartu");
  lcd.setCursor(0, 1);
  lcd.print("RFID...");
}
```

Token device (opsional):

- Set `RFID_DEVICE_TOKEN` di `.env`
- Jika diisi, request ke `/api/rfid/scan` harus mengirim `token` yang sama

## Catatan

- Project ini menggunakan 2 role (`admin`, `employee`). Jabatan employee dibedakan dengan `job_role` (`staff`, `mandor`) untuk kebutuhan statistik/tampilan.
