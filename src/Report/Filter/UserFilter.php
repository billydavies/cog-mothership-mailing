<?php

namespace Message\Mothership\Mailing\Report\Filter;

/**
 * Class UserFilter
 * @package Message\Mothership\Mailing\Report\Filter
 *
 * @author  Thomas Marchant <thomas@mothership.ec>
 */
class UserFilter extends AbstractBoolFilter
{
	const NAME = 'has_user';
	const LABEL = 'Has user account';

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

		return 'user.user_id IS ' . ($choice === 'yes' ? 'NOT ' : '') . 'NULL';
	}
}