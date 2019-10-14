<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Internet\InterDB\Drivers\GenericPDODriver;

final class GenericPDODriverTest extends TestCase {
	/** @var PDO */
	private static $driver;

	public static function setUpBeforeClass(): void{
		self::$driver = new PDO('sqlite::memory:');
	}

	public function testCreation(){
		$driver = new GenericPDODriver(self::$driver);
		$this->assertFalse($driver->any('sqlite_master'));
	}
}