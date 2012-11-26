<?php
	include_once '../lib/configure.php';
	include_once '../lib/function.php';
	include_once '../lib/db.php';
	
	mb_internal_encoding('UTF-8');
	//accept incoming search terms if the form has been submitted
	$words = array();

	$search = (isset($_POST['mygo']) && trim($_POST['mygo']) == "1") ? (isset($_POST['index_query']) ? trim($_POST['index_query']) : (isset($_POST['search_query']) ? trim($_POST['search_query']) : '')) : ''; 
	if(empty($search)){
		header("Location:../index.php");
	}
	
	ob_start();
?>
		
<div id="search_form">
	<form name="form" id="form" action="<?php htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
		<input name="search_query" id="search_query" maxlength="255" value="<?php echo htmlspecialchars($search);?>"></input>
		<input value="思考!" id="search_go" name="search_go" type="submit">
		<input type="hidden" name="mygo" class="mygo" value="1">
	</form>
</div>

<?php
		$GLOBALS['template']['title'] = $search;
		$conn_search = $GLOBALS['user']->connection();

		$en_zh_skip = en_zh_skip($GLOBALS['user'],$conn_search,$search);
		$en = isset($en_zh_skip['en'])?$en_zh_skip['en']:'';
		$zh = isset($en_zh_skip['zh'])?$en_zh_skip['zh']:'';

		$where = '';
		$query_user_search = "select distinct url_id,class,url,title,description from ".SEARCH_USER_VIEW." where ";
		for ($i=0;is_array($en[$i]); $i++){
			$where .= sprintf(' word like \'%s'.'%%\' or ',addslashes(strtolower(trim($en[1]))));
		}
		for ($i=0;is_array($zh[$i]);$i ++){
			if(empty($zh[$i])){
				continue;
			}
			$where .= sprintf(' word like \'%s'.'%%\' or ',addslashes(trim($zh[$i])));
		}

		$url_id = 0;
		$class = 0;
		$url = '';
		$title = '';
		$description = '';
		$query_user_search .= $where;
		$query_user_search = substr($query_user_search, 0,strlen($query_user_search) - 3);
		
		$rows = $GLOBALS['user']->query($conn_search,$query_user_search);
		
		$sort_url_id = array();
		$most_class = array();
		$info = array();
		$result = array();
		foreach($rows as $row){
			$result[$i] = class_splite($row['class']);
			$sort_url_id[$i][0] = $result[$i][0];
			$sort_url_id[$i][1] = array($row['url'],$row['title'],$row['description']);
			foreach ($result[$i][1] as $classes){
				isset($most_class['_'.$classes])?($most_class['_'.$classes] ++) : ($most_class['_'.$classes] = 0);
			}
		}
		//sort
		$num = count($sort_url_id);
		$exchange = false;
		for($i = 0;$i < $num-1;$i ++){
			$exchange = false;
			for($j = $i + 1;$j < $num ;$j ++){
				if($sort_url_id[$j][0] < $sort_url_id[$i][0]){
					$exchange = true;
					$tmp = $sort_url_id[$j];
					$sort_url_id[$j] = $sort_url_id[$i];
					$sort_url_id[$i] = $tmp;
				}
			}
			if ($exchange === false){
				break;
			}
		}

	echo '<div id="search_list"><ul>';
	echo '<p>查询 “<b>'.htmlspecialchars($search).'</b>” 结果'.$num.'个</p>';
	foreach($sort_url_id as $row)
	{
		echo '<li> <b> <a href="'.
			htmlspecialchars($row[1][0]).'">'.
			htmlspecialchars($row[1][1]).'</a></b> - '.
			htmlspecialchars($row[1][2]).'<br/><i>'.
			htmlspecialchars($row[1][0]).'</i></li><br/>'; 
	}
	echo '</ul></div><br/>';	

	$GLOBALS['template']['content'] = ob_get_clean();
	include '../templates/search_page.php';
?>