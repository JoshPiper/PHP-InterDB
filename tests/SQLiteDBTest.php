<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class SQLiteDBTest extends TestCase {
	/** @var string Test DB file path. */
	private $file;
	/** @var \Internet\InterDB\Drivers\SQLiteDriver */
	private $driver;

	protected function setUp(): void{
		$this->file = __DIR__ . '/test.sqlite';
		$this->driver = new \Internet\InterDB\Drivers\SQLiteDriver(['path' => $this->file]);
	}

	protected function tearDown(): void{
		unset($this->driver);
		if (is_file($this->file)){
			unlink($this->file);
		}
	}

	public function testBasic(): void{
		$this->assertEmpty($this->driver->select_all('SELECT * FROM sqlite_master'));
	}
}