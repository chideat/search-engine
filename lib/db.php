<?php 
class DB{
	private $host;
	private $user;
	private $password;
	private $db;
	private $class;
	function __construct($host,$user,$password,$db,$class){
		$this->host = $host;
		$this->user = $user;
		$this->password = $password;
		$this->db = $db;
		$this->class = $class;
	}
	public function connection(){
		@ $conn = new mysqli($this->host,$this->user,$this->password,$this->db);
		if (mysqli_connect_errno()) 
		{
			if($conn->errno <> 0){
				header("Location:../401.php?m=error : mysql connection ".$conn->errno);
			}
		}
		$conn->query("set names utf8");
		return $conn;
	}
	private function select($result){
		if(!$result->num_rows){
			return NULL;//no matched rows
		}
		$rows = array();
		for ($i = 0;$i < $result->num_rows;$i ++){
				$rows[$i] = $result->fetch_assoc();
		}
		return $rows;
	}
	private function insert($conn){
		$result['insert_id'] = $conn->insert_id;
		return $result;
	}
	public function query($conn,$query,$option = 'select'){
		$result = $conn->query($query);
		if($conn->errno <> 0){
			echo $query;
			die("mysql error.");
			//header("Location:../401.php?m=error : mysql query ".$conn->errno.$query);
		}
		switch ($option){
			case 'select':return $this->select($result);break;
			case 'insert':return $this->insert($conn);break;
			default:return null;
		}
	}
	public function close($conn){
		$conn->close();
		return true;
	}
}

	//includes the account of all the users
	$classes = array('user','spider','admin');
	$cf['user'] = array(
							'host' => 'localhost',
							'user' => 'search_user',
							'password' => 'search_user',
							'db' => 'search',
							'class' => $classes[0]
						   );
	
	$cf['spider'] = array(
							'host' => 'localhost',
							'user' => 'search_spider',
							'password' => 'search_spider',
							'db' => 'search',
							'class' => $classes[1]
							);
	
	$cf['admin'] = array(
							'host' => 'localhost',
							'user' => 'search_admin',
							'password' => 'search_admin',
							'db' => 'search',
							'class' => $classes[2]
							);
		//connection
	$GLOBALS['user'] = new DB(
					$cf['user']['host'],
					$cf['user']['user'],
					$cf['user']['password'],
					$cf['user']['db'],
					$cf['user']['class']
					);
	$GLOBALS['spider'] = new DB(
					$cf['spider']['host'],
					$cf['spider']['user'],
					$cf['spider']['password'],
					$cf['spider']['db'],
					$cf['spider']['class']
					);
	$GLOBALS['admin'] = new DB(
					$cf['admin']['host'],
					$cf['admin']['user'],
					$cf['admin']['password'],
					$cf['admin']['db'],
					$cf['admin']['class']
					);
?>
	