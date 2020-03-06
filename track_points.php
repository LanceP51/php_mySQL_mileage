<?php 

//////////get sql data///////

  // Connect to database server and database
  $mysqli = new mysqli('localhost:3308', 'root', '', 'uestudio_test');

  // If connection attempt failed, let us know
  if ($mysqli->connect_errno) {
      echo "Sorry, this website is experiencing problems.";
      echo "Error: Failed to make a MySQL connection, here is why: \n";
      echo "Errno: " . $mysqli->connect_errno . "\n";
      echo "Error: " . $mysqli->connect_error . "\n";
      exit;
  }

////////////distance formula////////////
function distance($lat1, $lon1, $lat2, $lon2, $unit) {
  /*
  if (($lat1 == $lat2) && ($lon1 == $lon2)) {
    return 0;
  }
  else {
  	*/
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

      return $miles;
    
  //}
}
////////////////////////////////////////

  function outputQueryResults($mysqli) { 

    $sql = 'SELECT lat,lon FROM track_points';

    // run the query 
    if (!$result = $mysqli->query($sql)) {
      // Handle error
      echo "Sorry, this website is experiencing problems.";
      echo "Error: Query failed to execute, here is why: \n";
      echo "Query: " . $sql . "\n";
      echo "Errno: " . $mysqli->errno . "\n";
      echo "Error: " . $mysqli->error . "\n";
      exit;
    }

		//array for all the states
		$states = array();
		
    $wv = array();
    $ky = array();
    
    while ($row = $result->fetch_assoc()) { 
    	//WV coordinates
    	if($row['lat']>38&&$row['lat']<39&&$row['lon']<-80&&$row['lon']>-81){ 
    		//put into array
				array_push($wv, "WV: ", $row['lat'], $row['lon']);
      }
     
      //KY coordinates
    	elseif($row['lat']>36&&$row['lat']<38&&$row['lon']<-80&&$row['lon']>-85){ 
    		//put into array
				array_push($ky, "KY: ", $row['lat'], $row['lon']);
      }
    }
    array_push($states, $wv, $ky);
    //print_r($states);
  
  // use distance formula to calcuate distance between each point in the array
  foreach ($states as $singleState){
		for ($i = 1; $i < count($singleState); $i+=2) {
			
  			$milesArray = [];
  			$lat1 = floatval ($singleState[$i][0]);
  			$lon1 = floatval ($singleState[$i][1]);
  			$lat2 = floatval ($singleState[$i+1][0]);
  			$lon2 = floatval ($singleState[$i+1][1]);
  
			$milesDriven = distance($lat1,$lon1, $lat2, $lon2,"M");

			array_push($milesArray,$milesDriven);	
			$currentState = $singleState[0];		
	}
		
	$mileageSum = array_sum($milesArray);
	//output your sum of all of the mileage for a state
	print_r($currentState.$mileageSum);
		}
	}
	
  // run query and output results 
  outputQueryResults($mysqli); 

  // close database connection 
  mysqli_close($mysqli);
  
echo '</track_points>';

  ?> 