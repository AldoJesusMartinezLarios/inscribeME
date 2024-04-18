<?php

session_start();

session_destroy();

header("location: ../../primera_parte/administracion/index.html");
exit();

?>