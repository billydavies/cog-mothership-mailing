<?php

namespace Message\Mothership\Mailing;

use Message\Cog\Console\Task\Task;

use Monolog\Logger;
use Monolog\Handler\AbstractHandler;

class TaskWritingHandler extends AbstractHandler
{
	protected $_task;

	public function setTask(Task $task)
	{
		$this->_task = $task;
	}

	public function handle(array $record)
	{
		if (!$this->_task) {
			return false;
		}

		if ($record['level'] < $this->level) {
			return false;
		}

		$record['formatted'] = $this->getFormatter()->format($record);

		$message = (string) $record['formatted'];

		switch ($record['level']) {
			case Logger::NOTICE:
			case Logger::INFO:
			case Logger::DEBUG:
				$message = '<info>' . $message . '</info>';
				break;
			default:
				$message = '<error>' . $message . '</error>';
		}

		$this->_task->writeln($message);

		return true;
	}
}