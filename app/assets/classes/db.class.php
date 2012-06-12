<?php
/*!
 * PHP MySQL abstraction class 
 *
 * Copyright (c) 2011 Seb Skuse (seb@skuse-consulting.co.uk)
 * All rights reserved.
 * Modifications made by Russell Newman & Phillip Whittlesea
 *
 * http://seb.skus.es/
 * https://github.com/sebskuse/db
 *
 * Licensed under the BSD Licence.
 * 
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 * 
 * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 * Neither the name of Skuse Consulting Limited nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

abstract class DBType {
	protected $val;
	
	public function __toString() {
		return $this->val;
	}
	
	public function sanitise() {
		$db = db::singleton();
		return $db->real_escape_string($this->val);
	}
}

class DBInt extends DBType { 
	public function __construct($val) { 
		if(!is_integer($val)) throw new Exception("Value is not an integer!", 9001);
		$this->val = $val;
	} 
}

class DBString extends DBType { 
	public function __construct($val) { 
		if(is_string($val)) throw new Exception("Value is not a string!", 9002);
		$this->val = "'$val'";
	} 
}

class DBFunction extends DBType { 
	public function __construct($val) { 
		// MySQL function names seem to be between 2 and 15 characters, should be uppercase, and only a few contain numbers.
		// Also, we are not yet real_escape_string-ing the contents of the function. Or the function name.
		if(!preg_match("/^[A-Z_0-25]{2,15}\(.*\)$/", $val)) throw new Exception("Value is not a function!", 9003);
		$this->val = $val;
	} 
}

class DBNull extends DBType {
	public function __construct() {
		$this->val = "NULL";
	}
};

class db extends mysqli {
	
	// Holds an instance of the class
	private static $instance;
	
	public $queries = array();
	private $numQueries = 0;
	private $database;
	public $currentQuery;
	
	const VERSION = 1.4;
	
	const ERR_UNAVAILABLE = 6001;
	const ERR_CONNECT_ERROR = 6002;
	const ERR_CLONE = 6003;
	
	// A private constructor; prevents direct creation of object
	private function __construct($server = "localhost", $username = "", $password = "", $schema = ""){
		$this->database = $schema;
		
		// Prevents mysql sock warnings. I prefer to throw exception as below.
		@parent::__construct($server, $username, $password, $schema);
		if ($this->connect_error) throw new Exception("Connect Error ({$this->connect_errno}) {$this->connect_error}", self::ERR_CONNECT_ERROR);
		if(!@parent::ping()) throw new Exception("Database server unavailable", self::ERR_UNAVAILABLE);
	}
	
	public static function singleton($server = null, $username = null, $password = null, $schema = null) {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c($server, $username, $password, $schema);
		}
		return self::$instance;
	}
	
	// Prevent users from cloning this instance
	public function __clone() {
		throw new Exception('Clone is not allowed.', self::ERR_CLONE);
	}
	
	public function startTransaction() {
		$this->autocommit(false);
	}
	
	public function commit() {
		parent::commit();
		$this->autocommit(true);
	}
	
	public function rollback() {
		parent::rollback();
		$this->autocommit(true);
	}
	
	public function queryCount(){
		return $this->numQueries;
	}
	
	public function getSQL(){
		return $this->currentQuery;
	}
	
	public function run(){
		return $this->runBatch();
	}

	// These functions are all custom implementations.
	// This allows us to build a query using arrays, rather than sending it SQL directly.

	/**
	 * Example: select(array("fURL", "feed_title"), "feeds", array(array("", "fID", "=", $id)));
	 * select fURL and feed_title from feeds where fID is $id.
	 * @ $fields = array list of fields that you want to select.
	 * @ $table = string of the table you want to select in the current database.
	 * @ $conditions = 2d array. Second level arrays contain four items - argument, column, match and data. Argument is ignored for the first item (obviously). Can be for example AND, OR, etc. Column is the string for the column name and data is the data that you want to match.
	 * @ $additionals = not required. Any additional bits of SQL you wish to have after the common bit.
	*/

	public function select($fields = array(), $table, $conditions = array(), $additionals = "", $format = "SELECT %s FROM %s %s %s"){
				
		$fieldsList = "";
		$tablesList = "";
		$conditionsList = "";
		
		// Populate the list of fields
		foreach($fields as $i => $field) {
			$field = $this->real_escape_string($field);
			$fieldsList .= ($i == 0) ? $field : ", {$field}";
		}
		
		// Addition of the possiblity of multiple table selection by Phillip Whittlesea on 21/12/2010
		// If function is passed an array in $table all tables will be added to SELECT statement
		if(is_array($table)){
			foreach($table as $i => $tbl) $tablesList .= ($i == 0) ? "`{$this->database}`.`{$tbl}`" : ", `{$this->database}`.`{$tbl}`";
		} else {
			$tablesList .= "`{$this->database}`.`{$table}`";
		}
		
		// Populate the list of conditions
		if(!empty($conditions)){
			$conditionsList .= "WHERE ";
			// For each of the conditions write them into the SQL variable.
			foreach($conditions as $i => $value){
				// SEB: This is not documented. Appears to let you write a WHERE in plaintext
				if(strtoupper($value[0]) == "CUSTOM"){
					$conditionsList .= $value[1];
					continue;
				}
			
				if($i != 0) $conditionsList .= $value[0] . " ";
				
				// TODO: Should the two ' characters be `?
				$conditionsList .=  $value[1] . " ". $value[2] . " '" . $this->real_escape_string($value[3]) . "' ";
			}
		}
		
		// Push the query to the class array queries.
		$currentQuery = sprintf($format, $fieldsList, $tablesList, $conditionsList, $additionals);
		
		$this->queries[] = $currentQuery;
		$this->currentQuery = $currentQuery;
		
		return $this;
	}
	
	// Converts WHERE array to regular SQL
	public function convertWhereArrayToSql($where) {
		$out = "";
		foreach($where as $i => $value) {
			if($i != 0) $out .= $value[0] . " ";
			$out .=  $value[1] . " ". $value[2] . " '" . $this->real_escape_string($value[3]) . "' ";
		}
		return $out;
	}


	/**
	 * Example: insert(array("fID"=> "NULL", "uID" => $uID, "fURL"=>$feedURL, "feed_title"=>$image_title, "feed_description"=>$feed_description, "last_refreshed"=>date('l dS F Y h:i A')), "feeds");
	 * insert a new record into feeds where fID is null, uID is $uID, fURL is $feedURL, feed_title is $image_title, feed_description is $feed_description and last_refreshed is date('l dS F Y h:i A');
	 * @ $dataArr = array list with keys of fields that you want to put in with their data.
	 * @ $table = string of the table you want to select in the current database.
	 * @ $additionals = not required. Any additional bits of SQL you wish to have after the common bit.
	*/

	public function insert($fields = array(), $table, $additionals = "") {
		
		// These variables will hold the fields and values parts of the INSERT query
		$fieldsList = "";
		$valuesList = "";
		
		// Let's build the fields and values parts. "comma" is used to add commas where necessary
		$comma = "";
		foreach($fields as $field => $data) {
			// Sanitise, sanitise, sanitise
			$field = $this->real_escape_string($field);
			
			// Convert NULLs to DBNull types
			if($data == "NULL") $data = new DBNull();
			
			// Sanitise anything that isn't a DBType
			$data = $this->sanitiseData($data);
			
			// Append to the list of fields
			$fieldsList .= "{$comma}`{$field}`";
			
			// Append to the list of values. Objects don't get quotes around them.
			$valuesList .= $comma.$data;
			//var_dump(array($data, is_object($data)));
			// Sets comma to be a comma, so the first item in the list won't have a comma before it
			$comma = ", ";
		}
		
		// Format query, append additionals and push to query list
		$currentQuery = "INSERT INTO `{$this->database}`.`{$table}` ({$fieldsList}) VALUES ({$valuesList}) {$additionals};";
		
		$this->queries[] = $currentQuery;
		$this->currentQuery = $currentQuery;
		
		return $this;
	}


	/**
	 * Example: update(array("last_refreshed" => date('l dS F Y h:i A')), "feeds", array(array("", "fURL", $_POST['fURL']), array("AND", "uID", $_SESSION['uID'])));
	 * update a record's last_refreshed field with date('l dS F Y h:i A') in the table feeds where fURL is $_POST['fURL'] and uUD is the same as $_SESSION['uID']
	 * @ $updateFLDS = array list with keys of fields that you want to update in with their data.
	 * @ $table = string of the table you want to select in the current database.
	 * @ $fields = 2d array. Second level arrays contain three items - argument, column and data. Argument is ignored for the first item (obviously). Can be for example AND, OR, etc. Column is the string for the column name and data is the data that you want to match.
	 * @ $additionals = not required. Any additional bits of SQL you wish to have after the common bit.
	*/

	public function update($fields = array(), $table, $conditions = array(), $additionals = ""){
		
		$fieldsList = "";
		$conditionsList = "";
		
		// For each update field output to the string in the format $key = '$data',. This will allow multiple fields to be updated.
		$comma = "";
		foreach($fields as $field => $value) {
			$fieldsList .= "{$comma}{$field} = {$this->sanitiseData($value)}";
			$comma = ", ";
		}

		// Append all of the conditional fields to the end of the statement that the user has added.
		foreach($conditions as $i => $value){
			if($i != 0) $conditionsList .= " {$value[0]}";
			// TODO: ARG! This 'conditions' arr doesn't appear to contain the operator, unlike in INSERT. This assumes the op is always '='
			$conditionsList .= " {$value[1]} = {$this->sanitiseData($value[2])}";
		}
		
		// Format query, append additionals and push to query list
		// TODO: Could use the queuedQuery method to do this, but need to write the currentQuery storer into that function.
		// Also, what is currentQuery, and why isn't it a fuction? It seems to ALWAYS be the last item in $this->queries...
		$currentQuery = "UPDATE `{$this->database}`.`{$table}` SET {$fieldsList} WHERE {$conditionsList} {$additionals};";
		
		$this->queries[] = $currentQuery;
		$this->currentQuery = $currentQuery;
		
		return $this;
	}


	/**
	 * Example: delete("feeds", array(array("", "fID", $_POST['feedid'])));
	 * delete a record from feeds where fID is the same as $_POST['feedid']
	 * @ $table = string of the table you want to select in the current database.
	 * @ $fields = 2d array. Second level arrays contain three items - argument, column and data. Argument is ignored for the first item (obviously). Can be for example AND, OR, etc. Column is the string for the column name and data is the data that you want to match.
	 * @ $additionals = not required. Any additional bits of SQL you wish to have after the common bit.
	*/

	public function delete($table, $conditions = array(), $additionals = ""){
		
		// SEB: Is this a good idea or not? Nice safety net, but is it really practical?
		if(empty($conditions)) throw new Exception("No conditions were specified for the delete operation", 9004);
		
		$conditionsList = "";
		
		// For each of the conditions that the user has entered, append them to the SQL string.
		foreach($conditions as $i => $value){
			if($i != 0) $conditionsList .= " {$value[0]}";
			$conditionsList .= " {$value[1]} = {$this->sanitiseData($value[2])}";
		}
		
		// Push the query to the class array queries.
		$currentQuery = "DELETE FROM `{$this->database}`.`{$table}` WHERE {$conditionsList} {$additionals};";
		
		$this->queries[] = $currentQuery;
		$this->currentQuery = $currentQuery;
		
		return $this;
	}
	
	// Accepts a raw query and returns the first row of the results (i.e. a 2D array). Handy for logins, IDs, etc.
	public function oneRow($query) {
		$out = $this->single($query);
		if(!empty($out)) return $out[0];
		return null;
	}
	
	public function single($query){
		$result = parent::query($query);
		if($this->error) throw new Exception($this->error, $this->errno); 

		$out = array();
		if(is_bool($result)) {
				$out[] = "";
		} else {
			while($row = $result->fetch_assoc()) {
				$row = array_map(function($v){ 
					if(is_numeric($v)) return (int)$v;
					return $v;
				}, $row);
				
				$out[] = $row;
			}
		}
		return $out;
	}
	
	// Add raw SQL to the query queue.
	public function queuedQuery($query) {
		$this->queries[] = $query;
		return $query;
	}
	
	public function runBatch(){
		// SEB: What is going on here? If numQueries is a count of the number of queries, why is it adding to itself? Why isn't it a function?
		$this->numQueries += count($this->queries);
		$out = array();
		// Ping the server and re-establish the connection if it has been dropped.
		parent::ping();
		
		// For each query...
		foreach($this->queries as $queryId => $query){
			// Run the query.
			$res = parent::query($query, MYSQLI_USE_RESULT);
			if($this->error) throw new exception($this->error, $this->errno); 

			// Append the results into a 3d array in $out.
			if(is_bool($res) == true) {
				$out[$queryId] = "";
			} else {
				while($row = $res->fetch_assoc()) {
					$row = array_map(function($v){ 
						if(is_numeric($v)) return (int)$v;
						return $v;
					}, $row);
				
					$out[$queryId][] = $row;
				}
			}
		}
		
		$this->queries = array();
		
		// Return the output to the caller.
		return $out;
	}
	
	// Sanitises data for MySQL queries. Adds apostrophes around data where needed. This should not be used for field names.
	public function sanitiseData($val) {
		return is_object($val) ? $val->sanitise() : "'{$this->real_escape_string($val)}'";
	}
}