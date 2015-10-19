<?php

use Message\Cog\Migration\Adapter\MySQL\Migration;

class _1386346899_SetUp extends Migration
{
	public function up()
	{
		$this->run("
			CREATE TABLE IF NOT EXISTS `email_subscription` (
			  `email` varchar(255) NOT NULL DEFAULT '',
			  `subscribed` tinyint(1) DEFAULT '1',
			  `updated_at` int(11) unsigned DEFAULT NULL,
			  `updated_by` int(11) unsigned DEFAULT NULL,
			  PRIMARY KEY (`email`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		");
	}

	public function down()
	{
		$this->run('
			DROP TABLE IF EXISTS
				`email_subscription`
		');
	}
}