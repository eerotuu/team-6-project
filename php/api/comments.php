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
	
	function readId() {
		$query = "SELECT * FROM comments WHERE id=:id";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":id", $this->id);
		$stmt->execute();
		return $stmt;
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
		$query = "INSERT INTO " . $this->table_name . " SET name=:name, message=:message, image_url=:image_url";
		
		// prepare statement
			$stmt = $this->conn->prepare($query);

			// sanitize data and insert to object properties
			$this->name=substr(strip_tags(urldecode($this->name)),0 ,20);
			$this->message=strip_tags(urldecode($this->message));

			// bind parameters
			$stmt->bindParam(":name", $this->name);
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
	
	function update(){
	
		// update query
		$query = "UPDATE " . $this->table_name . " SET name=:name, message=:message, image_url=:image_url WHERE id=:id";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->name=substr(strip_tags(urldecode($this->name)),0 ,20);
		$this->message=strip_tags(urldecode($this->message));
		$this->id=strip_tags(urldecode($this->id));
	 
		// bind new values
		$stmt->bindParam(":name", $this->name);
		$stmt->bindParam(":message", $this->message);
		$stmt->bindParam(":image_url", $this->image_url);
		$stmt->bindParam(":id", $this->id);
	 
		// execute the query
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}
}




?>