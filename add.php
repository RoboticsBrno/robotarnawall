<?php
function check_pass($pass) {
  $hash = hash("sha256", "solnicka1984!" . $pass);
  return $hash === "5ae054f4c55ab800ee68a791a238ccbb169e470264da6ab9d4497ef94e91ebe7" ||
    $hash === "156119b3f0f348f059cf0d27e887d51be4f861592f2efda3611ddee6e2c6df50";
}

$dbname = "data.sqlite";
$id = "";
$error_message="";
if(isset($_GET["delete"]) && isset($_GET["pass"]) && isset($_GET["id"]) && is_numeric($_GET["id"])) {
  if(check_pass($_GET["pass"])) {
    setcookie("passcookie", $_GET["pass"]);
    $db = new SQLite3($dbname);
    $stmt = $db->prepare("DELETE FROM messages WHERE id=?;");
    $stmt->bindValue(1, $_GET["id"]);
    $stmt->execute();
    $stmt->close();
    $db->close();
    header('Location: index.php', true, 301);
    die();
  } else {
    $error_message = "Invalid password";
  }
} else if(isset($_POST["title"]) && isset($_POST["pass"])) {
  $title = $_POST["title"];
  $text = $_POST["text"];

  if(check_pass($_POST["pass"])) {
    setcookie("passcookie", $_POST["pass"]);
    $db = new SQLite3($dbname);
    if(isset($_POST["id"]) && is_numeric($_POST["id"])) {
      $stmt = $db->prepare("UPDATE messages SET text=?, title=? WHERE id=?;");
      $stmt->bindValue(1, $text);
      $stmt->bindValue(2, $title);
      $stmt->bindValue(3, $_POST["id"]);   
    } else {
      $stmt = $db->prepare("INSERT INTO messages (title, text) VALUES (?, ?);");
      $stmt->bindValue(1, $title);
      $stmt->bindValue(2, $text);
    }
    $stmt->execute();
    $stmt->close();
    $db->close();
    
    header('Location: index.php', true, 301);
    die();
  } else {
    $error_message = "Wrong password.";
  }
} else if(isset($_GET["id"])) {
  $id = $_GET["id"];
  $db = new SQLite3($dbname);
  $stmt = $db->prepare("SELECT text, title FROM messages WHERE id=?;");
  $stmt->bindValue(1, $id);
  $res = $stmt->execute();
  if($row = $res->fetchArray()) {
    $title = $row["title"];
    $text = $row["text"];
  }
  $res->finalize();
  $stmt->close();
  $db->close();
}

?>
<html>
<head>
  <title>RobotikaWall</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.25/css/uikit.min.css" />

  <!-- jQuery is required -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

  <!-- UIkit JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.25/js/uikit.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.25/js/uikit-icons.min.js"></script>

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta charset="UTF-8">
  <style>
    .smaller {
      max-width: 720px;
    }
    .muter {
      background-color: #e8e8e8;
    }
  </style>
</head>
<body class="muter" uk-height-viewport>
  <div class="uk-section uk-section-primary uk-padding-small" uk-grid>
    <a href="index.php"><h2 class="uk-margin-left">RobotárnaWall</h2></a>
    <div class="uk-width-expand"></div>
    <a class="uk-button uk-button-default" href="add.php">Add message</a>
  </div>

  <div class="uk-container uk-container-small smaller uk-padding">
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-margin">
      <h3 class="uk-card-title">Add a new message</h3>
      <div class="uk-text-danger uk-text-center uk-text-bold"><?php echo $error_message; ?></div>
      <form method="POST" class="uk-form-horizontal uk-margin-small">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="uk-margin">
          <label class="uk-form-label" for="form-horizontal-text">Title</label>
          <div class="uk-form-controls">
            <input class="uk-input" id="form-horizontal-text" name="title" type="text"
            placeholder="Title..." value="<?php echo $title; ?>" required>
          </div>
        </div>

        <div class="uk-margin">
          <div class="uk-form-label">Text</div>
          <div class="uk-form-controls uk-form-controls-text">
            <textarea class="uk-textarea" rows="5" name="text" placeholder="Text..."><?php echo $text; ?></textarea>
          </div>
        </div>

        <div class="uk-margin">
          <label class="uk-form-label uk-text-bold" for="form-horizontal-text">Password</label>
          <div class="uk-form-controls">
            <input class="uk-input" id="form-horizontal-text" name="pass" type="password"
            value="<?php echo $_COOKIE["passcookie"]; ?>" required>
          </div>
        </div>

        <div class="uk-margin">
          <div class="uk-form-controls">
            <button class="uk-button uk-button-primary">Save</button>
          </div>
        </div>

      </form>
    </div>
  </div>

</body>
</html>