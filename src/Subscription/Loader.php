<?php

namespace Message\Mothership\Mailing\Subscription;

use Message\User\User;

use Message\Cog\DB;
use Message\Cog\ValueObject\DateTimeImmutable;

class Loader
{
	/**
	 * @var DB\Query
	 */
	protected $_query;

	public function __construct(DB\Query $query)
	{
		$this->_query = $query;
	}

	/**	
	 * Gets a subscriber by email
	 * 
	 * @param  string $email The email to load by
	 * @return Subscriber    The loaded subscriber
	 */
	public function getByEmail($email)
	{
		return $this->_load($email);
	}

	/**
	 * Gets a Subscriber based on a User using their email
	 * 
	 * @param  User       $user The user to load by
	 * @return Subscriber       The loaded Subscriber
	 */
	public function getByUser(User $user)
	{
		return $this->_load($user->email);
	}

	/**
	 * Gets all subscribers which have been updated since a given date
	 * 
	 * @param  \DateTime $date   The date to load from
	 * @return Subscriber[]      Array of loaded subscribers
	 */
	public function getModifiedSince(\DateTime $date)
	{
		$result = $this->_query->run("
			SELECT
				email
			FROM
				email_subscription
			WHERE
				updated_at > :date?d
		", array('date' => $date));

		return $this->_load($result->flatten(), true);
	}

	/**
	 * Gets all subscribers
	 * 
	 * @return Subscriber[] An array of all the subscribers
	 */
	public function getAll()
	{
		$result = $this->_query->run("
			SELECT
				email
			FROM
				email_subscription
		");

		return $this->_load($result->flatten());
	}

	public function _load($emails, $alwaysReturnArray = false)
	{
		if (!is_array($emails)) {
			$emails = array($emails);
		}

		if (!$emails) {
			return $alwaysReturnArray ? array() : false;
		}

		$result = $this->_query->run('
			SELECT
				email_subscription.*,
				forename,
				surname
			FROM
				email_subscription
			LEFT JOIN
				user USING (email)
			WHERE
				email IN (?sj)
		', array(
			$emails
		));

		$return     = array();
		$resultData = $result->transpose('email');

		foreach ($emails as $email) {
			$subscriber = new Subscriber;

			if (array_key_exists($email, $resultData)) {
				$row = $resultData[$email];

				$subscriber->email      = $row->email;
				$subscriber->forename   = $row->forename;
				$subscriber->surname    = $row->surname;
				$subscriber->subscribed = (bool) $row->subscribed;

				if ($row->created_at) {
					$subscriber->authorship->create(new DateTimeImmutable(date('c', $row->creatd_at)), $row->created_by);
				}
				if ($row->updated_at) {
					$subscriber->authorship->update(new DateTimeImmutable(date('c', $row->updated_at)), $row->updated_by);
				}
			}
			else {
				$subscriber->email      = $email;
				$subscriber->subscribed = false;
			}

			$return[$email] = $subscriber;
		}

		return $alwaysReturnArray || count($return) > 1 ? $return : reset($return);
	}
}