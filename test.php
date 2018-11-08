<?php session_start();//开启Session ?>
<?php require_once('config/global.config.php');?>
<?php require_once('class/extend.class.php');?>
<?php require_once('class/check.class.php');?>
<?php require_once('class/database.class.php');?>
<?php require_once('class/cache.class.php');?>
<?php require_once('class/api.interface.php');?>

<?php //开始加载APP ?>
<?php require_once('class/loadapp.class.php');?>
<?php AppLoadManager_Class::loadApp();?>

<?php //开始加载外部插件 ?>
<?php //require_once('plugin/simple_html_dom.php');?>
<?php require_once('plugin/querylist.class.php');?>
<?php require_once('plugin/AipSpeech.php');?>


<?php
//
$name = 'api.my.weather';
$type = 'getData';
$expired = 1000;
$top = 100;
$key = 'test';
$time = ExtendManager_Class::getTimestamp();
$vPre = $name.$type.$expired.$top.$key.$time;
$v = ExtendManager_Class::encryptValideData($vPre);
$param = [
	'name' => $name,
	'type' => $type,
	'expired' => $expired,
	'top' => $top,
	'key' => $key,
	'time' => $time,
	'value' =>$v,
	'city' => '济南'
];

?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta charset="utf-8">
</head>
<body>
<form action="index.php" method="POST">
	<textarea name="param"><?php echo json_encode($param);?></textarea>
	<input type="submit" name="">
</form>
</body>
</html>