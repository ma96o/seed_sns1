<?php 

    session_start();

    if (!isset($_SESSION['join1'])) {
      header('location: index.php');
      exit();
    }



    require('dbconnect.php');

    if(!empty($_POST)) {

      $sql = sprintf('INSERT INTO `members` SET `nick_name`="%s", `email`="%s", `password`="%s", `picture_path`="%s", `created`=now()',
        mysqli_real_escape_string($db, $_SESSION['join1']['nick_name']),
        mysqli_real_escape_string($db, $_SESSION['join1']['email']),
        mysqli_real_escape_string($db, sha1($_SESSION['join1']['password'])),
        mysqli_real_escape_string($db, $_SESSION['join1']['picture_path']));
      mysqli_query($db, $sql) or die(mysqli_error($db));
      unset($_SESSION['join1']);

      header('location: thanks.php');
      exit();
    }



    // if (!empty($_POST)) {
    //   $sql = 'INSERT INTO `members` SET `nick_name` =?, `email` =?, `password` =?, `picture_path` = "", `created` = now()';

    //   $data[] = $_SESSION['join1']['nick_name'];
    //   $data[] = $_SESSION['join1']['email'];
    //   $data[] = $_SESSION['join1']['password'];
    //   // $data[] = $_SESSION['join1']['picture_path'];

    //   $stmt = $dbh->prepare($sql);
    //   $stmt->execute($data);

    //   // echo "<br>";
    //   // echo "<br>";
    //   // echo "<br>";

    //   // var_dump($data);
    //   // echo "<br>";
    //   // var_dump($_SESSION);

    //   header('Location: thanks.php');
    //   exit;

    // }


    // $nick_name = htmlspecialchars($_POST['nick_name']);
    // $email = htmlspecialchars($_POST['email']);
    // $password = htmlspecialchars($_POST['password']);
    // $picture_path = htmlspecialchars($_POST['picture_path']);
    // if (!empty($_POST)) {


    //   $sql = 'INSERT INTO `members` SET `nick_name` = ?, `email` = ?, `password` = ?, `picture_path` = ?, `created` = now()';

    //   $data[] = $_POST['nick_name'];
    //   $data[] = $_POST['email'];
    //   $data[] = $_POST['password'];
    //   $data[] = $_POST['picture_path'];

    //   $stmt = $dbh->prepare($sql);
    //   $stmt->execute($data);

    // }




    $dbh = null;
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SeedSNS</title>

    <!-- Bootstrap -->
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link href="../assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../assets/css/form.css" rel="stylesheet">
    <link href="../assets/css/timeline.css" rel="stylesheet">
    <link href="../assets/css/main.css" rel="stylesheet">
    <!--
      designフォルダ内では2つパスの位置を戻ってからcssにアクセスしていることに注意！
     -->


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
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-4 col-md-offset-4 content-margin-top">
        <form method="post" action="" class="form-horizontal" role="form">
          <input type="hidden" name="action" value="submit">
          <div class="well">ご登録内容をご確認ください。</div>
            <table class="table table-striped table-condensed">
              <tbody>
                <!-- 登録内容を表示 -->
                <tr>
                  <td><div class="text-center">ニックネーム</div></td>
                  <td><div class="text-center">
                    <?php echo htmlspecialchars($_SESSION['join1']['nick_name'], ENT_QUOTES, 'UTF-8'); ?>
                  </div></td>
                </tr>
                <tr>
                  <td><div class="text-center">メールアドレス</div></td>
                  <td><div class="text-center">
                    <?php echo htmlspecialchars($_SESSION['join1']['email'], ENT_QUOTES, 'UTF-8'); ?>
                  </div></td>
                </tr>
                <tr>
                  <td><div class="text-center">パスワード</div></td>
                  <td><div class="text-center">
                  <?php
                    $i = strlen($_SESSION['join1']['password']);
                    $pass = '●';
                    while ($i > 1) {
                      $pass .= '●';
                      $i--;
                    }

                    echo $pass;
                  ?>
                  </div></td>
                </tr>
                <tr>
                  <td><div class="text-center">プロフィール画像</div></td>
                  <td><div class="text-center"><img src="../member_picture/<?php echo $_SESSION['join1']['picture_path'];?>" width="100" height="100"></div></td>
                </tr>
              </tbody>
            </table>

            <a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a> | 
            <input type="submit" class="btn btn-default" value="会員登録">
          </div>
        </form>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
