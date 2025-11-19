<?php
session_start();
require 'conexao.php';

$login = $_POST['login'] ?? null;
$senha = $_POST['senha'] ?? null;

if ($login && $senha) {
    $sql = "SELECT * FROM usuarios WHERE login = :login";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':login', $login, PDO::PARAM_STR);
    $stmt->execute();
    
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['user_login'] = $usuario['login'];
        
        header("Location: /projeto_gj/index.php");
        exit;
    } else {
        $_SESSION['msg']="<p> Usuário e/ou senha incorretos";
        header("Location: /projeto_gj/login.php");
        exit;
    }
}
?>
