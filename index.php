<?php

function regexp_replace($regexp, $text, $callback) {
  preg_match_all($regexp, $text, $matches, PREG_OFFSET_CAPTURE);

  $newstr = "";
  $lastidx = 0;
  foreach($matches[0] as $m) {
    $newstr .= substr($text, $lastidx, $m[1] - $lastidx);
    $newstr .= $callback($m);
    $lastidx = $m[1] + strlen($m[0]);
  }
  $newstr .= substr($text, $lastidx);
  return $newstr;
}

function fmt($text) {
  $text = regexp_replace('/\n/', $text, function($m) { return "<br>"; });

  $text = regexp_replace(
    '/((http|https)\:\/\/)[a-zA-Z0-9\.\/\?\:@\-_=#]+\.([a-zA-Z0-9\&\.\/\?\:@\-_=#%])*/',
    $text,
    function($m) {return sprintf("<a href=\"%s\">%s</a>", $m[0], $m[0]); });

  $text = regexp_replace('/\[code\].*\[\/code\]/U', $text,
    function($m) {
      return sprintf("<pre><code>%s</code></pre>",
        str_replace("<br>", "\n", substr($m[0], 6, strlen($m[0])-13)));
    });

  return $text;
}

function format_anchor($title) {
    $res = str_replace(" ", "", $title);
    $res = filter_var($res, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    return $res;
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

  <meta name="viewport" content="width=dev ice-width, initial-scale=1">
  <meta charset="UTF-8">

  <style>
    .smaller {
      max-width: 720px;
    }
    .muter {
      background-color: #e8e8e8;
    }
  </style>

  <script type="text/javascript">
  function deleteMsg(id) {
    var prompt = UIkit.modal.dialog(`
      <form class="uk-form-stacked">
        <div class="uk-modal-body">
          <label>Enter password to delete this message:</label>
          <input class="uk-input" type="password" autofocus>
          </div>
        <div class="uk-modal-footer uk-text-right">
          <button class="uk-button uk-button-default uk-modal-close" type="button">Cancel</button>
          <button class="uk-button uk-button-primary" type="submit">Delete message</button>
        </div>
      </form>`);

    var input = prompt.$el.find('input').val("<?php echo $_COOKIE["passcookie"]; ?>");

    prompt.$el.on('submit', 'form', e => {
      e.preventDefault();
      prompt.hide();

      var pass = input.val();
      if(pass !== null && pass !== "") {
        window.location.href = encodeURI("add.php?delete=1&id=" + id + "&pass=" + pass);
      }
    });
  }
  </script>
</head>
<body class="muter" uk-height-viewport>
  <div class="uk-section uk-section-primary uk-padding-small" uk-grid>
    <a href="index.php"><h2 class="uk-margin-left">RobotárnaWall</h2></a>
    <div class="uk-width-expand"></div>
    <a class="uk-button uk-button-default" href="add.php">Add message</a>
  </div>

  <div class="uk-container uk-container-small smaller uk-padding">
    <?php
      $db = new SQLite3("data.sqlite");
      $res = $db->query("SELECT id, text, title FROM messages ORDER BY id DESC;");
      while($row = $res->fetchArray()) {
    ?>
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-margin" id="<?php echo format_anchor($row['title']); ?>">
      <div class="uk-inline uk-width-1-1">
        <h3 class="uk-card-title"><a class="uk-link-text" href="#<?php echo format_anchor($row['title']); ?>"><?php echo $row["title"]; ?></a></h3>
        <div class="uk-position-right">
          <a href="add.php?id=<?php echo $row["id"]; ?>" uk-icon="icon: pencil"></a>
          <a href="javascript:void(0);" onclick="deleteMsg(<?php echo $row["id"]; ?>);" uk-icon="icon: trash"></a>
        </div>
      </div>
      <p><?php echo fmt($row["text"]); ?><p>
    </div>
    <?php
      }
      $res->finalize();
      $db->close();
    ?>
  </div>

</body>
</html>