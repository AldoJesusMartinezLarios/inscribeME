#include <Arduino.h>
#include <SPI.h>
#include <MFRC522.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <WiFiMulti.h>

const char* apiEndpoint = "https://fastapi-mongodb-b1r3.onrender.com/v1/actualizar_valor_rfid";

#if defined(ESP32)
#define SS_PIN 5
#define RST_PIN 22
#elif defined(ESP8266)
#define SS_PIN D8
#define RST_PIN D0
#endif

MFRC522 rfid(SS_PIN, RST_PIN); // Instance of the class
MFRC522::MIFARE_Key key;
// Init array that will store new NUID
byte nuidPICC[4];

String DatoHex;

// Prototipo de la función printHex
String printHex(byte *buffer, byte bufferSize);

const byte led_gpio1 = 33; // LED conectado al pin 33
const byte led_gpio2 = 25; // LED conectado al pin 25

void setup() {
  Serial.begin(115200);

  pinMode(led_gpio1, OUTPUT);
  pinMode(led_gpio2, OUTPUT);

  WiFiMulti wifiMulti; // Crear instancia de WiFiMulti

  // Agregar las redes WiFi disponibles
  wifiMulti.addAP("WIFI_1", "password1");
  wifiMulti.addAP("WIFI_2", "password2");
  wifiMulti.addAP("WIFI_3", "password3");
  wifiMulti.addAP("WIFI_4", "password4");

  Serial.println("Conectando a Wifi...");
  
  // Esperar hasta que se conecte a una red WiFi
  while (wifiMulti.run() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("");
  Serial.println("WiFi conectado");
  Serial.println(WiFi.localIP());
  Serial.println("LECTOR PARA ASIGNAR TARJETA -- Listo para usarse");

  SPI.begin();
  rfid.PCD_Init();

  Serial.print(F("Lector: "));
  rfid.PCD_DumpVersionToSerial();

  for (byte i = 0; i < 6; i++) {
    key.keyByte[i] = 0xFF;
  }

  DatoHex = printHex(key.keyByte, MFRC522::MF_KEY_SIZE);

  configTime(-6 * 3600, 0, "pool.ntp.org");
}

void loop() {
  digitalWrite(led_gpio1, HIGH); // Encender el LED indicador de conexión a internet
  
  if (!rfid.PICC_IsNewCardPresent()) {
    return;
  }

  if (!rfid.PICC_ReadCardSerial()) {
    return;
  }

  Serial.print(F("PICC type: "));
  MFRC522::PICC_Type piccType = rfid.PICC_GetType(rfid.uid.sak);
  Serial.println(rfid.PICC_GetTypeName(piccType));
  
  if (piccType != MFRC522::PICC_TYPE_MIFARE_MINI && piccType != MFRC522::PICC_TYPE_MIFARE_1K && piccType != MFRC522::PICC_TYPE_MIFARE_4K) {
    Serial.println("Su Tarjeta no es del tipo MIFARE Classic.");
    return;
  }

  if (rfid.uid.uidByte[0] != nuidPICC[0] || rfid.uid.uidByte[1] != nuidPICC[1] || rfid.uid.uidByte[2] != nuidPICC[2] || rfid.uid.uidByte[3] != nuidPICC[3]) {
    Serial.println("Se ha detectado una nueva tarjeta.");

    for (byte i = 0; i < 4; i++) {
      nuidPICC[i] = rfid.uid.uidByte[i];
    }

    DatoHex = printHex(rfid.uid.uidByte, rfid.uid.size);
    Serial.print("Codigo Tarjeta: ");
    Serial.println(DatoHex);
    Serial.println();

    rfid.PICC_HaltA();
    rfid.PCD_StopCrypto1();

    String requestBody = "{\"valorRFID\":\"" + DatoHex + "\"}";

    HTTPClient http;
    http.begin(apiEndpoint);
    http.addHeader("Content-Type", "application/json");
    
    int httpCode = http.POST(requestBody);
    
    if (httpCode > 0) {
      Serial.print("Respuesta del servidor: ");
      Serial.println(httpCode);
    
      if (httpCode == HTTP_CODE_OK) {
        digitalWrite(led_gpio2, HIGH);
        delay(1000);
        digitalWrite(led_gpio2, LOW);
        String payload = http.getString();
        Serial.print("Respuesta del servidor: ");
        Serial.println(payload);
      } else if (httpCode == HTTP_CODE_TEMPORARY_REDIRECT) {
        String newUrl = http.header("Location");
        http.end();

        http.begin(newUrl);
        http.addHeader("Content-Type", "application/json");
        httpCode = http.POST(requestBody);
        
        if (httpCode == HTTP_CODE_OK) {
          digitalWrite(led_gpio2, HIGH);
          delay(1000);
          digitalWrite(led_gpio2, LOW);
          String payload = http.getString();
          Serial.print("Respuesta del servidor: ");
          Serial.println(payload);
        }
      }
    } else {
      Serial.println("Error en la solicitud POST");
    }
    
    http.end();
    
    Serial.println("Solicitud enviada al servidor");
  }
}

String printHex(byte *buffer, byte bufferSize) {
  String DatoHexAux = "";
  for (byte i = 0; i < bufferSize; i++) {
    if (buffer[i] < 0x10) {
      DatoHexAux = DatoHexAux + "0";
      DatoHexAux = DatoHexAux + String(buffer[i], HEX);
    } else {
      DatoHexAux = DatoHexAux + String(buffer[i], HEX);
    }
  }

  for (int i = 0; i < DatoHexAux.length(); i++) {
    DatoHexAux[i] = toupper(DatoHexAux[i]);
  }
  return DatoHexAux;
}