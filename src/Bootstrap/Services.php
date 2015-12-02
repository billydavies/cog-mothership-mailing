<?php

namespace Message\Mothership\Mailing\Bootstrap;

use Message\Mothership\Mailing;

use Message\User\AnonymousUser;

use Message\Cog\Bootstrap\ServicesInterface;
use Message\Cog\Logging\TouchingStreamHandler;

use Monolog\Logger;

class Services implements ServicesInterface
{
	public function registerServices($services)
	{
		$services['mailing.sync.log'] = function($c) {
			$logger = new \Monolog\Logger('mailing_sync');

			// Set up handler for logging to file (as default)
			$logger->pushHandler(
				new \Message\Cog\Logging\TouchingStreamHandler('cog://logs/mailing_sync.log')
			);

			return $logger;
		};

		$services['mailing.sync.adapter'] = $services->factory(function($c) {
			return $c['mailing.sync.adapter.collection']->get($c['cfg']->mailing->sync->adapter);
		});

		$services['mailing.sync.adapter.collection'] = $services->factory(function($c) {

			$collection = new Mailing\ThirdParty\Sync\AdapterCollection;

			$collection->add(new Mailing\ThirdParty\Sync\NoneAdapter);

			return $collection;
		});

		$services['mailing.sync'] = $services->factory(function($c) {
			return new Mailing\ThirdParty\Sync\Syncer(
				$c['mailing.sync.adapter'],
				$c['mailing.subscription.loader'],
				$c['mailing.subscription.edit'],
				$c['mailing.sync.log']
			);
		});

		$services['mailing.reports'] = function ($c) {
			return new \Message\Mothership\Report\Report\Collection([
				$c['mailing.report.subscribers']
			]);
		};

		$services['mailing.report.subscribers'] = function ($c) {
			return new Mailing\Report\Subscribers(
				$c['db.query.builder.factory'],
				$c['routing.generator'],
				$c['mailing.report.subscribers.filters'],
				$c['event.dispatcher'],
				$c['translator']
			);
		};

		$services['mailing.report.subscribers.filters'] = function ($c) {
			return new \Message\Mothership\Report\Filter\Collection([
				$c['mailing.report.filter.user'],
				$c['mailing.report.filter.subscribed'],
				$c['mailing.report.filter.created_at'],
			]);
		};

		$services['mailing.report.filter.user'] = function ($c) {
			return new Mailing\Report\Filter\UserFilter;
		};

		$services['mailing.report.filter.subscribed'] = function ($c) {
			return new Mailing\Report\Filter\SubscribedFilter;
		};

		$services['mailing.report.filter.created_at'] = function ($c) {
			return new Mailing\Report\Filter\CreatedAtFilter;
		};

		$this->registerSubscriptionServices($services);
	}

	public function registerSubscriptionServices($services)
	{
		$services['mailing.subscription.loader'] = $services->factory(function($c) {
			return new \Message\Mothership\Mailing\Subscription\Loader($c['db.query']);
		});

		$services['mailing.subscription.edit'] = $services->factory(function($c) {
			return new \Message\Mothership\Mailing\Subscription\Edit(
				$c['db.query'],
				$c['mailing.subscription.loader'],
				$c['user.current']
			);
		});

		$services['user.current.mailing.subscription'] = $services->factory(function($c) {
			$current = $c['user.current'];

			if ($current instanceof AnonymousUser) {
				$subscriber = new Mailing\Subscription\Subscriber;
				$subscriber->subscribed = false;

				return $subscriber;
			}

			return $this->get('mailing.subscription.loader')->getByUser($current);
		});
	}
}