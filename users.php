<?php
    session_start();

    require('function.php');
    require('join/dbconnect.php');

    $members = show_members();
    $followMemberId = followMemberId($_SESSION['id']);
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
     <!-- <div class="col-md-8 content-margin-top"> -->
       <h1>list of members</h1>


        <?php  if(count($members)): ?>
          <?php foreach($members as $k => $v): ?>
            <?php if($k != $_SESSION['id']): ?>
          <div class="msg">
            <img src="member_picture/<?php echo memberPicture($k); ?>" width="48" height="48">
            <p>
              <span class="name"> <?php echo $v; ?> </span>
              <?php if(in_array($k, $followMemberId)): ?>
                <a href="action.php?member_id=<?php echo $k; ?>&do=unfollow" class="btn btn-xs btn-default">フォローを外す</a>
              <?php else: ?>
                <a href="action.php?member_id=<?php echo $k; ?>&do=follow" class="btn btn-xs btn-primary">フォロー</a>
              <?php endif; ?>
            </p>

          </div>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php else: ?>
          <p>there is no members in the system!</p>
        <?php endif; ?>

      </div>
    </div>
  </div>


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
