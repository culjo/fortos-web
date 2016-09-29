<?php
/**
 * Created by PhpStorm.
 * User: WeaverBird
 * Date: 9/26/2016
 * Time: 1:30 AM
 */
session_start();
include_once "../api/config/site_config.php";
include "system/api_caller.php";

$_SESSION['success'] = false;

if(!empty($_GET['p'])){

    $apiCaller = new Api_Caller('1', 'asdfghjkl', FORTOS_API_URL);

    $deleted = $apiCaller->sendRequest([
        'controller' => 'image',
        'action' => 'delete',
        'image_id' => $_GET['p']
    ]);

    if($deleted['deleted']){
        $_SESSION['success'] = true;
    }else{
        $_SESSION['error_msg'] = "Could Not Delete Your Photo";
    }

}

header('location:index.php'); exit();
