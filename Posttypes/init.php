<?php


// Register posttypes
add_action('init', function() {
	$posttypes = new \Cdz\Posttypes\Posttypes();
	$posttypes = $posttypes->posttypeSearch();
	foreach($posttypes as $posttype) {
		//dd($posttype); die;
		register_post_type($posttype['posttype_slug'], $posttype['posttype_data']);
	}
});


add_action('520-settings', function() {
	cdz_tab('Post types', function() {
		include __DIR__ . '/views/posttypes.php';
	});
});
