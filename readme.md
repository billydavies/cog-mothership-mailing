# Mothership Mailing

The `Message\Mothership\Mailing` module provides functionality for users to decide whether they are subscribed to receive marketing emails.

This module comes only with the framework for integrating with third party email marketing software, but does not come with any native support.

## Intergrating third parties

### Implementing the AdapterInterface

To integrate third party software, you must create a class that implements the `Message\Mothership\Mailing\ThirdParty\Sync\AdapterInterface` interface.

This interface contains the following methods:

+ **`getName()`** - This method must return a string that represents how the adapter will be referred to in the collection and in the `mailing.yml` config
+ **`getAllSubscribers(\DateTime $since = null)`** - This method should connect to the third party software and collect all existing subscribers (even if they have unsubscribed). If it is given an instance of `Datetime` then it should return only email addresses which have been modified since that time.
+ **`getSubscriber($email)`** - This method should get the details of a specific email address from the third party
+ **`subscribe(\Message\Mothership\Mailing\SubscriberInterface $subscriber)`** - This method should subscribe an email address to the marketing campaign. This module comes packaged with the `Message\Mothership\Mailing\Subscription\Subscriber` class which implements `Message\Mothership\Mailing\SubscriberInterface` interface. This class contains basic information about the subscriber which can be saved to and loaded from the database
+ **`unsubscribe(\Message\Mothership\Mailing\SubscriberInterface $subscriber)`** - This method should unsubscribe the email address from the marketing campaign
+ **`isSyncable()`** - This method determines whether or not it is possible to automatically synchronise the subscriber information stored in the database with the third party software. If this is returns false then email addresses will need to be subscribed manually

### Implementing the third party adapter

Once you have written your third party adapter class, you will need to add it to the adapter collection via the service container.

To do this, extend the `mailing.sync.adapter.collection` service and add it, like so:

```php
$services->extend('mailing.sync.adapter.collection', function ($collection, $c) {

    // Where 'MyAdapterClass' is the third party adapter that implements
    // the AdapterInterface
    $collection->add(new MyAdapterClass);

    return $collection;
});
```

You will then need to add the return value of the `getName()` method to the `mailing.yml` file, so if it returns 'my-adapter', the config would look like this:

```
sync:
    enabled: false
    adapter: my-adapter
```

## Synchronising with the third party

This module uses a cron to synchronise with the third party software every hour. To enable this

+ You must ensure that your adapter class' `isSyncable()` method returns `true`.
+ You should then ensure that it is enabled in the `mailing.yml` file:

```
sync:
    enabled: true
    adapter: my-adapter
```        
+ You must set up the `crontab` on the server to run all Mothership scheduled tasks. This is not used soley for mailing and should be set up on all Mothership installations. However, to set it up run `crontab -e` in the terminal, and then add the following line:

```
# Fill in the path as applicable
* * * * * /[path to your installation]/bin/cog task:run_scheduled --env=live > /dev/null`
```
