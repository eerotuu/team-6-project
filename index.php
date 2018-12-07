<?php
# initialize

include_once 'php/database.php';
include_once 'php/api/events.php';
include_once 'php/api/comments.php';

error_reporting(E_PARSE);

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

# Events resource
# ----------------------------------------------------------------------------
	
	# GET ALL EVENTS
	function getEvents() {
		header("Access-Control-Allow-Origin: *");
		header('Content-Type: application/json');
		
		// Create requierd objects.
		$database = new Database();
		$conn = $database->getConnection();
		$events = new Events($conn);
		
		// Get result statement.
		$stmt = $events->read(); 
		
		// Extract row count from statement and check that it is not empty.
		$num = $stmt->rowCount();
		if ($num>0) {
			
			$events_arr = array();
			
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

                // extract row data to array and push it to output array
				extract($row);

				$event=array(
					"id" => $id,
					"Name" => $Name,
					"HomeTeam" => $HomeTeam,
					"AwayTeam" => $AwayTeam,
					"Draw" => $Draw,
					"Date" => $Date
				);
 
				array_push($events_arr, $event);
			}
            http_response_code(200);
            echo json_encode($events_arr, JSON_PRETTY_PRINT);
		} else {
            http_response_code(204); # No Content
            echo json_encode(array("message" => "No Data Found"));
		}

	}
	
	# GET EVENTS BY NAME
	function getEventsByName(){
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
		 
		// Create required objects.
		$database = new Database();
		$db = $database->getConnection();
		$events = new Events($db);
		
		// Check if keywords is set.
		$keywords=isset($_GET["name"]) ? $_GET["name"] : "";
		
		// Get statement
		$stmt = $events->search($keywords);
		
		// Extract row count from statement and check that it is not empty.
		$num = $stmt->rowCount();
		if($num>0){
		 
			$events_arr=array();
		 
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				
				// Extract row data to array and push it to output array.
				extract($row);
		 
				$event=array(
					"id" => $id,
					"Name" => $Name,
					"HomeTeam" => $HomeTeam,
					"AwayTeam" => $AwayTeam,
					"Draw" => $Draw,
					"Date" => $Date
				);
		 
				array_push($events_arr, $event);
			}
			http_response_code(200);
			echo json_encode($events_arr, JSON_PRETTY_PRINT);
		} else {
            http_response_code(204); # No Content
            echo json_encode(array("message" => "No Data Found"));
        }
	}
	
