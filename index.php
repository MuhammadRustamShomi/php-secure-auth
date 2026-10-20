<?php
// Home page: send users to the right place.
require "functions.php";

redirect(empty($_SESSION["user_id"]) ? "login.php" : "dashboard.php");
