<?php

namespace Message\Mothership\Mailing\Bootstrap;

use Message\Mothership\Mailing;

use Message\Cog\Bootstrap\TasksInterface;
use Message\Cog\Service\ContainerAwareInterface;
use Message\Cog\Service\ContainerInterface;

class Tasks implements TasksInterface, ContainerAwareInterface
{
	protected $_services;

	public function setContainer(ContainerInterface $services)
	{
		$this->_services = $services;
	}

	public function registerTasks($tasks)
	{
		$tasks->add(new Mailing\Task\Sync('mailing:sync'), 'Syncs subscribers to the mailing list with the selected third party');
	}
}