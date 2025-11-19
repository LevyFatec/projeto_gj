<?php
    session_start();

if (!isset($_SESSION['user_id'])) 
{
    header("Location: /projeto_gj/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoramento de Granja</title>
    <link rel="stylesheet" href="/projeto_gj/css/style.css">

</head>

<body>
        <h1>Monitoramento Remoto - Granja</h1>
<nav>
    <a href="/projeto_gj/index.php">Dashboard</a>
    <a href="/projeto_gj/sensores/sensores.php">Sensores</a>
    <a href="/projeto_gj/usuarios/usuario.php">Perfil</a>
    <a href="/projeto_gj/sair.php">Sair</a>
</nav>

<div class="conteudo">
