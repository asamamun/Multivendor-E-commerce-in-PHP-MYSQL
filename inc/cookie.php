<?php
//if session not started, start it
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

//if user has cookies set, login automatically
if(isset($_COOKIE['user_id']) && isset($_COOKIE['user_name']) && isset($_COOKIE['user_email']) && isset($_COOKIE['user_role'])){
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['user_name'] = $_COOKIE['user_name'];
    $_SESSION['user_email'] = $_COOKIE['user_email'];
    $_SESSION['user_role'] = $_COOKIE['user_role'];
    if($_SESSION['user_role'] == 'customer'){
        header("Location: index.php");
        exit;
    }
    if($_SESSION['user_role'] == 'vendor'){
        header("Location: vendor/dashboard.php");
        exit;
    }    
}