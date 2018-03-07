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


// Custom pages search
// add_action('load-edit.php', function() {
// 	add_filter('views_edit-520-projects', function() {
// 		include __DIR__ . '/views/custom-posts-list.php';
// 		include( ABSPATH . 'wp-admin/admin-footer.php' ); die;
// 	});
// });



// add_action('init', function() {
// 	register_taxonomy('520-projects-clients', array('520-projects'), array(
// 		'hierarchical' => true,
// 		'labels' => array(
// 			'name' => 'Clientes',
// 			'singular_name' => 'Cliente',
// 			'search_items' => 'Pesquisar clientes',
// 			'all_items' => 'Todos os clientes',
// 			'parent_item' => 'parent_item',
// 			'parent_item_colon' => 'parent_item_colon',
// 			'edit_item' => 'Editar cliente',
// 			'update_item' => 'Salvar cliente',
// 			'add_new_item' => 'Novo cliente',
// 			'new_item_name' => 'new_item_name',
// 			'menu_name' => 'Clientes',
// 		),
// 		'show_ui' => true,
// 		'show_admin_column' => true,
// 		'query_var' => true,
// 		'rewrite' => array('slug' => '520-projects-clients'),
// 	));
// }, 0);

add_action('save_post', function() {
	if (isset($_REQUEST['520-project-data'])) {
		$save = stripslashes($_REQUEST['520-project-data']);
		$save = json_decode($save, true);
		$save = new \Cdz\Projects\Project($save);
		$save->save($save);
	}
});

add_action('edit_form_after_editor', function($post) {
	global $post;
	if ($post->post_type == '520-projects') {
		include __DIR__ . '/views/project-edit.php';
	}
});


add_filter('manage_520-projects_posts_columns', function ( $columns ) {
	$columns['520-project-info'] = 'Projeto';
	return $columns;
});


add_action('manage_520-projects_posts_custom_column', function ($column_name, $post_id ) {
    
    if ($column_name == '520-project-info') {

    	$infos = new \Cdz\Projects\Project($post_id);
    	$infos = $infos->infos();

    	?>
		<div style="">
			<?php foreach($infos as $info): ?>
			<div>
				
				<?php if ($info['type']=='$'): ?>
				<div class="progress" title="<?php echo $info['name']; ?>" style="margin:0px 0px 3px 0px;">
					<div class="progress-bar progress-bar-striped active" style="text-align:left; overflow:initial; white-space:nowrap; color:#333 !important; width:0%;">
						&nbsp; <?php echo $info['name']; ?>: R$<?php echo number_format(intval($info['value']), 2, ',', '.'); ?>
					</div>
				</div>
				<?php endif; ?>

				<?php if ($info['type']=='%'): ?>
				<div class="progress" title="<?php echo $info['name']; ?>: <?php echo round($info['value'], 2); ?>%" style="margin:0px 0px 3px 0px;">
					<div class="progress-bar progress-bar-striped active" style="text-align:left; overflow:initial; white-space:nowrap; color:#333 !important; width:<?php echo $info['value']; ?>%">
						&nbsp; <?php echo $info['name']; ?>: <?php echo $info['value']; ?>%
					</div>
				</div>
				<?php endif; ?>
			</div>
			<?php endforeach; ?>
		</div>
    	<?php
	}

}, 10, 2);



// Customize list and edit
add_action('add_meta_boxes', function() {
	remove_meta_box('wpseo_meta', '520-projects', 'normal');
}, 100);

add_filter('manage_edit-520-projects_columns', function($columns) {
	return array(
		'cb' => $columns['cb'],
		'title' => $columns['title'],
		'520-project-info' => $columns['520-project-info'],
		// 'date' => $columns['date'],
	);
});


// add_action('manage_posts_custom_column' , function($column, $post_id) {
// 	switch ( $column ) {
// 		case 'book_author':
// 			$terms = get_the_term_list( $post_id, 'book_author', '', ',', '' );
// 			if ( is_string( $terms ) ) {
// 				echo $terms;
// 			} else {
// 				_e( 'Unable to get author(s)', 'your_text_domain' );
// 			}
// 			break;

// 		case 'publisher':
// 			echo get_post_meta( $post_id, 'publisher', true ); 
// 			break;
// 	}
// }, 10, 2 );


add_action('template_redirect', function() {
	global $post;
	if ($post->post_type != '520-projects') return false;
	$post = new \Cdz\Projects\Project($post);
	?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<?php cdz_header(); ?>
</head>
<body>
	<br>
	<div class="container">
		<div class="panel panel-default">
			<div class="panel-heading"><?php echo $post->post_title; ?></div>
			<div class="panel-body">
				<?php echo $post->post_content; ?>
			</div>
		</div>
		<?php // dd($post); ?>
	</div>
	<br>
</body>
</html>
<?php die; });
