<?php namespace Cdz\Projects;

class Project
{
	public function __construct($post=null)
	{
		if ($post) { $this->loadPost($post); }
	}


	public function loadPost($post)
	{
		if (is_numeric($post)) {
			$post = get_post($post);
		}

		foreach($post as $key=>$val) {
			$this->{$key} = $val;
		}

		$defaults = array(
			'meta_date_start' => false,
			'meta_date_final' => false,
			'meta_tasks' => array(),
			'meta_logins' => array(),
		);

		foreach($defaults as $key=>$val) {
			if (! $this->{$key}) {
				$this->{$key} = $defaults[$key];
			}
		}
	}


	public function tasks()
	{
		$this->meta_tasks = get_post_meta($this->ID, 'meta_tasks', true);
		$this->meta_tasks = json_decode($this->meta_tasks, true);
		$this->meta_tasks = is_array($this->meta_tasks)? $this->meta_tasks: array();
		return $this->meta_tasks;
	}


	public function taskDefault($task)
	{
		$uniqid = 'id'. uniqid(rand());
		return array_merge(array(
			'id' => $uniqid,
			'title' => "Task #{$uniqid}",
			'description' => '',
			'status' => '',
			'date_start' => '',
			'date_final' => '',
			'budget' => '0',
			'closed' => '0',
		), array_filter($task, 'strlen'));
	}


	public function logins()
	{
		$this->meta_logins = get_post_meta($this->ID, 'meta_logins', true);
		$this->meta_logins = json_decode($this->meta_logins, true);
		$this->meta_logins = is_array($this->meta_logins)? $this->meta_logins: array();
		return $this->meta_logins;
	}

	public function loginDefault($login)
	{
		$uniqid = 'id'. uniqid(rand());
		$login = array_merge(array(
			'id' => $uniqid,
			'name' => "Login #{$uniqid}",
			'host' => '',
			'user' => '',
			'pass' => '',
			'port' => '',
			'type' => '',
			'url' => '',
		), array_filter($login, 'strlen'));
		$parse = array_merge(array(
			'scheme' => 'http',
			'host' => '',
			'path' => '',
		), parse_url($login['host']));
		$parse['host'] = "{$parse['host']}{$parse['path']}";

		if ($login['type']=='ftp' AND $login['host'] AND $login['user'] AND $login['pass']) {
			$login['url'] = "ftp://{$login['user']}:{$login['pass']}@{$parse['host']}";
		}
		else if ($login['type']=='cpanel' AND $login['host'] AND $login['user'] AND $login['pass']) {
			$login['url'] = "{$parse['scheme']}://{$parse['host']}:2082/login/?user={$login['user']}&pass={$login['pass']}";
		}
		else {
			$login['url'] = '';
		}
		return $login;
	}

	public function save($post)
	{
		$post = new self($post);

		// meta_logins
		if (is_array($post->meta_logins)) {
			foreach($post->meta_logins as $i=>$login) {
				$login = $this->loginDefault($login);
				$post->meta_logins[$i] = $login;
			}
			update_post_meta($post->ID, 'meta_logins', json_encode($post->meta_logins));
		}


		// meta_tasks
		if (is_array($post->meta_tasks)) {
			foreach($post->meta_tasks as $i=>$task) {
				$task = $this->taskDefault($task);
				$post->meta_tasks[$i] = $task;
			}
			update_post_meta($post->ID, 'meta_tasks', json_encode($post->meta_tasks));
		}

		return $post;
	}


	public function loginAdd($data=array())
	{
		$id = uniqid();
		$data = array_merge(array(
			'id' => $id,
			'name' => "Login {$id}",
			'host' => '',
			'user' => '',
			'pass' => '',
			'port' => '',
			'type' => '',
		), $data);
		$this->meta_logins = $this->logins();
		$this->meta_logins[] = $data;
		update_post_meta($this->ID, 'meta_logins', $this->meta_logins);
		return $this;
	}


	public function loginTypes()
	{
		return array(
			array('id'=>'ftp', 'name'=>'FTP'),
			array('id'=>'cpanel', 'name'=>'CPanel'),
			array('id'=>'wordpress', 'name'=>'Wordpress'),
			array('id'=>'mysql', 'name'=>'MySQL'),
		);
	}




	/* ############## APIS ############## */

	public function apiSave()
	{
		if (isset($_REQUEST['post'])) {
			$this->loadPost($_REQUEST['post']);
			$this->meta_logins = $this->logins();
			$return['post'] = $this->save($_REQUEST['post']);
			return $return;
		}
	}


	public function infos()
	{
		if (! $this->ID) return false;
		$this->tasks();
		$info = array();

		// Tasks
		$tasks_closed = 0;
		$percent = 0;
		if (sizeof($this->meta_tasks)) {
			foreach($this->meta_tasks as $task) { if ($task['closed']) $tasks_closed++; }
			$percent = round(($tasks_closed*100) / sizeof($this->meta_tasks), 2);
		}
		$info[] = array('name'=>'Tasks', 'value'=>$percent, 'type'=>'%');

		// Budget
		$budget = 0;
		foreach($this->meta_tasks as $task) { $budget += $task['budget']; }
		$info[] = array('name'=>'Budget', 'value'=>$budget, 'type'=>'$');

		// Deadline
		$dates = array();
		foreach($this->meta_tasks as $task) {
			if ($task['date_start']) $dates[] = $task['date_start'];
			if ($task['date_final']) $dates[] = $task['date_final'];
		}
		$percent = 0;
		if (sizeof($dates)) {
			$date_start = strtotime(min($dates));
			$date_final = strtotime(max($dates));
			$date_today = time();
			$percent = round(((($date_today - $date_start) / ($date_final - $date_start)) * 100), 2);
		}
		$info[] = array('name'=>'Deadline', 'value'=>$percent, 'type'=>'%');

		// Payd

		// Total percent
		$avg = array();
		foreach($info as $i=>$inf) {
			if ($inf['type']=='%' AND $inf['value']) {
				$avg[] = $inf['value'];
			}
		}
		$avg = sizeof($avg)? (array_sum($avg) / count($avg)): 0;
		$avg = array('name'=>'Média geral', 'value'=>$avg, 'type'=>'%');
		array_unshift($info, $avg);

		return $info;
	}


	public function apiInfos()
	{
		$this->loadPost($_REQUEST['post']);
		return array(
			'infos' => $this->infos(),
		);
	}
}