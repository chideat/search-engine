<?php
function en_zh_skip($user,$conn,$string){
	$skip = 'select word from '.SEARCH_SKIP;
	$rows = $user->query($conn,$skip);
	if (!is_array($rows)) {
		header("Location:../401.php?m=check you skip words");
	}
	$result = array();
	foreach ($rows as $row){
		//$row['word'] = mb_convert_encoding($row['word'], 'UTF-8');
		$result[$row['word']] = true;
	}
	$all = null;
	$string= mb_convert_encoding($string,'UTF-8');
	$string = preg_replace('/[[:cntrl:]]/', ' ', $string);
	if(!empty($string)){
		$string = urlencode(trim(iconv("UTF-8","GBK//IGNORE",  $string)));
		$opts = array(
						"http"=>array(
						"method"=>"POST",
						"header"=>"Content-type: application/x-www-form-urlencoded\r\n",
						"Content-length:".strlen($string)."\r\n",
						"Cookie: foo=bar\r\n",
						"\r\n",
						"content" => $string,
						));
		$string = stream_context_create($opts);
		$all = file_get_contents("http://0.0.0.0:1985", false, $string);
		$all = iconv("GBK", "UTF-8//IGNORE", $all);
		$tmp = explode(' ', $all);
		$all = '';
		foreach ($tmp as $word){
			if (!isset($result[$word]) && trim($word)){
				$all[] = trim($word);
			}
		}
	}
	return $all;
}


function hi($user){
	$conn_user = $user->connection();	
	$query_hi = "select hi,author from ".SEARCH_HI." order by rand() limit 10;";//here add the hello
	$rows = $user ->query($conn_user,$query_hi);
	$hi = '';
	if (is_array($rows)){
		foreach ($rows as $row){
			if (strlen($row['hi']) < 180){
				$hi->hi = 	$row['hi'];
				$hi->author = $row['author'];		
			}
		}
	}
	if(!$hi){
		$hi-> hi = 'GOOD LUCK TO YOU';
		$hi-> author = '思考zhe';
	}
	return $hi;
}

function class_splite($class){
	//class must bigger than 1
	if ($class < 1 ){
		return null;
	}
	$n = -1;
	$tmp = $class;
	$classes = array();
	$result = array();
	do{
		while($tmp >= 1){
			$tmp = $tmp / 2.00;
			$n ++;
		}
		$tmp = $tmp - pow(2, $n);
		$classes['_'.$n] = true;
	}while ($tmp >= 1);
	$result[0] = count($classes);
	$result[1] = $classes; 
	return $result;
}

function getPageContent($url,$ch) {     

		 $pageinfo = array();     
        $pageinfo['content_type'] = '';     
        $pageinfo['charset'] = '';     
        $pageinfo['title'] = '';     
        $pageinfo['description'] = '';     
        $pageinfo['keywords'] = '';     
        $pageinfo['body'] = '';     
        $pageinfo['httpcode'] = 200;     
        $pageinfo['all'] = '';     
         
        curl_setopt($ch, CURLOPT_URL,$url);
        $curl_start = microtime(true);     
        $store = curl_exec ($ch);     
        

        
        $curl_time = microtime(true) - $curl_start; 

        if( curl_error($ch) ) {     
            $pageinfo['httpcode'] = 505;  //gate way error     
            echo 'Curl error: ' . curl_error($ch) ."\n";     
            return NULL;
        }

        $pageinfo['httpcode'] = curl_getinfo($ch,CURLINFO_HTTP_CODE);     
        $pageinfo['content_type'] = curl_getinfo($ch,CURLINFO_CONTENT_TYPE);     
        if(intval($pageinfo['httpcode']) <> 200 || !preg_match('@text/html@',curl_getinfo($ch,CURLINFO_CONTENT_TYPE) )) {
                return $pageinfo;
        }
        preg_match('/charset=([^\s\n\r]+)/i',curl_getinfo($ch,CURLINFO_CONTENT_TYPE),$matches); //从header 里取charset     
        $pageinfo['charset'] = isset($matches[1]) ? $matches[1]:'';

        //charset     
        if($pageinfo['charset'] == '' ) {    
            preg_match('@<meta.+charset=([\w\-]+)[^>]*>@i',$store,$matches);
            $pageinfo['charset'] = isset($matches[1]) ? $matches[1]:'';
        }
        $char = (!empty($pageinfo['charset']))?'UTF-8':'GBK';
 		$store = iconv($char, 'UTF-8//IGNORE', $store);
 		
 		/*        echo $store;
        exit();*/
 		
   //    $store= mb_convert_encoding($store,'UTF-8');
        $store = preg_replace("/<!--.*-->/i",' ',$store);
        //remove <style  </<style>
        //remove javascript     
        $store = preg_replace("/<script.*>(.*)<\/script>/smUi",' ',$store);
        //remove link      
        $store = preg_replace("/<link[^>]+>/smUi",' ',$store);     
        //remove <!--  -->     
        $store = preg_replace("/<style.*>(.*)<\/style>/smUi",' ',$store);
        //remove 中文空格     
        //$store = preg_replace("/　/",' ',$store);     
        //remove 标点符号     
     //   $store = preg_replace("/[[:punct:]]/",' ',$store);


        //desctiption    
        preg_match('@<meta\s+name=\"*description\"*\s+content\s*=\s*([^/>]+)/*>@i',$store,$matches);    
        $desc = isset($matches[1]) ? $matches[1]:'';
        $pageinfo['description'] = str_replace("\"", '',$desc);    
         

   
        preg_match('@<meta\s+name=\"*keywords\"*\s+content\s*=\s*([^/>]+)/*>@i',$store,$matches);    
        $keywords = isset($matches[1]) ? $matches[1]:''; 
       // $pageinfo['keywords'] = preg_replace('/[^a-zA-Z0-9]+/i',' ',$keywords);
		
        preg_match("/<title.*>(.*)<\/title>/smUi",$store, $matches);    
        $pageinfo['title'] = isset($matches[1]) ? $matches[1]:'';

        preg_match("/<body.*>(.*)<\/body>/smUi",$store, $matches);
        $pageinfo['body'] = isset($matches[1]) ? $matches[1]:'';
        $pageinfo['all'] = $store;
        
       // echo $store;
       // exit();
       // ECHO "where is here ".$pageinfo['title']." store end here ";
/*           echo      "content_type".$pageinfo['content_type']."<br/>"."charset".$pageinfo['charset']."<br/>"."title".$pageinfo['title'].'<br/>'."description".$pageinfo['description']."<br/>"."keywords".$pageinfo['keywords']   
        ."body".$pageinfo['body'].'end body<br/>';
        exit();*/
        return $pageinfo;
}   

