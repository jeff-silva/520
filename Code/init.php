<?php


add_action('520-settings', function() {
	cdz_tab('Code', function() {
		include __DIR__ . '/views/520-settings-code.php';
	});
});