<?php

add_action('init', function() {
	echo '<!-- Hello World';
	print_r($_SERVER);
	echo '-->';
});
