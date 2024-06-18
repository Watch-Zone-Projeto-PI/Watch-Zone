<?php
// Inicia a sessão, se ainda não estiver iniciada
session_start();

// Remove todas as variáveis de sessão
session_unset();

// Destrói a sessão
session_destroy();

// Redireciona o usuário de volta para a página de login
header("Location: login.php");
exit;
?>