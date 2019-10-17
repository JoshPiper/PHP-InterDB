<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Internet\InterDB\Drivers\TestDriver;
use Internet\InterDB\Exceptions\DSNCreationException;

final class AbstractDriverTest extends TestCase {
	public function testRemapping(): void{
		$driver = new TestDriver(['setting' => 'abcd']);
		$driver->testRemap('setting', 'newsetting');
		$this->assertEquals(['newsetting' => 'abcd'], $driver->settings);
	}

	public function testRequiredParamMissing(): void{
		$this->expectException(DSNCreationException::class);
		$driver = new TestDriver(['setting' => 'abcd']);
		$driver->testRequired('notkey');
	}

	public function testRequiredParamPresent(): void{
		$driver = new TestDriver(['setting' => 'abcd']);
		$this->assertEquals('abcd', $driver->testRequired('setting'));
	}

	public function testOptionalParamPresent(): void{
		$driver = new TestDriver(['host' => 'localhost', 'port' => 3307]);
		$this->assertEquals(3307, $driver->testOptional('port', 3306));
	}

	public function testOptionalParamNotPresent(): void{
		$driver = new TestDriver(['host' => 'localhost']);
		$this->assertEquals(3306, $driver->testOptional('port', 3306));
	}
}