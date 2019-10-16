<?php

namespace Internet\InterDB;
use PDO, PDOStatement, PDOException;
use Generator;
use Internet\InterDB\Drivers\MySQLDriver;
use Internet\InterDB\Exceptions\SQLException;
use Internet\InterDB\Interfaces\QueryableInterface;

class DB {
	private $settings = [
		'host' => 'localhost',
		'username' => 'root',
		'password' => '',
		'db' => '',
		'port' => '3306',
	];
	private $connection;

	/**
	 * InterDB constructor.
	 * @param (string|int)[string]|QueryableInterface $settings Array of settings to pass.
	 * @throws SQLException
	 */
	public function __construct($settings){
		if (is_array($settings)){
			$this->settings = array_replace($this->settings, $settings);
			$user = $this->settings['username'];
			unset($this->settings['username']);
			$pass = $this->settings['password'];
			unset($this->settings['password']);
			$this->connection = new MySQLDriver($this->settings, $user, $pass);
		} elseif ($settings instanceof QueryableInterface){
			$this->connection = $settings;
		}
	}

	/**
	 * Run a select query on the DB.
	 * @param $q string Query to run.
	 * @param array $a Key/pair or integer indexed values to pass into prepared query.
	 * @param int $mode PDO fetch mode.
	 * @return array Array of results.
	 */
	public function select($q, $a = [], $mode = PDO::FETCH_ASSOC){
		return $this->connection->select_all($q, $a, $mode);
	}

	/** Select a single row for a given query.
	 * @param $q string Query to run
	 * @param array $a Unsafe query values.
	 * @param int $mode PDO fetch mode.
	 * @return mixed
	 */
	public function selecto($q, $a = [], $mode = PDO::FETCH_ASSOC){
		return $this->connection->select($q, $a, $mode);
	}

	/** Run a statement, but return the query rather than the data.
	 * @param $q string Query
	 * @param array $a Unsafe query values.
	 * @return PDOStatement Executed query.
	 */
	public function return_stmt($q, $a = []){
		return $this->run($q, $a);
	}

	/** Run a bulk select using multiple data sets.
	 * @param $q string Query.
	 * @param array $a Array of array of unsafe query values.
	 * @param int $mode PDO fetch mode.
	 * @return Generator
	 */
	public function bulk_select($q, $a = [], $mode = PDO::FETCH_ASSOC){
		foreach ($a as $v){
			if (!is_array($v)){
				$v = [$v];
			}

			try {
				yield $this->connection->select_all($q, $v, $mode);
			} catch (SQLException $exception){
				throw new PDOException($exception->getMessage(), $exception->getCode(), $exception);
			}
		}
	}

	/** Check if a table exists.
	 * @param $t string Table name.
	 * @return bool
	 */
	public function exists($t){
		return $this->connection->any("information_schema.TABLES", "TABLE_SCHEMA = ? AND TABLE_NAME = ?", [$this->settings['db'], $t]);
	}

	/** Check if there's any data in a given table matching a given where.
	 * @param $t string Table name.
	 * @param string|bool $w Where statement
	 * @param array $a Unsafe query values.
	 * @return bool
	 */
	public function any($t, $w = false, $a = []){
		return $this->connection->any($t, $w ?: '', $a) > 0;
	}

	/** Fetch the number of rows in a given table matching a given where.
	 * @param $t string Table name.
	 * @param string|bool $w Where statement
	 * @param array $a Unsafe query values.
	 * @return integer
	 */
	public function count($t, $w = false, $a = []){
		return $this->connection->count($t, $w ?: '', $a);
	}

	/** Drop a given table.
	 * @param $t string Table name.
	 * @return int Number of effected tables.
	 */
	public function drop($t){
		return $this->connection->query(sprintf("DROP TABLE %s", $t));
	}

	/** Run a raw query on the DB.
	 * @param $q string Query string.
	 * @param array $a Unsafe query values.
	 * @return int Number of effected rows.
	 */
	public function query($q, $a = []){
		return $this->connection->query($q, $a);
	}

	/** Generate a table with given name.
	 * @param $t string Table name.
	 * @param array $c List of column definitions.
	 * @param bool|string $e SQL engine to use.
	 */
	public function table($t, $c = [], $e = false){
		$q = [];
		$pk = [];

		foreach ($c as $name => $col){
			$q[] = "\t" . $this->defineColumn($name, $col, $pk);
		}

		if (count($pk) > 0){
			$q[] = "\tPRIMARY KEY (" . join(", ", $pk) . ")";
		}

		$q = sprintf("CREATE TABLE `%s` (\n%s\n)", $t, join(",\n", $q));
		if ($e){
			$q .= " ENGINE={$e}";
		}

		$this->query($q);
	}

	/** Function to take a column definition and turn it into a MySQL column def.
	 * @param $name string Column name.
	 * @param array $col Column data.
	 * @param array $pk Primary Key column name array.
	 * @return string
	 */
	protected function defineColumn($name, $col = [], &$pk = []){
		$name = "`{$name}`";
		$data = ["{$name}"];

		if (isset($col["length"])){
			$col["type"] = "{$col["type"]}({$col["length"]})";
		}

		$data[] = $col["type"];

		if (!isset($col["null"]) || !$col["null"]){
			$data[] = "NOT NULL";
		}

		if (isset($col["default"])){
			$data[] = "DEFAULT {$col["default"]}";
		}

		if (isset($col["ai"]) && $col["ai"]){
			$data[] = "AUTO_INCREMENT";
		}

		if (isset($col["pk"]) && $col["pk"]){
			$pk[] = $name;
		}

		return join(" ", $data);
	}

	/** Add a migration name to the migrations table.
	 * @param $name string Name of the migration.
	 */
	public function migrated($name){
		$this->query("INSERT INTO migrations VALUES (?)", [$name]);
	}

	/** Remove a migration name to the migrations table.
	 * @param $name string Name of the migration.
	 */
	public function unmigrated($name){
		$this->query("DELETE FROM migrations WHERE migration = ?", [$name]);
	}

	/** Add a column to an existing table.
	 * @param $t string Name of the table.
	 * @param $c string Column name.
	 * @param array $d Column data.
	 * @param bool|string $a Column to add after, false for last or true for first.
	 */
	public function addColumn($t, $c, $d = [], $a = false){
		$t = "`{$t}`";


		$c = $this->defineColumn($c, $d);
		$q = "ALTER TABLE {$t} ADD COLUMN {$c}";

		if ($a){
			$q .= (is_string($a) ? " AFTER `{$a}`" : "FIRST");
		}
		$this->query($q);
	}

	/** Drop a column from a table.
	 * @param $t string Table name.
	 * @param $c string Column name.
	 */
	public function dropColumn($t, $c){
		$t = "`{$t}`";
		$c = "`{$c}`";
		$this->query("ALTER TABLE {$t} DROP COLUMN {$c}");
	}
}