<?php


namespace Internet\InterDB\Drivers;


use PDO;
use PDOException;
use Internet\InterDB\Exceptions\SQLException;
use Internet\InterDB\Traits\SettingsAccessorTrait;
use Internet\InterDB\Exceptions\DSNCreationException;

class TestDriver {
	use SettingsAccessorTrait;

	/**
	 * @var array
	 */
	public $settings;

	public function __construct($settings = []){
		$this->settings = $settings;
	}

	/**
	 * @param $key
	 * @return mixed
	 * @throws DSNCreationException
	 */
	public function testRequired($key){
		return self::required($this->settings, $key);
	}

	/**
	 * @param $key
	 * @param null $default
	 * @return mixed
	 */
	public function testOptional($key, $default=null){
		return self::optional($this->settings, $key, $default);
	}

	/**
	 * @param $from
	 * @param $to
	 */
	public function testRemap($from, $to){
		self::remap($this->settings, $from, $to);
	}
}