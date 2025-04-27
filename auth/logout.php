<?php
session_start();
session_destroy();
header("Location: /healthy-habitat-network/index.php"); 
exit();
?>
