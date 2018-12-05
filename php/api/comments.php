<?php
class Comment{
	
	private $conn;
    private $table_name = "comments";
	
	public $id;
	public $name;
	public $time_stamp;
	public $message;
	public $image_url;
	
	public function __construct($db){
        $this->conn = $db;
    }

    # Read all comments
	function read(){
 
		$query = "SELECT * FROM comments ORDER BY time_stamp DESC";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
	 
		return $stmt;
	}

	# Create comment
    # - Returns TRUE on success or FALSE on failure.
	function create(){
		//if($this->$image_url){
			$query = "INSERT INTO " . $this->table_name . "
					SET name=:name, time_stamp=:time_stamp, message=:message, image_url=:image_url";
		//} else {
		//	$query = "INSERT INTO " . $this->table_name . "
		//			SET name=:name, time_stamp=:time_stamp, message=:message";
		//}
		// prepare statement
			$stmt = $this->conn->prepare($query);

			// sanitize data and insert to object properties
			$this->name=substr(strip_tags(urldecode($this->name)),0 ,20);
			$this->time_stamp=htmlspecialchars(strip_tags($this->time_stamp));
			$this->message=strip_tags(urldecode($this->message));

			// bind parameters
			$stmt->bindParam(":name", $this->name);
			$stmt->bindParam(":time_stamp", $this->time_stamp);
			$stmt->bindParam(":message", $this->message);
			$stmt->bindParam(":image_url", $this->image_url);

			// execute query
			if($stmt->execute()){
				return true;
			}

			// query failed
			return false;
		
	}
	
	function delete(){
 
		$query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);

		if($stmt->execute()){
			return true;
		}
	 
		return false;
		 
	}
}




?>