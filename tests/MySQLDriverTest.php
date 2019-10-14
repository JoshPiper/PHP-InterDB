<?php


use Internet\InterDB\DB;
use Internet\InterDB\Drivers\MySQLDriver;
use PHPUnit\Framework\TestCase;
use Internet\InterDB\Exceptions\SQLException;

class MySQLDriverTest extends TestCase {
	/** @var string Test DB file path. */
	private static $file;
	/** @var MySQLDriver */
	private static $driver;
	/** @var DB */
	private static $wrapper;

	public static function setUpBeforeClass(): void{
		$retries = 10;
		do {
			try {
				self::$driver = new MySQLDriver(['host' => 'mariadb', 'db' => 'information_schema', 'port' => 3306], 'root', 'ci');
				self::$wrapper = new DB(self::$driver);
			} catch (SQLException $exception){
				$retries--;
				if ($retries < 0){
					echo "Failed to connect: breaking." . PHP_EOL;
				} else {
					echo "Failed to connect: " . $exception->getMessage() . PHP_EOL;
					sleep(2);
				}
			}
		} while (!self::$driver && $retries >= 0);
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
