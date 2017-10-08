<?php


add_action('init', function() {
	$posttypes = new Posttypes\Posttypes();
	$posttypes = $posttypes->search();
	foreach($posttypes as $posttype) {
		register_post_type($posttype['post_type'], $posttype['post_type_args']);
	}
});


add_action('admin_menu', function() {
	add_menu_page('Post types', 'Post types', 'manage_options', '520-posttypes', function() {
		include __DIR__ . '/views/posttypes.php';
	}, 'dashicons-admin-users', 10);
});