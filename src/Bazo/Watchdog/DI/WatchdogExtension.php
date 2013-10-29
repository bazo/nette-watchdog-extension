<?php

namespace Bazo\Watchdog\DI;

/**
 * WatchdogExtension
 *
 * @author Martin Bažík
 */
class WatchdogExtension extends \Nette\DI\CompilerExtension
{

	/** @var array */
	public $defaults = [
		'appId' => NULL,
		'appKey' => NULL,
		'server' => 'http://watchdog.pagodabox.com',
		'useLogger' => TRUE
	];

	private $useLogger;

	/**
	 * Processes configuration data
	 *
	 * @return void
	 */
	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();

		$config = $this->getConfig($this->defaults, TRUE);
		$this->useLogger = $config['useLogger'];
		
		$container->addDefinition($this->prefix('client'))
				->setClass('\Bazo\Watchdog\WatchdogClient', $config);

		$container->addDefinition('netteLogger')
				->setClass('\Bazo\Watchdog\NetteLogger')
				->setFactory('@container::getService', [$this->prefix('netteLogger')]);

		$container->addDefinition($this->prefix('logger'))
				->setClass('\Bazo\Watchdog\NetteLogger')
				->setAutowired(FALSE);

		$container->addDefinition('watchdogLogger')
				->setClass('\Bazo\Watchdog\NetteLogger')
				->setFactory('@container::getService', [$this->prefix('logger')]);
	}


	public function afterCompile(\Nette\PhpGenerator\ClassType $class)
	{
		if ($this->useLogger === TRUE) {
			$initialize = $class->methods['initialize'];
			$initialize->addBody('\Nette\Diagnostics\Debugger::$logger = $this->getService(?);', [$this->prefix('netteLogger')]);
		}
	}


}

