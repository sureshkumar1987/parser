<?php
error_reporting(1);

index();

function index()
{
	//~ $url="http://xml.donbest.com/v2/schedule/?token=h!7i!9aIPS-6g!X!";
	//~ $ch = curl_init();
	//~ curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	//~ curl_setopt($ch, CURLOPT_URL, $url);    // get the url contents
	//~ 
	//~ $data = curl_exec($ch); // execute curl request
	//~ curl_close($ch);
	
	$data = file_get_contents('xmlData.xml');

	$xml = simplexml_load_string($data); // Geting schedule data
	$xmlArray = get_sport($xml);

	//~ echo"<pre>";
	//~ print_r($xmlArray);
	//~ echo"</pre>";
	$con = mysqli_connect("localhost","root","root","betadrianDemo");
	$date = date('Y-m-d H:i:s');
	foreach($xmlArray as $data){
		foreach($data['game_league'] as $game_league){
			foreach($game_league['game_groups'] as $game_groups){
				foreach($game_groups['game_events'] as $game_events){
					foreach($game_events['participant'] as $participant){
						if($participant['participant_side'] == 'AWAY')
							$aParticipant = $participant['participant_team_id'];
						else
							$hParticipant = $participant['participant_team_id'];
						
					}
					$query = "INSERT INTO `events`(`event_id`, `event_name`, `date`, `game_id`, `league_id`, `group_id`, `particepent_home_id`, `particepent_away_id`,`location_id`, `created`, `updated`) values(".$game_events['id'].",'". $game_events['name']."', '".$game_events['date']."',". $data['game_id'].",". $game_league['league_id'].",". $game_groups['group_id'].",". $hParticipant." ," .$aParticipant.",".$game_events['location_id'].",'". $date."' ,'". $date."')";
					mysqli_query($con,$query);
				}
			}
		}
	}
}


function get_sport($xml)
{
	$xmlArray = array();
	$index = 0;
	foreach($xml->schedule->sport as $key=> $data){
		$gameArray = array();
		$gameArray['game_id'] = xml_attribute($data,'id');
		$gameArray['game_name'] = xml_attribute($data,'name');
		$gameArray['game_link'] = xml_attribute($data,'link');
		
		$gameLeague = get_league($data->league); // get League for sport
		$gameArray['game_league'] = $gameLeague;	
		
		$xmlArray[$index] = $gameArray;
		$index++;
	}
	return $xmlArray;
}

function get_league($data)
{
	$gameLeague = array();
	$lindex = 0;
	foreach($data as $lData){
			$cGameLeague = array();
			$cGameLeague['league_id'] = xml_attribute($lData,'id');
			$cGameLeague['league_name'] = xml_attribute($lData,'name');
			$cGameLeague['league_link'] = xml_attribute($lData,'link');
			$linesArr = getLines($lData->lines);   //  Get League Lines
			$cGameLeague['game_lines'] = $linesArr;
			
			$groupArr = get_groups($lData->group);    // get groups for League
			$cGameLeague['game_groups'] = $groupArr;
			
			$gameLeague[$lindex] = $cGameLeague;
			$lindex++;
		}
	return $gameLeague;
}

function get_groups($lData)
{
	$groupArr = array();
	$groupIndex = 0 ;
	foreach($lData as $group){
		$groups = array();
		$groups['group_id'] = xml_attribute($group,'id');
		$groups['group_name'] = xml_attribute($group,'name');
		
		$events = get_event($group->event); // Get Events for Groups
		$groups['game_events'] = $events;
		
		$groupArr[$groupIndex] = $groups;
		$groupIndex++;
	}
	return $groupArr;
}

function get_event($group)
{
	$events = array();
	$eventIndex = 0;
	foreach($group as $event){
		$cEvent = array();
		$cEvent['id'] = xml_attribute($event,'id');
		$cEvent['name'] = xml_attribute($event,'name');
		$cEvent['date'] = xml_attribute($event,'date');
		$cEvent['season'] = xml_attribute($event,'season');
		
		$cEvent['location_id'] = xml_attribute($event->location,'id');
		$cEvent['location_name'] = xml_attribute($event->location,'name');
		$cEvent['location_link'] = xml_attribute($event->location,'link');
		$cEvent['Event_current_line'] = xml_attribute($event->lines->current,'link');
		$cEvent['Event_open_line'] = xml_attribute($event->lines->opening,'link');
		$cEvent['Event_score'] = xml_attribute($event->score,'link');
		
		$participantArr = get_participant($event->participant);	 	// get participant for Event
		$cEvent['participant'] = $participantArr;
			
		
		$events[$eventIndex] = $cEvent;
		$eventIndex++;
	}
	return $events;
}

function get_participant($event)
{ 	$participantArr = array();
	$pIndex = 0;
	foreach($event as $participant){
		$cParticipant = array();
		$cParticipant['participant_rot'] = xml_attribute($participant,'rot');
		$cParticipant['participant_side'] = xml_attribute($participant,'side');
		$cParticipant['participant_team_id'] = xml_attribute($participant->team,'id');
		$cParticipant['participant_team_name'] = xml_attribute($participant->team,'name');
		$cParticipant['participant_team_link'] = xml_attribute($participant->team,'link');
		$participantArr[$pIndex] = $cParticipant;
		$pIndex++;
	}
	return $participantArr;
}

function getLines($lData) {

	$linesArr 	= array();
	$linesIndex = 0;
	
	foreach($lData as $lines){
			$cLines = array();
			$cLines['current_link'] = xml_attribute($lines->current,'link');
			$cLines['open_link'] = xml_attribute($lines->open,'link');
			$cLines['close_link'] = xml_attribute($lines->close,'link');
			$linesArr[$linesIndex] = $cLines;
			$linesIndex++;
		}
		return $linesArr;
}

function xml_attribute($object, $attribute) {

    if(isset($object[$attribute]))
        return (string) $object[$attribute];
}