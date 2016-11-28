<?php
    session_start();
    require('join/dbconnect.php');

    if(isset($_SESSION['id'])){
      if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'like'){
        $sql = sprintf('INSERT INTO `likes` SET `member_id`=%d, `tweet_id`=%d',
          mysqli_real_escape_string($db, $_REQUEST['member_id']),
          mysqli_real_escape_string($db, $_REQUEST['tweet_id'])
          );
        mysqli_query($db, $sql) or die(mysqli_error($db));
      }

      if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'unlike'){
        $sql = sprintf('DELETE FROM `likes` WHERE `member_id`=%d AND`tweet_id`=%d',
          mysqli_real_escape_string($db, $_REQUEST['member_id']),
          mysqli_real_escape_string($db, $_REQUEST['tweet_id'])
          );
        mysqli_query($db, $sql) or die(mysqli_error($db));
      }
    }

    header('location: index.php');
    exit();
?>