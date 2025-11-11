<?php
$STORE = @$tpl['store'];
$FORM = @$tpl['form']['passenger'];
$index = pjObject::escapeString($_GET['index']);
$title_arr = pjUtil::getTitles();
$name_titles = __('personal_titles', true, false);
include_once 'elements/passenger.php';
?>