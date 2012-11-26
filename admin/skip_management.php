<?php
	include_once '../lib/db.php';
	include_once '../lib/configure.php';
	
	$conn_admin = $GLOBALS['admin']->connection();

	//process the incoming data if the form is summitted
	if(isset($_POST['submitted']))
	{
	//delete existing stop words
		$query = 'delete from  '.SEARCH_SKIP;
		$GLOBALS['admin']->query($conn_admin,$query);
		
		$words = explode("\r\n", $_POST['admin']);
		$query = "insert into ".SEARCH_SKIP." (word) values (?)";
		
		$word = '';
		$stmt  = $conn_admin->prepare($query);
		$stmt->bind_param("s",$word);
		if(count($words)){
			$values = array();
			foreach ($words as $word){
				if (!trim($word)) continue;
				$word = addslashes($word);
				$stmt->execute();
			}
		}
	
	/*	echo "<script>";
	 *echo "alert('提交成功')；";
	 *	echo "</script>";
	*/
	}
	ob_start();
?>
<div id="admin_title"><?php echo "WORD MANAGEMENT"?></div>
<div id="admin_form">
	<form action="<?php echo $_SERVER[PHP_SELF];?>" method="post">
			<label for="admin">Include Addresses</label> <small>Enter your addresses to include in crawling,one address per line</small><br/>
			<textarea rows="100%" cols="100%" name="admin" id="admin"><?php
				$query = 'select word from '.SEARCH_SKIP.' order by word asc';
				$rows = $GLOBALS['admin']->query($conn_admin,$query);
				foreach ($rows as $row){
					echo stripslashes($row['word'])."\n";
				}
			?></textarea>
		<input type="submit" value="submit" name="admin_go" id="admin_go">
		<input type="hidden" name="submitted" value="1">
	</form>
</div>
<?php
	$GLOBALS['template']['content'] = ob_get_clean();
	include_once '../templates/managment_page.php';
?>