<?php 
    session_start();
    require('join/dbconnect.php');
    include_once('function.php');

    $followMemberId = followMemberId($_SESSION['id']);
    $followMemberId[] = $_SESSION['id'];
    $id_string = implode(',', $followMemberId);
    $extra =  " AND m.`member_id` IN ($id_string)";


    if(empty($_SESSION['selectTime'])){
      $_SESSION['selectTime'] = '';
    }
    $lastSelectTime = $_SESSION['selectTime'];


    function makeLink($value) {
      return mb_ereg_replace("(https?://[-_.!~*\'()a-zA-Z0-9;/?:@&=+$,%#]+)", '<a href="\1" target="_blank">\1</a>', $value);
    }

    if (isset($_SESSION['id'])) {
      if($_SESSION['time'] + 60*60 > time()){
        $_SESSION['time'] = time();

        $sql = sprintf('SELECT * FROM `members` WHERE `member_id`=%d',
                       mysqli_real_escape_string($db, $_SESSION['id']));
        $rec = mysqli_query($db, $sql) or die(mysqli_error($db));
        $member = mysqli_fetch_assoc($rec);
      } else {
        header('location: login.php');
        exit();
      }
    } else {

      header('location: login.php');
      exit();
    }

    if(!empty($_POST)) {
      if($_POST['tweet'] != ''){
        $sql = sprintf('INSERT INTO `tweets` SET `tweet`="%s", `member_id`="%s", `reply_tweet_id`=%d, `created`=now()',
          mysqli_real_escape_string($db, $_POST['tweet']),
          mysqli_real_escape_string($db, $member['member_id']),
          mysqli_real_escape_string($db, $_POST
            ['reply_tweet_id']));
        mysqli_query($db, $sql) or die(mysqli_error($db));

        header('location: index.php');
        exit();
      }
    }
    $page = '';
    if(isset($_REQUEST['page'])){
      $page = $_REQUEST['page'];
    }
    if ($page == '') {
      $page = 1;
    }
    $page = max($page, 1);

    //最終ページを取得する
    $sql = 'SELECT COUNT(*) AS cnt FROM `tweets` WHERE `member_id` IN ('.$id_string.')';
    $rec = mysqli_query($db, $sql) or die(mysqli_error($db));
    $table = mysqli_fetch_assoc($rec);
    
    $maxPage = ceil($table['cnt'] / 10);
    $page = min($page, $maxPage);

    $start = ($page - 1) * 10;
    $start = max(0, $start);

    $search_word = '';
    $search_column_select = '';
    if(!empty($_GET['search_word'])){
      $search_word = $_GET['search_word'];
      $search_column_select = $_GET['search_column'];
      if($_GET['search_column'] == 'all'){
        $sql = sprintf(
          'SELECT m.`nick_name`, m.`picture_path`, t.* FROM `members` m, `tweets` t WHERE m.`member_id`=t.`member_id` AND t.`tweet` LIKE "%%%s%%" UNION SELECT m.`nick_name`, m.`picture_path`, t.* FROM `members` m, `tweets` t WHERE m.`member_id`=t.`member_id` AND m.`nick_name` LIKE "%%%s%%"'.$extra,
          mysqli_real_escape_string($db, $_GET['search_word']),
          mysqli_real_escape_string($db, $_GET['search_word'])
          );
        $tweets = mysqli_query($db, $sql) or die(mysqli_error($db));

 // ORDER BY t.`created` DESC LIMIT %d,10
      } else {

        if ($_GET['search_column'] == 'nick_name'){
          $search_column = 'm.`nick_name`';

          $sql = sprintf('SELECT m.`nick_name`, m.`picture_path`, t.* FROM `members` m, `tweets` t WHERE m.`member_id`=t.`member_id` AND %s LIKE "%%%s%%" '.$extra.' ORDER BY t.`created` DESC LIMIT %d,10',
            $search_column,
            mysqli_real_escape_string($db, $_GET['search_word']),
            $start
            );
          $tweets = mysqli_query($db, $sql) or die(mysqli_error($db));
        } else {
          $search_column = 't.`tweet`';

          $sql = sprintf('SELECT m.`nick_name`, m.`picture_path`, t.* FROM `members` m, `tweets` t WHERE m.`member_id`=t.`member_id` AND %s LIKE "%%%s%%" '.$extra.' ORDER BY t.`created` DESC LIMIT %d,10',
            $search_column,
            mysqli_real_escape_string($db, $_GET['search_word']),
            $start
            );
          $tweets = mysqli_query($db, $sql) or die(mysqli_error($db));

        }
      }
    } else {

      $sql = sprintf('SELECT m.`nick_name`, m.`picture_path`, t.* FROM `members` m, `tweets` t WHERE m.`member_id`=t.`member_id` '.$extra.' ORDER BY t.`created` DESC LIMIT %d,10',
        $start
        );
      $tweets = mysqli_query($db, $sql) or die(mysqli_error($db));

    }


    $reply_tweet = '';
    if (isset($_REQUEST['res'])) {
      $sql = sprintf('SELECT m.`nick_name`, t.`tweet` FROM `tweets` t, `members` m WHERE m.`member_id`=t.`member_id` AND t.`tweet_id`=%d',
        mysqli_real_escape_string($db, $_REQUEST['res']));
      $rec = mysqli_query($db, $sql) or die(mysqli_error($db));
      $table = mysqli_fetch_assoc($rec);
      $reply_tweet = '@' .$table['nick_name']. ' : ' .$table['tweet']. ' -> ';

    }

    if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'delete'){
      $sql = sprintf('DELETE FROM `tweets` WHERE `tweet_id`=%d',
                      mysqli_real_escape_string($db, $_REQUEST['id']));
      mysqli_query($db, $sql) or die(mysqli_error($db));

      header('location: index.php');
      exit();
    }

    $sql = sprintf('SELECT `tweet_id` FROM `likes` WHERE `member_id`=%d',
      mysqli_real_escape_string($db, $_SESSION['id'])
      );
    $rec = mysqli_query($db, $sql) or die(mysqli_error($db));
    $like_tweets = array();
    while($table = mysqli_fetch_assoc($rec)){
      $like_tweets[] = $table['tweet_id'];
    }


