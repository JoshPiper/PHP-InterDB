<?php

namespace Internet\InterDB;

use ArrayAccess;
use Exception;
use Generator;
use IteratorAggregate;
use LogicException;

/**
 * Handles caching of and access to DB objects.
 */
Class Handler implements ArrayAccess, IteratorAggregate {
	/** @var array[string] */
	private $cfgs = [];
	/** @var DB[] */
	private $stored = [];

	/**
	 * DB handler constructor.
	 * @param $settings array[string] Array of settings arrays.
	 */
	public function __construct($settings = []){
		$this->cfgs = $settings;
	}

	/**
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset){
		return isset($this->cfgs[$offset]);
	}

	/**
	 * @param mixed $offset
	 * @return DB
	 * @throws Exception
	 */
	public function offsetGet($offset){
		return $this->get($offset);
	}

	/**
	 * @param string $name DB config to fetch.
	 * @return DB
	 * @throws Exception
	 */
	public function get($name = 'acp'){
		if (!isset($this->cfgs[$name])){
			throw new Exception("Missing DB config required: ${name}");
		}
		if (!isset($this->stored[$name])){
			$this->load($name);
		}
		return $this->stored[$name];
	}

	/** Load a DB object into the cache.
	 * @param string $name DB config to load.
	 * @throws Exception
	 */
	private function load($name = 'acp'){
		if (!isset($this->cfgs[$name])){
			throw new Exception("Missing DB config required: ${name}");
		}

		$this->stored[$name] = new DB($this->cfgs[$name]);
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 * @throws LogicException
	 */
	public function offsetSet($offset, $value){
		throw new LogicException('Writing to immutable array.');
	}

	/**
	 * @param mixed $offset
	 * @throws LogicException
	 */
	public function offsetUnset($offset){
		throw new LogicException('Writing to immutable array.');
	}

	/**
	 * @return Generator
	 */
	public function getIterator(){
		foreach ($this->stored as $k => $v){
			yield $k => $v;
		}
	}

	/** Fetch DB object from cache.
	 * @param string $name DB config to fetch.
	 * @return DB
	 * @throws Exception
	 */
	public function __invoke($name = 'acp'){
		return $this->get($name);
	}

	/** Convert the DB handler into a pretty string object.
	 * @return string
	 */
	public function __toString(){
		$c = [count($this->cfgs), count($this->stored)];
		return "DB Handler: {$c[1]}/{$c[0]}";
	}

	/** Fetch array to be returned for print_r and var_dump.
	 * @return array
	 */
	public function __debugInfo(){
		return [
			'cached' => array_keys($this->stored),
			'configs' => array_keys($this->cfgs)
		];
	}
}