<?php 
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: /projeto_gj/login.php");
        exit;
    }
    require '../conexao.php';
    require 'dompdf/vendor/autoload.php';
    use Dompdf\Dompdf;
    use Dompdf\Options;
    

    $sensor_id  = $_GET['id'] ?? null;
    $data_inicio = $_GET['data_inicio'] ?? null;
    $data_fim    = $_GET['data_fim'] ?? null;

    date_default_timezone_set('America/Sao_Paulo');
    $dataHoraRelatorioGerado = date('d/m/Y H:i');

    if(!$data_inicio || !$data_fim || !$sensor_id){
        header('Location: /projeto_gj/index.php?msg=Selecione um período e um sensor');
        exit;
    }

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
    
    $sql = "SELECT descricao, localizacao FROM sensor WHERE id = :id LIMIT 1";
    $stmt = $pdo-> prepare($sql);
    $stmt-> execute([':id'=> $sensor_id]);
    $sensor = $stmt->fetch(PDO::FETCH_ASSOC);

    $html = "
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        
        .header {
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 3px solid #1a4d2e;
        }
        
        h2 {
            text-align: center;
            color: #1a4d2e;
            font-size: 24px;
            margin-top: 0;
        }
        
        .header p {
            margin: 5px 0;
            font-size: 14px;
            text-align: center;
        }
        
        section {
            margin-top: 30px;
            padding: 10px;
        }

        .data-container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        
        .temperatura-section, .umidade-section {
            width: 48%;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.05);
            background-color: #f9f9f9;
            page-break-inside: avoid;
        }

        .temperatura-section > div:first-child, .umidade-section > div:first-child {
            font-size: 18px;
            font-weight: bold;
            color: #1a4d2e;
            text-transform: uppercase;
            padding-bottom: 5px;
            margin-bottom: 15px;
            border-bottom: 2px solid #a3c1ad;
        }

        .metric-label {
            font-weight: bold;
            color: #555;
            margin: 10px 0 0px 0;
            font-size: 13px;
            display: block;
        }

        .metric-value {
            font-size: 22px;
            color: #333;
            margin: 0 0 15px 0;
            padding-left: 0;
            border-bottom: 1px dashed #ccc;
            display: block; /* Garante que o valor ocupe uma linha */
        }

        .no-data {
            color: #cc0000;
            font-style: italic;
            text-align: center;
            padding: 20px 0;
        }
        
        /* Rodapé fixo */
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: right;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #e0e0e0;
            padding-top: 5px;
        }
    
    </style>

    <div class='header'>
        <h2>Relatório de Temperatura da Granja</h2>
        <p>Sensor: ".htmlspecialchars($sensor['descricao'])." - ".htmlspecialchars($sensor['localizacao'])."</p>
        <p>Período: ".date('d/m/Y H:i', strtotime($data_inicio))." até ".date('d/m/Y H:i', strtotime($data_fim))."</p>
    </div>

    <section>
        <div class='data-container'>
            <div class='temperatura-section'>
                <div>Temperatura</div>
                ";
                if(isset($filtro_medicoes['temperatura_max'])):
    $html .= "
                   <span class='metric-label'>Máxima</span>
                        <span class='metric-value'>".number_format($filtro_medicoes['temperatura_max'], 2, ',', '.')." °C</span>
                        
                        <span class='metric-label'>Mínima</span>
                        <span class='metric-value'>".number_format($filtro_medicoes['temperatura_min'], 2, ',', '.')." °C</span>
                        
                        <span class='metric-label'>Média do período</span>
                        <span class='metric-value'>".number_format($filtro_medicoes['temperatura_media'], 2, ',', '.')." °C</span>
                ";
                else:
    $html .=        "<p class='no-data'>Nenhuma medição encontrada neste período</p>";

                endif;
    $html .= "
     </div>
           <div class='umidade-section'>
            <div>Umidade</div>
              ";  
              if(isset($filtro_medicoes['umidade_max'])):
    $html .= "         
                     <span class='metric-label'>Máxima</span>
                        <span class='metric-value'>".number_format($filtro_medicoes['umidade_max'], 2, ',', '.')." %</span>
                        
                        <span class='metric-label'>Mínima</span>
                        <span class='metric-value'>".number_format($filtro_medicoes['umidade_min'], 2, ',', '.')."%</span>
                        
                        <span class='metric-label'>Média do período</span>
                        <span class='metric-value'>".number_format($filtro_medicoes['umidade_media'], 2, ',', '.')." %</span>
                ";
                else:
    $html .= "
                    <p class='no-data'>Nenhuma medição encontrada neste período</p>
            ";
                endif;
    $html .= "
            </div>
        </div>
    </section>

    <div class='footer'>
        Gerado por: ".htmlspecialchars($_SESSION['user_login'])." em {$dataHoraRelatorioGerado}
    </div>
    ";

    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("relatorio_temperatura_granja.pdf", array("Attachment" => false));
?>