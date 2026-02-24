<?php
session_start();
session_unset();
session_destroy();
header("Location: http://localhost/WebDev_Repository/pages/user/user_home.html");
exit();
?>