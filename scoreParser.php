<?php 
class BaseClass {
   function __construct() {
	   
       $this->index();
   }

	public function index()
	{
		//~ $url="http://xml.donbest.com/v2/score/?token=-_!s!h-5Cp_ic9!a";
		//~ $ch = curl_init();
		//~ curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//~ curl_setopt($ch, CURLOPT_URL, $url);    // get the url contents
		//~ 
		//~ $data = curl_exec($ch); // execute curl request
		//~ curl_close($ch);
		//~ 
		$data = file_get_contents('scoreXML.xml');

		$xml = simplexml_load_string($data); // Geting schedule data
		$xmlArray = $this->get_score($xml);
		echo"<pre>";
		print_r($xmlArray);
		echo"</pre>";
	}

	function get_score($xml)
	{
		$xmlArray = $eventArr = array();
		$xmlArray['id'] = (int)$xml->id[0];
		$xmlArray['title'] = (string)$xml->title[0];
		$xmlArray['link'] = (string)$xml->link[0];
		$xmlArray['updated'] = (string)$xml->updated[0];
		$eventIndex = 0;
		foreach($xml->event as $event)
		{
			$cEventArr = $period = array();
			$cEventArr['event_id'] = $this->xml_attribute($event,'id');
			$cEventArr['league_id'] = $this->xml_attribute($event,'league_id');
			$cEventArr['away_rot'] = $this->xml_attribute($event,'away_rot');
			$cEventArr['home_rot'] = $this->xml_attribute($event,'home_rot');
			$cEventArr['current_away_score'] = $this->xml_attribute($event->current_score,'away_score');
			$cEventArr['current_home_score'] = $this->xml_attribute($event->current_score,'home_score');
			$cEventArr['current_description'] = $this->xml_attribute($event->current_score,'description');
			$cEventArr['current_time'] = $this->xml_attribute($event->current_score,'time');
			$cEventArr['current_period'] = $this->xml_attribute($event->current_score,'period');
			$cEventArr['current_period_id'] = $this->xml_attribute($event->current_score,'period_id');
			$cEventArr['current_away_score_ext'] = $this->xml_attribute($event->current_score,'away_score_ext');
			$cEventArr['current_home_score_ext'] = $this->xml_attribute($event->current_score,'home_score_ext');
			$periodIndex = 0;
			foreach($event->period_summary->period as $periodData)
			{
				$cPeriodArr = $score = array();
				$cPeriodArr['period_name'] = $this->xml_attribute($periodData,'name');
				$cPeriodArr['period_description'] = $this->xml_attribute($periodData,'description');
				$cPeriodArr['period_time'] = $this->xml_attribute($periodData,'time');
				$cPeriodArr['period_id'] = $this->xml_attribute($periodData,'period_id');
				$scoreIndex = 0;
				foreach($periodData->score as $scoreData)
				{
					$cScoreArr = array();
					$cScoreArr['period_score_rot'] = $this->xml_attribute($scoreData,'rot');
					$cScoreArr['period_score_value'] = $this->xml_attribute($scoreData,'value');
					$score[$scoreIndex] = $cScoreArr;
					$scoreIndex++;
				}
				$cPeriodArr['score'] = $score;
				$period[$periodIndex] = $cPeriodArr;
				$periodIndex++;
			}
			$cEventArr['period'] = $period;
			$eventArr[$eventIndex] = $cEventArr;
			$eventIndex++;
		}
		$xmlArray['events'] = $eventArr;
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
