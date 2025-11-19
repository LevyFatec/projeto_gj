<?php 
    if(session_status() !== PHP_SESSION_ACTIVE){
        session_start();
    }
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login de Usuário</title>
    <link rel="stylesheet" href="/projeto_gj/css/style.css">
</head>
<body class="auth-body">
    <div class="auth-container">

<?php 
        if(isset($_SESSION['msg'])){
            echo $_SESSION['msg'];
            unset($_SESSION['msg']);
        }
    ?>


    <h1 class="">Login do Sistema</h1>

    <form method="post" action="autenticacao.php" class="">

    <div>
        <label for="login">Usuário:</label>
        <input type="text" id="login" name="login" required size="50">
    </div>
    <div>
        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required size="50">
    </div>
    <div class="auth-actions">
        <button type="submit">
            Entrar
        </button>
        <a href="/projeto_gj/usuarios/tela_cadastro.php">Cadastre-se</a>
    </div>

    </form>

</div>
    
</body>
</html>
