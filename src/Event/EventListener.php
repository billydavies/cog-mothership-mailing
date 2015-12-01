<?php

namespace Message\Mothership\Mailing\Event;

use Message\Mothership\Mailing\Report\Subscribers;
use Message\Cog\Event\EventListener as BaseListener;
use Message\Cog\Event\SubscriberInterface;
use Message\Mothership\Report\Event as ReportEvents;
use Message\Mothership\Report\Filter\ModifyQueryInterface;

/**
 * Class EventListener
 * @package Message\Mothership\Mailing\Event
 *
 * @author  Thomas Marchant <thomas@mothership.ec>
 */
class EventListener extends BaseListener implements SubscriberInterface
{
	/**
	 * {@inheritDoc}
	 */
	static public function getSubscribedEvents()
	{
		return [
			ReportEvents\Events::REGISTER_REPORTS => [
				['registerReports']
			],
			Subscribers::MAILING_SUBSCRIBER_REPORT => [
				['applySubscriberReportFilters']
			],
		];
	}

	/**
	 * Register reports to reports module
	 *
	 * @param ReportEvents\BuildReportCollectionEvent $event
	 */
	public function registerReports(ReportEvents\BuildReportCollectionEvent $event)
	{
		foreach ($this->get('mailing.reports') as $report) {
			$event->registerReport($report);
		}
	}

	/**
	 * Apply filters to subscriber
	 *
	 * @param ReportEvents\ReportEvent $event
	 */
	public function applySubscriberReportFilters(ReportEvents\ReportEvent $event)
	{
		foreach ($event->getQueryBuilders() as $queryBuilder) {
			foreach ($event->getFilters() as $filter) {
				if ($filter instanceof ModifyQueryInterface) {
					$filter->apply($queryBuilder);
				}
			}
		}
	}
}