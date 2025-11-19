<?php 
    require '../conexao.php';
    require '../cabecalho.php';

    $sql = "SELECT * FROM sensor ORDER BY descricao";
    $stmt = $pdo->query($sql);
    $sensores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


    <h2>Gerenciar Sensores:</h2>
    <div class="acao-topo">
        <a href="adicionar_sensor.php">Adicionar novo sensor</a>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descrição</th>
                    <th>Localização</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($sensores as $sensor):?>
                    <tr>
                        <td><?= $sensor['id']?></td>
                        <td><?= htmlspecialchars($sensor['descricao'])?></td>
                        <td><?= htmlspecialchars($sensor['localizacao'])?></td>
                        <td>
                            <a href="editar_sensor.php?id=<?= $sensor['id']?>">Editar</a>

                            <a href="excluir_sensor.php?id=<?= $sensor['id']?>" class="botao excluir" onclick="return confirm('Confirmar exclusão? Isso excluirá TODAS as leituras deste sensor!')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    

<?php 
    require '../rodape.php';
?>