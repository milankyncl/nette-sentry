<?php

/**
 * Sentry-Nette bridge.
 *
 * @author Milan Kyncl
 * @copyright 2018
 */

namespace MilanKyncl\Nette\Sentry;

use Exception;
use Tracy\Debugger;
use Tracy\Logger;
use Nette\Security\User;

/**
 * Class SentryBridge
 *
 * @package MilanKyncl\Nette\Sentry
 */

class SentryBridge extends Logger {

	/** @var User @inject */

	public $user;

	/** @var \Raven_Client */

	private $client;

	/** @var bool */

	private $isEnabled = true;

	/**
	 * Sentry constructor.
	 *
	 * @param mixed $dsn
	 * @param mixed $isDebugMode
	 * @param mixed $options
	 */

	public function __construct($dsn, $isDebugMode = false, $options = []) {

		parent::__construct(Debugger::$logDirectory, Debugger::$email, Debugger::getBlueScreen());

		$this->isEnabled = Debugger::$productionMode || $isDebugMode;
		$this->isEnabled = true;
		$this->client = new \Raven_Client($dsn, $options);

		$sentry = $this;

		Debugger::$onFatalError[] = function ($error) use ($sentry) {

			$sentry->onFatalError($error);
		};

		Debugger::setLogger($this);
	}

	/**
	 * onFatalError handler.
	 *
	 * @param $error
	 */

	public function onFatalError($error) {

		if ($this->isEnabled) {

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

		if ($this->isEnabled) {

			$data = $message instanceof Exception ? $this->getExceptionFile($message) : null;
			$data = $this->formatLogLine($message, $data);
			$this->client->captureException($message, $data, $priority);
		}

		return parent::log($message, $priority);
	}
}
