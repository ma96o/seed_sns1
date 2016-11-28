<?php
    session_start();
    require("join/dbconnect.php");
    include_once("function.php");

    $member_id = $_GET['member_id'];
    $do = $_GET['do'];

    switch ($do){
      case "follow":
        follow_user($_SESSION['id'],$member_id);
        $msg = "You have followed a user!";
        echo $msg;
      break;

      case "unfollow":
        unfollow_user($_SESSION['id'],$member_id);
        $msg = "You have unfollowed a user!";
        echo $msg;
        break;

    }
    $_SESSION['message'] = $msg;

    header("Location:users.php");
    exit();


?>