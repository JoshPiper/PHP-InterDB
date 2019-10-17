<?php


namespace Internet\InterDB\Drivers;


use Internet\InterDB\Exceptions\SQLException;
use Internet\InterDB\Definers\SQLiteTableDefiner;
use Internet\InterDB\Definers\SQLiteColumnDefiner;
use Internet\InterDB\Exceptions\DSNCreationException;

class SQLiteDriver extends AbstractCreatorPDODriver {
	protected const tableDefiner = SQLiteTableDefiner::class;
	protected const columnDefiner = SQLiteColumnDefiner::class;

	/** {@inheritDoc}
	 * @throws DSNCreationException
	 */
	function buildDSN(array $settings): string{
		self::required($settings, 'path');

		$settings['prefix'] = 'sqlite';
		self::remap($settings, 'path', 0);

		return $this->constructDSN($settings);
	}

	/** {@inheritDoc}
	 * @param string $table
	 * @param string $schema Unused.
	 * @return bool
	 * @throws SQLException
	 */
	public function table_exists(string $table, string $schema = ''): bool{
		return $this->any('sqlite_master', 'type="table" AND name=?', [$table]);
	}
}