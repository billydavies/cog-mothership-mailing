<?php

namespace Message\Mothership\Mailing\ThirdParty\Sync;

use Message\Cog\ValueObject\Collection as BaseCollection;

/**
 * Collection for adapters
 *
 * @author Sam Trangmar-Keates <samtkeates@gmail.com>
 */
class AdapterCollection extends BaseCollection
{
	protected function _configure()
	{
		$this->setType('\\Message\\Mothership\\Mailing\\ThirdParty\\Sync\\AdapterInterface');
		$this->setSort(null);
		$this->setKey(function($x) {
			return $x->getName();
		});
	}
}