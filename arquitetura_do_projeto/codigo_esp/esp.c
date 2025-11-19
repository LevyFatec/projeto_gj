#include <WiFi.h>
#include <HTTPClient.h>

// Bibliotecas do Sensor DHT
#include <Adafruit_Sensor.h>
#include <DHT.h>

// --- Configurações do Sensor ---
#define DHTPIN 4
#define DHTTYPE DHT22

const int SENSOR_ID = 1; 

DHT dht(DHTPIN, DHTTYPE);

const char* ssid = "NOME_DA_SUA_REDE_WIFI"; // <<< EDITAR AQUI       
const char* password = "SENHA_DA_SUA_REDE_WIFI"; // <<< EDITAR AQUI


const char* serverIP = "192.168.1.10"; // <<< EDITAR AQUI
const char* serverPath = "/projeto_granja/api_registrar_leitura.php";

// --- Intervalo de Leitura
const long interval = 300000; 
unsigned long previousMillis = 0; 

//================================================
// FUNÇÃO DE SETUP
//================================================
void setup() {
  Serial.begin(115200); // Inicia a comunicação serial (para debug)
  Serial.println("Iniciando o sensor...");

  dht.begin(); // Inicia o sensor DHT
  
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

  // Mede o tempo atual
  unsigned long currentMillis = millis();

  // Verifica se já passou o tempo de intervalo
  if (currentMillis - previousMillis >= interval) {
    previousMillis = currentMillis; // Reseta o contador

    // Chama a função para ler e enviar os dados
    readAndSendData();
  }
}

//================================================
// FUNÇÃO DE LEITURA E ENVIO
//================================================
void readAndSendData() {
  Serial.println("\n--------------------------");
  Serial.println("Coletando leitura...");

  float umidade = dht.readHumidity();
  float temperatura = dht.readTemperature(); 

  // Verifica se a leitura falhou (NaN = Not a Number)
  if (isnan(umidade) || isnan(temperatura)) {
    Serial.println("Falha ao ler do sensor DHT!");
    return; 
  }

  // Exibe no Serial Monitor (para debug)
  Serial.print("Umidade: ");
  Serial.print(umidade);
  Serial.println(" %");
  Serial.print("Temperatura: ");
  Serial.print(temperatura);
  Serial.println(" *C");

  // Envia os dados para o banco de dados
  sendDataToDB(temperatura, umidade);
}

//================================================
// FUNÇÃO DE ENVIO HTTP POST
//================================================
void sendDataToDB(float temperatura, float umidade) {
  
  Serial.println("Enviando dados para o servidor...");

  // Cria o objeto HTTP
  HTTPClient http;

  // Monta a URL completa
  String serverUrl = "http://" + String(serverIP) + String(serverPath);
  http.begin(serverUrl);

  // Prepara os dados para o POST no formato x-www-form-urlencoded
  // (O PHP espera os dados em $_POST)
  String postData = "sensor_id=" + String(SENSOR_ID) +
                    "&temperatura=" + String(temperatura) +
                    "&umidade=" + String(umidade);

  // Adiciona o cabeçalho (obrigatório para POST)
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  // Envia a requisição POST
  int httpResponseCode = http.POST(postData);

  if (httpResponseCode > 0) {
    String response = http.getString();
    Serial.print("Código de resposta HTTP: ");
    Serial.println(httpResponseCode);
    Serial.print("Resposta do servidor: ");
    Serial.println(response); // Mostra a resposta JSON (ex: "status: success")
  } else {
    Serial.print("Erro no envio HTTP. Código: ");
    Serial.println(httpResponseCode);
  }

  // Libera os recursos
  http.end();
}

//================================================
// FUNÇÃO DE CONEXÃO WIFI
//================================================
void connectWiFi() {
  Serial.print("Conectando ao WiFi: ");
  Serial.println(ssid);

  WiFi.begin(ssid, password);

  // Espera a conexão
  int attempts = 0;
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
    attempts++;
    if (attempts > 20) {
      Serial.println("\nFalha ao conectar. Reiniciando...");
      ESP.restart(); // Reinicia o ESP se não conseguir conectar
    }
  }

  Serial.println("\nWiFi Conectado!");
  Serial.print("Endereço IP do ESP32: ");
  Serial.println(WiFi.localIP());
}