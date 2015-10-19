<?php

namespace Message\Mothership\Mailing\Bootstrap;

use Message\Cog\Bootstrap\RoutesInterface;

class Routes implements RoutesInterface
{
	public function registerRoutes($router)
	{
		$router->add('mailing.subscribe.action', '/mailing/subscribe', 'Message:Mothership:Mailing::Controller:Subscribe#action')
			->setMethod('POST');
	}
}