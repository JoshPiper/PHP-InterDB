<?php


namespace Internet\InterDB\Definers;


class SQLiteColumnDefiner extends AbstractColumnDefiner {
	public function toSQL(): string{
		return join(' ',
			array_filter([
				"`{$this->name}`",
				$this->type->toString(),
				$this->isNullable ? '' : 'NOT NULL',
				$this->default ? "DEFAULT {$this->default}": '',
				$this->isAutoIncrement ? 'AUTOINCREMENT' : ''
			])
		);
	}
}