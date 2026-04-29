<?php
session_start();
session_unset();
session_destroy();

header("Location: /Impulso_Fitness/resources/views/auth/login.php");
exit;