<?php

namespace Bazo\Watchdog;
use Bazo\Watchdog\WatchdogClient;

/**
 * NetteLogger
 *
 * @author Martin
 */
class NetteLogger extends \Nette\Diagnostics\Logger
{

	/** @var WatchdogClient */
	private $watchdogClient;


	public function __construct(WatchdogClient $watchdogClient)
	{
		$this->watchdogClient = $watchdogClient;
		$this->directory = \Nette\Diagnostics\Debugger::$logDirectory;
	}


	public function log($message, $priority = self::INFO)
	{
		$res = parent::log($message, $priority);
		$levelMap = [
			self::DEBUG => Alert::NOTICE,
			self::CRITICAL => Alert::ERROR,
			self::ERROR => Alert::ERROR,
			self::INFO => Alert::INFO,
			self::WARNING => Alert::ERROR
		];
		
		$level = isset($levelMap[$priority]) ? $levelMap[$priority] : Alert::ERROR;
		
		$this->watchdogClient->logNette($message, $level);
		return $res;
	}


}

