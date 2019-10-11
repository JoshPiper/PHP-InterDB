<?php


use Internet\InterDB\DB;
use Internet\InterDB\Drivers\MySQLDriver;
use PHPUnit\Framework\TestCase;

class MySQLDriverTest extends TestCase {
	/** @var string Test DB file path. */
	private static $file;
	/** @var MySQLDriver */
	private static $driver;
	/** @var DB */
	private static $wrapper;

	public static function setUpBeforeClass(): void{
		self::$driver = new MySQLDriver(['host' => 'mysql', 'db' => 'testdb'], 'root', 'test_db_password');
		self::$wrapper = new DB(self::$driver);
	}

	public static function tearDownAfterClass(): void{
		self::$wrapper = null;
		self::$driver = null;
	}

	public function testBuildDSN(){
		$driver = new MySQLDriver(['host' => 'mariadb'], 'root', 'test_db_password');
		$this->assertIsString($driver->buildDSN(['host' => 'mariadb']));
		unset($driver);
	}
}
