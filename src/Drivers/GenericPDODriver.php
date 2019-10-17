<?php


namespace Internet\InterDB\Drivers;


use Internet\InterDB\Exceptions\DSNCreationException;

class GenericPDODriver extends AbstractPDODriver {
	public function __construct($connection){
		$this->connection = $connection;
	}
}