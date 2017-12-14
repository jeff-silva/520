<?php


add_action('init', function() {
	$posttypes = new Posttypes\Posttypes();
	$posttypes->register();
});


cdz_settings_tab('Post types', '520-settings-posttypes', function() {
	include __DIR__ . '/views/posttypes.php';
});
