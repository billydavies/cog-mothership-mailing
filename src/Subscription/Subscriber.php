<?php

namespace Message\Mothership\Mailing\Subscription;

use Message\Mothership\Mailing\SubscriberInterface;

use Message\Cog\ValueObject\Authorship;

class Subscriber implements SubscriberInterface
{
	/**
	 * @var string
	 */
	public $email;

	/**
	 * @var string
	 */
	public $forename;

	/**
	 * @var string
	 */
	public $surname;

	/**
	 * @var bool
	 */
	public $subscribed;

	/**
	 * @var Authorship
	 */
	public $authorship;

	public function __construct()
	{
		$this->authorship = new Authorship;

		$this->authorship->disableDelete();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName()
	{
		return $this->forename . ($this->surname ? ' ' . $this->surname : '');
	}

	/**
	 * {@inheritDoc}
	 */
	public function getLastModified()
	{
		return $this->authorship->updatedAt();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSubscribedState()
	{
		if ($this->isSubscribed()) {
			return self::STATE_SUBSCRIBED;
		}

		return self::STATE_UNSUBSCRIBED;
	}

	/**
	 * @return boolean true if subscribed
	 */
	public function isSubscribed()
	{
		return true === $this->subscribed;
	}
}