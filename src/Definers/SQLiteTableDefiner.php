<?php


namespace Internet\InterDB\Definers;


class SQLiteTableDefiner extends AbstractTableDefiner {
	public function toSQL(): string{
		$data = join(",\n\t", $this->getColumnDefs());
		return "CREATE TABLE `{$this->table}` (\n\t{$data}\n);";
	}
}