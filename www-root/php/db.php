<?php require_once('AKSimpleORM.php'); ?>
<?php

//TODO: API
class IronManDB
{
	private $host;
	private $user;
	private $dbfed;
	
	function __construct()
	{
		$this->host = 'localhost';
		$this->user = $this->pass = 'root';
		$this->dbfed = new AKSimpleORM($this->host, $this->user, $this->pass, 'gov_fed');
	}
	
	function getAll()
	{
		$bills = $this->dbfed->getRows('Bills', 'ORDER BY introduced DESC LIMIT 100');
		$bills_status = $this->dbfed->getRows('Bills_History', 'ORDER BY time DESC LIMIT 100');
		$votes = $this->dbfed->getRows('Votes', 'ORDER BY time DESC LIMIT 100');
		
		$items = array_merge($bills, $bills_status, $votes);
		return $items;
	}
	
	
	
/*
	//NOTE: to be killed
	
	function getLegs($clauses)
	{
		global $dbfed;
		return $dbfed->getRows('Legislators', $clauses);
	}

	function getLeg($id)
	{
		$clauses = "WHERE id = '$id'";
		return getLegs($clauses)[0];
	}

	function getLegsCur()
	{
		$clauses = "WHERE inOffice = true ORDER BY lastName, firstName ASC";
		return getLegs($clauses);
	}

	function getLegsFmr()
	{
		$clauses = "WHERE inOffice = false ORDER BY lastName, firstName ASC";
		return getLegs($clauses);
	}

	function getRepCur($state, $district)
	{
		$clauses = "WHERE chamber = 'house' AND state = '$state' AND district = $district AND inOffice = true";
		return getLegs($clauses)[0];
	}

	function getRepsPrev($state, $district)
	{
		//TODO: actual ordering -- use term table since this only finds reps who END in the 10th
		$clauses = "WHERE chamber = 'house' AND state = '$state' AND district = $district AND inOffice = false ORDER BY birthdate DESC";
		return getLegs($clauses);
	}

	function getSenJR($state)
	{
		$clauses = "WHERE chamber = 'senate' AND state = '$state' AND rank = 'junior' AND inOffice = true";
		return getLegs($clauses)[0];
	}

	function getSenSR($state)
	{
		$clauses = "WHERE chamber = 'senate' AND state = '$state' AND rank = 'senior' AND inOffice = true";
		return getLegs($clauses)[0];
	}

	//TODO: getSensPrev
		//TODO: order

	function getLegTerms($id)
	{
		global $dbfed;
		$clauses = "WHERE ref = '$id' ORDER BY start ASC";
		return $dbfed->getRows('Legislators_Terms', $clauses);
	}

	function getSupercomms($chamber)
	{
		global $dbfed;
		$clauses = "WHERE parent IS NULL AND chamber = '$chamber' ORDER BY name ASC"; //TODO: order
		return $dbfed->getRows('Committees', $clauses);
	}

	function getSubcomms($id)
	{
		global $dbfed;
		$clauses = "WHERE parent = '$id'";
		return $dbfed->getRows('Committees', $clauses);
	}

	function getComm($id)
	{
		global $dbfed;
		$clauses = "WHERE id = '$id'";
		return $dbfed->getRows('Committees', $clauses)[0];
	}

	function getCommMembers($id)
	{
		global $dbfed;
		$clauses = "INNER JOIN Legislators ON Committees_Members.ref_leg = Legislators.id ".
			"WHERE ref_comm = '$id' ".
			"ORDER BY Committees_Members.rank";
		return $dbfed->getRows('Committees_Members', $clauses);
	}

	function getCommsForLeg($id)
	{
		global $dbfed;
		$clauses = "INNER JOIN Committees_Members ON Committees.id = Committees_Members.ref_comm ".
			"WHERE Committees_Members.ref_leg = '$id'";
		return $dbfed->getRows('Committees', $clauses);
	}

	function getCommForLeg($legID, $commID)
	{
		global $dbfed;
		$clauses = "INNER JOIN Committees_Members ON Committees.id = Committees_Members.ref_comm ".
			"WHERE Committees_Members.ref_comm = '$commID' AND Committees_Members.ref_leg = '$legID'";
		return $dbfed->getRows('Committees', $clauses)[0];
	}

	//OBSOLETE
	function getSupercommsForLeg($id)
	{
		global $dbfed;
		$clauses = "INNER JOIN Committees_Members ON Committees.id = Committees_Members.ref_comm ".
			"WHERE Committees_Members.ref_leg = '$id' AND Committees.parent IS NULL";
		return $dbfed->getRows('Committees', $clauses);
	}

	//OBSOLETE
	function getSubcommsForLeg($id, $superID)
	{
		global $dbfed;
		$clauses = "INNER JOIN Committees_Members ON Committees.id = Committees_Members.ref_comm ".
			"WHERE Committees_Members.ref_leg = '$id' AND Committees.parent = '$superID'";
		return $dbfed->getRows('Committees', $clauses);
	}

	function getBill($id)
	{
		global $dbfed;
		$clauses = "WHERE id = '$id'";
		return $dbfed->getRows('Bills', $clauses)[0];
	}

	//OBSOLETE
	function getBillCount($clauses)
	{
		global $dbfed;
		$select = 'SELECT COUNT(*) AS N FROM Bills';
		return $dbfed->select($select.' '.$clauses)[0]['N'];
	}

	function getLawCount($cnum, $status)
	{
		global $dbfed;
		$rows = $dbfed->select("SELECT count FROM Summary_Law_Status WHERE congress = $cnum AND status = '$status'");
		return count($rows) > 0 ? $rows[0]['count'] : 0;
	}

	function getBillsByCongress($cnum)
	{
		global $dbfed;
		$clauses = "WHERE congress = $cnum ORDER BY updated DESC LIMIT 100";
		return $dbfed->getRows('Bills', $clauses);
	}

	function getBillsBySponsor($id)
	{
		global $dbfed;
		$clauses = "WHERE sponsor = '$id' ORDER BY introduced DESC";
		return $dbfed->getRows('Bills', $clauses);
	}

	function getBillHistory($id)
	{
		global $dbfed;
		$clauses = "WHERE ref_bill = '$id' ORDER BY time DESC";
		return $dbfed->getRows('Bills_History', $clauses);
	}

	function getVotesForLeg($id, $page = 1, $limit = 25)
	{
		global $dbfed;
		$offset = ($page - 1) * $limit;
		$clauses = "INNER JOIN Votes_Results ON Votes.id = Votes_Results.ref_vote "
			."WHERE Votes_Results.ref_leg = '$id' "
			."ORDER BY Votes.time DESC LIMIT $limit OFFSET $offset";
		return $dbfed->getRows('Votes', $clauses);
	}

	function getScore($cnum, $legID, $item)
	{
		global $dbfed;
		$datum = $dbfed->select("SELECT datum FROM Scoreboard WHERE congress = $cnum AND ref_leg = '$legID' AND scoreType = '$item'")[0]['datum'];
		return isset($datum) ? $datum : 0;
	}

	function getStateMeta($state)
	{
		global $dbstates;
		$clauses = "WHERE id = '$state'";
		return $dbstates->getRows('Meta', $clauses)[0];
	}
	*/
}
