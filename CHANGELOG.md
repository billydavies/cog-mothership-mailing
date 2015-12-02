# Changelog

## 1.1.0

- Added **Subscribers** report
- Added `Report` namespace
- Added `Report\Subscribers` class, representing the subscriber report, which has the following columns:
    - Email
    - Created at
    - Subscribed (**Yes** or **No**)
    - Has account (**Yes** or **No**)
- Added `Report\Filter\AbstractBoolFilter` class for providing the basic functionality for a two option choice filter
- Added `Report\Filter\SubscribedFilter` class for enabling users to filter out users who are/are not subscribed, extends `Report\Filter\AbstractBoolFilter`
- Added `Report\Filter\UserFilter` class for enabling users to filter out users who are/are not users, extends `Report\Filter\AbstractBoolFilter`
- Added `Report\Filter\CreatedAtFilter` class for allowing users to filter subscribers who subscribed within a certain date
- Added `Event\EventListener` class to enable report filtering and registration, as well as any event listening to be done in the future
- Added `mailing.reports` service which returns a collection of registered reports in the module
- Added `mailing.report.subscribers` service which returns instance of `Report\Subscribers`
- Added `mailing.report.subscribers.filters` service which returns a collection of filters used by the `Report\Subscribers` class
- Added `mailing.report.filter.user` service which returns an instance of `Report\Filter\UserFilter`
- Added `mailing.report.filter.subscribed` which returns an instance of `Report\Filter\SubscribedFilter`
- Added `mailing.report.filter.created_at` which returns an instance of `Report\Filter\CreatedAtFilter`
- Added `ms.mailing.report.subscribers.description` translation for displaying on **Subscribers** report screen
- Save creation date and user when saving a subscriber to the database
- Load creation date and user to the `$authorship` property of the subscriber when saving a subscriber to the database
- Added migration for adding `created_at` column to `email_subscription` table, which defaults to the value for `updated_at`
- Added migration for adding `created_by` column to `email_subscription` table, which defaults to the value for `updated_by`
- Added `cog-mothership-reports` requirement of version 2.2
- Added `cog-mothership-user` requirement of 4.0

## 1.0.0

- Initial open source release