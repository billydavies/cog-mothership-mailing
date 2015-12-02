<?php

namespace Message\Mothership\Mailing\Bootstrap;

use Message\Cog\Bootstrap\EventsInterface;
use Message\Mothership\Mailing\Event;

class Events implements EventsInterface
{
	public function registerEvents($dispatcher)
	{
		$dispatcher->addSubscriber(new Event\EventListener);
	}
}