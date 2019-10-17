<?php


namespace Internet\InterDB\Drivers;


use Internet\InterDB\Exceptions\DSNCreationException;

class SQLiteDriver extends AbstractCreatorPDODriver {
	/** {@inheritDoc}
	 * @throws DSNCreationException
	 */
	function buildDSN(array $settings): string{
		self::required($settings, 'path');

		$settings['prefix'] = 'sqlite';
		self::remap($settings, 'path', 0);

		return $this->constructDSN($settings);
	}
}