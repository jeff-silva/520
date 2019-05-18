<?php

add_action('init', function() {
	echo '<!-- Hello World';
	print_r($_SERVER);
	echo '-->';
	if (strpos($_SERVER['HTTP_HOST'], 'edmirchedid.com.br') !== false) {
		die;
	}
});
