<html>
<head>
<title>Admin</title>
</head>
<body>
<h1 style="text-align:center;">Admin</h1>
<?php
	//link  lists
	
	$links = array(
					array('WORD MANAGEMENT','skip_management.php'),
					array('URL MANAGEMENT','url_management.php')
	);
	
	echo "<ul>";
	foreach ($links as $value) {
		echo '<li><a href="'.$value[1].'">'.$value[0].'</a></li>';
	}
	echo "</ul>";
?>
</body>
</html>