<?php

namespace Message\Mothership\Mailing\Subscription;

use Message\User\UserInterface;

use Message\Cog\DB;
use Message\Cog\ValueObject\DateTimeImmutable;

class Edit implements DB\TransactionalInterface
{
	/**
	 * @var DB\Query
	 */
	protected $_query;

	/**
	 * @var UserInterface
	 */
	protected $_currentUser;

	public function __construct(DB\Query $query, Loader $loader, UserInterface $currentUser)
	{
		$this->_query       = $query;
		$this->_loader      = $loader;
		$this->_currentUser = $currentUser;
	}

	/**
	 * @param DB\Transaction $query transaction to set
	 */
	public function setTransaction(DB\Transaction $query)
	{
		$this->_query = $query;
	}

	/**
	 * Subscribes a Subscriber. Can either be passed an instance of Subscriber
	 * or an email address (string) from which a subscriber may be created.
	 * 
	 * @param  Subscriber|string $subscriber The subscriber
	 * @return Subscriber             The subscriber                                      
	 */
	public function subscribe($subscriber)
	{
		return $this->_setSubscribed($subscriber, true);
	}

	/**
	 * Unsubscribes a Subscriber. Can either be passed an instance of Subscriber
	 * or an email address (string) from which a subscriber may be created.
	 * 
	 * @param  Subscriber|string $subscriber The subscriber
	 * @return Subscriber             The subscriber                                      
	 */
	public function unsubscribe($subscriber)
	{
		return $this->_setSubscribed($subscriber, false);
	}

	protected function _setSubscribed($subscriber, $subscribed)
	{
		if (!($subscriber instanceof Subscriber)) {
			$email = $subscriber;

			$subscriber = new Subscriber;
			$subscriber->email      = $email;
			$subscriber->subscribed = (bool) $subscribed;
		}

		$subscriber->authorship->update(null, $this->_currentUser->id);

		$result = $this->_query->run('
			REPLACE INTO
				email_subscription
			SET
				email      = :email?s,
				subscribed = :subscribed?b,
				updated_at = :updatedAt?d,
				updated_by = :updatedBy?in
		', array(
			'updatedAt'  => $subscriber->authorship->updatedAt(),
			'updatedBy'  => $subscriber->authorship->updatedBy(),
			'email'      => $subscriber->email,
			'subscribed' => $subscriber->subscribed,
		));

		if ($this->_query instanceof DB\Transaction) {
			return $subscriber;
		}

		return $this->_loader->getByEmail($subscriber->email);
	}
}