<?php 
class BaseClass {
   
   function __construct() {
	   
       $this->index();
   }

	public function index()
	{
		$url="http://xml.donbest.com/v2/location/?token=-_!s!h-5Cp_ic9!a";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);    // get the url contents
		
		$data = curl_exec($ch); // execute curl request
		curl_close($ch);
		//$data = file_get_contents('leagugeXML.xml');

		$xml = simplexml_load_string($data); // Geting schedule data
		$xmlArray = $this->get_location($xml);
		
		$date = date('Y-m-d H:i:s');
		$con = mysqli_connect("192.168.1.135","root","root","betadrianDemo");
		foreach($xmlArray['Location'] as $data){
			$query = "INSERT INTO `location` (`location_id`,`location_name`, `description`, `abbreviation` , `stadium_type` , `seating_capacity` , `elevation` , `city_id` , `city_name` , `state` , `country` , `postal_code` ,`created`, `updated`) VALUES ('".$data['location_id']."','". $data['location_name']."','". $data['location_description']."','". $data['location_abbreviation']."','". $data['location_stadium_type']."','". $data['location_seating_capacity']."','". $data['location_elevation']."','". $data['city']."','". $data['city_name']."','". $data['state']."','". $data['country']."','". $data['postal_code']."','". $date."','".$date."')";
			mysqli_query($con,$query);
		}
		echo "SUCCESS";
	}

	function get_location($xml)
	{
		$xmlArray = $locationArray = array();
		$xmlArray['id'] = (int)$xml->id[0];
		$xmlArray['title'] = (string)$xml->title[0];
		$xmlArray['link'] = (string)$xml->link[0];
		$xmlArray['updated'] = (string)$xml->updated[0];
		$locationIndex = 0;
		foreach($xml->location as $location)
		{
			$cLocationArr = array();
			$cLocationArr['location_id'] = $this->xml_attribute($location,'id');
			$cLocationArr['location_name'] = (string)$location->name;
			$cLocationArr['location_description'] = (string)$location->description[0];
			$cLocationArr['location_abbreviation'] = (string)$location->abbreviation[0];
			$cLocationArr['location_stadium_type'] = (string)$location->stadium_type[0];
			$cLocationArr['location_surface_type'] = (string)$location->surface_type[0];
			$cLocationArr['location_seating_capacity'] = (int)$location->seating_capacity;
			$cLocationArr['location_elevation'] = (int)$location->elevation;
			$cLocationArr['city'] = $this->xml_attribute($location->city,'id');
			$cLocationArr['city_name'] = (string)$location->city->name;
			$cLocationArr['state'] = (string)$location->city->state;
			$cLocationArr['country'] = (string)$location->city->country;
			$cLocationArr['postal_code'] = (string)$location->city->postalCode;
			
			$locationArray[$locationIndex] = $cLocationArr;
			$locationIndex++;
		}
		$xmlArray['Location'] = $locationArray;
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
