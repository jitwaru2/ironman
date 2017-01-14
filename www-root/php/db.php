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
			$overview['tags'] = array($obj['subjectMain'], explode(',', $obj['subjectsAlt']));
		}
		else if ($type == 'bill_hist')
		{
			$overview['id'] = $obj['ref_bill'].':'.$obj['ordinal'];
			$overview['type'] = 2;
			$overview['datetime'] = $obj['time'];
			$overview['name'] = '';
			$overview['tags'] = array(); //TOOD: should this event type be tagged??
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
			$overview['time'] = $obj['updated'];
			$overview['name'] = $obj['question']; //TODO: consider naming like "Vote on H.R.123" if available
			$overview['description'] = $obj['question']; //TODO: consider naming like "Vote on H.R.123" if available
		}

		$result = new stdClass();
		$result->entity_overview = $overview;
		$result->entity_details = $obj;
		return $result;
	}

	function getAll()
	{
		$bills = $this->dbfed->getRows('Bills', 'ORDER BY introduced DESC LIMIT 100');
		$bills_status = $this->dbfed->getRows('Bills_History', 'ORDER BY time DESC LIMIT 100');
		$amdts = $this->dbfed->getRows('Amendments', 'ORDER BY updated DESC LIMIT 100');
		$votes = $this->dbfed->getRows('Votes', 'ORDER BY time DESC LIMIT 100');

		$items = array();
		foreach ($bills as $item)
		{
			$items[] = $this->standardizeEntity($item, 'bill');
		}
		foreach ($bills_status as $item)
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
	
	function query()
	{
		
	}

}
