<?php

function beginsWith($str, $sub)
{
   return (substr($str, 0, strlen($sub)) === $sub);
}

function endsWith($str, $sub)
{
   return (substr($str, strlen($str) - strlen($sub)) === $sub);
}

function ordinalIndicator($num)
{
	if (endsWith($num, '11') || endsWith($num, '12') || endsWith($num, '13'))
		return 'th';
	else if (endsWith($num, '1'))
		return 'st';
	else if (endsWith($num, '2'))
		return 'nd';
	else if (endsWith($num, '3'))
		return 'rd';
	else
		return 'th';
}

