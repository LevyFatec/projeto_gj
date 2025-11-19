<?php
    require '../cabecalho.php';
    require '../conexao.php';
   
    $descricao = "";
    $localizacao = "";

    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $descricao = $_POST['descricao'] ?? null;
        $localizacao = $_POST['localizacao'] ?? null;
        
        if($descricao && $localizacao)
        {
            $sql = "INSERT INTO sensor(descricao, localizacao)
            VALUES(:descricao, :localizacao)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":descricao", $descricao);
            $stmt->bindParam(":localizacao", $localizacao);

            if($stmt->execute()){
                $mensagem = "Sensor cadastrado com sucesso!";
            } else{
                $mensagem = "Erro ao cadastrar sensor.";
            }

            header("Location: /projeto_gj/sensores/sensores.php");
            exit;

        }else{
            $mensagem = "Preencha todos os campos obrigatórios.";
        }
    }

?>


<div class="container-form" id="adicionar-sensor-container">
        <section><h2>Adicionar Sensor</h2>

    <form action="adicionar_sensor.php" method="POST">
    
    <div class="form-grupo-input">
        <label for="descricao">Descricao:</label>
        <input type="text" name="descricao" id="descricao" required>
    </div>
    <div class="form-grupo-input">
        <label for="localizacao">Localização:</label>
        <input type="text" name="localizacao" id="localizacao" required>
    </div>
        <div class="form-acoes">
        <button type="submit">Adicionar</button>
        <a href="/projeto_gj/sensores/sensores.php">Cancelar</a>
        </div>
    </form>
    </section>
</div>
