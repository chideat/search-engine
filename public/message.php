<?php
	include_once '../lib/db.php';
	include_once '../lib/configure.php';
	
/*	if(!isset($_GET['author'])){
		header("Location:index.php");
	}*/
	$author = isset($_GET['author'])?$_GET['author']:'网友';
	$id = isset($_GET['id'])?$_GET['id']:'2';
	if (isset($_POST['hidden']) && trim($_POST['message']) && trim($_POST['author'])){
		$conn_user=$GLOBALS['user']->connection();
		$query = sprintf('insert into '.SEARCH_HI.' (hi,author,creater) values("'.$_POST['message'].'","'.$author.'","'.$id.'")');
		$GLOBALS['user']->query($conn_user,$query,'insert');
		$flag = true;
	}
	else{
		$flag = false;
	}
	ob_start();
?>
<div id="message_form">
	<div id="title"><h1>Happy Sending Messages</h1></div>
	<form action="<?php echo $_SERVER[PHP_SELF];?>" method="post">
		<div id="input_message"><label for="message">想法：</label><input name="message" id="message" type="text" maxlength="150"></div>
		<div id="input_author"><label for="author">作者：</label><input name="author" id="author" type="text" maxlength="30"></div>
		<input type="submit" name="message_go" id="message_go" value="快点">
		<input type="hidden" name="hidden" value="hidden">
		<input type="hidden" name="author" value="<?php echo $author;?>">
		<input type="hidden" name="id" value="<?php echo $id;?>">
	</form>
	<?php 
		if($flag == true){
			echo '<div id="succeed"> <span style="color:red;font-size:20px;"> Succeed </span></div>';
		}
	?>
</div>
<?php 
	$GLOBALS['template']['content'] = ob_get_clean();
	include_once '../templates/search_page.php';
?>


