<?php

namespace Message\Mothership\Mailing\Task;

use Message\Mothership\Mailing;

use Message\Cog\Console\Task\Task;

use Symfony\Component\Console\Input\InputArgument;

class Sync extends Task
{
	public function configure()
	{
		// Runs every hour
		$this->schedule('0 */1 * * *');

		// Add an optional argument to override the last run time
		$this->addArgument(
			'last-run',
			InputArgument::OPTIONAL,
			'UNIX timestamp of the last run time, overrides the last CRON run time (or "none" to use no last run time)'
		);
	}

	public function process()
	{
		if (!$this->get('cfg')->mailing->sync->enabled) {
			$this->writeln('<error>Mailing sync not run. Please ensure that the the sync is enabled in the `mailing.yml` config file</error>');
			return false;
		}

		// Check if last run argument was passed
		if ($lastRun = $this->getRawInput()->getArgument('last-run')) {
			if ('none' === $lastRun) {
				$lastRun = null;
			}
			else {
				$lastRun = new \DateTime(date('c', $lastRun));
			}
		}
		// Otherwise, find the last run time for the CRON
		else {
			$lastRun = $this->getCronExpression()->getPreviousRunDate();
		}

		// Add a handler to the sync log to output to the console (useful when running this task directly)
		$logHandler = new Mailing\TaskWritingHandler;
		$logHandler->setTask($this);
		$this->get('mailing.sync.log')->pushHandler($logHandler);

		// Set up the sync
		$syncer = $this->get('mailing.sync');

		$syncer->setLastRunTime($lastRun);

		// Run the sync
		$syncer->run();
	}
}