<?php

use Message\Cog\Migration\Adapter\MySQL\Migration;

class _1448644640_CreatedColumns extends Migration
{
	public function up()
	{
		$this->run("
			ALTER TABLE
				email_subscription
			ADD
				`created_at` int(11) unsigned DEFAULT NULL
			AFTER
				subscribed,
			ADD
				created_by int(11) unsigned DEFAULT NULL
			AFTER
				created_at
			;
		");
	}

	public function down()
	{
		$this->run("
			ALTER TABLE
				email_subscription
			DROP COLUMN
				created_at,
			DROP COLUMN
				created_by
			;
		");
	}
}