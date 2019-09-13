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

		if($this->user->isLoggedIn()) {
			configureScope(function (Scope $scope): void {
				$scope->setUser([
					'id' => $this->user->getId()
				]);
			});
		}
	}
}
