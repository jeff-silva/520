<?php


add_action('init', function() {
	$posttypes = new Posttypes\Posttypes();
	$posttypes = $posttypes->search();
	foreach($posttypes as $posttype) {
		register_post_type($posttype['post_type'], $posttype['post_type_args']);
	}
});


cdz_settings_tab('Post types', '520-settings-posttypes', function() {
	include __DIR__ . '/views/posttypes.php';
});
