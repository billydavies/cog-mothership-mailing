<?php

namespace Message\Mothership\Mailing\Report\Filter;

use Message\Cog\DB;
use Message\Mothership\Report\Filter\Choices;
use Message\Mothership\Report\Filter\ModifyQueryInterface;

abstract class AbstractBoolFilter extends Choices implements ModifyQueryInterface
{
	public function __construct()
	{
		$this->setFormChoices([
			'yes' => 'Yes',
			'no' => 'No',
		]);

		parent::__construct($this->_getName(), $this->_getLabel());
	}

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

	protected function _validateChoice($choice)
	{
		if (!is_string($choice)) {
			throw new \InvalidArgumentException('Choice must be a string, ' . gettype($choice) . ' given');
		}

		if (!in_array($choice, ['yes', 'no'])) {
			throw new \LogicException('Choice must be either `yes` or `no`, `' . $choice , '` given');
		}
	}

	abstract protected function _getName();
	abstract protected function _getLabel();
	abstract protected function _getStatement($choice);
}