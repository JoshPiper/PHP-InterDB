<?php

namespace Internet\InterDB\Drivers;

use PDO;
use PDOStatement;
use PDOException;
use Internet\InterDB\Exceptions\SQLException;
use Internet\InterDB\Interfaces\QueryableInterface;
use Internet\InterDB\Definers\AbstractTableDefiner;
use Internet\InterDB\Definers\AbstractColumnDefiner;
use const Internet\InterDB\Constants\FETCH_ASSOC;

abstract class AbstractPDODriver implements QueryableInterface {
	/**
	 * @var $connection PDO;
	 */
	protected $connection;

	protected const tableDefiner = AbstractTableDefiner::class;
	protected const columnDefiner = AbstractColumnDefiner::class;

	/** Generate a prepared statement
	 * @param $query
	 * @param array $args
	 * @return PDOStatement
	 * @throws SQLException
	 */
	protected function prepare($query, $args = []){
		try {
			$stmt = $this->connection->prepare($query);
		} catch (PDOException $exception){
			throw new SQLException($exception->getMessage());
		}

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
		$stmt->execute();
		return $stmt;
	}

	/** Query the data source and return the rows effected.
	 * @param string $query
	 * @param array $args
	 * @return int
	 * @throws SQLException
	 */
	public function query(string $query, array $args = []): int{
		return $this->execute($query, $args)->rowCount();
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

		$row = $stmt->fetch($mode);
		while ($row){
			yield $row;
			$row = $stmt->fetch($mode);
		}
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

	/** {@inheritDoc}
	 * @param string $table
	 * @param string $schema
	 * @param array $columns
	 * @throws SQLException
	 */
	public function table(string $table, string $schema = '', array $columns = [], string $engine = ''): void{
		/** @var AbstractTableDefiner $table */
		$td = static::tableDefiner;
		$table = new $td($schema, $table);

		$cd = (static::columnDefiner);
		foreach ($columns as $name => $column){
			$table->addColumn($cd::fromArray($name, $column));
		}
		$table->setEngine($engine);

		$this->query($table->toSQL());
	}
}