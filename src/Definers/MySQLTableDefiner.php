<?php


namespace Internet\InterDB\Definers;


class MySQLTableDefiner extends AbstractTableDefiner {
	public function toSQL(): string{
		$data = join(",\n\t", $this->getColumnDefs());
		$data = "CREATE TABLE `{$this->schema}`.`{$this->table}` (\n\t{$data}\n)";
		if ($this->engine){$data .= " ENGINE={$this->engine}";}
		$data .= ';';

		return $data;
	}
}