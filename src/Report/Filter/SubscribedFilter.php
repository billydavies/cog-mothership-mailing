<?php

namespace Message\Mothership\Mailing\Report\Filter;


class SubscribedFilter extends AbstractBoolFilter
{
	const NAME = 'subscribed';
	const LABEL = 'Subscribed?';

	protected function _getName()
	{
		return self::NAME;
	}

	protected function _getLabel()
	{
		return self::LABEL;
	}

	protected function _getStatement($choice)
	{
		$this->_validateChoice($choice);

		return 'email_subscription.subscribed = ' . (int) ($choice === 'yes');
	}
}