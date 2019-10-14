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
		self::$file = __DIR__ . '/test.sqlite';
		if (is_file(self::$file)){
			unlink(self::$file);
		}

		$retries = 10;
		$cancel = false;
		do {
			try {
				self::$driver = new MySQLDriver(['host' => 'mariadb', 'db' => 'testdb'], 'root', 'test_db_password');
				self::$wrapper = new DB(self::$driver);
			} catch (SQLException $exception){
				$retries--;
				if ($retries < 0){
					$cancel = true;
					echo "Failed to connect: breaking." . PHP_EOL;
				} else {
					echo "Failed to connect: " . $exception->getMessage() . PHP_EOL;
					sleep(2);
				}
			}
		} while (!self::$driver && !$cancel);


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
