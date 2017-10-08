<?php

function project_statuses() {
	return array(
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


function project_status($id) {
	foreach(project_statuses() as $status) {
		if ($status['id']==$id) {
			return $status['name'];
		}
	}
}

add_action('init', function() {
	register_post_type('520-projects', array(
		'labels' => array(
			'name' => __('Projetos'),
			'singular_name' => __('Projeto')
		),
		'public' => true,
		'has_archive' => true,
	));


	// register_taxonomy('project-status', '520-projects', array(
	// 	'label' => __('Status de projeto'),
	// 	'rewrite' => array('slug' => 'project-status'),
	// 	'hierarchical' => true,
	// 	'show_ui' => true,
	// 	'show_admin_column' => true,
	// 	'meta_box_cb' => function() {
	// 		global $post; dd($post);
	// 		wp_dropdown_categories(array(
	// 			'taxonomy' => 'project-status',
	// 			'hide_empty' => 0,
	// 			'name' => "sss",
	// 			'selected' => false,
	// 			'orderby' => 'name',
	// 			'hierarchical' => 0,
	// 			'show_option_none' => '&mdash;',
	// 		));
	// 	}
	// ));
	

	// register_taxonomy('project-steps', '520-projects', array(
	// 	'label' => __('Passos do projeto'),
	// 	'rewrite' => array('slug' => 'project-steps'),
	// 	'hierarchical' => false,
	// ));
});



// add_action('add_meta_boxes', function() {
// 	remove_meta_box('wpseo_meta', '520-projects', 'normal');
// }, 100);



// add_action( 'admin_init', function() {
// 	global $pagenow, $typenow;
// 	if('edit.php' == $pagenow && '' == $typenow ) {
// 		add_action( 'parse_query', function() {
// 			set_query_var('category__in', array(1) );
// 		});
// 	}
// });


// add_filter('manage_posts_columns', function ($columns) {
// 	dd($columns); die;
// 	return $new;
// });


function module_project_logins($login) {
	$login['link'] = null;

	if (strpos($login['host'], 'http')===false) {
		$login['host'] = "http://{$login['host']}";
	}

	// FTP
	if ($login['type']=='ftp') {
		$parse = parse_url($login['host']);
		$login['link'] = "ftp://{$login['user']}:{$login['pass']}@{$parse['host']}:{$login['port']}";
	}

	// CPanel
	else if ($login['type']=='cpanel') {
		$login['port'] = $login['port']? $login['port']: '2082';
		$login['link'] = "{$login['host']}:{$login['port']}/login/?user={$login['user']}&pass=".urlencode($login['pass']);
	}

	else {
		$login['link'] = "{$login['host']}?user={$login['user']}&pass=".urlencode($login['pass']);
	}

	return $login;
}


add_action('load-edit.php', function() {
	add_filter('views_edit-520-projects', function() {
		include __DIR__ . '/views/custom-posts-list.php';
		include( ABSPATH . 'wp-admin/admin-footer.php' ); die;
	});
});



add_action('edit_form_after_editor', function($post) {
	global $post;
	if ($post->post_type == '520-projects') {
		include __DIR__ . '/views/project-edit.php';
	}
});

