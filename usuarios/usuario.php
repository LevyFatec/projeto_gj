<?php
require '../cabecalho.php';

$usuario_atual = $_SESSION['user_login'];
?>

<div>
    <h2>Perfil do usuário</h2>
</div>
<div>
    <p>Bem-vindo(a), <?= htmlspecialchars($usuario_atual) ?>.</p>
</div>


<?php
require '../rodape.php';
?>
