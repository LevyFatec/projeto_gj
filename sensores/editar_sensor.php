<?php
    require '../cabecalho.php';
    require "../conexao.php";
    $id = $_GET['id'] ?? null;
    
    $sql = "SELECT * FROM sensor WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $sensor = $stmt->fetch(PDO::FETCH_ASSOC);

    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $id = $_POST['id'] ?? null;
        $descricao = $_POST['descricao'] ?? null;
        $localizacao = $_POST['localizacao'] ?? null;
         
        if($descricao && $localizacao && $id)
        {
            $sql = "UPDATE sensor SET descricao = :descricao, localizacao= :localizacao WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":descricao", $descricao);
            $stmt->bindParam(":localizacao", $localizacao);
            $stmt->bindParam(":id", $id);

            if($stmt->execute()){
                $mensagem = "Sensor atualizado com sucesso!";
            } else{
                $mensagem = "Erro ao atualizar sensor.";
            }

            header("Location: /projeto_gj/sensores/sensores.php");
            exit;

        }else{
            $mensagem = "Preencha todos os campos obrigatórios.";
        }
        
    }

?>



<div class="container-form" id="editar-sensor-container">

    <section>
            <h2>Editar Sensor</h2>
    
    <form action="editar_sensor.php" method="POST">
        <input type="hidden" name="id" value="<?= $sensor['id'] ?>">
        <div class="form-grupo-input">
            <label for="descricao">Descricao:</label>
            <input type="text" name="descricao" id="descricao"  value="<?= htmlspecialchars($sensor['descricao']) ?>" required>
        </div>
        <div class="form-grupo-input">
            <label for="localizacao">Localização:</label>
            <input type="text" name="localizacao" id="localizacao" value="<?= htmlspecialchars($sensor['localizacao']) ?>" required>
        </div>
        <div class="form-acoes">
            <button type="submit">Salvar</button>
            <a href="/projeto_gj/sensores/sensores.php">Cancelar</a>
        </div>
    </form>
</section>
</div>
