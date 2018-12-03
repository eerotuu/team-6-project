<?php
class Events{
 
    private $conn;
    private $table_name = "events";
	
    public $id;
    public $Name;
    public $HomeTeam;
    public $AwayTeam;
    public $Draw;
    public $Date;

    public function __construct($db){
        $this->conn = $db;
    }

    # Read all events
	function read(){
 
		$query = "SELECT * FROM events";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
	 
		return $stmt;
	}

	# Search comments like ? given keyword
	function search($keywords){

		$query = "SELECT
					e.id, e.Name, e.HomeTeam, e.AwayTeam, e.Draw, e.Date
				FROM
					" . $this->table_name . " e
				WHERE
					e.Name LIKE ?";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$keywords=htmlspecialchars(strip_tags($keywords));
		$keywords = "%{$keywords}%";
	 
		// bind parameter
		$stmt->bindParam(1, $keywords);
	 
		// execute
		$stmt->execute();
	 
		return $stmt;
	}
}