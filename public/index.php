<?php 
	include_once '../lib/db.php';
	include_once '../lib/configure.php';
	
	if (isset($_POST['hidden']) && trim($_POST['input_password']) && trim($_POST['input_username'])){
		$conn_admin = $GLOBALS['admin']->connection();
		$query = "select user_id,rights from ".SEARCH_USER." where name='".addslashes($_POST['input_username'])."' and password='".addslashes($_POST['input_password'])."'";
		$rows = $GLOBALS['admin']->query($conn_admin,$query);
		if (!is_null($rows)){
			$query = "update ".SEARCH_USER." set is_logon = 'Y' where name='".addslashes($_POST['input_username'])."'";
			$conn_admin->query($query);
			if (trim($rows[0]['rights']) == '1'){
				header("Location:../admin/index.php");
			}
			elseif(trim($rows[0]['rights'] == '0')){
				header("Location:../public/message.php?author='".$_POST['input_username']."'&id='".$rows[0]['user_id']."'");
			}
		}
		else{
			$flag = TRUE;
		}
	}
	else{
		$flag = FALSE;
	}
	
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href= "../css/public.css"/>
    <title>思考-登录</title>
</head>

<body class="loginform">
<h1 style="text-align:center;">欢迎使用 <bdo dir="ltr">思考zhe</bdo></h1>

<!-- Login form -->
<div id="log_form">
<form method="post" action="<?php echo $_SERVER[PHP_SELF];?>" name="login_form" target="_top" class="login">
    <fieldset>
    <legend>登录<a href="log.php"></a></legend>
        <div class="item">
            <label for="name">用户名：</label>
            <input type="text" name="input_username" id="input_username" value="<?php echo $_POST['input_username'];?>" size="24" class="textfield"/>
        </div>
        <div class="item">
            <label for="password">密码：</label>
            <input type="password" name="input_password" id="input_password" value="<?php echo $_POST['input_password'];?>" size="24" class="textfield" />
        </div>
        <input type="hidden" name="server" value="1" />    </fieldset>
    <fieldset class="tblFooters">
    	<?php 
	    	if($flag){
	    		echo '<span style="color:red;font-size:20px;"> Log Error </span>';
	    	}
 		?>
        <input value="登录" type="submit" id="input_go" />
        <input name="hidden" type="hidden"/>
     </fieldset>
	
</form>
</div>
<div id="foot">
	<p><font size="1"> 思考zhe &copy; <?php echo date('Y');?> </font></p>
</div>
    </body>
</html>