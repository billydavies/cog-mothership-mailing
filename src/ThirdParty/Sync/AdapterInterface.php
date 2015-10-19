<?php

namespace Message\Mothership\Mailing\ThirdParty\Sync;

use Message\Mothership\Mailing\SubscriberInterface;

/**
 * Interface defining a third-party sync adapter.
 *
 * Implementations of this interface should handle retrieving a list of
 * subscribers; getting details for a specific subscriber and for subscribing
 * and unsubscribing.
 *
 * @author Joe Holdcroft <joe@message.co.uk>
 */
interface AdapterInterface
{
	/**
	 * Gets the adapter name
	 *
	 * @return string
	 */
	public function getName();
	
	/**
	 * Get all subscribers (subscribed or unsubscribed) that have been modified
	 * since a given date/time.
	 *
	 * If the date/time is null, all subscribers regardless of their last
	 * modified time should be returned.
	 *
	 * @param  \DateTime|null $since      "Since" date & time, or null to get ALL
	 *                                    subscribers
	 *
	 * @return array[SubscriberInterface] Array of relevant subscribers
	 */
	public function getAllSubscribers(\DateTime $since = null);

	/**
	 * Get details for a subscriber by email address.
	 *
	 * @param  string $email            Email to get the subscriber for
	 *
	 * @return SubscriberInterface|null Subscriber, or null if not found
	 */
	public function getSubscriber($email);

	/**
	 * Add a subscriber at the third party.
	 *
	 * @param  SubscriberInterface $subscriber The subscriber to subscribe
	 *
	 * @return boolean                         True on success, false on failure
	 */
	public function subscribe(SubscriberInterface $subscriber);

	/**
	 * Unsubscribe a subscriber at the third party.
	 *
	 * @param  SubscriberInterface $subscriber The subscriber to unsubscribe
	 *
	 * @return boolean                         True on success, false on failure
	 */
	public function unsubscribe(SubscriberInterface $subscriber);

	/**
	 * Return true if the adapter allows syncronization with a remote
	 * 
	 * @return boolean
	 */
	public function isSyncable();
}