<?php

add_action('520-settings', function() {
	
	/* Dependencies tab */
	cdz_tab('Dependências', function() {

		$dependencies[] = array(
			'slug' => 'google-analytics-dashboard-for-wp',
			'active' => is_plugin_active('google-analytics-dashboard-for-wp/gadwp.php'),
		);
		$dependencies[] = array(
			'slug' => 'cache-enabler',
			'active' => is_plugin_active('cache-enabler/cache-enabler.php'),
		);
		$dependencies[] = array(
			'slug' => 'simply-show-hooks',
			'active' => is_plugin_active('simply-show-hooks/simply-show-hooks.php'),
		);
		$dependencies[] = array(
			'slug' => 'what-the-file',
			'active' => is_plugin_active('what-the-file/what-the-file.php'),
		);
		$dependencies[] = array(
			'slug' => 'wordpress-seo',
			'active' => is_plugin_active('wordpress-seo/wordpress-seo.php'),
		);
		$dependencies[] = array(
			'slug' => 'ml-slider',
			'active' => is_plugin_active('ml-slider/ml-slider.php'),
		);
		
		include ABSPATH . 'wp-admin/includes/plugin-install.php';

		foreach($dependencies as $dependency) {
			$plugin  = plugins_api('plugin_information', array(
				'fields' => array(
					'banners' => true,
					'reviews' => true,
					'downloaded' => false,
					'active_installs' => true,
					'installed_plugins' => true,
				),
				'slug' => $dependency['slug'],
			));
			$plugin->active = $dependency['active'];
			$plugins[] = $plugin;
		}

	?>

	<div class="row">
		<?php foreach($plugins as $i=>$plugin): ?>
		<div class="col-xs-4 plugins-each <?php echo "plugins-each-{$i}"; ?>">
			
			<div class="popup plugin-each-modal-images <?php echo "plugin-each-modal-images-{$i}"; ?>">
				<div class="panel panel-default">
					<div class="panel-body" style="max-height:400px;">
						<div class="plugin-each-modal-images-mini">
							<div class="cover plugin-each-modal-images-mini-each" style="background-image:url(<?php echo $plugin->banners['low']; ?>);" onclick="set_modal_image(this, '<?php echo $plugin->banners['low']; ?>');"></div>
							<?php foreach($plugin->screenshots as $screen): ?>
							<div class="cover plugin-each-modal-images-mini-each" style="background-image:url(<?php echo $screen['src']; ?>);" onclick="set_modal_image(this, '<?php echo $screen['src']; ?>');"></div>
							<?php endforeach; ?>
						</div>
						<img src="<?php echo $plugin->banners['low']; ?>" alt="" class="plugin-each-modal-image-preview">
					</div>
					<div class="panel-footer text-right">
						<button type="button" class="btn btn-default" onclick="jQuery(this).closest('.popup').fadeOut(200);">Fechar</button>
					</div>
				</div>
			</div>

			<div class="cover plugins-each-cover" style="background-image:url(<?php echo $plugin->banners['low']; ?>);" onclick="open_modal_images(this);"></div>
			<div class="plugins-each-title"><?php echo $plugin->name; ?></div>
			<div class="plugins-each-actions">
				<?php $class = $plugin->active? 'btn btn-xs btn-success': 'btn btn-xs btn-primary'; ?>
				<?php $inner = $plugin->active? 'Instalado': 'Instalar'; ?>
				<a href="<?php echo admin_url("update.php?action=install-plugin&plugin={$plugin->slug}&_wpnonce=f60bd59328"); ?>" onclick="plugin_install(event);" class="<?php echo $class; ?>"><?php echo $inner; ?></a>
			</div>
		</div>
		<?php endforeach; ?>
	</div>

	<style>
	.plugins-each {margin-bottom:25px;}
	.plugin-each-modal-images-mini {}
	.plugin-each-modal-images-mini-each {width:50px !important; height:50px !important; display:inline-block !important; margin:0px 5px 0px 0px; cursor:pointer;}
	.plugin-each-modal-image-preview {width:100%;}
	.plugins-each-cover {width:100% !important; height:110px !important; cursor:pointer;}
	.plugins-each-title {height:20px; overflow:hidden; margin:7px 0px 4px 0px; text-transform:uppercase; font-weight:bold;}
	</style>

	<script>
	function open_modal_images(el) {
		var $=jQuery;
		$(el).closest(".plugins-each").find(".plugin-each-modal-images").fadeIn(200);
	}

	function set_modal_image(el, image_url) {
		var $=jQuery;
		$(el).closest(".plugins-each").find(".plugin-each-modal-image-preview").attr("src", image_url);
	}

	function plugin_install(ev) {
		ev.preventDefault();
		$(ev.target).html('Instalando...');
		var $iframe = $('<iframe src="'+ (ev.target.href||"") +'" style="display:none;"></iframe>');
		$iframe.on("load", function() {
			$(ev.target).attr("class", "btn btn-xs btn-success").html('Instalado');
		});
		$iframe.appendTo('body');
	}
	</script>


	<?php /*<div class="row">
		<?php foreach($dependencies as $depend): ?>
		<div class="col-sm-4">
			<iframe src="<?php echo admin_url("plugin-install.php?tab=plugin-information&plugin={$depend['slug']}&TB_iframe=true&height=400"); ?>" style="border:none; width:100%; height:400px;"></iframe>
			<br><br>
		</div>
		<?php endforeach; ?>
	</div>*/ ?>


	<?php });
});

