<?php
    require '../cabecalho.php';
    require '../conexao.php';

    $id = $_GET['id'] ?? null;

    if($id){
        $stmt = $pdo->prepare("DELETE FROM sensor WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        
        if($stmt->execute()){
            header("Location: /projeto_gj/sensores/sensores.php?msg=Sensor excluído com sucesso");
        }else{
            echo "Erro ao excluir sensor.";
        }
    }else{
             echo "ID do sensor não informado.";
    } 
?>