<?php
session_start();
unset($_SESSION['loja_usuario_id'], $_SESSION['loja_usuario_nome']);
header('Location: index.php');
exit;
