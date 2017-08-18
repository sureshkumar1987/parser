<?php 
class BaseClass {
   function __construct() {
	   
       $this->index();
   }

	public function index()
	{
		$url="http://xml.donbest.com/v2/team/?token=-_!s!h-5Cp_ic9!a";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);    // get the url contents
		
		$data = curl_exec($ch); // execute curl request
		curl_close($ch);
		
		//~ $data = file_get_contents('teamsXML.xml');

		$xml = simplexml_load_string($data); // Geting schedule data
		$xmlArray = $this->get_Teams($xml);
		//~ echo"<pre>";
		//~ print_r($xmlArray);
		//~ echo"</pre>";
		$date = date('Y-m-d H:i:s');
		$con = mysqli_connect("localhost","root","root","betadrianDemo");
		foreach($xmlArray['Sports'] as $data){
			$leagueID = $data['league_id'];
			foreach($data['teams'] as $teamData){
				$query = "INSERT INTO `teams` (`team_id`,`team_name`, `abbreviation`,  `full_name`, `information`, `league_id`, `created`, `updated`) VALUES ('".$teamData['team_id']."','". $teamData['team_name']."','".$teamData['team_abbreviation']."','".$teamData['team_full_name']."','".$teamData['team_information']."',".$leagueID.",'". $date."','".$date."')";
				mysqli_query($con,$query);
			}
		}
		echo "SUCCESS";
	}

	function get_Teams($xml)
	{
		$xmlArray = array();
		$xmlArray['id'] = (int)$xml->id[0];
		$xmlArray['title'] = (string)$xml->title[0];
		$xmlArray['link'] = (string)$xml->link[0];
		$xmlArray['updated'] = (string)$xml->updated[0];
		$sportIndex = 0;
		foreach($xml->sport as $sport)
		{
			$cSportArr = $LeagueArray = array();
			$cSportArr['sport_id'] = $this->xml_attribute($sport,'id');
			$cSportArr['sport_name'] = $this->xml_attribute($sport,'name');
			$cSportArr['sport_link'] = (string)$sport->link;
			foreach($sport->league as $league)
			{
				$cLeague = $teamArr = array();
				$cLeague['league_id'] = $this->xml_attribute($league,'id');
				$cLeague['league_name'] = $this->xml_attribute($league,'name');
				$cLeague['league_linK'] = (string)$league->link;
				$teamIndex = 0;
				foreach($league->team as $team){
					$cTeamArr = array();
					$cTeamArr['team_id'] = $this->xml_attribute($team,'id');
					$cTeamArr['team_name'] = (string)$team->name;
					$cTeamArr['team_abbreviation'] = (string)$team->abbreviation;
					$cTeamArr['team_full_name'] = (string)$team->full_name;
					$cTeamArr['team_information'] = (string)$team->information;
					$teamArr[$teamIndex] = $cTeamArr;
					$teamIndex++;
				}
				$cLeague['teams'] = $teamArr;
				$LeagueArray[$leagueIndex] = $cLeague;
				$leagueIndex++;
			}
			$cSportArr['leagues'] = $LeagueArray;
			$LeagueArray[$sportIndex] = $cSportArr;
			$leagueIndex++;
		}
		$xmlArray['Sports'] = $LeagueArray;
		return $xmlArray;
	}
	function xml_attribute($object, $attribute)
	{
		if(isset($object[$attribute]))
			return (string) $object[$attribute];
	}
}
$obj = new BaseClass();
$obj->index();
?>
