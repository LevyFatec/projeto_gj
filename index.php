<?php
    require 'cabecalho.php';
    require 'conexao.php';

    $sensores = $pdo->query("SELECT id, descricao, localizacao FROM sensor ORDER BY descricao")->fetchAll(PDO::FETCH_ASSOC);
    
    date_default_timezone_set('America/Sao_Paulo');
    $data_inicio_default = date('Y-m-d\TH:i', strtotime('-24 hours'));
    $data_fim_default = date('Y-m-d\TH:i');

    //recebendo vvalores
    $data_inicio = $_GET['data_inicio'] ?? $data_inicio_default;
    $data_fim    = $_GET['data_fim'] ?? $data_fim_default;
    $sensor_id = $_GET['sensor_id'] ?? 1;

    //última leitura feita
    $sql = "SELECT temperatura, umidade, data_hora 
    FROM leitura WHERE sensor_id = :sensor_id
    ORDER BY data_hora DESC 
    LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':sensor_id' => $sensor_id]);
    $leitura_atual = $stmt->fetch(PDO::FETCH_ASSOC);

    //filtro
    $sql = "SELECT 
        MAX(temperatura) AS temperatura_max,
        MIN(temperatura) AS temperatura_min,
        AVG(temperatura) AS temperatura_media,
        MAX(umidade) AS umidade_max,
        MIN(umidade) AS umidade_min,
        AVG(umidade) AS umidade_media
    FROM leitura
    WHERE sensor_id = :sensor_id
    AND data_hora BETWEEN :data_inicio AND :data_fim;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':sensor_id'=>$sensor_id,':data_inicio'=>$data_inicio,':data_fim'=>$data_fim]);
    $filtro_medicoes = $stmt->fetch(PDO::FETCH_ASSOC);

?>

    <div>

    <section>
        <h2>Leitura em Tempo Real</h2>
        <div class="cards-leitura">
            <div>
                <h3>
                    Temperatura atual
                </h3>
                <?php if($leitura_atual): ?>
                    <p><?= number_format($leitura_atual['temperatura'], 2, ',', '.') ?> °C</p>
                    <div>
                            <small>Leitura realizada: <?= date('d/m/Y H:i', strtotime($leitura_atual['data_hora']))?></small>
                    </div>
                <?php else: ?>
                    <small>Verifique se há um sensor válido selecionado</small>
                <?php endif;?>
            </div>
            <div>
                <h3>
                    Umidade atual
                </h3>
                    <?php if($leitura_atual): ?>
                        <p><?= number_format($leitura_atual['umidade'], 2, ',', '.') ?> %</p>
                        <div>
                            <small>Leitura realizada: <?= date('d/m/Y H:i', strtotime($leitura_atual['data_hora']))?></small>
                        </div>
                    <?php else:?>
                        <small>Verifique se há um sensor válido selecionado</small>
                    <?php endif;?>
            </div>
            
        </div>

    </section>





    <section>
        <h2>Filtro de Consulta</h2>
        <form action="index.php" method="GET">
            <div>
                <label for="sensor_id">Sensor</label>
                    <select name="sensor_id" id="sensor_id" required>
                        <option value="">Selecione um sensor</option>
                        <?php foreach ($sensores as $sensor):?>
                            <option value="<?= $sensor['id']?>"
                                <?= $sensor_id == $sensor['id'] ? 'selected' : '' ?>>
                                <?=  htmlspecialchars($sensor['descricao'])?> - 
                                <?= htmlspecialchars($sensor['localizacao']) ?>
                            </option>
                            <?php endforeach; ?>
                    </select>
            </div>
            
           <div class="form-grupo">
                <label for="data_inicio">Data/Hora Início:</label>
                <input type="datetime-local" id="data_inicio" name="data_inicio" value="<?= htmlspecialchars($data_inicio) ?>" required>
            </div>
            
            <div class="form-grupo">
                <label for="data_fim">Data/Hora Fim:</label>
                <input type="datetime-local" id="data_fim" name="data_fim" value="<?= htmlspecialchars($data_fim) ?>" required>
            </div>
            <div>
                <button type="submit" class="botao">Filtrar</button>
            </div>
            <div>
                <div>
                    
                    <?php if(isset($_GET['msg'])): ?>
                <small style="color:red"><?= htmlspecialchars($_GET['msg']) ?></small>
                    <?php endif; ?>
                    <a href="#" onclick="this.href='/projeto_gj/relatorio/gerar_relatorio.php?id=' + document.getElementById('sensor_id').value + '&data_inicio=' + encodeURIComponent(document.getElementById('data_inicio').value) + '&data_fim=' + encodeURIComponent(document.getElementById('data_fim').value)" target="_blank">Gerar Relatório</a>
                </div>
            </div>

        </form>
    </section>



    <section>
        <h2>Resultados do Filtro</h2>
        <p><?=date('d/m/Y H:i', strtotime($data_inicio))?> - <?=date('d/m/Y H:i', strtotime($data_fim))?> </p>
        <div>
            <div>
                Temperatura
                <?php if(isset($filtro_medicoes['temperatura_max'])):?>
                    <p>Máxima</p>
                    <p><?= number_format($filtro_medicoes['temperatura_max'], 2, ',', '.') ?> °C</p>
                    <p>Mínima</p>
                    <p><?= number_format($filtro_medicoes['temperatura_min'], 2, ',', '.') ?> °C</p>
                    <p>Média do período</p>
                    <p><?= number_format($filtro_medicoes['temperatura_media'], 2, ',', '.') ?> °C</p>
                <?php else:?>
                    <p>Nenhuma medição encontrada</p>
                <?php endif;?>
            </div>
            <div>
                Umidade
                <?php if(isset($filtro_medicoes['umidade_max'])):?>
                    <p>Máxima</p>
                    <p><?= number_format($filtro_medicoes['umidade_max'], 2, ',', '.') ?> %</p>
                    <p>Mínima</p>
                    <p><?= number_format($filtro_medicoes['umidade_min'], 2, ',', '.') ?> %</p>
                    <p>Média do período</p>
                    <p><?= number_format($filtro_medicoes['umidade_media'], 2, ',', '.') ?> %</p>
                <?php else:?>
                    <p>Nenhuma medição encontrada</p>
                <?php endif;?>
            </div>



        </div>
    </section>

    </div>


<?php 
    require 'rodape.php';
?>