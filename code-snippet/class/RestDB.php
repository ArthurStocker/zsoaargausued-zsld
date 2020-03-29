<?php
class Rest{
    private $host  = 'localhost';
    private $user  = 'rest_user';
    private $password   = "rest2020";
    private $database  = "rest_demo";      
    private $empTable = 'restdemo';	
	private $dbConnect = false;
    public function __construct(){
        if(!$this->dbConnect){ 
            $conn = new mysqli($this->host, $this->user, $this->password, $this->database);
            if($conn->connect_error){
                die("Error failed to connect to MySQL: " . $conn->connect_error);
            }else{
                $this->dbConnect = $conn;
            }
        }
    }
	private function getData($sqlQuery) {
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if(!$result){
			die('Error in query: '. mysqli_error());
		}
		$data= array();
		while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
			$data[]=$row;            
		}
		return $data;
	}
	private function getNumRows($sqlQuery) {
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if(!$result){
			die('Error in query: '. mysqli_error());
		}
		$numRows = mysqli_num_rows($result);
		return $numRows;
	}
	public function getEmployee($empId) {		
		$sqlQuery = '';
		if($empId) {
			$sqlQuery = "WHERE id = '".$empId."'";
		}	
		$empQuery = "
			SELECT id, name, skills, address, age 
			FROM ".$this->empTable." $sqlQuery
			ORDER BY id DESC";	
		$resultData = mysqli_query($this->dbConnect, $empQuery);
		$empData = array();
		while( $empRecord = mysqli_fetch_assoc($resultData) ) {
			$empData[] = $empRecord;
		}
		header('Content-Type: application/json');
		echo json_encode($empData);	
	}
	function insertEmployee($empData){ 		
		$empName=$empData["empName"];
		$empAge=$empData["empAge"];
		$empSkills=$empData["empSkills"];
		$empAddress=$empData["empAddress"];		
		$empDesignation=$empData["empDesignation"];
		$empQuery="
			INSERT INTO ".$this->empTable." 
			SET name='".$empName."', age='".$empAge."', skills='".$empSkills."', address='".$empAddress."', designation='".$empDesignation."'";
		if( mysqli_query($this->dbConnect, $empQuery)) {
			$messgae = "Employee created Successfully.";
			$status = 1;			
		} else {
			$messgae = "Employee creation failed.";
			$status = 0;			
		}
		$empResponse = array(
			'status' => $status,
			'status_message' => $messgae
		);
		header('Content-Type: application/json');
		echo json_encode($empResponse);
	}
	function updateEmployee($empData){ 		
		if($empData["id"]) {
			$empName=$empData["empName"];
			$empAge=$empData["empAge"];
			$empSkills=$empData["empSkills"];
			$empAddress=$empData["empAddress"];		
			$empDesignation=$empData["empDesignation"];
			$empQuery="
				UPDATE ".$this->empTable." 
				SET name='".$empName."', age='".$empAge."', skills='".$empSkills."', address='".$empAddress."', designation='".$empDesignation."' 
				WHERE id = '".$empData["id"]."'";
				echo $empQuery;
			if( mysqli_query($this->dbConnect, $empQuery)) {
				$messgae = "Employee updated successfully.";
				$status = 1;			
			} else {
				$messgae = "Employee update failed.";
				$status = 0;			
			}
		} else {
			$messgae = "Invalid request.";
			$status = 0;
		}
		$empResponse = array(
			'status' => $status,
			'status_message' => $messgae
		);
		header('Content-Type: application/json');
		echo json_encode($empResponse);
	}
	public function deleteEmployee($empId) {		
		if($empId) {			
			$empQuery = "
				DELETE FROM ".$this->empTable." 
				WHERE id = '".$empId."'	ORDER BY id DESC";	
			if( mysqli_query($this->dbConnect, $empQuery)) {
				$messgae = "Employee delete Successfully.";
				$status = 1;			
			} else {
				$messgae = "Employee delete failed.";
				$status = 0;			
			}		
		} else {
			$messgae = "Invalid request.";
			$status = 0;
		}
		$empResponse = array(
			'status' => $status,
			'status_message' => $messgae
		);
		header('Content-Type: application/json');
		echo json_encode($empResponse);	
	}
}
?>