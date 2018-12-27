<?php

/**
 * Sentry-Nette extension.
 *
 * @author Milan Kyncl
 * @copyright 2018
 */

namespace MilanKyncl\Nette\Sentry\DI;

use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;
use Nette\Utils\Validators;
use MilanKyncl\Nette\Sentry\SentryBridge;
use Tracy\Debugger;

/**
 * Class SentryExtension
 *
 * @package MilanKyncl\Nette\Sentry\DI
 */

class SentryExtension extends CompilerExtension {

	/** @var array */

	private $defaults = [];

	/**
	 * SentryExtension constructor.
	 */

	public function __construct() {

		$this->defaults = $this->getDefaults();
	}

	/**
	 * afterCompile DI method.
	 *
	 * @param ClassType $class
	 *
	 * @throws \Nette\Utils\AssertionException
	 */

	public function afterCompile(ClassType $class) {

		$config = $this->getConfig($this->defaults);

		Validators::assertField($config, 'dsn', 'string');

		if (method_exists($class, 'getMethod'))
			$init = $class->getMethod('initialize');

		else
			$init = $class->methods['initialize'];

		$code = '$sentry = new '.SentryBridge::class.'(?, ?, ?);' . PHP_EOL;
		$code .= Debugger::class.'::$onFatalError[] = function($e) use ($sentry) {$sentry->onFatalError($e);};' . PHP_EOL;
		$code .= Debugger::class.'::setLogger($sentry);';

		$init->addBody($code, $config);
	}

	/**
	 * register DI method.
	 *
	 * @param Configurator $configurator
	 */

	public static function register(Configurator $configurator) {

		$configurator->onCompile[] = function ($config, Compiler $compiler) {
			$compiler->addExtension('sentry', new SentryExtension());
		};
	}

	/**
	 * Get default preferences.
	 *
	 * @return array
	 */

	private function getDefaults() {

		$defaults = [];

		$defaults['dsn'] = null;
		$defaults['debug'] = false;
		$defaults['options'] = [];

		return $defaults;
	}
}
