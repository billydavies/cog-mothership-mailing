<?php

namespace Message\Mothership\Mailing\Event;

use Message\Cog\Event\EventListener as BaseListener;
use Message\Cog\Event\SubscriberInterface;
use Message\Mothership\Report\Event as ReportEvents;

class EventListener extends BaseListener implements SubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return [
			ReportEvents\Events::REGISTER_REPORTS => [
				['registerReports']
			],
		];
	}

	public function registerReports(ReportEvents\BuildReportCollectionEvent $event)
	{
		foreach ($this->get('mailing.reports') as $report) {
			$event->registerReport($report);
		}
	}
}