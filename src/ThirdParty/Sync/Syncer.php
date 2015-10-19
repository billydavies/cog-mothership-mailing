<?php

namespace Message\Mothership\Mailing\ThirdParty\Sync;

use Message\Mothership\Mailing\SubscriberInterface;
use Message\Mothership\Mailing\Subscription;

use Psr\Log\LoggerInterface;

class Syncer
{
	/**
	 * @var AdapterInterface
	 */
	protected $_adapter;

	/**
	 * @var LoggerInterface
	 */
	protected $_logger;

	/**
	 * @var Subscription\Loader
	 */
	protected $_subscriberLoader;

	/**
	 * @var Subscription\Edit
	 */
	protected $_subscriberEdit;

	/**
	 * @var \DateTime
	 */
	protected $_lastRunTime;

	public function __construct(AdapterInterface $adapter, Subscription\Loader $subscriberLoader,
		Subscription\Edit $subscriptionEdit,
		LoggerInterface $logger
	)
	{
		$this->_adapter           = $adapter;
		$this->_logger           = $logger;
		$this->_subscriberLoader = $subscriberLoader;
		$this->_subscriberEdit   = $subscriptionEdit;
	}

	/**
	 * Sets the last run time
	 * @param \DateTime|null $lastRunTime The last run time
	 */
	public function setLastRunTime(\DateTime $lastRunTime = null)
	{
		$this->_lastRunTime = $lastRunTime;
	}

	/**
	 * Runs the sync
	 */
	public function run()
	{
		// no adapter set so return null on run
		if (!$this->_adapter->isSyncable()) {
			return;
		}

		// MAKE SURE THIS WORKS FOR WHEN NO LAST RUN TIME IS SET ALSO
		$thirdPartySubscribers = $this->_adapter->getAllSubscribers($this->_lastRunTime);
		$localSubscribers      = $this->_lastRunTime
			? $this->_subscriberLoader->getModifiedSince($this->_lastRunTime)
			: $this->_subscriberLoader->getAll();

		$this->_logger->info('Running sync.');
		$this->_logger->info($this->_lastRunTime
			? 'Last run at: ' . $this->_lastRunTime->format('Y-m-d h:ia')
			: 'No last run time defined.'
		);

		$this->_logger->info(count($thirdPartySubscribers) . ' subscribers at third party to sync.');
		$this->_logger->info(count($localSubscribers) . ' subscribers locally to sync.');
		$this->_logger->info('Resolving third party subscribers now.');

		foreach ($thirdPartySubscribers as $subscriber) {
			$this->_resolveSubscriber($subscriber);
		}

		$this->_logger->info('Resolving local subscribers now.');

		foreach ($localSubscribers as $subscriber) {
			$this->_resolveSubscriber(null, $subscriber);
		}
	}

	/**
	 * Resolve a what needs to happen with a subscriber, and do it.
	 *
	 * At least one argument must be passed. If just the third party subscriber
	 * is passed, it will try to load the same subscriber from the local
	 * database. If just the local subscriber is passed, it will try to load the
	 * same subscriber from the third party.
	 *
	 * Assuming the subscriber was found both locally and at the third party and
	 * they have different subscription states, the "last modified" dates of
	 * both are checked and if the local subscriber was more recently modified,
	 * their local state is applied at the third party. If the third party
	 * subscriber was more recently modified, their third party state is applied
	 * locally.
	 *
	 * @param  SubscriberInterface|null $thirdPartySubscriber The subscriber from
	 *                                                        thethird party
	 * @param  SubscriberInterface|null $localSubscriber      The same subscriber
	 *                                                        from the local database
	 *
	 * @return boolean True is something had to be changed, false otherwise
	 */
	protected function _resolveSubscriber(SubscriberInterface $thirdPartySubscriber = null, SubscriberInterface $localSubscriber = null)
	{
		// Throw exception if neither a third party nor local subscriber was passed
		if (null === $thirdPartySubscriber && null === $localSubscriber) {
			throw new \InvalidArgumentException('Cannot resolve subscriber: either a local or a third party subscriber (or both) must be passed');
		}

		// If the third party subscriber was not passed, try to load it
		if (null === $thirdPartySubscriber) {
			$thirdPartySubscriber = $this->_adapter->getSubscriber($localSubscriber->getEmail());
		}

		// If the local subscriber was not passed, try to load it
		if (null === $localSubscriber) {
			$localSubscriber = $this->_subscriberLoader->getByEmail($thirdPartySubscriber->getEmail());
		}

		// If the local and third party state for the subscriber is the same, continue
		if (null !== $thirdPartySubscriber && null !== $localSubscriber
		 && $thirdPartySubscriber->getSubscribedState() === $localSubscriber->getSubscribedState()) {
			$this->_logger->info(sprintf(
				'Skipping `%s`: %s both locally and at the third party',
				$localSubscriber->getEmail(),
				($localSubscriber::STATE_SUBSCRIBED === $localSubscriber->getSubscribedState())
					? 'SUBSCRIBED'
					: 'UNSUBSCRIBED'
			));

			return false;
		}

		// If the subscriber was not found locally, apply their state locally
		if (null === $localSubscriber) {
			$this->_logger->info(sprintf('`%s` does not exist locally', $thirdPartySubscriber->getEmail()));

			$this->_updateSubscriberLocally($thirdPartySubscriber);
		}
		// If the subscriber was not found at the third party, apply their state there
		else if (null === $thirdPartySubscriber) {
			$this->_logger->info(sprintf('`%s` does not exist at third party', $localSubscriber->getEmail()));

			$this->_updateSubscriberAtThirdParty($localSubscriber);
		}
		// Apply subscriber's state to third party if was updated locally more recently
		else if ($localSubscriber->getLastModified() > $thirdPartySubscriber->getLastModified()) {
			$this->_logger->info(sprintf('`%s` more recently updated locally', $localSubscriber->getEmail()));

			$this->_updateSubscriberAtThirdParty($localSubscriber);
		}
		// Otherwise, apply their state locally
		else {
			$this->_logger->info(sprintf('`%s` more recently updated at third party', $localSubscriber->getEmail()));

			$this->_updateSubscriberLocally($thirdPartySubscriber);
		}

		return true;
	}

	protected function _updateSubscriberAtThirdParty(SubscriberInterface $subscriber)
	{
		$subscribe = $subscriber::STATE_SUBSCRIBED === $subscriber->getSubscribedState();

		$this->_logger->info(sprintf(
			'`%s` %s at third party',
			$subscriber->getEmail(),
			$subscribe ? 'SUBSCRIBED' : 'UNSUBSCRIBED'
		));

		if ($subscribe) {
			$this->_adapter->subscribe($subscriber);
		}
		else {
			$this->_adapter->unsubscribe($subscriber);
		}
	}

	protected function _updateSubscriberLocally(SubscriberInterface $subscriber)
	{
		$subscribe = $subscriber::STATE_SUBSCRIBED === $subscriber->getSubscribedState();

		$this->_logger->info(sprintf(
			'`%s` %s locally',
			$subscriber->getEmail(),
			$subscribe ? 'SUBSCRIBED' : 'UNSUBSCRIBED'
		));

		if ($subscribe) {
			$this->_subscriberEdit->subscribe($subscriber->getEmail());
		}
		else {
			$this->_subscriberEdit->unsubscribe($subscriber->getEmail());
		}
	}
}