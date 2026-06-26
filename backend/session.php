<?php

session_start();

if (!isset($_SESSION["user_id"])) {

    header("Location: ../html/home.php");
    exit;
}