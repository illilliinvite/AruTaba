<?php

session_start();

if (empty($_SESSION)){

    header("Location: ../html/login.php");
    exit;
}

?>