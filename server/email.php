<?php
chdir("common");
require_once("init.php");
$smarty->assign('user','Peter');
$smarty->assign('key','1222333');
$smarty->display('recoverPasswordEmail.tpl');
?>