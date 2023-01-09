<?php
class LoggingService
{
	private $log_file;

	function __construct()
	{
		$this->log_file = fopen($_SERVER["DOCUMENT_ROOT"] . "/@chan/log.txt", "a") or die("Unable to open the log file!");
	}

	function LogToFile($data)
	{
		fwrite($this->log_file, date("Y/m/d") . " - " . date("h:i:sa") . " -> " . $data . "\n");
		fwrite($this->log_file, "\n");
		fclose($this->log_file);
	}
}
