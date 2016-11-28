<?php
    function show_tweets($member_id){
      $tweets = array();

      $member_string = implode(',', $member_id);
      $extra = ' AND `tweet_id` IN ($member_string)';

      $sql = 'SELECT `tweet`, `created` FROM `tweets` WHERE `member_id`="'.$member_id.'" ORDER BY `created` DESC';
      $rec = mysqli_query($db, $sql) or die(mysqli_error($db));

      while($table = mysqli_fetch_assoc($rec)){
        $tweets[] = array('created' => $table['created'],
                          'member_id' => $member_id,
                          'tweet' => $table['tweet']
                          );
      }
      return $tweets;
    }



    function show_members(){
      $members = array();
      $db = mysqli_connect('localhost', 'root', 'mysql', 'seed_sns1') or die(mysqli_connect_error());
      mysqli_set_charset($db, 'utf8');
      $sql = 'SELECT `member_id`, `nick_name` FROM `members` WHERE `status`="active" ORDER BY `nick_name`';
      $rec = mysqli_query($db, $sql) or die(mysqli_error($db));

      while($table = mysqli_fetch_assoc($rec)){
        $members[$table['member_id']] = $table['nick_name'];
      }
      return $members;
    }

    function memberPicture($member_id){
      $db = mysqli_connect('localhost', 'root', 'mysql', 'seed_sns1') or die(mysqli_connect_error());
      mysqli_set_charset($db, 'utf8');
      $sql = 'SELECT `picture_path` FROM `members` WHERE `member_id`='.$member_id;
      $rec = mysqli_query($db, $sql) or die(mysqli_error($db));
      $member_picture = mysqli_fetch_assoc($rec);
      return $member_picture['picture_path'];
    }

    function followMemberIdNames($member_id=0){
      $db = mysqli_connect('localhost', 'root', 'mysql', 'seed_sns1') or die(mysqli_connect_error());
      mysqli_set_charset($db, 'utf8');

      // $members = array();
      if ($member_id > 0){
        $follow = array();

        $fsql = "SELECT `member_id` FROM `following` WHERE `follower_id`='$member_id'";
        $fresult = mysqli_query($db, $fsql) or die(mysqli_error($db));
        while($f = mysqli_fetch_assoc($fresult)){
          array_push($follow, $f['member_id']);
        }
        // var_dump($follow);

        if (count($follow)){
          $id_string = implode(',', $follow);
          $extra =  " AND `member_id` IN ($id_string)";
        } else {
          return array();
        }
      }
      $members = array();
      $sql = "SELECT `member_id`, `nick_name` FROM `members` WHERE `status`='active' $extra ORDER BY `nick_name`";

      $result = mysqli_query($db, $sql) or die(mysqli_error($db));

      while ($data = mysqli_fetch_assoc($result)){
        $id = $data['member_id'];
        $members[$id] = $data['nick_name'];
      }
      return $members;
    }




    function followMemberId($member_id){
      $members = array();
      $db = mysqli_connect('localhost', 'root', 'mysql', 'seed_sns1') or die(mysqli_connect_error());
      mysqli_set_charset($db, 'utf8');
      $sql = 'SELECT DISTINCT `member_id` FROM `following` WHERE `follower_id`="'.$member_id.'"';
      $rec = mysqli_query($db, $sql) or die(mysqli_error($db));

      while($table = mysqli_fetch_assoc($rec)){
        $members[] = $table['member_id'];

      }
      return $members;
    }


    function check_count($first, $second){
      $db = mysqli_connect('localhost', 'root', 'mysql', 'seed_sns1') or die(mysqli_connect_error());
      mysqli_set_charset($db, 'utf8');

      $sql = "SELECT COUNT(*) FROM `following`
          WHERE `member_id`='$second' AND `follower_id`='$first'";
      $result = mysqli_query($db, $sql) or die(mysqli_error($db));

      $row = mysqli_fetch_row($result);
      return $row[0];

    }

    function follow_user($me,$them){
      $count = check_count($me,$them);

      if ($count == 0){
        $db = mysqli_connect('localhost', 'root', 'mysql', 'seed_sns1') or die(mysqli_connect_error());
        mysqli_set_charset($db, 'utf8');
        $sql = "INSERT INTO `following` (`member_id`, `follower_id`) values ('$them','$me')";

        $result = mysqli_query($db, $sql) or die(mysqli_error($db));
        var_dump($result);
      }
      echo $count;
    }


    function unfollow_user($me,$them){
      $count = check_count($me,$them);
      $db = mysqli_connect('localhost', 'root', 'mysql', 'seed_sns1') or die(mysqli_connect_error());
      mysqli_set_charset($db, 'utf8');
      if ($count != 0){
        $sql = "DELETE FROM `following` WHERE `member_id`='$them' AND `follower_id`='$me' LIMIT 1";
        $result = mysqli_query($db, $sql) or die(mysqli_error($db));
      }
    }



    function columnSelect($col){
      if($col == 'all'){
        echo '
            <option value="all" selected>すべて</option>
            <option value="nick_name">投稿者名</option>
            <option value="tweet">つぶやき</option>
            ';
      } elseif ($col == 'nick_name'){
        echo '
            <option value="all">すべて</option>
            <option value="nick_name" selected>投稿者名</option>
            <option value="tweet">つぶやき</option>
            ';
      } elseif ($col == 'tweet') {
        echo '
            <option value="all">すべて</option>
            <option value="nick_name">投稿者名</option>
            <option value="tweet" selected>つぶやき</option>
            ';
      } else {
        echo '
            <option value="all">すべて</option>
            <option value="nick_name">投稿者名</option>
            <option value="tweet">つぶやき</option>
            ';
      }
    }

?>