?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SeedSNS</title>

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="assets/css/form.css" rel="stylesheet">
    <link href="assets/css/timeline.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="index.php"><span class="strong-title"><i class="fa fa-twitter-square"></i> Seed SNS</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
                <li><a href="logout.php">ログアウト</a></li>
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-4 content-margin-top">
        <legend>ようこそ<?php echo $member['nick_name']; ?>さん！</legend>
        <form method="post" action="" class="form-horizontal" role="form">
            <!-- つぶやき -->
            <div class="form-group">
              <label class="col-sm-4 control-label">つぶやき</label>
              <div class="col-sm-8">
                <textarea name="tweet" cols="50" rows="5" class="form-control" placeholder="例：Hello World!"><?php echo $reply_tweet; ?></textarea>
                <input type="hidden" name="reply_tweet_id" value="<?php echo $_REQUEST['res']; ?>">
              </div>
            </div>
          <ul class="paging">
            <input type="submit" class="btn btn-info" value="つぶやく">
            <?php if ($page > 1): ?>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <li><a href="index.php?page=<?php echo $page - 1; ?>" class="btn btn-default">前</a></li>
            <?php else: ?>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <li>前</li>
            <?php endif; ?>
            <?php if($page < $maxPage): ?>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <li><a href="index.php?page=<?php echo $page + 1; ?>" class="btn btn-default">次</a></li>
            <?php else: ?>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <li>次</li>
            <?php endif; ?>
          </ul>
        </form>
      <legend>フォローしているユーザー   <a href='users.php' class="btn btn-xs btn-primary">他のユーザーを探す</a></legend>
      <?php $members = followMemberIdNames($_SESSION['id']); ?>
        <?php  if(count($members)): ?>
          <?php foreach($members as $k => $v): ?>
          <div class="msg">
            <img src="member_picture/<?php echo memberPicture($k); ?>" width="48" height="48">
            <p>
              <span class="name"> <?php echo $v; ?> </span>
                <a href="action.php?member_id=<?php echo $k; ?>&do=unfollow" class="btn btn-xs btn-default">フォローを外す</a>
            </p>
            <p class="day">
          </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>there is no members in the system!</p>
        <?php endif; ?>
      </div>


      <div class="col-md-8 content-margin-top">
      <form action="" method="get">
      <select name="search_column"'>
<?php columnSelect($search_column_select); ?>
      </select>
      <input type="text" name="search_word" value="<?php echo $search_word; ?>">
      <input type="submit" name="" value="検索">
      </form>


      <?php while($tweet = mysqli_fetch_assoc($tweets)): ?>

<?php

    $sql = sprintf('SELECT COUNT(*) AS cnt FROM `likes` WHERE `tweet_id`=%d',
      mysqli_real_escape_string($db, $tweet['tweet_id'])
      );
    $rec = mysqli_query($db, $sql) or die(mysqli_error($db));
    if ($table =mysqli_fetch_assoc($rec)){
      $like_cnt = $table['cnt'];
    } else {
      $like_cnt = 0;
    }

?>

        <div class="msg">
           <img src="member_picture/<?php echo $tweet['picture_path']; ?>" width="48" height="48">
          <p><?php echo $lastSelectTime ?>
            <?php echo makeLink($tweet['tweet']); ?><span class="name"> (<?php echo $tweet['nick_name']; ?>) </span>
            [<a href="index.php?res=<?php echo $tweet['tweet_id']; ?>">Re</a>]
          </p>
          <p class="day">
            <a href="view.php?tweet_id=<?php echo $tweet['tweet_id'] ?>">
              <?php echo $tweet['created']; ?>
            </a>
            <?php if($_SESSION['id'] == $tweet['member_id']): ?>
            [<a href="edit.php?tweet_id=<?php echo $tweet['tweet_id']; ?>" style="color: #00994C;">編集</a>]
            [<a href="delete.php?action=delete&tweet_id=<?php echo $tweet['tweet_id']; ?>" style="color: #F33;">削除</a>]
            <?php endif; ?>
            <?php if ($tweet['reply_tweet_id'] != '0'): ?>
              [<a href="view.php?tweet_id=<?php echo $tweet['reply_tweet_id']; ?>">返信元のつぶやき</a>]
            <?php endif; ?>
            <?php if(in_array($tweet['tweet_id'], $like_tweets)): ?>
            [<a href="likeaction.php?action=unlike&tweet_id=<?php echo $tweet['tweet_id']; ?>&member_id=<?php echo $_SESSION['id']; ?>" style="color: orange;"><i class="fa fa-star"></i><?php echo $like_cnt; ?></a>]
            <?php else: ?>
            [<a href="likeaction.php?action=like&tweet_id=<?php echo $tweet['tweet_id']; ?>&member_id=<?php echo $_SESSION['id']; ?>"><i class="fa fa-star"></i><?php echo $like_cnt; ?></a>]
            <?php endif; ?>
          </p>
        </div>
      <?php endwhile; ?>

      </div>

    </div>
  </div>



    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
