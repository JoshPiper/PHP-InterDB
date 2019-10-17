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

		self::$wrapper->addColumn('testtable', 'newcolumn', ['type' => 'varchar', 'length' => 20, 'default' => '"no"'], 'keycol');
		self::$wrapper->dropColumn('testtable', 'newcolumn');
	}

	/**
	 * @depends testColumns
	 */
	public function testInsert(): void{
		$this->expectNotToPerformAssertions();
		self::$wrapper->query("INSERT INTO testtable VALUES (DEFAULT, 'bigname'), (DEFAULT, 'notorious B.I.G')");
	}

	/**
	 * @depends testInsert
	 */
	public function testSelectAll(): void{
		$data = self::$wrapper->select("SELECT * FROM testtable");
		$this->assertEquals([
			['keycol' => 1, 'namecol' => 'bigname'],
			['keycol' => 2, 'namecol' => 'notorious B.I.G'],
		], $data);
	}

	/**
	 * @depends testInsert
	 */
	public function testSelectOne(): void{
		$data = self::$wrapper->selecto("SELECT * FROM testtable");
		$this->assertEquals(['keycol' => 1, 'namecol' => 'bigname'], $data);
	}

	/**
	 * @depends testInsert
	 */
	public function testSelectBulk(): void{
		$gen = self::$wrapper->bulk_select("SELECT * FROM testtable WHERE keycol = ?", [1, 2]);
		$data = [];
		foreach ($gen as $row){$data[] = $row;}

		$this->assertEquals([
			[['keycol' => 1, 'namecol' => 'bigname']],
			[['keycol' => 2, 'namecol' => 'notorious B.I.G']],
		], $data);
	}

	/**
	 * @depends testColumns
	 */
	public function testExists(){
		$this->assertTrue(self::$wrapper->exists('ci', 'testtable'));
		$this->assertFalse(self::$wrapper->exists('ci', 'faketable'));
	}

	public function testMigrations(){
		self::$wrapper->table('migrations', [
			'migration' => ['type' => 'varchar', 'length' => 255, 'pk' => true]
		], 'InnoDB');
		self::$wrapper->migrated("test_migration");
		$this->assertTrue(self::$wrapper->any('migrations', 'migration = "test_migration"'));
		self::$wrapper->unmigrated("test_migration");
		self::$wrapper->drop('migrations');
	}
}