<?php

namespace Message\Mothership\Mailing\Report\Filter;

use Message\Cog\DB;
use Message\Mothership\Report\Filter\Choices;
use Message\Mothership\Report\Filter\ModifyQueryInterface;

/**
 * Class AbstractBoolFilter
 * @package Message\Mothership\Mailing\Report\Filter
 *
 * @author  Thomas Marchant <thomas@mothership.ec>
 *
 * Abstract class representing a filter that only has yes or no options
 */
abstract class AbstractBoolFilter extends Choices implements ModifyQueryInterface
{
	/**
	 * Set form choices in constructor and get name and label from child class
	 */
	public function __construct()
	{
		$this->setFormChoices([
			'yes' => 'Yes',
			'no' => 'No',
		]);

		parent::__construct($this->_getName(), $this->_getLabel());
	}

	/**
	 * {@inheritDoc}
	 * @throws \InvalidArgumentException     Throws exception if choice is not a string or array
	 */
	public function addChoice($choice)
	{
		if (!is_string($choice) && !is_array($choice)) {
			throw new \InvalidArgumentException('Choice must be a string or an array');
		}

		if (is_string($choice)) {
			$choice = [$choice => $choice];
		}

		$this->setChoices($choice + $this->getChoices());

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function apply(DB\QueryBuilder $queryBuilder)
	{
		$choice = $this->getChoices();

		if (is_array($choice)) {
			$choice = array_shift($choice);
		}

		if (!$choice) {
			return;
		}

		$queryBuilder->where($this->_getStatement($choice));
	}

	/**
	 * Validate that the choice value is either equal to 'yes' or 'no'
	 *
	 * @param $choice
	 */
	protected function _validateChoice($choice)
	{
		if (!is_string($choice)) {
			throw new \InvalidArgumentException('Choice must be a string, ' . gettype($choice) . ' given');
		}

		if (!in_array($choice, ['yes', 'no'])) {
			throw new \LogicException('Choice must be either `yes` or `no`, `' . $choice , '` given');
		}
	}

	/**
	 * Get the name of the filter
	 *
	 * @return string
	 */
	abstract protected function _getName();

	/**
	 * Get the label for the filter
	 *
	 * @return string
	 */
	abstract protected function _getLabel();

	/**
	 * Get the WHERE statement depending on the choice
	 *
	 * @param $choice
	 *
	 * @return string
	 */
	abstract protected function _getStatement($choice);
}