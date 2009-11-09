<?php
namespace Prosper;

class ParadoxAdapter extends BaseAdapter {
	
	private $file_handle;
	
	/**
	 * Establishes a Paradox Adapter
	 * @param string $username Database username
	 * @param string $password Database password
	 * @param string $hostname Database hostname
	 * @param string $schema Database schema
	 * @return New Adapter Instance
	 */
	function __construct($username, $password, $hostname, $schema) {
		parent::__construct($username, $password, $hostname, $schema);
		$this->file_handle = fopen($username, "rw");
		$this->connection = new paradox_db();
		$this->connection->open_fp($this->file_handle);
	}
	
	/**
	 * Clean up, destroy the connection
	 */
	function __destruct() {
		$this->connection->close();
		fclose($this->file_handle);
	}
	
	/**
	 * This function is experimental, as is the adapter.  
	 * It can only be used with the non-portable native function
	 * @see Query#native($sql)
	 * @see BaseAdapter#platform_execute($sql, $mode) 
	 */
	function platform_execute($sql, $mode) {
		switch($mode) {
			case Query::DELETE_STMT:
				return px_delete_record($this->connection, $sql);
				break;
			case Query::INSERT_STMT:
				return px_insert_record($this->connection, $sql);
				break;
			case Query::SELECT_STMT:
				return px_retrieve_record($this->connection, $sql);
				break;
			case Query::UPDATE_STMT:
				$data = $sql['data'];
				$row = $sql['row'];
				return px_update_record($this->connection, $data, $row);
				break;
		}
	}
	
	/**
	 * @see BaseAdapter#fetch_assoc($set) 
	 */
	function fetch_assoc($set) {
		return $set;
	}
	
}