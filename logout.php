<?php
session_start();
session_destroy();
header("Location: /@chan/index.php");
exit;
