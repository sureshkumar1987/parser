<!DOCTYPE html>
<html>
<head>
	<title>Betadrian Parser Demo</title>
	<style type="text/css">
		body{
			padding: 10px;
			margin: 10px;
			background: #ddd;
			font-family: arial;
		}
		.wrapper{
			width: 100%;
			min-height: 100%;
			float: left;
		}
		.container {
			min-height: 500px;
			background: #fff;
			padding: 10px;
		}
		.menu {
			float: left;
			width: 97%;
			background: #eeee;
			border: 1px solid #eee;
		}
		li {
			list-style: none;
			display: inline-block;
			padding: 17px;
		}
		li a {
			text-decoration: none;
			color: #000;
			font-weight: bold;
			
		}
		.content h3{
			text-align: center;
		}
	</style>
</head>
<body>
	<div class="wrapper">
		<div class="container">
			
			<div class = "listing">
				<ul class = "menu">
					<li><a href = "data_list.php">List Data</a></li>
					<li><a href = "parse.php">Parse Data</a></li>
				</ul>
				<div class="content">
					<h3>Welcome to Betadrian</h3>
				</div>
			</div>
		</div>
	</div>
</body>
</html>