<?php

namespace Message\Mothership\Mailing;

interface SubscriberInterface
{
	const STATE_SUBSCRIBED   = 1;
	const STATE_UNSUBSCRIBED = 0;

	/**
	 * Gets the subscriber's email
	 * 
	 * @return string Email address
	 */
	public function getEmail();

	/**
	 * Gets the subscriber's fullname
	 * 
	 * @return string The subscriber's full name
	 */
	public function getName();

	/**
	 * Get's when the subscriber was last modified
	 * 
	 * @return \DateTime Last modified time
	 */
	public function getLastModified();

	/**
	 * Gets the subscribed state, will be one of
	 * - SubscriberInterface::STATE_SUBSCRIBED
	 * - SubscriberInterface::STATE_UNSUBSCRIBED
	 * 
	 * @return int The subscribed state
	 */
	public function getSubscribedState();
}