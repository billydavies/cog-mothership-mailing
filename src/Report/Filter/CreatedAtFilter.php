<?php

namespace Message\Mothership\Mailing\Report\Filter;

use Message\Cog\DB;
use Message\Mothership\Report\Filter\DateRange;
use Message\Mothership\Report\Filter\ModifyQueryInterface;

/**
 * Class CreatedAtFilter
 * @package Message\Mothership\Mailing\Report\Filter
 *
 * @author  Thomas Marchant <thomas@mothership.ec>
 */
class CreatedAtFilter extends DateRange implements ModifyQueryInterface
{
	/**
	 * Set form to a use a `date` field, and set default time period to the last 3 months
	 * {@inheritDoc}
	 */
	public function getForm()
	{
		$this->setFormType('date');

		if (!$this->getStartDate() && !$this->getEndDate()) {
			$this->setStartDate(new \Datetime('- 3 months'));
		}

		return parent::getForm();
	}

	/**
	 * {@inheritDoc}
	 */
	public function apply(DB\QueryBuilder $queryBuilder)
	{
		if ($this->getStartDate()) {
			$queryBuilder->where('email_subscription.created_at >= ?d', [$this->getStartDate()]);
		}

		if ($this->getEndDate()) {
			$queryBuilder->where('email_subscription.created_at <= ?d', [$this->getEndDate()]);
		};
	}
}