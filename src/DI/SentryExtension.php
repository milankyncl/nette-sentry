<?php

/**
 * Sentry-Nette extension.
 * @copyright 2018
 */

namespace MilanKyncl\Nette\Sentry\DI;

use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;
use Nette\Utils\AssertionException;
use Nette\Utils\Validators;
use MilanKyncl\Nette\Sentry\SentryBridge;

/**
 * Class SentryExtension
 */
class SentryExtension extends CompilerExtension
{
	/** @var array */
	private $defaults = [];

	/**
	 * SentryExtension constructor.
	 */
	public function __construct()
	{
		$this->defaults = $this->getDefaults();
	}

	/**
	 * afterCompile DI method.
	 *
	 * @param ClassType $class
	 * @return void
	 * @throws AssertionException
	 */
	public function afterCompile(ClassType $class)
	{
		$config = $this->getConfig($this->defaults);

		Validators::assertField($config, 'dsn', 'string');

		if (method_exists($class, 'getMethod'))
			$init = $class->getMethod('initialize');

		else
			$init = $class->methods['initialize'];

		$code = '$sentry = new '.SentryBridge::class.'(?, ?);' . PHP_EOL;

		$init->addBody($code, $config);
	}

	/**
	 * register DI method.
	 *
	 * @param Configurator $configurator
	 * @return void
	 */
	public static function register(Configurator $configurator)
	{
		$configurator->onCompile[] = function ($config, Compiler $compiler) {
			$compiler->addExtension('sentry', new SentryExtension());
		};
	}

	/**
	 * Get default preferences.
	 *
	 * @return array
	 */
	private function getDefaults(): array
	{
		$defaults = [];
		$defaults['dsn'] = null;
		$defaults['release'] = null;

		return $defaults;
	}
}
