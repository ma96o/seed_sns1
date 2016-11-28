<?php  
    session_start();
    require('join/dbconnect.php');
    include_once('function.php');

    if (empty($_REQUEST['tweet_id'])){
      header('location: index.php');
      exit();
    }

      $followMemberId = followMemberId($_SESSION['id']);

      $tweet_id = $_REQUEST['tweet_id'];

      $sql = sprintf('SELECT m.`picture_path`, m.`nick_name`, t.* FROM `members` m, `tweets` t WHERE m.`member_id`=t.`member_id` AND t.`tweet_id`=%d',
        mysqli_real_escape_string($db, $tweet_id));
      $rec = mysqli_query($db, $sql) or die(mysqli_error($db));

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
      <div class="col-md-4 col-md-offset-4 content-margin-top">
      <?php if($table = mysqli_fetch_assoc($rec)): ?>

        <?php if($table['reply_tweet_id'] > 0): ?>
          <?php
              $sql = sprintf('SELECT m.`picture_path`, m.`nick_name`, t.* FROM `members` m, `tweets` t WHERE m.`member_id`=t.`member_id` AND t.`tweet_id`=%d',
              mysqli_real_escape_string($db, $table['reply_tweet_id']));
              $recRe = mysqli_query($db, $sql) or die(mysqli_error($db));
              if($tableRe = mysqli_fetch_assoc($recRe)):
          ?>
        <div class="msg">
          <img src="member_picture/<?php echo $tableRe['picture_path']; ?>" width="100" height="100">
          <p>投稿者 : <span class="name"><?php echo $tableRe['nick_name']; ?></span>
          <?php if($tableRe['member_id'] != $_SESSION['id']): ?>
            <?php if(in_array($tableRe['member_id'], $followMemberId)): ?>
              <a href="action.php?member_id=<?php echo $tableRe['member_id']; ?>&do=unfollow" class="btn btn-xs btn-default">フォローを外す</a>
            <?php else: ?>
              <a href="action.php?member_id=<?php echo $tableRe['member_id']; ?>&do=follow" class="btn btn-xs btn-primary">フォロー</a>
            <?php endif; ?>
          <?php endif; ?>
          </p>
          <p>
            つぶやき : <br>
            <?php echo $tableRe['tweet'] ?>
          </p>
          <p class="day">
            <?php echo $tableRe['created']; ?>
            <?php if($_SESSION['id'] == $tableRe['member_id']): ?>
            [<a href="edit.php?tweet_id=<?php echo $tableRe['tweet_id']; ?>" style="color: #00994C;">編集</a>]
            [<a href="delete.php?action=delete&tweet_id=<?php echo $tableRe['tweet_id']; ?>" style="color: #F33;">削除</a>]
            <?php endif; ?>
          </p>
        </div>
        <?php endif; ?>
      <?php endif; ?>
        <div class="msg">
          <img src="member_picture/<?php echo $table['picture_path']; ?>" width="100" height="100">
          <p>投稿者 : <span class="name"><?php echo $table['nick_name']; ?></span>
          <?php if($table['member_id'] != $_SESSION['id']): ?>
            <?php if(in_array($table['member_id'], $followMemberId)): ?>
              <a href="action.php?member_id=<?php echo $table['member_id']; ?>&do=unfollow" class="btn btn-xs btn-default">フォローを外す</a>
            <?php else: ?>
              <a href="action.php?member_id=<?php echo $table['member_id']; ?>&do=follow" class="btn btn-xs btn-primary">フォロー</a>
            <?php endif; ?>
          <?php endif; ?>
          </p>
          <p>
            つぶやき : <br>
            <?php echo $table['tweet'] ?>
          </p>
          <p class="day">
            <?php echo $table['created']; ?>
            <?php if($_SESSION['id'] == $table['member_id']): ?>
            [<a href="edit.php?tweet_id=<?php echo $table['tweet_id']; ?>" style="color: #00994C;">編集</a>]
            [<a href="delete.php?action=delete&tweet_id=<?php echo $table['tweet_id']; ?>" style="color: #F33;">削除</a>]
            <?php endif; ?>
          </p>
        </div>
        <?php else: ?>
          <p>そのつぶやきは削除されたか、URLが間違っています。</p>
        <?php endif; ?>
        <a href="index.php">&laquo;&nbsp;一覧へ戻る</a>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
