<?php

use Message\Cog\Migration\Adapter\MySQL\Migration;

class _1448898542_SetCreatedAt extends Migration
{
	public function up()
	{
		$this->run("
			UPDATE
				email_subscription
			SET
				created_at = updated_at,
				created_by = updated_by
		");
	}

	public function down()
	{
		$this->run("
			UPDATE
				email_subscription
			SET
				created_at = NULL,
				created_by = NULL
		");
	}
}