<?php namespace Cdz\Projects;

class Project
{
	public function __construct($post=null)
	{
		if ($post) { $this->loadPost($post); }
	}


	public function loadPost($post)
	{
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
		if (! $this->meta_tasks) {
			$this->meta_tasks = get_post_meta($this->ID, 'meta_tasks');
			$this->meta_tasks = is_array($this->meta_tasks)? $this->meta_tasks: array();
		}

		return $this->meta_tasks;
	}


	public function taskAdd($data)
	{
		// $data = array_merge();
	}


	public function logins()
	{
		$this->meta_logins = get_post_meta($this->ID, 'meta_logins', true);
		$this->meta_logins = json_decode($this->meta_logins, true);
		$this->meta_logins = is_array($this->meta_logins)? $this->meta_logins: array();
		return $this->meta_logins;
	}

	public function save($post)
	{
		$post = new self($post);

		// meta_logins
		if (is_array($post->meta_logins)) {
			foreach($post->meta_logins as $i=>$login) {
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

				if ($login['type']=='ftp' AND $login['host'] AND $login['user'] AND $login['pass']) {
					$login['url'] = "ftp://{$login['user']}:{$login['pass']}@{$login['host']}";
				}
				else if ($login['type']=='cpanel' AND $login['host'] AND $login['user'] AND $login['pass']) {
					$login['url'] = "https://{$login['host']}";
				}
				else {
					$login['url'] = '';
				}

				$post->meta_logins[$i] = $login;
			}
			update_post_meta($post->ID, 'meta_logins', json_encode($post->meta_logins));
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
}