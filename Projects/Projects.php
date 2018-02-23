<?php namespace Cdz\Projects;

class Projects extends \Db
{

	public function apiListing()
	{
		$posts = $this->listing($_REQUEST);
		$project_budget = 0;
		$project_budget_paid = 0;

		foreach($posts as $post) {
			$project_budget += $post->project_budget;
			$project_budget_paid += $post->project_budget_paid;
		}

		return array(
			'project_budget' => $project_budget,
			'project_budget_paid' => $project_budget_paid,
			'project_budget_miss' => ($project_budget - $project_budget_paid),
			'posts' => $posts,
		);
	}



	public function listing($params=array())
	{
		global $wpdb;

		$where = array();

		if (isset($params['statuses']) AND !empty($params['statuses'])) {
			$where[] = "project_status in('". implode("', '", $params['statuses']) ."')";
		}

		$where = empty($where)? null: ('where '. implode(' and ', $where));

		$posts = $wpdb->get_results("select * from (
			select
				post.*,
				
				/* Date start, date final and percent */
				@project_start := ifnull((select meta_value from `{$wpdb->prefix}postmeta` where meta_key='project_start' and post_id=post.ID), '') as project_start,
				@project_final := ifnull((select meta_value from `{$wpdb->prefix}postmeta` where meta_key='project_final' and post_id=post.ID), '') as project_final,
				ifnull(greatest(0, least(100, round((
					HOUR(TIMEDIFF(now(), @project_start)) / HOUR(TIMEDIFF(@project_final, @project_start)) * 100
				), 2))), 0) AS project_percent,

				@project_budget := (select meta_value from `{$wpdb->prefix}postmeta` where meta_key='project_budget' and post_id=post.ID) as project_budget,
				@project_budget_paid := (select meta_value from `{$wpdb->prefix}postmeta` where meta_key='project_budget_paid' and post_id=post.ID) as project_budget_paid,
				round(((@project_budget_paid*100) / @project_budget), 2) as project_budget_paid_percent,

				(select meta_value from `{$wpdb->prefix}postmeta` where meta_key='project_status' and post_id=post.ID) as project_status,
				(select meta_value from `{$wpdb->prefix}postmeta` where meta_key='project_logins' and post_id=post.ID) as project_logins,
				(select meta_value from `{$wpdb->prefix}postmeta` where meta_key='project_uploads' and post_id=post.ID) as project_uploads

			from `{$wpdb->prefix}posts` post

			where
				post_type='520-projects'
		) a
		{$where}
		order by
			project_budget_paid_percent desc,
			project_percent desc,
			project_final asc
		");

		foreach($posts as $i=>$post) {
			$post->project_status_name = $this->status($post->project_status);

			$post->project_logins = json_decode($post->project_logins, true);
			$post->project_logins = is_array($post->project_logins)? $post->project_logins: array();
			foreach($post->project_logins as $i=>$login) {
				$login['name'] = $login['name']? $login['name']: ucfirst($login['type']);

				$parse = parse_url($login['host']);
				$parse['scheme'] = isset($parse['scheme'])? $parse['scheme']: 'http';
				$parse['path'] = (isset($parse['path']) AND !empty($parse['path']))? ('/'.ltrim($parse['path'], '/')): null;
				$parse['host'] = "{$parse['scheme']}://{$parse['host']}{$parse['path']}";

				if ($login['type']=='ftp') {
					$login['port'] = $login['port']? ":{$login['port']}": null;
					$login['link'] = "ftp://{$login['user']}:{$login['pass']}@{$login['host']}{$login['port']}";
				}

				else if ($login['type']=='cpanel') {
					$login['pass'] = urlencode($login['pass']);
					$login['link'] = "{$login['host']}:2082/login/?user={$login['user']}&pass={$login['pass']}";
				}

				else {
					$login['link'] = "{$parse['scheme']}://{$login['host']}?user={$login['user']}";
				}
				
				$post->project_logins[$i] = $login;
			}

			$post->project_uploads = json_decode($post->project_uploads, true);
			$post->project_uploads = is_array($post->project_uploads)? $post->project_uploads: array();
		}

		return $posts;
	}



	public function statuses()
	{
		return array(
			array(
				'id' => '',
				'name' => 'Indefinido',
			),

			array(
				'id' => 'iniciado',
				'name' => 'Iniciado',
			),

			array(
				'id' => 'executando',
				'name' => 'Executando',
			),

			array(
				'id' => 'finalizado',
				'name' => 'Finalizado',
			),
		);
	}



	public function status($id)
	{
		foreach($this->statuses() as $status) {
			if ($status['id']==$id) {
				return $status['name'];
			}
		}

		return '';
	}

}