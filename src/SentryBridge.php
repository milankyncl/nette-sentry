<?php

/**
 * Sentry-Nette bridge.
 * @copyright 2018
 */

namespace MilanKyncl\Nette\Sentry;

use Exception;
use function Sentry\configureScope;
use function Sentry\init;
use Sentry\State\Scope;
use Tracy\Debugger;
use Tracy\Logger;
use Nette\Security\User;

/**
 * Class SentryBridge
 * @author Milan Kyncl <kontakt@milankyncl.cz>
 */
class SentryBridge extends Logger
{
	/** @var User @inject */
	public $user;

	/** @var bool */
	private $isEnabled = true;

	/**
	 * SentryBridge constructor.
	 * @param string $dsn
	 * @param string $release
	 */
	public function __construct($dsn, $release = null)
	{
		parent::__construct(Debugger::$logDirectory, Debugger::$email, Debugger::getBlueScreen());

		$settings = [
			'dsn' => $dsn
		];

		if($release != null) {
			$settings['release'] = $release;
		}

		init($settings);

		configureScope(function (Scope $scope): void {
			$scope->setUser([
				'id' => $this->user->getId(),
				''
			]);
		});
	}

	/**
	 * onFatalError handler.
	 *
	 * @param $error
	 */
	public function onFatalError($error)
	{
		if($this->isEnabled) {
			$this->client->captureException($error);
		}
	}

	/**
	 * Log message to client.
	 *
	 * @param mixed $message
	 * @param string $priority
	 *
	 * @return null|string
	 */
	public function log($message, $priority = self::INFO) {

		if($this->isEnabled) {
			$data = $message instanceof Exception ? $this->getExceptionFile($message) : null;
			$data = $this->formatLogLine($message, $data);
			$this->client->captureException($message, $data, $priority);
		}

		return parent::log($message, $priority);
	}
}
