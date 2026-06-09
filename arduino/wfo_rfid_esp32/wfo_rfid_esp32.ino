#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <SPI.h>
#include <MFRC522.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>

// Ubah sesuai WiFi yang dipakai ESP32.
const char* WIFI_SSID = "NAMA_WIFI";
const char* WIFI_PASSWORD = "PASSWORD_WIFI";

// Jangan pakai 127.0.0.1 dari ESP32. Pakai IP laptop/server Laravel.
// Jalankan Laravel dengan: php artisan serve --host=0.0.0.0 --port=8000
const char* LARAVEL_SCAN_URL = "http://192.168.1.10:8000/api/rfid/scan";

// Kosongkan kalau RFID_TOKEN di .env Laravel tidak dipakai.
const char* DEVICE_TOKEN = "";
const char* DEVICE_NAME = "ESP32-RFID-01";

// RC522 untuk ESP32.
constexpr uint8_t SS_PIN = 5;
constexpr uint8_t RST_PIN = 27;

// LCD I2C umumnya 0x27 atau 0x3F.
LiquidCrystal_I2C lcd(0x27, 16, 2);
MFRC522 rfid(SS_PIN, RST_PIN);

unsigned long lastScanAt = 0;
const unsigned long SCAN_COOLDOWN_MS = 2500;

void showMessage(const String& line1, const String& line2 = "") {
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print(line1.substring(0, 16));
  lcd.setCursor(0, 1);
  lcd.print(line2.substring(0, 16));
}

void connectWifi() {
  showMessage("Menghubungkan", "WiFi...");
  Serial.println();
  Serial.print("Menghubungkan ke WiFi: ");
  Serial.println(WIFI_SSID);

  WiFi.mode(WIFI_STA);
  WiFi.disconnect(true);
  delay(500);
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

  unsigned long startedAt = millis();
  int dotCount = 0;

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");

    dotCount = (dotCount + 1) % 4;
    String dots = "";
    for (int i = 0; i < dotCount; i++) {
      dots += ".";
    }
    showMessage("Menghubungkan", "WiFi" + dots);

    if (millis() - startedAt > 20000) {
      Serial.println();
      Serial.println("WiFi gagal tersambung.");
      Serial.print("Status WiFi: ");
      Serial.println(WiFi.status());
      showMessage("WiFi gagal", "Cek SSID/pass");
      delay(3000);
      startedAt = millis();
      showMessage("Menghubungkan", "WiFi...");
      WiFi.disconnect();
      delay(500);
      WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
    }
  }

  Serial.println();
  Serial.println("WiFi tersambung.");
  Serial.print("IP ESP32: ");
  Serial.println(WiFi.localIP());

  showMessage("WFO.id siap", WiFi.localIP().toString());
  delay(1200);
  showMessage("Tempel kartu", "RFID");
}

String uidToString() {
  String uid = "";

  for (byte i = 0; i < rfid.uid.size; i++) {
    if (rfid.uid.uidByte[i] < 0x10) {
      uid += "0";
    }
    uid += String(rfid.uid.uidByte[i], HEX);
  }

  uid.toUpperCase();
  return uid;
}

void sendScanToLaravel(const String& uid) {
  if (WiFi.status() != WL_CONNECTED) {
    connectWifi();
  }

  showMessage("Memproses", uid);

  HTTPClient http;
  http.begin(LARAVEL_SCAN_URL);
  http.addHeader("Content-Type", "application/json");
  http.setTimeout(8000);

  StaticJsonDocument<256> request;
  request["uid"] = uid;
  request["nama_perangkat"] = DEVICE_NAME;
  request["source"] = "esp32";

  if (String(DEVICE_TOKEN).length() > 0) {
    request["token"] = DEVICE_TOKEN;
  }

  String body;
  serializeJson(request, body);

  int statusCode = http.POST(body);
  String response = http.getString();
  http.end();

  if (statusCode <= 0) {
    showMessage("Server gagal", "Coba lagi");
    return;
  }

  StaticJsonDocument<768> doc;
  DeserializationError error = deserializeJson(doc, response);

  if (error) {
    showMessage("Respon error", String(statusCode));
    return;
  }

  const char* responseMessage = doc["message"] | "Scan selesai";
  const char* responseName = doc["nama_karyawan"] | "";
  String line1 = doc["lcd"]["line_1"] | responseMessage;
  String line2 = doc["lcd"]["line_2"] | responseName;

  showMessage(line1, line2);
  delay(2200);
  showMessage("Tempel kartu", "RFID");
}

void setup() {
  Serial.begin(115200);

  Wire.begin(21, 22);
  lcd.init();
  lcd.backlight();

  SPI.begin(18, 19, 23, SS_PIN);
  rfid.PCD_Init();

  connectWifi();
}

void loop() {
  if (!rfid.PICC_IsNewCardPresent() || !rfid.PICC_ReadCardSerial()) {
    return;
  }

  if (millis() - lastScanAt < SCAN_COOLDOWN_MS) {
    rfid.PICC_HaltA();
    rfid.PCD_StopCrypto1();
    return;
  }

  lastScanAt = millis();
  String uid = uidToString();

  Serial.print("UID: ");
  Serial.println(uid);

  sendScanToLaravel(uid);

  rfid.PICC_HaltA();
  rfid.PCD_StopCrypto1();
}
