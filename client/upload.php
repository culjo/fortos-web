<?php
/**
 * Created by PhpStorm.
 * User: WeaverBird
 * Date: 9/26/2016
 * Time: 2:07 PM
 */

session_start();


$_SESSION['success'] = false;


header('location:index.php'); exit();