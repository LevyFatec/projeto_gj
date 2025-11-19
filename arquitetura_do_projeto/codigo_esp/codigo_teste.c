/*
 * CÓDIGO DE TESTE (FAKE)
 * Simula um sensor DHT22, gerando dados falsos de temperatura e umidade.
 * Envia os dados para o servidor XAMPP.
 */

// Bibliotecas de WiFi e Conexão HTTP
#include <WiFi.h>
#include <HTTPClient.h>

// --- Configurações (NÃO PRECISA DO DHT) ---
// ID deste sensor no banco de dados (criamos o sensor '1' no script SQL)
const int SENSOR_ID = 1;

// --- Configurações de Rede (Suas configs) ---
const char* ssid = "xxxxxxxx"; //------------- EDITE AQUI
const char* password = "xxxxxxxx."; //------------- EDITE AQUI

// --- Configurações do Servidor XAMPP (Seu IP) ---
const char* serverIP = "XXXXXXXXXX"; //------------- EDITE AQUI
const char* serverPath = "/projeto_gj/api/api_registrar_leitura.php";

// --- Intervalo de Leitura (10 segundos) ---
const long interval = 10000; // 10 segundos
unsigned long previousMillis = 0;

//================================================
// FUNÇÃO DE SETUP
//================================================
void setup() {
  Serial.begin(115200); 
  Serial.println("Iniciando SIMULADOR DE SENSOR (FAKE)...");
  
  // Inicia o gerador de números aleatórios
  randomSeed(millis());
  
  connectWiFi(); // Conecta ao WiFi
}

//================================================
// FUNÇÃO DE LOOP
//================================================
void loop() {
  // Verifica se o WiFi ainda está conectado
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("WiFi desconectado. Tentando reconectar...");
    connectWiFi();
  }

  unsigned long currentMillis = millis();

  // Verifica se já passou o tempo de intervalo
  if (currentMillis - previousMillis >= interval) {
    previousMillis = currentMillis; 
    
    // Chama a função para GERAR e enviar os dados
    generateAndSendData();
  }
}

//================================================
// FUNÇÃO DE GERAÇÃO E ENVIO DE DADOS FAKE
//================================================
void generateAndSendData() {
  Serial.println("\n--------------------------");
  Serial.println("Gerando dados FAKE...");

  // (RF-01 FAKE) Gera dados aleatórios
  // Gera temperatura entre 19.0°C e 21.0°C
  float temperatura = 20.0 + (random(-10, 11) / 10.0);
  // Gera umidade entre 55.0% e 65.0%
  float umidade = 60.0 + (random(-50, 51) / 10.0);

  // Exibe no Serial Monitor (para debug)
  Serial.print("Umidade (FAKE): ");
  Serial.print(umidade);
  Serial.println(" %");
  Serial.print("Temperatura (FAKE): ");
  Serial.print(temperatura);
  Serial.println(" *C");

  // Envia os dados para o banco de dados
  sendDataToDB(temperatura, umidade);
}

//================================================
// FUNÇÃO DE ENVIO HTTP POST
// (Exatamente como estava antes)
//================================================
void sendDataToDB(float temperatura, float umidade) {
  
  // (RF-02) Armazenamento de Dados
  Serial.println("Enviando dados para o servidor...");
  HTTPClient http;
  String serverUrl = "http://" + String(serverIP) + String(serverPath);
  http.begin(serverUrl);

  String postData = "sensor_id=" + String(SENSOR_ID) +
                    "&temperatura=" + String(temperatura) +
                    "&umidade=" + String(umidade);

  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  // (RNFR 02.1) Envia a requisição POST
  int httpResponseCode = http.POST(postData);

  if (httpResponseCode > 0) {
    String response = http.getString();
    Serial.print("Código de resposta HTTP: ");
    Serial.println(httpResponseCode);
    Serial.print("Resposta do servidor: ");
    Serial.println(response); // Deve mostrar {"status":"success",...}
  } else {
    Serial.print("Erro no envio HTTP. Código: ");
    Serial.println(httpResponseCode);
  }
  http.end();
}

//================================================
// FUNÇÃO DE CONEXÃO WIFI
// (Exatamente como estava antes)
//================================================
void connectWiFi() {
  Serial.print("Conectando ao WiFi: ");
  Serial.println(ssid);
  WiFi.begin(ssid, password);
  int attempts = 0;
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
    attempts++;
    if (attempts > 20) {
      Serial.println("\nFalha ao conectar. Reiniciando...");
      ESP.restart();
    }
  }
  Serial.println("\nWiFi Conectado!");
  Serial.print("Endereço IP do ESP32: ");
  Serial.println(WiFi.localIP());
}