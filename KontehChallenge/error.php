
<html>
<head>
<title>KONTEH - error</title>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="css/bootstrap.min.css">
<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="turn/jquery-ui.min.js"></script>

<script type="text/javascript" src="turn/turn.js"></script>
<link rel="stylesheet" type="text/css" href="turn/turn.css">
<style>

</style>

</head>
<body>
<div class="page-header"><div class="row col-md-offset-3"><img src="images/logo.png"></div></div>
<div class="col-md-12" id="mainContainer">
	<div class="col-md-offset-3"><h1 style="color:red">ERROR</h1>
		<h3>
			<?php
				require_once 'models/models.php';
				
				if (isset($_GET['code'])) {
					$code = $_GET['code'];
					if (array_key_exists($code, Errors::$ErrorsArray)) {
						echo Errors::$ErrorsArray[$code];
					} else {
						echo "Invalid code provided.";
					}
				} else {
					echo "To kad budete igrali na radiju, bwahahahahah!";
				}
			?>
		</h3>
	</div>
</div>

<!-- Latest compiled and minified JavaScript -->
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript">


</script>
</body>
</html>