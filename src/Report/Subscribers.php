<?php

namespace Message\Mothership\Mailing\Report;

use Message\Cog\DB;
use Message\Cog\Routing\UrlGenerator;
use Message\Cog\Event\Dispatcher as EventDispatcher;
use Message\Mothership\Report\Event\ReportEvent;
use Message\Mothership\Report\Report\AbstractReport;
use Message\Mothership\Report\Report\AppendQuery\FilterableInterface;
use Message\Mothership\Report\Filter\Collection as FilterCollection;
use Message\Mothership\Report\Chart\TableChart;

/**
 * Class Subscribers
 * @package Message\Mothership\Mailing\Report
 *
 * @author  Thomas Marchant <thomas@mothership.ec>
 *
 * Report for displaying subscriber information.
 *
 * Displays:
 * - Email address
 * - Date subscribed
 * - Whether subscriber is still subscribed
 * - Whether subscriber has a user account
 */
class Subscribers extends AbstractReport implements FilterableInterface
{
	const MAILING_SUBSCRIBER_REPORT = 'mailing.subscriber.report';

	/**
	 * @var DB\QueryBuilder
	 */
	private $_queryBuilder;

	/**
	 * @var EventDispatcher
	 */
	private $_dispatcher;

	/**
	 * @param DB\QueryBuilderFactory $builderFactory
	 * @param UrlGenerator $routingGenerator
	 * @param FilterCollection $filters
	 * @param EventDispatcher $dispatcher
	 */
	public function __construct(
		DB\QueryBuilderFactory $builderFactory,
		UrlGenerator $routingGenerator,
		FilterCollection $filters,
		EventDispatcher $dispatcher
	)
	{
		parent::__construct($builderFactory, $routingGenerator);
		$this->_setName('mailing_subscribers');
		$this->_setDisplayName('Subscribers');
		$this->_setReportGroup('Mailing');
		$this->_charts = [new TableChart];
		$this->_filters = $filters;
		$this->_dispatcher = $dispatcher;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getCharts()
	{
		$data = $this->_dataTransform($this->_getQuery()->run(), "json");
		$rawColumns = $this->getColumns();
		$columns = [];

		foreach ($rawColumns as $name => $type) {
			$columns[] = ['type' => $type, 'name' => $name];
		}

		foreach ($this->_charts as $chart) {
			$chart->setColumns(json_encode($columns));
			$chart->setData($data);
		}

		return $this->_charts;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getQueryBuilder()
	{
		if (null === $this->_queryBuilder) {
			$this->_queryBuilder = $this->_builderFactory->getQueryBuilder()
				->select([
					'email_subscription.email',
					'email_subscription.subscribed',
					'email_subscription.created_at',
					'email_subscription.updated_at',
					'user.user_id'
				], true)
				->from('email_subscription')
				->leftJoinUsing('user', 'email');

			$this->_dispatchEvent();
		}

		return $this->_queryBuilder;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getColumns()
	{
		return [
			'Email' => 'string',
			'Created at' => 'number',
			'Subscribed' => 'string',
			'Has account' => 'string',
		];
	}

	/**
	 * {@inheritDoc}
	 */
	protected function _dataTransform($data, $output = null)
	{
		$result = [];

		switch ($output) {
			case 'json' :
				foreach ($data as $row) {
					$result[] = [
						$row->user_id ? $this->_getUserLink($row->user_id, $row->email) : $row->email,
						['v' => (int) $row->created_at, 'f' => date('Y-m-d H:i', $row->created_at)],
						$row->subscribed ? 'Yes' : 'No',
						$row->user_id ? 'Yes' : 'No'
					];
				}

				return json_encode($result);
			default :
				foreach ($data as $row) {
					$result[] = [
						utf8_encode(trim($row->email)),
						date('Y-m-d H:i', $row->created_at),
						$row->subscribed ? 'Yes' : 'No',
						$row->user_id ? 'Yes' : 'No'
					];
				}

				return $result;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function _getQuery()
	{
		return $this->getQueryBuilder()->getQuery();
	}

	/**
	 * Get data for a user link to pass to the json data
	 *
	 * @param $userID
	 * @param $value
	 *
	 * @return array
	 */
	private function _getUserLink($userID, $value)
	{
		return [
			'v' => utf8_encode(trim($value)),
			'f' => '<a href="' .
				$this->generateUrl('ms.cp.user.admin.detail.edit', ['userID' => $userID]) .
				'">' . utf8_encode(trim($value)) . '</a>'
		];
	}

	/**
	 * Dispatch effect to apply filters
	 */
	private function _dispatchEvent()
	{
		$event = new ReportEvent;
		$event->setFilters($this->_filters);
		$event->addQueryBuilder($this->_queryBuilder);

		$this->_dispatcher->dispatch(self::MAILING_SUBSCRIBER_REPORT, $event);
	}
}