<?php require_once('AKLogger.php'); ?>
<?php

class AKSimpleORM
{
	public $logQuery;
	public $logError;
	public $logHeavy;
	
	protected $con;

	function __construct($host, $user, $pass, $dbname)
	{
		$this->logQuery = new AKLogger('./sql.log');
		$this->logQuery->enabled = false;
		$this->logError = new AKLogger('./sql_errors.log');
		$this->logHeavy = new AKLogger('./sql_heavy.log');
		$this->con = new mysqli($host, $user, $pass, $dbname);
		
		if ($this->con->connect_errno)
		{
			$this->logError->line('Failed to connect to MySQL: '.$this->con->connect_error);
			throw new Exception('Failed to connect to database');
		}
	}
	
	function __destruct()
	{
		$this->close();
	}
	
	function close()
	{
		$this->con->close();
	}
	
	function startTransaction()
	{
		$sql = 'START TRANSACTION;';
		$this->con->query($sql);
		$this->logQuery->line($sql);
	}
	
	function commit()
	{
		$sql = 'COMMIT;';
		$this->con->query($sql);
		$this->logQuery->line($sql);
	}
	
	function rollback()
	{
		$sql = 'ROLLBACK;';
		$this->con->query($sql);
		$this->logQuery->line($sql);
	}

	//TODO: bind into statement for secuirty and retuning of php array?
	function rawQuery($sql)
	{
		$this->logQuery->line($sql);
		return $this->con->query($sql);
	}
	
	//TODO: batch add / edit?

	//used to ensure correct typing of SELECT
	//Ref: https://gunjanpatidar.wordpress.com/2010/10/03/bind_result-to-array-with-mysqli-prepared-statements/
	//Ref: http://stackoverflow.com/questions/7133575/whats-wrong-with-mysqliget-result
	private function bindArray($stmt, &$row)
	{
		$md = $stmt->result_metadata();
		$params = array();
		while($field = $md->fetch_field())
		{
			$params[] = &$row[$field->name];
		}

		call_user_func_array(array($stmt, 'bind_result'), $params);
	}
	
	function select($sql)
	{
		$time_pre = microtime(true);
		$rows = array();
	
		//TODO: impl this error throw on other methods
		$stmt = $this->con->prepare($sql);
		if ($stmt)
		{
			$stmt->execute();
		}
		else
		{
			throw new Exception('Invalid SQL: '.$sql);
		}
		
		$row = null;		
		$this->bindArray($stmt, $row);
		
		while ($stmt->fetch())
		{
			if ($stmt->errno)
			{
				$this->logError->line('Select failed: '.$stmt->error);
				throw new Exception('Failed to fetch data');
			}
		
			//NOTE: needs a because the array is bound in memory
			$copy = array_map(create_function('$a', 'return $a;'), $row);
			$rows[] = $copy;
		}
		$stmt->close();
		$this->logQuery->line($sql);
		
		$time_post = microtime(true);
		$exec_time = $time_post - $time_pre;
		if ($exec_time >= 0.1)
			$this->logHeavy->line("$exec_time\t".$sql);
			
		return $rows;
	}

	function getRows($table, $clauses = '')
	{
		$sql = "SELECT * FROM $table $clauses";
		return $this->select($sql);
	}

	//used to ensure correct typing of INSERT and UPDATE
	private function bindRow($stmt, $row)
	{
		$keys = array_keys($row);
		$vals = array();
		$types = '';
	
		for ($i = 0; $i < count($row); $i++)
		{
			$val = $row[$keys[$i]];
			array_push($vals, $val);
	
			if (is_string($val)) $t = 's';
			else if (is_int($val)) $t = 'i';
			else if (is_double($val)) $t = 'd';
			else throw new Exception('Unknown Type: '.print_r($val, true));
			$types .= $t;
		}

		//NOTE: bind_param is Variadic, so we need to be tricky with the $vals array
		//Ref: http://forums.phpfreaks.com/topic/174829-solved-problem-with-mysqli-and-bind-param/
		array_unshift($vals, $types);
		$tmp = array();
        foreach($vals as $key => $value) $tmp[$key] = &$vals[$key];
		call_user_func_array(array($stmt, 'bind_param'), $tmp);
	}

	function addRow($row, $table)
	{
		$keys = array_keys($row);
		$cols = implode(',', $keys);
		$quests = str_repeat('?,', count($row));
		$quests = rtrim($quests, ',');
	
		$sql = "INSERT INTO $table($cols) VALUES($quests)";
		$stmt = $this->con->prepare($sql);
		$this->bindRow($stmt, $row);
		if ($stmt->execute())
		{
			//TODO: something here?
			$stmt->close();
			$this->logQuery->line($sql."\t".var_export($row, true));
		}
		else
		{
			$this->logError->line('Insert failed: '.$stmt->error);
			throw new Exception('Failed to execute query');
		}

		//TODO: sort this out -- stmt doesn't seem to work, con does... since con is sync, shouldn't be race issues??		
		//NOTE: this will be the wrong id for multiple execs of a prepared statement
		//NOTE: this reduces the chance of the race condition when using con->insert_id??
		//$lastID = $stmt->insert_id;
		$lastID = $this->con->insert_id;
		return $lastID;
	}

	function setRow($row, $table, $where = '')
	{
		$keys = array_keys($row);
	
		$mapping = '';
		for ($i = 0; $i < count($keys); $i++)
		{
			$mapping .= $keys[$i].'=?,';
		}
		$mapping = rtrim($mapping, ',');
	
		$sql = "UPDATE $table SET $mapping $where";
		$stmt = $this->con->prepare($sql);
		$this->bindRow($stmt, $row);
		if ($stmt->execute())
		{
			//TODO: something here?
			$stmt->close();
			$this->logQuery->line($sql."\t".var_export($row, true));
		}
		else
		{
			$this->logError->line('Update failed: '.$stmt->error);
			throw new Exception('Failed to execute query');
		}
	}

	//TODO: remove for security reasons?
// 	function deleteRows($table, $where = '')
// 	{
// 		$sql = "DELETE FROM $table $where";
// 		
// 		$this->con->query($sql);
// 		
// 		//TODO: error handling
// 		
// 		$this->logQuery->line($sql);
// 		
// 		//TODO: anything else????
// 	}
}
