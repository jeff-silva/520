<?php


add_action('init', function() {
	$posttypes = new \Cdz\Posttypes\Posttypes();
	$posttypes->register();
});

add_action('520-settings', function() {
	cdz_tab('Post types', function() {
		include __DIR__ . '/views/posttypes.php';
	});
});
