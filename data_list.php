<?php
require_once __DIR__.'/vendor/autoload.php';

$betdb 	= new Database();

if ($_REQUEST['gameID']) {

	$query = 'SELECT * FROM`leagues` WHERE `sport_id` = ' . $_REQUEST['gameID'];
	$data = $betdb->query($query);


	if (isset($_REQUEST['leagueID']) && $_REQUEST['leagueID'] != '')
		$query = $query . ' and league_id = ' . $_REQUEST['leagueID'];

	$data 	= $betdb->query($query);
	$requestDataArray = array();
	$mIndex = 0;

	foreach ($data as $key => $row) {

		$requestDataArray [$mIndex]['League_title'] = $row['Leaguge_title'];

		$iQuery = 'SELECT s.sport_title, l.leaguge_title, e.* 
					FROM `events` as e 
						INNER JOIN `sports` as s ON e.game_id = s.sport_id 
						INNER JOIN `leagues` as l ON e.league_id = l. league_id 
						WHERE e.game_id = ' . $_REQUEST['gameID'] . ' and e.league_id = ' . $row['league_id'];

		$iData = $betdb->query($iQuery);

		$index = 0;
		$dataArray = array();

		foreach ($iData as $key => $iRow) {
			$dataArray[$index]['event_id'] 		= $iRow['event_id'];
			$dataArray[$index]['event_name'] 	= $iRow['event_name'];
			$dataArray[$index]['sport_title'] 	= $iRow['sport_title'];
			$dataArray[$index]['date'] 			= $iRow['date'];
			$index++;
		}

		$requestDataArray [$mIndex]['events'] = $dataArray;
		$mIndex++;
	}
}


$query = 'SELECT * FROM `sports` WHERE sport_id != 0';

$data = $betdb->query($query);

$finalArray = array();
$index = 0;
foreach ($data as $key => $row) {
	$finalArray[$index]['gameID'] 	= $row['sport_id'];
	$finalArray[$index]['gameName'] = $row['sport_title'];
	
	$lQuery = 'SELECT * FROM `leagues` WHERE sport_id = ' . $row['sport_id'];
	$lData 	= $betdb->query($lQuery);
	$lIndex = 0;

	foreach ($lData as $key => $lRow) {
		
		$leagueArray = array();
		$leagueArray['league_id'] = $lRow['league_id'];
		$leagueArray['league_title'] = $lRow['Leaguge_title'];
		$leagueData[$lIndex++] = $leagueArray;
	}
	$finalArray[$index++]['leagues'] = $leagueData;
}

?>
<html>
<head>
	<title>Betadrian Demo</title>
	<script src="JS/jquery.min.js"></script>
	<script type='text/javascript'>
            /*	
            $(document).ready(function(){
             
            $('.outerUl').mouseover(function(){
            $('.outerUl').removeClass('active');
            $(this).addClass('active');
            });
            $('.outerUl').mouseleave(function(){
            $('.outerUl').removeClass('active');
             
            });
        });
        */
    </script>
    <style type='text/css'>
    	.outer{
    		width:90%;
    		height:100%;
    		margin:0px;
    		padding:0px;
    	}
    	.left{
    		width:30%;
    		border-right:2px solid black;
    		margin:0px;
    		float:left;
    		min-height:100%
    	}
    	.right{
    		width:69%;
    		float:right;
    	}
    	.outerTable{
    		border:1px solid black;
    	}
    	.innerTable{
    		border:1px solid black;
    		width:100%
    	}
    	.innerUl{
    		display:none;
    	}
    	.active .innerUl{
    		display:block;
    	}
    	.outerUl {
    		width: 35%;
    	}
    	.innerUl {
    		width: 250%;
    	}
    </style>
</head>
<body>
	<div class='outer'>
		<div class='left'>
			<h2>Sports</h2>
			<hr/>
			<ul>
				<?php 
				foreach ($finalArray as $data) { ?>
				<li <?php
				if (isset($_REQUEST['gameID']) && $_REQUEST['gameID'] == $data['gameID']) {
					echo "class='active'";
				}
				?> >
				<?php 
				echo "<a href='?gameID=" . $data['gameID'] . "'>" . $data['gameName'] . "</a>";
				if (isset($data['leagues']) && !empty($data['leagues'])) {
					foreach ($data['leagues'] as $leagueData) {
						?>
						<ul>
							<li class='innerUl'>
								<?php 
								echo "<a href='?gameID=" . $data['gameID'] . "&leagueID=" . $leagueData['league_id'] . "'>" . $leagueData['league_title']; ?>
							</a>
						</li>
					</ul>
					<?php } ?>
				</li>
				<?php
			}
		}
		?>
	</ul>
</Div>
<div class='right'>
	<?php 
	if (isset($requestDataArray) && !empty($requestDataArray)) { ?>
	<h2>Events</h2>
	<hr/>
	<table class = 'outerTable' width = "100%">
		<?php 
		foreach ($requestDataArray as $fData) {
			?>
			<tr class='outerTable'>
				<Th>League Name: <?php echo $fData['League_title']; ?></Th>
				<Th></Th>
			</tr>
			<?php if (!empty($fData['events'])) { ?>
			<tr><th colspan="2">
				<Table class='innerTable'>
					<Tr>
						<th>Date</th>
						<th>Event</th>
						<th>Odds</th>
					</Tr>
					<?php foreach ($fData['events'] as $events) { ?> 
					<Tr>
						<td><?php echo $events['date']; ?></td>
						<td><?php echo $events['event_name']; ?></td>
						<td>--</td>
					</Tr><?php } ?>
				</Table></th></tr>
				<?php
			} else {
				echo "<tr><td colspan ='3' align='center' style='padding:10px; border-top:1px solid'>Currently this league has no events!</td></tr>";
			}
		}
		?>
	</table>
	<?php } else { ?>
	<h3>Betadrian - Demo</h3>
	<p>Please select sport / league from left section to see the events list</p>
	<?php } ?>
</div>
</div>
</body>
</html>