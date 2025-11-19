<?php 
     if(session_status() !== PHP_SESSION_ACTIVE){
        session_start();
    }
    require '../conexao.php';

    $login = $_POST['login'];
    $senha = $_POST['senha_u'];
    $senha_u = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "SELECT login FROM usuarios WHERE login = :login";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":login", $login);
    $stmt->execute();
    $login_exst = $stmt->fetch(PDO::FETCH_ASSOC);

    if($login == "" || $login =="null"){
        $_SESSION['msg'] = "<p> O campo de login deve ser preenchido";
        header("Location: /projeto_gj/usuarios/tela_cadastro.php");
    }else{
        if($login_exst['login'] == $login){
            $_SESSION['msg'] = "<p> O login já existe";
            header("Location: /projeto_gj/usuarios/tela_cadastro.php");
        }else{
            $sql = "INSERT INTO usuarios (login, senha) VALUES (:login, :senha)";
            $stmt = $pdo-> prepare($sql);
            $stmt-> bindParam(":login", $login);
            $stmt-> bindParam(":senha", $senha_u);

            if($stmt->execute()){
                $_SESSION['msg'] = "<p>Usuário cadastrado com sucesso!";
                header("Location: /projeto_gj/login.php");
            }else{
                $_SESSION['msg'] = "<p>Erro ao cadastrar usuário.";
                header("Location: /projeto_gj/usuarios/tela_cadastro.php");
            }
        }
    }

?>