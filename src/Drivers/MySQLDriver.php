<?php


namespace Internet\InterDB\Drivers;


use Internet\InterDB\Exceptions\DSNCreationException;

class MySQLDriver extends AbstractCreatorPDODriver {
	/** {@inheritDoc}
	 * @throws DSNCreationException
	 */
	function buildDSN(array $settings): string{
		if (isset($settings['socket'])){
			unset($settings['host']);
			unset($settings['port']);
		} else {
			var_dump($settings);
			self::required($settings, 'host');
			self::optional($settings, 'port', 3306);
		}

		$settings['prefix'] = 'mysql';
		self::remap($settings, 'socket', 'unix_socket');
		self::remap($settings, 'db', 'dbname');

		return $this->constructDSN($settings);
	}
}