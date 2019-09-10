<?php

namespace Internet\InterDB;

/**
 * Class Schema
 * Represents the difference between two database versions.
 * @package InterDB\Migrations
 */
abstract class Schema {
	/**
	 * @var string The name of the migration to add to the migration DB.
	 */
	protected $name = 'abstract';
	public function name(){return $this->name;}

	/**
	 * @var DB The database to run against.
	 */
	protected $db;

	/**
	 * @var int The sort order for the migration.
	 */
	public $order = 999;

	/**
	 * Schema constructor.
	 * @param $db DB The database this schema is running against.
	 */
	public function __construct($db){
		$this->db = $db;
		if ($this->name === 'abstract'){
			$this->name = static::class;
		}
	}

	/**
	 * Checks if the schema migration has already taken place.
	 * @return boolean
	 */
	public function exists(){
		return $this->db->exists('migrations') && $this->db->any('migrations', 'migration = ?', [$this->name]);
	}

	/**
	 * Upgrade from the prior version to the next version.
	 */
    public abstract function upgrade();

	/**
	 * Downgrade back to the past version.
	 */
    public abstract function downgrade();
}