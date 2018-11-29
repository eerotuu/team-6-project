<?php
// initialize

//include 'php/api_testing.php';

# URI parser helper functions
# ---------------------------

    function getResource() {
        # returns numerically indexed array of URI parts
        $resource_string = $_SERVER['REQUEST_URI'];
        if (strstr($resource_string, '?')) {
            $resource_string = substr($resource_string, 0, strpos($resource_string, '?'));
        }
        $resource = array();
        $resource = explode('/', $resource_string);
        array_shift($resource);   
        return $resource;
    }

    function getParameters() {
        # returns an associative array containing the parameters
        $resource = $_SERVER['REQUEST_URI'];
        $param_string = "";
        $param_array = array();
        if (strstr($resource, '?')) {
            # URI has parameters
            $param_string = substr($resource, strpos($resource, '?')+1);
            $parameters = explode('&', $param_string);                      
            foreach ($parameters as $single_parameter) {
                $param_name = substr($single_parameter, 0, strpos($single_parameter, '='));
                $param_value = substr($single_parameter, strpos($single_parameter, '=')+1);
                $param_array[$param_name] = $param_value;
            }
        }
        return $param_array;
    }

    function getMethod() {
        # returns a string containing the HTTP method
        $method = $_SERVER['REQUEST_METHOD'];
        return $method;
    }
 
# Handlers
# ------------------------------

	function postEvents($parameters) {
		$connect = mysqli_connect("localhost","eero","eero","betfair");
		$query = "CREATE TABLE IF NOT EXISTS Events (
					id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
					Name VARCHAR(30) NOT NULL,
					HomeTeam FLOAT NOT NULL,
					AwayTeam FLOAT NOT NULL,
					Draw FLOAT NOT NULL,
					Date DATETIME NOT NULL
				)";
		if ($connect->query($query) === FALSE) {
			echo "Error creating table: " . $conn->error;
		} 
	}
	
	function getEvents(){
		$connect = mysqli_connect("localhost","eero","eero","betfair");
		if ($connect->connect_error) {
			die("Connection failed: " . $connect->connect_error);
		} 
		$query = "SELECT * FROM events";
		$result = $connect->query($query);
		$json_array = array();
		$connect->close();
		while($row = mysqli_fetch_assoc($result)) {
			$json_array[] = $row;
		}
		header('Content-Type: application/json');
		echo json_encode($json_array, JSON_PRETTY_PRINT);
	}


	function postData($parameters) {
		$connect = mysqli_connect("localhost","eero","eero","betfair");
		$query = "CREATE TABLE IF NOT EXISTS test (
					id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
					firstname VARCHAR(30) NOT NULL,
					lastname VARCHAR(30) NOT NULL
				)";
		if ($connect->query($query) === FALSE) {
			echo "Error creating table: " . $conn->error;
		} 
		
		//$id=urldecode($parameters["id"]);
		$firstname=urldecode($parameters["firstname"]);
		$lastname=urldecode($parameters["lastname"]);
		$query = "INSERT INTO test(firstname, lastname) VALUES('$firstname', '$lastname')";
		if ($connect->query($query) === FALSE) {
			echo "Error inserting data: " . $conn->error;
		} else {
			echo 'OK';
		}
		
	}
	
	function getBetfair() {
		$connect = mysqli_connect("localhost","eero","eero","betfair");
		$query = "SELECT * FROM test";
		$result = mysqli_query($connect, $query);
		$json_array = array();

		while($row = mysqli_fetch_assoc($result)) {
			$json_array[] = $row;
		}
		header('Content-Type: application/json');
		echo json_encode($json_array, JSON_PRETTY_PRINT);
	}

# Main
# ----

	$resource = getResource();
    $request_method = getMethod();
    $parameters = getParameters();

    # Redirect to appropriate handlers.
	if ($resource[0]=="api") {
		if ($request_method=="POST" && $resource[1]=="person") {
        	postData($parameters);
    	}
		else if ($request_method=="GET" && $resource[1]=="persons") {
			getBetfair();
		}
		else if ($request_method=="GET" && $resource[1]=="events") {
			getEvents();
		}
		else if ($request_method=="POST" && $resource[1]=="event") {
			postEvents($parameters);
		}
	}
	
	if ($resource[0]==null) {
		header("Location: /html/index.html"); /* Redirect browser */
		exit();
	}
	
	else {
		http_response_code(405); # Method not allowed
	}
?>

