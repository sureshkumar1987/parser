<?php 
class BaseClass {
   function __construct() {
	   
       $this->index();
   }

	public function index()
	{
		$url="http://xml.donbest.com/v2/close/5/?token=31SbUS-WvKpZfA-!";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);    // get the url contents
		
		$data = curl_exec($ch); // execute curl request
		curl_close($ch);
		
		//~ $data = file_get_contents('lineXML.xml');

		$xml = simplexml_load_string($data); // Geting schedule data
		$xmlArray = $this->get_odds($xml);
		echo"<pre>";
		print_r($xmlArray);
		echo"</pre>";
	}

	function get_odds($xml)
	{
		$xmlArray = $eventArr = array();
		$xmlArray['id'] = (int)$xml->id[0];
		$xmlArray['title'] = (string)$xml->title[0];
		$xmlArray['link'] = (string)$xml->link[0];
		$xmlArray['updated'] = (string)$xml->updated[0];
		$eventIndex = 0;
		foreach($xml->event as $event)
		{
			$cEventArr = $lineArr = array();
			$cEventArr['event_id'] = $this->xml_attribute($event,'id');
			$cEventArr['event_date'] = $this->xml_attribute($event,'date');
			$lineIndex = 0;
			foreach($event->line as $line)
			{
				$cLineArr = array();
				$cLineArr['away_rot'] = $this->xml_attribute($line,'away_rot');
				$cLineArr['home_rot'] = $this->xml_attribute($line,'home_rot');
				$cLineArr['time'] = $this->xml_attribute($line,'time');
				$cLineArr['period_id'] = $this->xml_attribute($line,'period_id');
				$cLineArr['type'] = $this->xml_attribute($line,'type');
				$cLineArr['sportsbook'] = $this->xml_attribute($line,'sportsbook');
				$cLineArr['no_line'] = $this->xml_attribute($line,'no_line');
				$cLineArr['ps_away_spread'] = $this->xml_attribute($line->ps,'away_spread');
				$cLineArr['ps_away_price'] = $this->xml_attribute($line->ps,'away_price');
				$cLineArr['ps_home_spread'] = $this->xml_attribute($line->ps,'home_spread');
				$cLineArr['ps_home_price'] = $this->xml_attribute($line->ps,'home_price');
				$cLineArr['total'] = $this->xml_attribute($line->total,'total');
				$cLineArr['over_price'] = $this->xml_attribute($line->total,'over_price');
				$cLineArr['under_price'] = $this->xml_attribute($line->total,'under_price');
				$cLineArr['display_away'] = $this->xml_attribute($line->display,'away');
				$cLineArr['display_home'] = $this->xml_attribute($line->display,'home');
				$cLineArr['money_away'] = $this->xml_attribute($line->money,'away_money');
				$cLineArr['money_home'] = $this->xml_attribute($line->money,'home_money');
				$cLineArr['money_drzaw'] = $this->xml_attribute($line->money,'draw_money');
				$lineArr[$lineIndex] = $cLineArr;
				$lineIndex++;
			}
			$cEventArr['lines'] = $lineArr;
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
?>
