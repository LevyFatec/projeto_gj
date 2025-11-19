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
    <title>Cadastre-se</title>
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
    
        <h2>Cadastre-se</h2>

        <form action="cadastro_usuario.php" method="POST">
            <div>
                <label for="login">Login:</label>
                <input type="text" name="login" id="login" size="50">
            </div>
            <div>
                    <label for="senha">Senha:</label>
                    <input type="password" name="senha_u" id="senha_u" size="50">
            </div>

            <div class="auth-actions">
                <button type="submit">Cadastar</button>
                <a href="/projeto_gj/login.php">Login</a>
            </div>
        </form>

    </div>
    
</body>
</html>

