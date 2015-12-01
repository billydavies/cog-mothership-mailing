<?php

namespace Message\Mothership\Mailing\Report\Filter;

class UserFilter extends AbstractBoolFilter
{
	const NAME = 'has_user';
	const LABEL = 'Has user account';

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

		return 'user.user_id IS ' . ($choice === 'yes' ? 'NOT ' : '') . 'NULL';
	}
}