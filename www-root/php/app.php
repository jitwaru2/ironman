<?php require_once('lib.php'); ?>
<?php require_once('db.php'); ?>
<?php

function legName($leg)
{
	return isset($leg['nickname']) ? $leg['nickname'] : $leg['fullName'];
}

function legTag($leg)
{
	$p = $leg['party'] == 'O' ? $leg['partyName'] : $leg['party'];
	$st = $leg['state'];
	$d = $leg['district'];
	$do = $d.ordinalIndicator($d);
	
	if ($d == 0)
		return $p.'-'.$st;
	else
		return $p.'-'.$st.' '.$do;
}

function collapseLegTerms($terms)
{
	$newTerms = array();
	foreach ($terms as $term)
	{
		if (count($newTerms) == 0)
		{
			$newTerms[] = $term;
			continue;
		}
		
		$lastTerm = $newTerms[count($newTerms) - 1];
		$sameChamber = $term['chamber'] == $lastTerm['chamber'];
		//$sameRank = $term['rank'] == $lastTerm['rank'];
		$sameState = $term['state'] == $lastTerm['state'];
		$sameParty = $term['party'] == $lastTerm['party'];
		$sameDistrict = $term['district'] == $lastTerm['district'];
		$date1 = date_create($term['start']);
		$date2 = date_create($lastTerm['end']);
		$dateDiff = date_diff($date1, $date2)->d;
		
		if ($sameChamber && $sameState && $sameParty && $sameDistrict && $dateDiff < 15)
		{
			$lastTerm['end'] = $term['end'];
			$newTerms[count($newTerms) - 1] = $lastTerm;
		}
		else
		{
			$newTerms[] = $term;
		}
	}
	return $newTerms;
}

function billTypeStr($type)
{
	switch ($type)
	{
	case 'hr':
		return 'H.R.';
	case 'hjres':
		return 'H.J.Res.';
	case 'hres':
		return 'H.Res.';
	case 'hconres':
		return 'H.Con.Res.';
	case 's':
		return 'S.';
	case 'sjres':
		return 'S.J.Res.';
	case 'sres':
		return 'S.Res.';
	case 'sconres':
		return 'S.Con.Res.';
	default:
		return '?';
	}
}

function billNum($bill, $full = false)
{
	$bn = billTypeStr($bill['billType']).' '.$bill['number'];
	if ($full)
		return $bn.' ('.$bill['congress'].')';
	else
		return $bn;
}

function billTag($bill)
{
	return billNum($bill).' ('.$bill['congress'].')';
}

function billStatus($bill, $ex = false)
{
	$bits = explode(':', $bill['status']);
	if ($ex && strlen($bits[1]) > 0)
		return $bits[0].' ('.$bits[1].')';
	else
		return $bits[0];
}
