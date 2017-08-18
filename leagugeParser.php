<?php 
class BaseClass {
   function __construct() {
	   
       $this->index();
   }

	public function index(){

		$data 		= file_get_contents('leagugeXML.xml');

		$xml 		= simplexml_load_string($data); // Geting schedule data
		$xmlArray 	= $this->get_league($xml);

		$date 		= date('Y-m-d H:i:s');
		$con 		= mysqli_connect("localhost","root","root","betadrianDemo");
		
		foreach($xmlArray['League'] as $data){

			$query = "INSERT INTO `leagues` (`leaguge_title`,`league_id`, `sport_id`,  `created`, `updated`, `g_name`, `enabled`) VALUES ('".$data['league_name']."','". $data['league_id']."','". $data['league_sport_id']."','". $date."','".$date."','". $data['league_sport_name']."',"."1)";
			mysqli_query($con,$query);
			
		}
		echo "SUCCESS";
	}

	function get_league($xml)
	{
		$xmlArray =	$LeagueArray = [];

		$xmlArray['id'] 	= (int)$xml->id[0];
		$xmlArray['title'] 	= (string)$xml->title[0];
		$xmlArray['link'] 	= (string)$xml->link[0];
		$xmlArray['updated'] = (string)$xml->updated[0];
		$leagueIndex 		= 0;

		foreach($xml->league as $league)
		{
			$cleagueArr = [];
			$cleagueArr['league_id'] 			= $this->xml_attribute($league,'id');
			$cleagueArr['league_name'] 			= (string)$league->name;
			$cleagueArr['league_abbreviation'] 	= (string)$league->abbreviation;
			$cleagueArr['league_information'] 	= (string)$league->information;
			$cleagueArr['league_sport_id'] 		= $this->xml_attribute($league->sport,'id');
			$cleagueArr['league_sport_name'] 	= (string)$league->sport->name;
			$cleagueArr['league_sport_abbreviation'] = (string)$league->sport->abbreviation;
			
			$LeagueArray[$leagueIndex] = $cleagueArr;
			$leagueIndex++;
		}
		$xmlArray['League'] = $LeagueArray;
		return $xmlArray;
	}
	function xml_attribute($object, $attribute)
	{
		if(isset($object[$attribute]))
			return (string) $object[$attribute];
	}
}
$obj = new BaseClass();
?>
