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
	
	function standardizeEntity($obj, $type)
	{
		$overview = array();
		$overview['id'] = $obj['id'];

		if ($type == 'bill')
		{
			$overview['type'] = 1;
			$overview['datetime'] = $obj['updated'];
			$overview['name'] = isset($obj['shortTitle']) ? $obj['shortTitle'] : $obj['officialTitle'];
			$overview['description'] = $obj['officialTitle'];
		}
		else if ($type == 'bill_hist')
		{
			$overview['id'] = $obj['ref_bill'].':'.$obj['ordinal'];
			$overview['type'] = 2;
			$overview['datetime'] = $obj['time'];
			$overview['name'] = '';
		}
		else if ($type == 'amdt')
		{
			$overview['type'] = 3;
			$overview['datetime'] = $obj['updated'];
			$overview['name'] = $obj['description'];
			$overview['description'] = $obj['description'];
		}
		else if ($type == 'vote')
		{
			$overview['type'] = 4;
			$overview['datetime'] = $obj['time'];
			$overview['name'] = $obj['question']; //TODO: consider naming like "Vote on H.R.123" if available
			$overview['description'] = $obj['question']; //TODO: consider naming like "Vote on H.R.123" if available
		}

		$overview['tags'] = array();
		$subjects = explode(',', $obj['subjectsAlt']);
		//$subjects[] = $obj['subjectMain']; //NOTE: seems to be included in full subject list...
		foreach ($subjects as $sub)
		{
			$tag = new stdClass();
			$tag->name = $sub;
			$overview['tags'][] = $tag;
		}

		$result = new stdClass();
		$result->entity_overview = $overview;
		$result->entity_details = $obj;
		return $result;
	}
	
	function groupItems($bills, $bill_hist, $amdts, $votes)
	{
		$items = array();
		foreach ($bills as $item)
		{
			$items[] = $this->standardizeEntity($item, 'bill');
		}
		foreach ($bill_hist as $item)
		{
			$items[] = $this->standardizeEntity($item, 'bill_hist');
		}
		foreach ($amdts as $item)
		{
			$items[] = $this->standardizeEntity($item, 'amdt');
		}
		foreach ($votes as $item)
		{
			$items[] = $this->standardizeEntity($item, 'vote');
		}
		return $items;
	}

	function getAll()
	{
		$bills =	 $this->dbfed->getRows('Bills',			'ORDER BY updated DESC LIMIT 100');
		$bill_hist = $this->dbfed->getRows('Bills_History',	'LEFT JOIN Bills ON ref_bill = Bills.id ORDER BY time DESC LIMIT 100');
		$amdts =	 $this->dbfed->getRows('Amendments',	'LEFT JOIN Bills ON ref_bill = Bills.id ORDER BY Amendments.updated DESC LIMIT 100');
		$votes =	 $this->dbfed->getRows('Votes',			'LEFT JOIN Bills ON ref_bill = Bills.id ORDER BY time DESC LIMIT 100');
		$items = $this->groupItems($bills, $bill_hist, $amdts, $votes);
		return $items;
	}
	
	function query($get)
	{
		$dateStart = isset($get['date_start']) ? $get['date_start'] : '1900-01-01';
		$dateEnd = isset($get['date_end']) ? $get['date_end'] : '2100-01-01';
		$tags = $get['tags'];
		$name = $get['name'];
		
		$whereDates = "BETWEEN '$dateStart 00:00:00' AND '$dateEnd 23:00:00'";
		$whereTags = '';
		foreach (explode(',', $tags) as $tag)
		{
			$whereTags .= "AND LOWER(subjectsAlt) LIKE '%$tag%' ";
		}
		//TODO: clean this up, make pretty. basic problem is different db query per field (i.e. date_start, name)
		$whereClause1 = "WHERE (updated				$whereDates) $whereTags";
		$whereClause2 = "WHERE (time				$whereDates) $whereTags";
		$whereClause3 = "WHERE (Amendments.updated	$whereDates) $whereTags";
		
		$bills = 	 $this->dbfed->getRows('Bills',			"$whereClause1 ORDER BY updated DESC LIMIT 100");
		$bill_hist = $this->dbfed->getRows('Bills_History',	"LEFT JOIN Bills ON ref_bill = Bills.id $whereClause2 ORDER BY time DESC LIMIT 100");
		$amdts = 	 $this->dbfed->getRows('Amendments',	"LEFT JOIN Bills ON ref_bill = Bills.id $whereClause3 ORDER BY Amendments.updated DESC LIMIT 100");
		$votes =	 $this->dbfed->getRows('Votes',			"LEFT JOIN Bills ON ref_bill = Bills.id $whereClause2 ORDER BY time DESC LIMIT 100");
		
		$items = $this->groupItems($bills, $bill_hist, $amdts, $votes);
		$items2 = array();
		foreach ($items as $item)
		{
			$needle = isset($item->entity_overview['name']) ? $item->entity_overview['name'] : '';
			$itemName = strtolower();
			if (strpos($itemName, strtolower($name)) !== false || strlen($needle) < 1) //php doesn't have a 'string contains' method
				$items2[] = $item;
		}
		return $items2;
	}

}
