<?php
    session_start();
    require('join/dbconnect.php');

    if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'delete'){

      $sql = sprintf('SELECT * FROM `tweets` WHERE `tweet_id`=%d',
        mysqli_real_escape_string($db, $_REQUEST['tweet_id'])
        );
      $rec = mysqli_query($db, $sql) or die(mysqli_error($db));

      if ($table = mysqli_fetch_assoc($rec)){

        if($_SESSION['id'] == $table['member_id']){
          $sql = sprintf('DELETE FROM `tweets` WHERE `tweet_id`=%d',
            mysqli_real_escape_string($db, $_REQUEST['tweet_id'])
            );
          mysqli_query($db, $sql) or die(mysqli_error($db));
        }
      }
    }

    header('location: index.php');
    exit();

?>