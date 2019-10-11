<?php

namespace Internet\InterDB\Drivers;

use PDO;
use PDOStatement;
use Internet\InterDB\Exceptions\SQLException;
use Internet\InterDB\Interfaces\QueryableInterface;
use Internet\InterDB\Exceptions\DSNCreationException;
use const Internet\InterDB\Constants\FETCH_ASSOC;

abstract class AbstractPDODriver implements QueryableInterface {
	/**
	 * @var $connection PDO;
	 */
	protected $connection;

	/** Generate a prepared statement
	 * @param $query
	 * @param array $args
	 * @return PDOStatement
	 */
	protected function prepare($query, $args = []){
		$stmt = $this->connection->prepare($query);
		foreach ($args as $name => $arg){
			$type = PDO::PARAM_STR;
			if (is_numeric($arg)){
				$type = PDO::PARAM_INT;
			} elseif (is_bool($arg)) {
				$type = PDO::PARAM_BOOL;
			}

			if (is_numeric($name)){
				$name++;
			}
			$stmt->bindValue($name, $arg, $type);
		}
		return $stmt;
	}

	/** Generate and execute a query.
	 * @param $query
	 * @param array $args
	 * @return PDOStatement
	 * @throws SQLException
	 */
	public function execute($query, $args = []){
		$stmt = $this->prepare($query, $args);
		$suc = $stmt->execute();
		if (!$suc){
			throw new SQLException($stmt->errorInfo()[2], $stmt->errorInfo()[1]);
		}
		return $stmt;
	}

	/** Query the data source and return the rows effected.
	 * @param string $query
	 * @param array $args
	 * @return int
	 */
	public function query(string $query, array $args = []): int{
		return $this->prepare($query, $args)->rowCount();
	}

	/** Select a single row from the data source and return it.
	 * @param string $query The query to send.
	 * @param array $args Array of keys and values to send with the query.
	 * @param int $mode Fetch Mode
	 * @return array
	 * @throws SQLException
	 */
	public function select(string $query, array $args = [], int $mode = FETCH_ASSOC): array{
		return $this->execute($query, $args)->fetch($mode);
	}

	/** Select an array of rows from the data source and return it.
	 * @param string $query The query to send.
	 * @param array $args Array of keys and values to send with the query.
	 * @param int $mode Fetch Mode
	 * @return array
	 * @throws SQLException
	 */
	public function select_all(string $query, array $args = [], int $mode = FETCH_ASSOC): array{
		return $this->execute($query, $args)->fetchAll($mode);
	}

	/** Get a row iterator from the data source.
	 * @param string $query The query to send.
	 * @param array $args Array of keys and values to send with the query.
	 * @param int $mode Fetch Mode
	 * @return iterable
	 * @throws SQLException
	 */
	public function selector(string $query, array $args = [], int $mode = FETCH_ASSOC): iterable{
		$stmt = $this->execute($query, $args);

		do {
			$row = $stmt->fetch($mode);
			yield $row;
		} while ($row);
	}

	/** Count the number of rows in a table that match a given where clause.
	 * @param string $table The table to check against.
	 * @param string $where The where clause to filter against.
	 * @param array $args Array of keys and values to send with the query.
	 * @return int
	 * @throws SQLException
	 */
	public function count(string $table, string $where = '', array $args = []): int{
		$where = empty($where) ? '' : "WHERE {$where}";
		$count = $this->select("SELECT COUNT(*) as `count` FROM {$table} {$where}", $args, FETCH_ASSOC)['count'];
		return intval($count);
	}

	/** Check if any rows in a table match against given where clause.
	 * @param string $table The table to check against.
	 * @param string $where The where clause to filter against.
	 * @param array $args Array of keys and values to send with the query.
	 * @throws SQLException
	 * @return bool
	 */
	public function any(string $table, string $where = '', array $args = []): bool{
		return $this->count($table, $where, $args) > 0;
	}
}