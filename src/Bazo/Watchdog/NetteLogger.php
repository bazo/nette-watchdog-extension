<?php

namespace Bazo\Watchdog;


use Bazo\Watchdog\WatchdogClient;

/**
 * @author Martin
 */
class NetteLogger implements \Tracy\ILogger
{

	/** @var WatchdogClient */
	private $watchdogClient;

	public function __construct(WatchdogClient $watchdogClient)
	{
		$this->watchdogClient	 = $watchdogClient;
	}


	public function log($message, $priority = self::INFO)
	{
		$res		 = parent::log($message, $priority);
		$levelMap	 = [
			self::DEBUG		 => Alert::NOTICE,
			self::CRITICAL	 => Alert::ERROR,
			self::ERROR		 => Alert::ERROR,
			self::INFO		 => Alert::INFO,
			self::WARNING	 => Alert::ERROR
		];

		$level = isset($levelMap[$priority]) ? $levelMap[$priority] : Alert::ERROR;

		$this->watchdogClient->logNette($message, $level);
		return $res;
	}


}
