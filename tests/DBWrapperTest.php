<?php

declare(strict_types=1);

use Internet\InterDB\DB;
use PHPUnit\Framework\TestCase;
use Internet\InterDB\Drivers\TestDriver;
use Internet\InterDB\Drivers\MySQLDriver;
use Internet\InterDB\Interfaces\QueryableInterface;
use Internet\InterDB\Exceptions\DSNCreationException;

final class DBWrapperTest extends TestCase {
	/** @var DB */
	private static $wrapper;
	/** @var QueryableInterface */
	private static $driver;

	public static function setUpBeforeClass(): void{
		self::$driver = new MySQLDriver(['host' => 'mysql', 'db' => 'ci', 'port' => 3306], 'ci', 'ci');
		self::$wrapper = new DB(self::$driver);
	}

	public function testSettingsConstructor(): void{
		$wrapper = new DB([
			'host' => 'mysql',
			'db' => 'information_schema',
			'port' => 3306,
			'username' => 'ci',
			'password' => 'ci'
		]);
		$this->assertIsNumeric($wrapper->count('tables'));
	}

	public function testPDOConstructor(): void{
		$wrapper = new DB(new MySQLDriver([
			'host' => 'mysql',
			'db' => 'information_schema',
			'port' => 3306
		], 'ci', 'ci'));
		$this->assertIsNumeric($wrapper->count('tables'));
	}

	public function testColumns(): void{
		$cols = [
			'keycol' => ['type' => 'bigint', 'ai' => true, 'pk' => true],
			'namecol' => ['type' => 'varchar', 'length' => '50', 'default' => '"string"']
		];
		self::$wrapper->table('testtable', $cols, 'InnoDB');
		$this->assertTrue(self::$wrapper->any('information_schema.tables', 'TABLE_NAME = ?', ['testtable']));
	}
}