# Comment resource
# -------------------------------------------------------------------------------------------------------------------------	
	# POST COMMENT
	function postComment($parameters){
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: text/plain");
		header("Access-Control-Allow-Methods: POST");
		header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
		 
		 
		$database = new Database();
		$db = $database->getConnection();
		$comment = new Comment($db);
		 
		// Define current timezone and time.
		date_default_timezone_set("Europe/Helsinki");
		$current_time = "Europe/Helsinki:".time();
		
		
		## COMMENTED FORM INPUT => requires JSON format now.
		#--------------------------------------------------
		/*
		if($parameters["image_url"]) {
			$url = $parameters["image_url"];
			$type = get_headers($url, 1)['Content-Type'];
			if(preg_match("^(image)(.png|.jpg|.png|.jpeg|.gif)/i", $type)) {
				$image_url = $url;
			} else {
				$image_url;
			}
		}
		
		
		$data=(object)array(
            "name" => $parameters["name"],
            "time_stamp" => date("Y-m-d H:i:s",  time()),
            "message" => $parameters["message"],
        );*/
		#-----------------------------------
		
		// Get input data into array object and insert current time.
		$data = json_decode(file_get_contents("php://input"), true);
		$data=(object)$data;
		
		// validate
		if(
			!empty($data->name) &&
			!empty($data->message) 
			
		){
			if(!empty($data->image_url)){
				
				// Store url content type into variable.
				$type = get_headers(($data->image_url), 1)['Content-Type'];
				
				// Check if type matches allowed content types. Example "image/png". Currently allowed: png, jpg, bmp, jpeg, gif
				if(preg_match("/^(image)(.png|.jpg|.bmp|.jpeg|.gif)/i", $type)) {
				} else {
					// If does not match set image_url variable to null.
					$data->image_url = null;
				}
			}
			
			
			// Set the comment property values.
			$comment->name = $data->name;
			$comment->message = $data->message;
			$comment->image_url = $data->image_url;
			
			if (!empty($data->id)) {
				$comment->id = $data->id;
				$stmt = $comment->readId();
				$num = $stmt->rowCount();
				if($num > 0) {
					// Update
					if($comment->update()){
						http_response_code(202); # Accepted
						echo json_encode(array("message" => "Comment was updated successfully."));
					} else {
						http_response_code(503); # Service unavailable
						echo json_encode(array("message" => "Unable to update Comment."));
					}
				} else {
					http_response_code(400); # Bad request
					echo json_encode(array("message" => "No comment found with given id."));
				}	
			} else {
				// Create
				if($comment->create()){
					http_response_code(201); # Created
					echo json_encode(array("message" => "Comment was created successfully."));
				}
			 
				// If unable to create the comment.
				else{
					http_response_code(503); # Service unavailable
					echo json_encode(array("message" => "Unable to create Comment."));
				}
			}
	
		}	 
		// Tell the user data is incomplete.
		else{
			http_response_code(400); # Bad request
			$response = array();
			$response["message"] = "Data is incomplete. Name and Message is required";
			echo json_encode(array("message" => "Data is incomplete. Name and Message is required"));
			//echo json_encode($response);	
		}
	}
	
	# GET ALL COMMENTS
	function getComments(){
		header("Access-Control-Allow-Origin: *");
		header('Content-Type: application/json');
		
		$database = new Database();
		$conn = $database->getConnection();
		$comments = new Comment($conn);
		
		$stmt = $comments->read();
		$num = $stmt->rowCount();
		if ($num>0) {
			$comments_arr = array();
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				extract($row);
				
				$comment=array(
					"id" => $id,
					"name" => $name,
					"time_stamp" => $time_stamp,
					"message" => $message,
					"image_url" => $image_url
				);
 
				array_push($comments_arr, $comment);
				
			}
            http_response_code(200); # OK
            echo json_encode($comments_arr, JSON_PRETTY_PRINT);
		} else {
            http_response_code(200);
            echo json_encode(array("message" => "No Data Found"));
        }

	}
	
	# DELETE
	function deleteComment($id){
		
		// required headers
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
		header("Access-Control-Allow-Methods: POST");
		header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
		 
		// get database connection
		$database = new Database();
		$db = $database->getConnection();
		 
		$comment = new comment($db);
		$comment->id = $id;
		
		// delete
		if($comment->delete()){
			http_response_code(200); # OK
			echo json_encode(array("message" => "Comment was deleted."));
		}
		 
		// if unable to delete
		else{
			http_response_code(503); # Service Unavailable
			echo json_encode(array("message" => "Unable to delete comment."));
		}
		
		
	}
	
	

# Main
# ----

	$resource = getResource();
    $request_method = getMethod();
    $parameters = getParameters();

    # Redirect to appropriate handlers.

	switch ($resource[0]) {
        case 'api' : {
        	switch ($request_method) {
            	case 'GET' :
                	getHandler($resource[1]);
                	break;
            	case 'POST' :
                	postHandler($resource[1], $parameters);
                	break;
				case 'DELETE' :
					deleteHandler($resource);
					break;
        	}
        	break;
        }
		case 'update': {
			include 'php/update.php';
			break;
		}
		case null : {
            readfile("html/mainsitewithboot.html");
		}
		

	}


	function getHandler($resource){
		switch($resource) {
			case 'events' :
                if(isset($_GET["name"])){
                    getEventsByName();
                } else {
                    getEvents();
                }
				break;
			case 'comments' :
                getComments();
				break;
			default :
                http_response_code(405); # Method not allowed
                echo json_encode(array("message" => "Method not Allowed"));

		}
	}

	function postHandler($resource, $param){
		switch($resource) {
			case 'comments' :
				postComment($param);
				break;
            default :
                http_response_code(405); # Method not allowed
                echo json_encode(array("message" => "Method not Allowed"));

		}
	}
	
	function deleteHandler ($resource) {
		if ($resource[1] == 'comments') {
			if ( ctype_digit($resource[2]) ){
				deleteComment($resource[2]);
			} else {
				 http_response_code(400); # Bad request
				 echo json_encode(array("message" => "Unable to delete comment. id needs to be integer"));
			}
		}
	}
	
?>

