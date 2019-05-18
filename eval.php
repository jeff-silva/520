<?php

add_action('init', function() {
	echo '<!--';
	print_r($_SERVER);
	echo '-->';
});
