<?php

namespace Bazo\Watchdog\DI;


/**
 * @author Martin Bažík
 */
class WatchdogExtension extends \Nette\DI\CompilerExtension
{

	/** @var array */
	public $defaults = [
		'appId'		 => NULL,
		'appKey'	 => NULL,
		'server'	 => 'http://watchdog.pagodabox.com',
		'useLogger'	 => TRUE
	];
	private $useLogger;

	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();

		$config			 = $this->getConfig($this->defaults, TRUE);
		$this->useLogger = $config['useLogger'];
		unset($config['useLogger']);

		$container->addDefinition($this->prefix('client'))
				->setClass(\Bazo\Watchdog\WatchdogClient::class, $config);

		$container->addDefinition($this->prefix('logger'))
				->setClass(\Bazo\Watchdog\NetteLogger::class)
				->setAutowired(FALSE);

		$container->addDefinition('watchdogLogger')
				->setClass(\Bazo\Watchdog\NetteLogger::class)
				->addTag('logger')
				->setFactory('@container::getService', [$this->prefix('logger')]);
	}


	public function afterCompile(\Nette\PhpGenerator\ClassType $class)
	{
		if ($this->useLogger === TRUE) {
			$initialize = $class->methods['initialize'];
			$initialize->addBody('\Nette\Diagnostics\Debugger::$logger = $this->getService(?);', [$this->prefix('logger')]);
		}
	}


}
