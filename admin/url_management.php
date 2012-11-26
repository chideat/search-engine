<?php
	include_once '../lib/db.php';
	include_once '../lib/configure.php';
	$conn_admin = $GLOBALS['admin']->connection();

	//process the incoming data if the form is summitted
	if(isset($_POST['submitted']))
	{
		//delete existing address
		$query = 'delete from '.SEARCH_AUTO_O_T;
		$GLOBALS['admin']->query($conn_admin,$query);
		$query = 'delete from '.SEARCH_AUTO_T_O;
		$GLOBALS['admin']->query($conn_admin,$query);
		$query = 'delete from '.SEARCH_URL_BACKUP;
		$GLOBALS['admin']->query($conn_admin,$query);
		//add addresses list to database
		$addresses = explode("\r\n",$_POST['admin']);
		if(count($addresses))
		{
			$query = "insert into ".SEARCH_URL_BACKUP." (url) values (?)";
			$url = '';
			$stmt  = $conn_admin->prepare($query);
			$stmt->bind_param("s",$url);
			foreach ($addresses as $address)
			{
				$url = addslashes($address);
				if (!trim($url)) continue;
				$stmt->execute();
			}
			$query = 'insert into '.SEARCH_AUTO_O_T.' (url) select url from '.SEARCH_URL_BACKUP;
			$GLOBALS['admin']->query($conn_admin,$query,"insert");
		}
	}
	ob_start();
?>
<div id="admin_title"><?php echo "URL MANAGENMENT"?></div>
<div id="admin_form">
	<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
			<label for="admin">Include Addresses</label> <small>Enter your addresses to include in crawling,one address per line</small><br/>
			<textarea rows="100%" cols="100%" name="admin" id="admin"><?php
				$query = 'select url from '.SEARCH_URL_BACKUP.' order by url asc';
				$rows = $GLOBALS['admin']->query($conn_admin,$query);
				foreach ($rows as $row)
				{
					echo stripcslashes($row['url'])."\n";
				}
				$GLOBALS['admin']->close($conn_admin);
			?></textarea>
		<input type="submit" value="submit" name="admin_go" id="admin_go">
		<input type="hidden" name="submitted" value="1">
	</form>
</div>
<?php
	$GLOBALS['template']['content'] = ob_get_clean();
	include_once '../templates/managment_page.php';
?>