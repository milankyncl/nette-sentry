<?php

/**
 * Sentry-Nette bridge.
 * @copyright 2018
 */

namespace MilanKyncl\Nette\Sentry;

use function Sentry\init;
use Tracy\Debugger;
use Tracy\Logger;
use Nette\Security\User;

/**
 * Class SentryBridge
 * @author Milan Kyncl <kontakt@milankyncl.cz>
 */
class SentryBridge extends Logger
{
	/**
	 * SentryBridge constructor.
	 * @param string $dsn
	 * @param string $release
	 */
	public function __construct(User $user, $dsn, $release = null)
	{
		parent::__construct(Debugger::$logDirectory, Debugger::$email, Debugger::getBlueScreen());

		$settings = [
			'dsn' => $dsn
		];

		if($release != null) {
			$settings['release'] = $release;
		}

		init($settings);
	}
}