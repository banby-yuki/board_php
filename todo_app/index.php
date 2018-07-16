<?php

require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/function.php');

 ?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>My Todos</title>
    <link rel="stylesheet" href="styles.css">
  </head>
  <body>
    <div id="container">
      <h1>Todos</h1>
      <form action="">
        <input type="text" id="new_todo" placeholder="What needs to be done?">
      </form>
      <ul>
        <li>
          <input type="checkbox">
          <span>Do something again!</span>
          <div class="delete_todo">×</div>
        </li>
        <li>
          <input type="checkbox"　checked>
          <span class="done">Do something again!</span>
          <div class="delete_todo">×</div>
        </li>
      </ul>
    </div>
  </body>
</html>
