<?php
class PDOUtils implements iDBOUtils{
	private static $host;
	private static $username;
	private static $password;
	private static $dbname;
	private static $encoding;
	
	private static $_instance;
	
	private function __construct($_host='',$_username='',$_password='',$_dbname='',$_encoding='') {
		self::$host = ($_host ? $_host : '127.0.0.1');
		self::$username = ($_username ? $_username : 'root');
		self::$password = ($_password ? $_password : 'root');
		self::$dbname = ($_dbname ? $_dbname : exit('No select database.'));
		self::$encoding = ($_encoding ? $_encoding : 'utf8');
	}
	private function __clone(){
		//私有化克隆方法,防止克隆
	}
	public static function getInstance(){
		if(self::$_instance === null){
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	private static function link(){
		$link = new PDO('mysql:host='.self::$host.';dbname='.self::$dbname,self::$username,self::$password);
		$link -> query("set names ".self::$encoding);
		return $link;
	}
	public function queryTotal($table_name,$sql_column="*"){
		$link = self::link();
		$sql="select {$sql_column} from {$table_name}";
		$stmt = $link -> prepare($sql);
		$stmt -> execute();
		$totalRows = $stmt -> rowCount();
		self::close($link,$stmt);
		return $totalRows;
	}
	public function queryOnce($sql,$bind_param=array(),$fetch_style=PDO::FETCH_BOTH){
		/**
		*成功时返回查询结果(一维数组)， 或者在失败时返回 FALSE. 
		*/
		$link = self::link();
		$stmt = $link -> prepare($sql);
		$stmt -> execute($bind_param);
		$rows = $stmt -> fetch($fetch_style);

		self::close($link,$stmt);
		return $rows;
	}
	public function query($sql,$bind_param=array(),$fetch_style=PDO::FETCH_BOTH){
		/**
		*成功时返回查询结果(多维数组)， 或者在失败时返回 FALSE. 
		*/
		$link = self::link();
		$stmt = $link -> prepare($sql);
		$stmt -> execute($bind_param);

		while($rows = $stmt -> fetch($fetch_style)){
			$rs[] = $rows;
		}
		self::close($link,$stmt);
		return empty($rs) ? false : $rs;
	}
	public function operate($sql,$bind_param=array()){
		/**
		*成功时返回 TRUE， 或者在失败时返回 FALSE. 
		*发送delete删除记录前先查询，否则删除不存在的记录时会返回true
		*/
		$link = self::link();
		$stmt = $link -> prepare($sql);
		$bool = $stmt -> execute($bind_param);
		/*
		if(DEBUG){
			echo mysql_error(); //common.inc.php定义的DEBUG常量
		}
		*/
		self::close($link,$stmt);
		return $bool;
	}
	public function operateLastId($sql,$bind_param=array()){
		$link = self::link();
		$stmt = $link -> prepare($sql);
		$bool = $stmt -> execute($bind_param);

		self::close($link,$stmt);
		return $bool ? $link -> lastInsertId() : false; 
	}
	public function queryRollBack($multisqls){
		/**
		*可以发送多条插入，更新，删除语句，但是不能用预处理绑定参数，只能将参数放在sql语句中
		*成功时返回 TRUE， 或者在失败时返回 FALSE. 
		*/
		try{
			$link = self::link();
			$link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$link -> beginTransaction();
			$i = 0;
			foreach($multisqls as $sql){
				$stmt = $link -> exec($sql);
				$i += $stmt;
			}
			return $i < count($multisqls) ? $link -> rollBack() : $link -> commit();
		}catch(Exception $e){
			$link -> rollBack();
			echo $e -> getMessage();
		}
	}
	private static function close($link,$stmt){
		$link = null;
		$stmt = null;
	}
}
/*
$pdo = PDOUtils::getInstance();
$multisqls = array("insert into t_note(name,cont,fromname) values('xzj','hi guys','kaka')",
					"update t_demo set aa = 3 where id = 2");
//$rs = $pdo -> queryRollBack($multisqls);
//$rs = $pdo -> operate("update t_cont set title=? where id=?",array('afdsdfe',9));
$rs = $pdo -> operate("insert into t_cont(title,cont) values(?,?)",array('xsfj','somsdfse...'));

echo "<pre>";
var_dump($rs);
echo "</pre>";
*/
?>