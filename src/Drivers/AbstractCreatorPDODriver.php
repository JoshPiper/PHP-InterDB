<?php


namespace Internet\InterDB\Drivers;


use PDO;
use PDOException;
use Internet\InterDB\Exceptions\SQLException;
use Internet\InterDB\Traits\SettingsAccessorTrait;
use Internet\InterDB\Exceptions\DSNCreationException;

abstract class AbstractCreatorPDODriver extends AbstractPDODriver {
	use SettingsAccessorTrait;

	public function __construct($settings = [], ...$extra){
		var_dump($this->buildDSN($settings));
		try {
			$this->connection = new PDO($this->buildDSN($settings));
		} catch (PDOException $exception){
			$code = $exception->getCode();
			switch ($code){
				case 2002: throw new SQLException('Failed to connect: connection refused.', $code);
				default: throw new SQLException('Failed to connect: ' . $exception->getMessage(), $code);
			}
		}
		$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	/** Take given settings and construct a DSN from it.
	 * @param $settings
	 * @return string
	 * @throws DSNCreationException
	 */
	protected function constructDSN($settings): string{
		self::required($settings, 'prefix');

		$dsn = "${settings['prefix']}:";
		unset($settings['prefix']);

		foreach ($settings as $key => $value){
			if (!is_numeric($key)){
				$settings[$key] = "{$key}={$value}";
			}
		}
		return $dsn . join(';', $settings);
	}

	/** Build a DSN from a settings array.
	 * @param array $settings
	 * @return string
	 */
	abstract function buildDSN(array $settings): string;

}