function getHrefs($file){
	//distinguish the web page with the picture and music location
	preg_match_all('/<a\s+href\s*=\s*"?(http:\/\/)?([^>"]+)"?[^>]*\>/i',$file,$matches,PREG_SET_ORDER);
	$nums = count($matches);
	$hrefs = array();
	for($i=0;$i < $nums;$i ++){
//		if(preg_match('/*[.](png|jpeg|bmp|gif|jpeg)/i', $$matches[$i][2])){
			$hrefs[] = "http://".$matches[$i][2];
//		}
	}
	return $hrefs;
}

function getP($file,$other){
	$p = array();//'<p[^>]*>([^>]*)<\/p>',
	$patterns = array('<li[^>]*>([^>]*)<\/li>',
					   '<h\d[^>]*>([^>]*)<\/h\d>',
					   '<a[^>]+href[^>]>([^>]*)<\/a>',
					   '<title[^>]*>([^>]*)<\/title>');
	foreach ($patterns as $pattern){
		preg_match_all('/'.$pattern.'/i', $file, $matches,PREG_SET_ORDER);
		$nums = isset($matches)? count($matches):0;
		for($i = 0;$i < $nums;$i ++){
			$p[] = preg_replace('/<[^>]*>/',' ',$matches[$i][1]);//获取了标签之间的内容，这一定程度上能保证获取的是关键词  但这只是针对英文而言
		}
	}
	$p =$other.join(' ', $p);
	//$p = preg_replace('/<[^>]+>/i', ' ', $p);
	$p = preg_replace('/[[:cntrl:]]/',' ',$p);  //这里有问题，不能很好处理asp.net这些特殊字符串
	return $p;
}

//count the words  and find the class this string belongs to
function class_belong($string,$conn){
	if(!is_array($string)){
		return null;
	}
	//先取频率的60%
	$words = array_count_values($string);
	arsort($words);
	//以上获得相关数组的排序
	
	$num = count($words)*DEFAULT_PERCENTAGE;
	$i = 0;
	
	$query = 'select word,class from '.SEARCH_DICT;
	$rows = $GLOBALS['spider']->query($conn,$query);
	$query = '';
	if (!is_array($rows)){
		header("Location:../401.php?m=check you dict");
	}
	foreach ($rows as $row){
		$dict[$row['word']] = class_splite($row['class']);
	}
	//以上获得字典数组
	
	//here get the classed 频率
	$result_class = array();
	foreach ($words as $key=>$value){
		if ($i > $num){
			break;
		}
		if (isset($dict[$key])){
			foreach ($dict[$key] as $c){
				foreach ($c as $c_tmp){
					(!isset($result_class[$c_tmp]))?($result_class[$c_tmp] = 1):$result_class[$c_tmp]++;
				}
			}
			$i ++;
		}
		else{
			$insert[] = addslashes($key);
		}
	}
	//获取拆分字词频率的前60%，前60%中字典中没有的加入字典
	//and word 表中加入所有的字词
	
	//here对这些词中的等级进行统计，得前60%
	asort($result_class);
	$i = 0;
	$sum = 0;
	foreach ($result_class as $value) {
		if ($i > $num*DEFAULT_PERCENTAGE){
			break;
		}
		$sum += $value;
	}
	$query = "insert into ".SEARCH_DICT." (word,class) values (?,?)";
	$stmt  = $conn->prepare($query);
	$word = '';
	$stmt->bind_param("sd",$word,$sum);
	foreach ($insert as $word){
		$stmt->execute();
	}
	return $sum;
}
?>