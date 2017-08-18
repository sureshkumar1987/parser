<?php 
require_once __DIR__.'/vendor/autoload.php';
$betdb 	= new Database();

class BaseClass {

	function __construct() {
		$this->index();
	}

	public function index(){
		global $betdb;
		$url="http://xml.donbest.com/v2/sport/?token=31SbUS-WvKpZfA-!";
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);    // get the url contents
		
		$data = curl_exec($ch); // execute curl request
		curl_close($ch);
		
		
		$xml = simplexml_load_string($data); // Geting schedule data
		$xmlArray = $this->get_Allsport($xml);
		
		$date = date('Y-m-d H:i:s');
		$con = mysqli_connect("localhost","root","root","betadrianDemo");
		
		foreach($xmlArray['sports'] as $data){
			
			if(!$this->check_duplicate($data['sport_id'])){

				$query = "INSERT INTO `sports` (`sport_title`,`sport_id`, `active`,  `created`, `updated`) 
				VALUES ('".$data['sport_name']."','". $data['sport_id']."','1','". $date."','".$date."')";
				$betdb->insert_data($query);
			}
		}
		echo "SUCCESS";
	}

	public function check_duplicate($sportID){
		global $betdb;
		$query = 'select id from sports where sport_id = '.$sportID;
		return $betdb->check_game($query);
	}


	function get_Allsport($xml) {

		$xmlArray 				= $sportArr = array();
		$xmlArray['id'] 		= (int)$xml->id[0];
		$xmlArray['title'] 		= (string)$xml->title[0];
		$xmlArray['link'] 		= (string)$xml->link[0];
		$xmlArray['updated'] 	= (string)$xml->updated[0];
		$sportIndex 			= 0;
		
		foreach($xml->sport as $sport)
		{
			$cSportArr 					= [];
			$cSportArr['sport_id'] 		= $this->xml_attribute($sport,'id');
			$cSportArr['sport_name'] 	= (string)$sport->name;
			$cSportArr['abbreviation'] 	= (string)$sport->abbreviation;
			$cSportArr['information'] 	= (string)$sport->information;
			$sportArr[$sportIndex] 		= $cSportArr;
			$sportIndex++;
		}
		$xmlArray['sports'] = $sportArr;
		return $xmlArray;
	}

	function xml_attribute($object, $attribute) {
		if(isset($object[$attribute]))
			return (string) $object[$attribute];
	}
}

$obj = new BaseClass();