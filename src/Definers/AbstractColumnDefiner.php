<?php


namespace Internet\InterDB\Definers;


use Internet\InterDB\Interfaces\DefinableInterface;

abstract class AbstractColumnDefiner implements DefinableInterface {
	protected $isNullable = true;
	protected $isAutoIncrement = false;
	protected $type;
	protected $name = '';
	protected $default = '';
	protected $isPrimary = false;

	public function __construct($name = ''){
		$this->type = new ColumnTypeDefiner();
		$this->name = $name;
	}

	public function setType(string $type): void{
		$this->type->setType($type);
	}

	public function setLength(int $length): void{
		$this->type->setLength($length);
	}

	public function setIsAutoIncrement(bool $isAutoIncrement): void{
		$this->isAutoIncrement = $isAutoIncrement;
	}

	public function setIsNullable(bool $isNullable): void{
		$this->isNullable = $isNullable;
	}

	public function setName(string $name): void{
		$this->name = $name;
	}

	public function setDefault(string $default): void{
		$this->default = $default;
	}

	public function getName(): string{
		return $this->name;
	}

	public function isPrimary(): bool{
		return $this->isPrimary;
	}

	public function setIsPrimary(bool $isPrimary): void{
		$this->isPrimary = $isPrimary;
	}

	public static function fromArray(string $name, array $col): AbstractColumnDefiner {
		$x = new static($name);
		$x->setType($col['type']);
		$x->setLength(isset($col['length']) ? $col['length'] : 0);
		$x->setIsNullable(isset($col['null']) && $col['null']);
		$x->setDefault(isset($col['default']) ? $col['default'] : '');
		$x->setIsAutoIncrement($col['ai']);
		$x->setIsPrimary($col['pk']);
		return $x;
	}
}