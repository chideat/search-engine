#!/usr/bin/php
<?php
	mb_internal_encoding('UTF-8');
	//目标：跨语言 因而，在模式匹配中少用字母
	include_once 'lib/db.php';
	include_once 'lib/function.php';
	include_once 'lib/configure.php';

	$conn_spider = $GLOBALS['spider']->connection();
	
	$query = 'select word from '.SEARCH_SKIP;
	$result = $conn_spider->query($query);
	$num = $result->num_rows;
	$skip = array();
	if (!$num){
		//header("Location:../401.php?m=check you skip words1");
		die("please check your skip words");
	}
	for ($i = 0;$i < $num;$i ++){
		$row = $result->fetch_assoc();
		$skip[$row['word']] = true;
	}
	mysqli_free_result($result);
	//open url handle for downloading
	$ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);     
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);     
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);     
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);     
    curl_setopt($ch, CURLOPT_FILETIME, 1);     
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);     
    curl_setopt($ch, CURLOPT_HEADER, 1);             
    //curl_setopt($ch, CURLOPT_USERAGENT, 'Search Engine Indexer'); 
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
    	
	for($k = 1;$k <= 2*SPIDER_LOOP;$k++){
		$flag = ( $k%2 == 1) ? true : false;
		$query = 'select url from '.($flag ? SEARCH_AUTO_O_T:SEARCH_AUTO_T_O);
		//echo $query;
		$result = $conn_spider->query($query);
		$n = $result->num_rows;
		if (!$n){
			curl_close($ch);
			die("END.");
		}
		for ($i = 0;$i < $n;$i ++){
			$row = $result->fetch_assoc();
			echo "\nProcessing: ".$row['url']." ...\n";
		

			$page = getPageContent($row['url'],$ch);
			//extact the title
			if($page['title']){
				$title = $page['title'];				//$GLOBALS['words'][] = strtolower($title);getP中已经处理
			}
			else{
				$title = $row['url'];//use the file basename
			}
		
			//extract the description
			$description = 'No description provided';
			if ($page['description']){
				$description = $page['description'];
			}
	  	    //此步极可能网址已经存在
/*	  	    echo $page['body']." end ";*/
	  	//    exit();
	  	    $hrefs = getHrefs($page['body']);
			$num = count($hrefs);
		    if($num){
				$values = '';
				$query = "insert into ".($flag ? SEARCH_AUTO_T_O:SEARCH_AUTO_O_T)." (url) values ";
				for($j = 0;$j < $num;$j++){
					$values .= sprintf('("%s"), ',addslashes($hrefs[$j]));
				}
				$values = substr($values, 0,strlen($values) - 2);
				$query .= $values;
				$conn_spider->query($query);
			}
			//strip HTML tags out from the content
			//$file = strip_tags($file);
			$file =  getP($page['body'],$page['keywords']);  //此处获得的是各种文字的集合体
			if (!$file){
				continue;
			}
			//get the seprated words
			$tmp = '';
			$file = preg_replace('/[[:cntrl:]]/', ' ', $file);
			if(!empty($file)){
				$file = urlencode(trim(iconv("UTF-8","GBK//IGNORE",  $file)));
				$opts = array(
								"http"=>array(
								"method"=>"POST",
								"header"=>"Content-type: application/x-www-form-urlencoded\r\n",
								"Content-length:".strlen($file)."\r\n",
								"Cookie: foo=bar\r\n",
								"\r\n",
								"content" => $file,
								));
				$file = stream_context_create($opts);
				$all = file_get_contents("http://0.0.0.0:1985", false, $file);
				$all = iconv("GBK", "UTF-8//IGNORE", $all);
				$tmp = explode(' ', $all);
				$all = '';
				foreach ($tmp as $word){
					if (!isset($skip[$word]) && trim($word)){
						$all[] = trim($word);
					}
				}
			}

 			//create the fun to get the key words and give the class which class this web belongs to
			$class = class_belong($all, $conn_spider);
			
			//add the url to the index
			$query = sprintf('insert into %s (url,title,description,class) values ("%s","%s","%s","%d")',
					SEARCH_URL,addslashes($row['url']),addslashes($title),addslashes($description),addslashes($class));
			
			//retrieve the url's id
			$conn_spider->query($query);
			if ($conn_spider->errno <> 0){
				continue;
			}
			$url_id = $conn_spider->insert_id;
			
			//lowercase for comparisons
			foreach ($all as $word){
				$query = sprintf('select word_id from %s where word="%s"',SEARCH_WORD,addslashes($word));
				$result2 = $conn_spider->query($query);
				if ($conn_spider->errno <> 0){
					continue;
				}
				$num2 = $result2->num_rows;
				if($num2){
					$row = $result2->fetch_assoc();
					$word_id = $row['word_id'];
				}
				else{
					$query = sprintf('insert into %s (word,class) values ("%s","%d")',SEARCH_WORD,addslashes($word),$class);
					$word_id = $GLOBALS['spider']->query($conn_spider,$query,'insert');
				}
				//add index record//////////////////////////////////////////wrong/////////////////////////////////////////////////////////
				$query = sprintf('insert into %s (word_id,url_id) values (%d,%d)',SEARCH_INDEX,$word_id,$url_id);
				$conn_spider->query($query);
				mysqli_free_result($result2);
			}
			mysqli_free_result($result);
		}
		$query = 'insert into '.SEARCH_URL_BACKUP.' (url) select (url) from '.($flag ? SEARCH_AUTO_O_T:SEARCH_AUTO_T_O);
		$conn_spider->query($query);
		$query = $flag ? 'delete from '.SEARCH_AUTO_O_T : 'delete from '.SEARCH_AUTO_T_O;
		$conn_spider->query($query);
	}
		curl_close($ch);
		echo 'Indexing complete.'."\n";
?>