<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>SPORT DATA</title>
<link href="https://fonts.googleapis.com/css?family=Merriweather:300,300i,400,400i,700,700i,900,900i" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Ultra" rel="stylesheet">
<!-- Plotly.js -->
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
    <!-- Numeric JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/numeric/1.2.6/numeric.min.js"></script>
    

	<link rel="stylesheet" type="text/css" href="style2.css">

	<script>
        <!-- JAVASCRIPT CODE GOES HERE -->
        var v = <?php echo json_encode($values); ?>;
        var data = [{
                values: v,
                labels: ['#NFL', '#NBA', '#MLS', '#MLB', '#NHL'],
                type: 'pie'
            }];
 
        Plotly.newPlot('myDiv', data);
    </script>

</head>
<body >





		<?php
				if(isset($_SESSION['u_id']))
				{
					$dbServername = "localhost";
        			$dbUsername = "root";
			        $dbPassword = "";
			        $dbName = "users";
			       
			        $con = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);
			       
			        if (!$con) {
			            die('Could not connect: ' . mysqpli_error($con));
			        }
			       
			        $sql = "SELECT * FROM data";
			        $result = mysqli_query($con, $sql);
			        $resultCheck = mysqli_num_rows($result);
			       
			        if ($resultCheck < 1) {
			            exit();
			        }
			 
			        $i = 0;
			        $values = array();
			       
			        while($row = mysqli_fetch_array($result)) {
			            $values[$i] = $row['counter'];
			            $i++;
			        }
			 
			        mysqli_close($con);				

					echo'<div id="myDiv"><!-- Plotly chart will be drawn inside this DIV --></div>';
				}else{
					echo '
					<section class="tickets-section">

						<br>
						<br>
						<br>
						<div class="wrapper">
					<form class="tickets-log" action="includes/signin.inc.php" method="POST">
						<br>
						<br>
						<br>
						<input type="text" name="uid" placeholder="Username/e-mail address">
						<br>
						<input type="password" name="password" placeholder="Password">
						<br>
						<button type="submit" name="signin" >Sign in</button>
					</form>
					<form class="tickets-log" action="includes/signup.inc.php" method="POST">
						<input type="text" name="firstname" placeholder="First name">
						<br>
						<input type="text" name="lastname" placeholder="Last name">
						<br>
						<input type="text" name="uid" placeholder="Username">
						<br>
						<input type="text" name="emailaddress" placeholder="Name@example.com">
						<br>
						<input type="password" name="password" placeholder="Password">
						<br>
						<button type="submit" name="submit" >Sign up</button>
					</form>
					</div>
				</section>';
				}
		?>




</body>
</html>