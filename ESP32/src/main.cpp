#include <Arduino.h>
//#include <WiFIMulti.h>

#include <WiFi.h>
#include <WebServer.h>

#define WIFI_SSID "/*WIFI_SSID*/"
#define WIFI_PASSWORD "/*WIFI_PASSWORD*/"
#define RELAY 4
#define BUTTON_PIN 21


// Function prototypes
void handle_OnConnect();
void handle_Execute();
String SendHTML();
String SendJSON();


//WiFiMulti wifiMulti;

WebServer server(80);

// Set IP address
IPAddress local_IP(192, 168, 1, 233);
// Set gateway address
IPAddress gateway(192, 168, 1, 1);
IPAddress subnet(255, 255, 255, 0);
IPAddress primaryDNS(192, 168, 1, 1);



void setup() {
  Serial.begin(921600);

  // Setup built-in LED
  pinMode(LED_BUILTIN, OUTPUT);

  // Setup relay pin
  pinMode(RELAY, OUTPUT);
  digitalWrite(RELAY, LOW);

  // Setup button pin
  pinMode(BUTTON_PIN, INPUT_PULLUP);

  // Configures static IP address
  if (!WiFi.config(local_IP, gateway, subnet, primaryDNS)) {
    Serial.println("STA Failed to configure");
  }

  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  Serial.print("Starting");

  server.on("/", handle_OnConnect);
  server.on("//*URL_KEY*/", handle_Execute);

  server.begin();


}

bool isConnected = false;

void loop() {
  if(WiFi.status() == WL_CONNECTED && !isConnected){
    Serial.print("Connected. IP: ");
    Serial.println(WiFi.localIP());
    digitalWrite(LED_BUILTIN, HIGH);

    isConnected = true;
  }

  if (WiFi.status() != WL_CONNECTED){
    Serial.print(".");
    digitalWrite(LED_BUILTIN, !digitalRead(LED_BUILTIN));
    delay(1000);
    isConnected = false;
  }

  if(digitalRead(BUTTON_PIN) == LOW){
    Serial.println("Button Pressed.");
    digitalWrite(RELAY, HIGH);
    delay(100);
    digitalWrite(RELAY, LOW);
    delay(500);
  }

  server.handleClient();
}

void handle_OnConnect(){
  server.send(200, "text/html", SendHTML());
}

void handle_Execute(){
  Serial.println("Remote open.");
  digitalWrite(RELAY, HIGH);
  delay(100);
  digitalWrite(RELAY, LOW);

  server.send(200, "text/json", SendJSON());
}


String SendJSON(){
  String text = "<!DOCTYPE text/json>";

  return text;
}


String SendHTML(){
  String html = "<!DOCTYPE html> <html>\n<body>\n<h1>Hello</h1>\n</body>\n</html>\n";
  return html;
}