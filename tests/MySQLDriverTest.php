<?php


use Internet\InterDB\DB;
use Internet\InterDB\Drivers\MySQLDriver;
use PHPUnit\Framework\TestCase;
use Internet\InterDB\Exceptions\SQLException;
use Internet\InterDB\Exceptions\DSNCreationException;

class MySQLDriverTest extends TestCase {
	/** @var MySQLDriver */
	private static $driver;
	/** @var DB */
	private static $wrapper;

	public static function setUpBeforeClass(): void{
		$retries = 20;
		do {
			try {
				self::$driver = new MySQLDriver(['host' => 'mysql', 'db' => 'information_schema', 'port' => 3306], 'ci', 'ci');
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
		$this->assertIsString(self::$driver->buildDSN(['host' => 'mysql']));
		$this->assertIsString(self::$driver->buildDSN(['socket' => '/var/socket/mysqld.sock']));
	}

	public function testDSNFailure(){
		$this->expectException(DSNCreationException::class);
		$driver = new MySQLDriver();
	}

	public function testConnectionFailure(){
		$this->expectException(SQLException::class);
		$driver = new MySQLDriver(['host' => 'badhost']);
	}

	public function testBadUser(){
		$this->expectException(SQLException::class);
		$driver = new MySQLDriver(['host' => 'mysql'], 'baduser', 'notarealpassword');
	}
}
