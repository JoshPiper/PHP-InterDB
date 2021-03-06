<?php

declare(strict_types=1);

use Internet\InterDB\DB;
use PHPUnit\Framework\TestCase;
use Internet\InterDB\Drivers\SQLiteDriver;
use Internet\InterDB\Exceptions\SQLException;

final class SQLiteDBTest extends TestCase {
	/** @var string Test DB file path. */
	private static $file;
	/** @var SQLiteDriver */
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
				self::$driver = new SQLiteDriver(['path' => self::$file]);
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

	public function testSetup(): void{
		$driver = new SQLiteDriver(['path' => ':memory:']);
		$this->assertIsString($driver->buildDSN(['path' => ':memory:']));
		unset($driver);
	}

	public function testBasic(): void{
		$this->assertEmpty(self::$driver->select_all('SELECT * FROM sqlite_master'));
	}

	public function testError(): void{
		$this->expectException(SQLException::class);
		self::$driver->query("suck my balls");
	}

	public function testTableCreation(): void{
		self::$wrapper->table('test', ['name' => ['type' => 'Varchar']]);
		$this->assertTrue(self::$driver->any('sqlite_master', 'type = "table" AND tbl_name = "test"'), 'no tables exist');
	}

	/**
	 * @depends testTableCreation
	 */
	public function testInsert(): void{
		$this->expectNotToPerformAssertions();
		self::$driver->query('INSERT INTO test VALUES ("hi"), ("no"), ("u")');
	}

	/**
	 * @depends testInsert
	 */
	public function testSelect(): void{
		$this->assertEquals(['name' => 'hi'], self::$driver->select('SELECT * FROM test'));
		$this->assertEquals([['name' => 'hi'], ['name' => 'no'], ['name' => 'u']], self::$driver->select_all('SELECT * FROM test'));

		$gen = self::$driver->selector('SELECT * FROM test');
		$this->assertCount(3, iterator_to_array($gen));
	}

	/**
	 * @depends testInsert
	 */
	public function testCount(): void{
		$this->assertEquals(3, self::$driver->count('test'));
		$this->assertEquals(0, self::$driver->count('test', 'name="balls"'));

		$this->assertEquals(1, self::$driver->count('test', 'name=?', ['hi']));
		$this->assertEquals(0, self::$driver->count('test', 'name=?', ['balls']));
	}

	/**
	 * @depends testInsert
	 */
	public function testAny(): void{
		$this->assertTrue(self::$driver->any('test'));
		$this->assertFalse(self::$driver->any('test', 'name="balls"'));

		$this->assertTrue(self::$driver->any('test', 'name=?', ['hi']));
		$this->assertFalse(self::$driver->any('test', 'name=?', ['balls']));

		$this->assertTrue(self::$driver->any('test', '?', [true]));
		$this->assertTrue(self::$driver->any('test', 'LENGTH(name) > ?', [0]));
	}
}