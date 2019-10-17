<?php


namespace Internet\InterDB\Definers;


use Internet\InterDB\Interfaces\DefinableInterface;

abstract class AbstractTableDefiner implements DefinableInterface {
	/** @var AbstractColumnDefiner[] */
	protected $columns = [];
	protected $keys = [];
	protected $engine = '';
	protected $schema = '';
	protected $table = '';

	public function __construct($schema = '', $table = ''){
		$this->setSchema($schema);
		$this->setTable($table);
	}

	/**
	 * @param string $engine
	 */
	public function setEngine(string $engine): void{
		$this->engine = $engine;
	}

	/**
	 * @param string $schema
	 */
	public function setSchema(string $schema): void{
		$this->schema = $schema;
	}

	/**
	 * @param array $rows
	 */
	public function setRows(array $rows): void{
		$this->rows = $rows;
	}

	/**
	 * @param string $table
	 */
	public function setTable(string $table): void{
		$this->table = $table;
	}

	public function addColumn(AbstractColumnDefiner $column){
		$this->columns[] = $column;
	}

	protected function getColumnDefs(){
		$data = [];
		foreach ($this->columns as $column){
			if ($column->isPrimary()){
				$this->keys[] = $column->getName();
				$data[] = $column->toSQL();
			}
		}

		if (count($this->keys) > 0){
			$data[] = "PRIMARY KEY (`" . join('`, `', $this->keys) . "`)";
		}

		return $data;
	}
}