<?php

namespace Message\Mothership\Mailing\ThirdParty\Sync;

use Message\Mothership\Mailing\SubscriberInterface;

/**
 * @author Sam Trangmar-Keates <samtkeates@gmail.com>
 *
 * This is the default adapter, it does nothing and does not allow syncing. 
 */
class NoneAdapter implements AdapterInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function getName()
	{
		return 'none';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getAllSubscribers(\DateTime $since = null)
	{
		return [];
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSubscriber($email)
	{
		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function subscribe(SubscriberInterface $subscriber)
	{
		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function unsubscribe(SubscriberInterface $subscriber)
	{
		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSyncable()
	{
		return false;
	}
}