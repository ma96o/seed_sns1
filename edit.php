<?php
    session_start();
    require('join/dbconnect.php');

    if(empty($_REQUEST['tweet_id'])){
      header('location: index.php');
      exit();
    }

    $sql = sprintf('SELECT * FROM `tweets` WHERE `tweet_id`=%d',
      mysqli_real_escape_string($db, $_REQUEST['tweet_id']));
    $rec = mysqli_query($db, $sql) or die(mysqli_error());


    if(!empty($_POST)){
      $sql = sprintf('UPDATE `tweets` SET `tweet`="%s" WHERE `tweet_id`=%d',
        mysqli_real_escape_string($db, $_POST['tweet']),
        mysqli_real_escape_string($db, $_REQUEST['tweet_id'])
        );
      mysqli_query($db, $sql) or die(mysqli_error($db));

      header('location: index.php');
      exit();
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
      <?php if($tweet = mysqli_fetch_assoc($rec)): ?>
        <form method="post" action="" class="form-horizontal" role="form">
            <!-- つぶやき -->
            <div class="form-group">
              <label class="col-sm-4 control-label">つぶやき</label>
              <div class="col-sm-8">
                <textarea name="tweet" cols="50" rows="5" class="form-control" placeholder="例：Hello World!"><?php echo $tweet['tweet']; ?></textarea>
                <input type="hidden" name="reply_tweet_id" value="<?php echo $_REQUEST['res']; ?>">
              </div>
            </div>
          <ul class="paging">
            <input type="submit" class="btn btn-info" value="編集する">
          </ul>
        </form>
      <?php else: ?>
        <p>そのつぶやきは削除されたか、URLが間違っています</p>
      <?php endif; ?>

      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
