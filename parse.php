<!DOCTYPE html>
<html>
<head>
	<title>Betadrian Parser Demo</title>
	<script src="JS/jquery.min.js"></script>
	<style type="text/css">
		body{padding: 10px;margin: 10px;background: #ddd;font-family: arial;}
		.wrapper{width: 100%;min-height: 100%;float: left;}
		.container {min-height: 500px;background: #fff;padding: 10px;}
		.menu {float: left;width: 97%;background: #eee;border: 1px solid #eee;}
		li {list-style: none;display: inline-block;	padding: 17px;}
		li a {text-decoration: none;color: #000;font-weight: bold;}
		.content h3{text-align: left;}
		.form-wrapper{
			width: 50%;
			margin-left: 200px;
			float: left;
			min-height: 300px;
			background: #eee;
			padding: 3px 47px;
		}
		.games{
			width: 335px;
			border: 1px solid #bbb;
			height: 34px;
			line-height: 20px;
			background: #fff;
		}
		.button{
			border: 1px solid #bbb;
			padding: 9px;
			background: #fff;
			width: 100px;
			font-weight: bold;
			text-align: center;
			margin-top: 37px;
			margin-left: 213px;
			cursor: pointer;
		}
		.button.reset{
			margin-left: 5px;
		}
	</style>
	<script type="text/javascript">
		$(document).ready(function(){

			$('#parse-form').on('submit',function(){
				
				game_id = $('.games').val();

				if(game_id){
					$.ajax({
						url: 'parse_game.php',
						type:'POST',
						data:{'game_id':game_id},
						beforeSend:function(){
							console.log('parsing data');
						},
						success:function(response){
							console.log(response);
						}
					});
				}
				return false;	
			});
		});
	</script>
</head>
<body>
	<?php 
	require_once __DIR__.'/vendor/autoload.php';
	$betdb 	= new Database();
	$games = $betdb->query('select sport_id, sport_title from sports where active = 1 and (sport_title !="Unkown" and sport_title != "Other")');

	?>

	<div class="wrapper">
		<div class="container">
			
			<div class = "listing">
				<ul class = "menu">
					<li><a href = "data_list.php">List Data</a></li>
					<li><a href = "parse.php">Parse Data</a></li>
				</ul>
				<div class="content">
					
					<div align="center" class ="form-wrapper">
						<form action ="" method="post" id ='parse-form'>
							<table width="100%">
								<tr>
									<th colspan="2">
										<h3>Parse games data</h3>
									</th>
								</tr>
								<tr>
									<td colspan="2">&nbsp;</td>
								</tr>
								<tr>
									<td width="210px;">
										<strong>Select game:</strong>
									</td>
									<td>
										<select name = "games" class="games">
											<option>Select Game</option>
											<?php 
											foreach ($games as $key => $game) {
												echo "<option value='{$game['sport_id']}'>{$game['sport_title']}</option>";
											}
											?>
										</select>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<input class='button' type="submit" value="Submit">
										<input class='button reset' type="reset" value="Reset">
									</td>
								</tr>
							</table>	
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>