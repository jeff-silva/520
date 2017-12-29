<?php

include __DIR__ . '/520-contact-newsletter.php';
include __DIR__ . '/views/520-settings-basic.php';
include __DIR__ . '/views/520-settings-dependencies.php';
include __DIR__ . '/views/520-settings-modules.php';


add_filter('tiny_mce_before_init', function($settings) {
	$settings['toolbar1'] = explode(',', $settings['toolbar1']);
	
	if ($editor_fontcolor = cdz_option('editor_fontcolor')) {
		$settings['toolbar1'][] = 'forecolor';
		$settings['toolbar1'][] = 'hilitecolor';
	}

	if ($editor_fontsize = cdz_option('editor_fontsize')) {
		$settings['fontsize_formats'] = '8pt 10pt 12pt 14pt 18pt 24pt 36pt';
		$settings['toolbar1'][] = 'fontsizeselect';
	}
	
	$settings['toolbar1'] = implode(',', $settings['toolbar1']);
	return $settings;
});
