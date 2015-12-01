<?php

namespace Message\Mothership\Mailing\Report\Filter;

/**
 * Class SubscribedFilter
 * @package Message\Mothership\Mailing\Report\Filter
 *
 * @author  Thomas Marchant <thomas@mothership.ec>
 */
class SubscribedFilter extends AbstractBoolFilter
{
	const NAME = 'subscribed';
	const LABEL = 'Subscribed?';

	/**
	 * {@inheritDoc}
	 */
	protected function _getName()
	{
		return self::NAME;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function _getLabel()
	{
		return self::LABEL;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function _getStatement($choice)
	{
		$this->_validateChoice($choice);

		return 'email_subscription.subscribed = ' . (int) ($choice === 'yes');
	}
}