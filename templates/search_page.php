<?php 
	// first choose one hi
	include_once '../lib/db.php';
	include_once '../lib/function.php';
	
	$hi = hi($user);
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<link rel="stylesheet" type="text/css" href= "../css/public.css"/>
<link rel="stylesheet" type="text/css" href= "../css/search.css"/>
<title> <?php echo $GLOBALS['template']['title'];?> </title>
</head>
<body>
<div id="head"> <?php echo $hi->hi.' ——'.$hi->author;//everyday one hi?> </div>
<div id="log">
	<ul>
		<li id="three"><a title="首页" href="../index.php">首页</a></li>
		<li id="one"><a title="顶一条" href="index.php">顶一条</a></li>
		<li id="two"><a title="登录" href="index.php">登录</a></li>
	</ul>
</div>
<?php 
	if (!empty($GLOBALS['template']['content'])){
		echo $GLOBALS['template']['content'];
	}
	else {
		include_once '../401.php';       ///////////////////////////////// here must put something
	}
?>
<div id="foot">
	<p><font size="1"> 思考zhe &copy; <?php echo date('Y');?> </font></p>
</div>
</body>
</html>