<?php


namespace Internet\InterDB\Interfaces;
use const Internet\InterDB\Constants\FETCH_ASSOC;

interface QueryableInterface {
	/** Query the data source and return the rows effected.
	 * @param string $query
	 * @param array $args
	 * @return int
	 */
	public function query(string $query, array $args = []): int;

	/** Select a single row from the data source and return it.
	 * @param string $query The query to send.
	 * @param array $args Array of keys and values to send with the query.
	 * @param int $mode Fetch Mode
	 * @return array
	 */
	public function select(string $query, array $args = [], int $mode = FETCH_ASSOC): array;

	/** Select an array of rows from the data source and return it.
	 * @param string $query The query to send.
	 * @param array $args Array of keys and values to send with the query.
	 * @param int $mode Fetch Mode
	 * @return array
	 */
	public function select_all(string $query, array $args = [], int $mode = FETCH_ASSOC): array;

	/** Get a row iterator from the data source.
	 * @param string $query The query to send.
	 * @param array $args Array of keys and values to send with the query.
	 * @param int $mode Fetch Mode
	 * @return iterable
	 */
	public function selector(string $query, array $args = [], int $mode = FETCH_ASSOC): iterable;

	/** Count the number of rows in a table that match a given where clause.
	 * @param string $table The table to check against.
	 * @param string $where The where clause to filter against.
	 * @param array $args Array of keys and values to send with the query.
	 * @return int
	 */
	public function count(string $table, string $where = '', array $args = []): int;

	/** Check if any rows in a table match against given where clause.
	 * @param string $table The table to check against.
	 * @param string $where The where clause to filter against.
	 * @param array $args Array of keys and values to send with the query.
	 * @return bool
	 */
	public function any(string $table, string $where = '', array $args = []): bool;
}