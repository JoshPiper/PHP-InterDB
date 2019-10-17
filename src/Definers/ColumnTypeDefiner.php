<?php


namespace Internet\InterDB\Definers;


use Internet\InterDB\Interfaces\DefinableInterface;

class ColumnTypeDefiner {
	private $type = '';
	private $length = 0;

	/**
	 * @param int $length
	 */
	public function setLength(int $length): void{
		$this->length = $length;
	}

	/**
	 * @param string $type
	 */
	public function setType(string $type): void{
		$this->type = $type;
	}

	/**
	 * @return int
	 */
	public function getLength(): int{
		return $this->length;
	}

	/**
	 * @return string
	 */
	public function getType(): string{
		return $this->type;
	}

	public function toString(): string{
		if ($this->length === 0){
			return $this->type;
		} else {
			return "{$this->type}({$this->length})";
		}
	}
}