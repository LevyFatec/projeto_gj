<?php 
    require '../conexao.php';
    header('Content-Type: application/json');

    $sensor_id = $_POST['sensor_id'] ?? null;
    $temperatura = $_POST['temperatura'] ?? null;
    $umidade = $_POST['umidade'] ?? null;

    if(!$sensor_id || !$temperatura || !$umidade)
    {
        echo json_encode(['status' => 'error', 'message' => 'Dados incompletos.']);
        exit;
    }


    try {
        $sql = "INSERT INTO leitura (sensor_id, temperatura, umidade) VALUES (:sensor_id, :temperatura, :umidade)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindParam(":sensor_id", $sensor_id, PDO::PARAM_INT);
        $stmt->bindParam(":temperatura", $temperatura);
        $stmt->bindParam(":umidade", $umidade);
        
        $stmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'Leitura registrada.']);

    }catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro no banco de dados: ' . $e->getMessage()]);
    }
?>
