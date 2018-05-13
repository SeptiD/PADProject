<?php
    session_start();
?>
<head>
    <!-- Plotly.js -->
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
    <!-- Numeric JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/numeric/1.2.6/numeric.min.js"></script>
    <meta http-equiv="refresh" content="30"/>
</head>

 
<body bgcolor="#E6E6FA">
    <header >
    <div class="wrapper" >

        <?php if (isset($_SESSION['u_id'])):
            echo '<form class="signout-form" action="includes/signout.inc.php" method="POST">
                    
                    <button type="submit" name="signout" ><a>Sign out</a></button>

            </form>'; ?>

        <?php endif; ?>
        <ul>
                    <li><a href="charts.php">Go to chart</a></li>
                    
        </ul>

    </div>
</header>
    <?php
 
        $dbServername = "localhost";
        $dbUsername = "root";
        $dbPassword = "";
        $dbName = "users";
       
        $con = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);
       
        if (!$con) {
            die('Could not connect: ' . mysqpli_error($con));
        }
       
        $sql = "SELECT * FROM countriesdata";
        $result = mysqli_query($con, $sql);
        $resultCheck = mysqli_num_rows($result);
       
        if ($resultCheck < 1) {
            exit();
        }
 
        $i = 0;
        $countervalues = array();
        $countryvalues = array();
        $sizevalues = array();
         
       
        while($row = mysqli_fetch_array($result)) {
            $countervalues[$i] = $row['counter'];
            $countryvalues[$i] = $row['country'];
            $sizevalues[$i] = 10;
            $i++;
        }
 
        mysqli_close($con);
    ?>
 
    <div id="myDiv"><!-- Plotly chart will be drawn inside this DIV --></div>
    <script>
        <!-- JAVASCRIPT CODE GOES HERE -->
        var counters = <?php echo json_encode($countervalues); ?>;
        var country_codes = <?php echo json_encode($countryvalues); ?>;
        var size_values = <?php echo json_encode($sizevalues); ?>;
        
        var data = [{
        type: 'scattergeo',
        mode: 'markers',
        locations: country_codes,
        marker: {
            size: size_values,
            color: counters,
            cmin: 0,
            cmax: Math.max(...counters),
            colorscale: 'Reds',
            colorbar: {
                title: 'Country',
                ticksuffix: ' tweets',
                showticksuffix: 'last'
            },
            line: {
                color: 'black'
            }
        },
        name: 'europe data'
    }];

    var layout = {
        'geo': {
            'scope': 'world',
            'resolution': 50
        }
    };

    Plotly.newPlot('myDiv', data, layout);
    </script>
</body