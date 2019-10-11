<?php


namespace Internet\InterDB\Drivers;


use PDO;
use PDOException;
use Internet\InterDB\Exceptions\SQLException;
use Internet\InterDB\Exceptions\DSNCreationException;

abstract class AbstractCreatorPDODriver extends AbstractPDODriver {
	public function __construct($settings = [], ...$extra){
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
				$settings[$key] = "{$key}:{$value}";
			}
		}
		return $dsn . join(';', $settings);
	}

	/** Build a DSN from a settings array.
	 * @param array $settings
	 * @return string
	 */
	abstract function buildDSN(array $settings): string;

	/** Remap an array member.
	 * @param $settings
	 * @param $from
	 * @param $to
	 */
	static function remap(&$settings, $from, $to){
		if (isset($settings[$from])){
			$settings[$to] = $settings[$from];
			if ($from !== $to){
				unset($settings[$from]);
			}
		}
	}

	/** Check for a required setting.
	 * @param $settings
	 * @param $key
	 * @throws DSNCreationException
	 */
	static function required($settings, $key){
		if (!isset($settings[$key])){
			throw new DSNCreationException("Missing Setting Key ${key}.");
		}
	}

	/** Add a default setting.
	 * @param $settings
	 * @param $key
	 * @param $default
	 */
	static function optional(&$settings, $key, $default){
		if (!isset($settings[$key])){
			$settings[$key] = $default;
		}
	}

}