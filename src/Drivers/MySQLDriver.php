<?php


namespace Internet\InterDB\Drivers;


use Internet\InterDB\Exceptions\SQLException;
use Internet\InterDB\Definers\MySQLTableDefiner;
use Internet\InterDB\Definers\MySQLColumnDefiner;
use Internet\InterDB\Exceptions\DSNCreationException;

class MySQLDriver extends AbstractCreatorPDODriver {
	protected const tableDefiner = MySQLTableDefiner::class;
	protected const columnDefiner = MySQLColumnDefiner::class;

	/** {@inheritDoc}
	 * @throws DSNCreationException
	 */
	function buildDSN(array $settings): string{
		if (isset($settings['socket'])){
			unset($settings['host']);
			unset($settings['port']);
		} else {
			self::required($settings, 'host');
			self::optional($settings, 'port', 3306);
		}

		$settings['prefix'] = 'mysql';
		self::remap($settings, 'socket', 'unix_socket');
		self::remap($settings, 'db', 'dbname');

		return $this->constructDSN($settings);
	}

	/**
	 * @param string $table
	 * @param string $schema
	 * @return bool
	 * @throws SQLException
	 */
	public function table_exists(string $table, string $schema = ''): bool{
		return $this->any('information_schema.TABLES', 'TABLE_SCHEMA = ? AND TABLE_NAME = ?', [$schema, $table]);
	}
}