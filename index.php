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
# ------------------------------
	
	# GET ALL EVENTS
	function getEvents() {
		header("Access-Control-Allow-Origin: *");
		header('Content-Type: application/json');
		
		$database = new Database();
		$conn = $database->getConnection();
		$events = new Events($conn);
		
		$stmt = $events->read(); // get result statement
		$num = $stmt->rowCount();
		if ($num>0) {
			$events_arr = array();
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

                // convert row data to array and add it to output array
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
		 
		$database = new Database();
		$db = $database->getConnection();
		$events = new Events($db);
		$keywords=isset($_GET["name"]) ? $_GET["name"] : "";
		$stmt = $events->search($keywords);
		$num = $stmt->rowCount();
		 
		if($num>0){
		 
			$events_arr=array();
		 
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

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
	
	# POST COMMENT
	function postComment($parameters){
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json");
		header("Access-Control-Allow-Methods: POST");
		header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
		 
		 
		$database = new Database();
		$db = $database->getConnection();
		 
		$comment = new Comment($db);
		 
		// get posted data from url to object array
		date_default_timezone_set("Europe/Helsinki");
		$current_time = "Europe/Helsinki:".time();
		
		
		if($parameters["image_url"]) {
			$url = $parameters["image_url"];
			$type = get_headers($url, 1)['Content-Type'];
			if(preg_match("/(image)(.png|.jpg|.png|.jpeg|.gif)/i", $type)) {
				$image_url = $url;
			} else {
				$image_url;
			}
		}
		$data=(object)array(
            "name" => $parameters["name"],
            "time_stamp" => date("Y-m-d H:i:s",  time()),
            "message" => $parameters["message"],
        );


		
		// make sure data is not empty
		if(
			!empty($data->name) &&
			!empty($data->time_stamp) &&
			!empty($data->message)
			
		){
		 
			// set the comment property values
			$comment->name = $data->name;
			$comment->time_stamp = $data->time_stamp;
			$comment->message = $data->message;
			$comment->image_url = $image_url;
		 
			// create
			if($comment->create()){
				http_response_code(201); # Created
				echo json_encode(array("message" => "Comment was created successfully."));
			}
		 
			// if unable to create the comment
			else{
				http_response_code(503); # Service unavailable
				echo json_encode(array("message" => "Unable to create Comment."));
			}
		}
		 
		// tell the user data is incomplete
		else{
			http_response_code(400); # Bad request
			echo json_encode(array("message" => "Unable to create comment. Data is incomplete."));
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
			case 'update' : {
				include 'php/update.php';
				break;
			}
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

