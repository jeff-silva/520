<?php

class Db {

	public $table=null;
	public $pk=null;
	public $fields=array();
	public $error=null;
	public $lastSql=null;

	public function __construct() {
		global $wpdb;
		$this->table = $wpdb->prefix . $this->table;
	}

	public function _onTableCreate() {}


	public function input($key, $default=null)
	{
		return isset($_REQUEST[$key])? $_REQUEST[$key]: $default;
	}


	public function error($error=null)
	{
		if ($error) $this->error .= $error."\n";
		return $this->error;
	}

	function tableCreate()
	{
		global $wpdb;

		$sql = "CREATE TABLE `{$this->table}` (";
		foreach($this->fields as $key=>$val) {
			if ($val=='primary') {
				$val = "INT(20) NOT NULL AUTO_INCREMENT";
				$this->pk = $key;
			}
			$sql .= "`{$key}` {$val},";
		}
		$sql .= "PRIMARY KEY (`{$this->pk}`)) COLLATE='{$wpdb->collate}' ENGINE=InnoDB;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$delta = dbDelta($sql);
		$this->_onTableCreate();
		return $delta;
	}

	public function tables()
	{
		return array_map(function($table) {
			$table = (array) $table;
			$table = array_values($table);
			return $table[0];
		}, $this->get("show tables"));
	}

	public function tableExists()
	{
		return in_array($this->table, $this->tables());
	}

	public function save($data)
	{
		global $wpdb;

		if ($this->tableExists()) {
			$this->tableCreate();
		}

		$fields = array();
		foreach($this->get("show fields from {table}") as $field) $fields[ $field->Field ] = $field->Field;
		$id = false;

		// Add table field
		if (sizeof($this->fields) > sizeof($fields)) {
			foreach(array_diff_key($this->fields, $fields) as $key=>$val) {
				$this->get("ALTER TABLE `{$this->table}` ADD `{$key}` {$val}");
			}
			return $this->save($data);
		}


		foreach($data as $key=>$val) {
			if (!isset($fields[ $key ])) {
				unset($data[ $key ]);
				continue;
			}
			if ($key==$this->pk) {
				unset($data[ $key ]);
				$id = $val;
				continue;
			}
			if (is_array($val)) {
				$val = json_encode($val);
			}
		}
		$sql = implode(', ', array_map(function($key, $val) {
			return "`{$key}`='{$val}'";
		}, array_keys($data), $data));


		$sql = $id? "UPDATE {table} SET {$sql} WHERE `{$this->pk}`='{$id}' ": "INSERT INTO {table} SET {$sql} ";
		$this->get($sql);
		$id = $id? $id: $wpdb->insert_id;
		return $this->first("select * from {table} where {id}='{$id}' ");
	}



	public function get($sql)
	{
		global $wpdb;
		$sql = $this->sql($sql);

		$results = $wpdb->get_results($sql);

		if ($wpdb->last_error) {
			if (isset($wpdb->dbh->errno)) {
				// Table doesnt exists
				if ('1146' == $wpdb->dbh->errno) {
					$this->tableCreate();
					$results = $wpdb->get_results($sql);
				}
			}
		}

		// foreach($results as $i=>$row) {
		// 	foreach($this->fields as $key=>$type) {
		// 		$row->{$key} = $row->{$key}? $row->{$key}: null;
		// 	}
		// 	$results[$i] = $row;
		// }

		return $results;
	}


	public function paginate($sql, $page=1, $perpage=15)
	{
		$results = $this->get($sql);
		$total = sizeof($results);
		$pages = ceil($total / $perpage);
		$page = $page? $page: 1;
		$offset = ($page - 1) * $perpage;
		return array(
			'page' => $page,
			'pages' => $pages,
			'perpage' => $perpage,
			'offset' => $offset,
			'total' => $total,
			'data' => array_slice($results, $offset, $perpage),
		);
	}


	public function first($sql)
	{
		$results = $this->get($sql);
		return isset($results[0])? $results[0]: false;
	}


	public function sql($sql)
	{
		global $wpdb;

		$table = $this->table;
		$id = $this->pk;

		$sql = preg_replace_callback('/\{(.+?)\}/', function($reg) use($table, $id) {
			$exp = $reg[0];
			if ($exp=='{table}') return "`{$table}`";
			else if ($exp=='{id}') return "`{$id}`";
			else {
				if ($include = realpath(__DIR__ .'/'. ucfirst($reg[1]) .'.php')) {
					$class = ucfirst($reg[1]);
					if (! class_exists($class)) {
						include $include;
					}
					$class = new $class;
					return "`{$class->table}`";
				}
			}
		}, $sql);

		return $sql;
	}

}