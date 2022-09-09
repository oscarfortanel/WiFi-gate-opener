<?php

	class DBOps{
		private $con;
		private $stmt;
		function __construct(){

			require_once dirname(__FILE__).'/dbConnect.php';
			$db = new DBConnect();
			$this->con = $db->connect();
		}

		function simpleInsert($tableName, $fieldsString, $questionMarks, $bindString, $valueArray){
			$query = "INSERT INTO " . $tableName . " (" . $fieldsString . ") VALUES (NULL, " . $questionMarks . ")";
			$this->stmt = $this->con->prepare($query);
			$this->stmt->bind_param($bindString, ...$valueArray);
			return $this->stmt->execute();

		}

		function simpleSelect($tableName, $fieldName, $bindString, $var){
			$query = "SELECT * FROM " . $tableName . " WHERE " . $fieldName . "=?";
			$this->stmt = $this->con->prepare($query);
			$this->stmt->bind_param($bindString, $var);
			return $this->stmt->execute();
		}

		function getRows(){
			return $this->stmt->get_result()->fetch_all(MYSQLI_ASSOC);
		}

		function getID(){
			return $this->stmt->insert_id;
		}

		function simpleDelete($tableName, $fieldName, $bindString, $var){
			$query = "DELETE FROM " . $tableName . " WHERE " . $fieldName . "=?";
			$this->stmt = $this->con->prepare($query);
			$this->stmt->bind_param($bindString, $var);
			return $this->stmt->execute();
		}
	}

?>