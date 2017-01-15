<?php

class AKLogger
{
	public $logFile;
	public $enabled = true;

	function __construct($logFile)
	{
		$this->logFile = $logFile;
	}
	
	function line($line)
	{
		if (!$this->enabled)
			return;

		$logline = date("Y-m-d H:i:s")."\t".$line;
		$logline = str_replace("\n", "", $logline);
		$logline = str_replace("\r", "", $logline);
		$logline = $logline."\n";
		file_put_contents($this->logFile, $logline, FILE_APPEND);
	}
	
	function exception($e)
	{
		$this->line('Unhandled Exception: '.$e->getMessage());
	}
}
