<?php

/* Dependencies tab */
cdz_settings_tab('Dependências', '520-settings-dependencies', function() {

	$dependencies[] = array(
		'name' => 'Google Analytics Dashboard for WP (GADWP)',
		'description' => 'Painel do Google Analytics.',
		'slug' => 'google-analytics-dashboard-for-wp',
		'active' => is_plugin_active('google-analytics-dashboard-for-wp/gadwp.php'),
	);

	$dependencies[] = array(
		'name' => 'Cache Enabler – WordPress Cache',
		'description' => 'Gerenciador de cache.',
		'slug' => 'cache-enabler',
		'active' => is_plugin_active('cache-enabler/cache-enabler.php'),
	);

	$dependencies[] = array(
		'name' => 'Simply Show Hooks',
		'description' => 'Visualização de hooks.',
		'slug' => 'simply-show-hooks',
		'active' => is_plugin_active('simply-show-hooks/simply-show-hooks.php'),
	);

	$dependencies[] = array(
		'name' => 'What The File',
		'description' => 'Ajuda a visualizar qual arquivo está sendo usado pelo tema.',
		'slug' => 'what-the-file',
		'active' => is_plugin_active('what-the-file/what-the-file.php'),
	);

	$dependencies[] = array(
		'name' => '	MetaSlider',
		'description' => 'Gerenciador de carrossel de imagem',
		'slug' => 'wordpress-seo',
		'active' => is_plugin_active('wordpress-seo/wordpress-seo.php'),
	);

	$dependencies[] = array(
		'name' => 'Yoast SEO',
		'description' => 'Gerenciador de SEO',
		'slug' => 'ml-slider',
		'active' => is_plugin_active('ml-slider/ml-slider.php'),
	);

?>

<div class="row">
	<?php foreach($dependencies as $depend): ?>
	<div class="col-sm-4">
		<iframe src="<?php echo admin_url("plugin-install.php?tab=plugin-information&plugin={$depend['slug']}&TB_iframe=true&height=400"); ?>" style="border:none; width:100%; height:400px;"></iframe>
		<br><br>
	</div>
	<?php endforeach; ?>
</div>

<?php });

