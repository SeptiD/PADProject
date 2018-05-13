<?php
    session_start();
    //echo $_SESSION['saved'] = 0;
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
    <div class="wrapper">

        <?php if (isset($_SESSION['u_id'])):
            echo '<form class="signout-form" action="includes/signout.inc.php" method="POST">
                    
                    <button type="submit" name="signout" ><a>Sign out</a></button>

            </form>
            '; ?>

        <?php endif; ?>
         

    </div>
</header>

<form action="charts.php" method="post">
        <select name="Type">
            <option value="1">Pie Chart</option>
            <option value="2">Bubble Map</option>
        </select>
        <input type="submit" name="submit" value="Go" />
    </form>

    <?php
        if (isset($_POST['submit']) || $_SESSION['saved']) {
            if (isset($_POST['submit'])) {
                $_SESSION['saved'] = $_POST['Type'];
                $selected_val = $_POST['Type'];
            }
            else 
                $selected_val = $_SESSION['saved'];  // Storing Selected Value In Variable
            

            $dbServername = "localhost";
            $dbUsername = "root";
            $dbPassword = "";
            $dbName = "users";
            
            $con = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);
            
            if (!$con) {
                die('Could not connect: ' . mysqpli_error($con));
            }
                 
            $sql1 = "SELECT * FROM data";
            $result1 = mysqli_query($con, $sql1);
            $resultCheck1 = mysqli_num_rows($result1);
           
            if ($resultCheck1 < 1) {
                exit();
            }
     
            $i = 0;
            $values = array();
           
            while ($row = mysqli_fetch_array($result1)) {
                
                $values[$i] = $row['counter'];
                $i++;
            
            }
            
              
            $sql2 = "SELECT * FROM countriesdata";
            $result2 = mysqli_query($con, $sql2);
            $resultCheck2 = mysqli_num_rows($result2);
           
            if ($resultCheck2 < 1) {
                exit();
            }
     
            $i = 0;
            $countervalues = array();
            $countryvalues = array();
            $sizevalues = array();
             
            while ($row = mysqli_fetch_array($result2)) {

                $countervalues[$i] = $row['counter'];
                $countryvalues[$i] = $row['country'];
                $sizevalues[$i] = 10;
                $i++;

            }
                     
            mysqli_close($con);
        }
    ?>

    <div id="myDiv"><!-- Plotly chart will be drawn inside this DIV --></div>
    <script>
        var selected_option = <?php echo json_decode($selected_val); ?>;

        if (selected_option == 1) {

            var v = <?php echo json_encode($values); ?>;
            var data = [{
                    values: v,
                    labels: ['#NFL', '#NBA', '#MLS', '#MLB', '#NHL'],
                    type: 'pie'
                }];
 
            Plotly.newPlot('myDiv', data);
        }
        else if (selected_option == 2) {
            
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
        }
    </script>
</body
