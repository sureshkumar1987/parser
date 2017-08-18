<?php

class Database {
	
	public $betDb;
	private $db_user 	= 'root';
	private $db_password = 'root';
	private $database 	= 'betadrianDemo';
	private $host 		= 'localhost';

	function __construct(){
		$this->betDb = new PDO("mysql:host={$this->host};dbname={$this->database}", $this->db_user, $this->db_password);
	}

	public function query( $query, $debug = false ){
		
		try{
			
			$sqlobj  = $this->betDb->query($query);
			$sqlobj->setFetchMode(PDO::FETCH_ASSOC);			
			$data 	= $sqlobj->fetchAll();

			if($debug){
				echo $query.'<br><br>';
				$this->pr($data);
				echo '<br><br>';
			}
			return $data;

		}catch (PDOException $e) {
			die("Could not connect to the database $dbname :" . $e->getMessage());
		}
	}

	/**
	* @param Sport id
	* @return true/false
	* DESC: check is there game already with passed ID exists
	*/
	public function check_game($query){
		
		$sql = $this->betDb->query($query);
		$sql->setFetchMode(PDO::FETCH_ASSOC);
		$data 	= $sql->fetch();
		return $data;
	}
	
	/**
	* @param Sport id
	* @return true/false
	* DESC: check is there game already with passed ID exists
	*/
	public function insert_data($query){
		$sql = $this->betDb->prepare($query);
		$sql->execute();
		return $this->betDb->lastInsertId();
	}

	public function pr($data){
		echo '<pre>';
		print_r($data);
		echo '</pre>';
	}
}

$betdb = new Database();