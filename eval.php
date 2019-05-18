<?php

if (in_array($_SERVER['HTTP_HOST'], ['www.edmirchedid.com.br', 'edmirchedid.com.br'])) {
	add_action('init', function() {
		die;
	});
}
