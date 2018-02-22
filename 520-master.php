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
			// dd($class, $method, $call, $json); die;
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



function cdz_header() { ?>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/3.3.7/flatly/bootstrap.min.css">

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

	<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.4.2/vue.min.js"></script>

	<style>/* CSS style | Bugfix */
	select.form-control {height:auto!important; padding:9px;}
	</style>
<?php }



function cdz_settings_tab($title=null, $slug=null, $callback=null) {
	global $cdz_settings_tabs;
	$cdz_settings_tabs = is_array($cdz_settings_tabs)? $cdz_settings_tabs: array();

	if ($title AND $slug AND $callback) {
		$cdz_settings_tabs[$slug] = array(
			'title' => $title,
			'slug' => $slug,
			'callback' => $callback,
		);
	}

	return $cdz_settings_tabs;
}


foreach(cdz_modules() as $mod) {
	if ($mod['active']) {
		include_once $mod['init'];
	}
}
include_once __DIR__ . '/Cdz/init.php';


add_action('wp_login', function() {
	$json1 = json_decode(helper_content(__DIR__ . '/info.json'), true);
	$json2 = json_decode(helper_content('https://raw.githubusercontent.com/jeff-silva/520/master/info.json'), true);
	if ($json1['version'] != $json2['version']) {
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
			
			<?php $tabs = cdz_settings_tab();
			$active = isset($_GET['tab'])? $tabs[$_GET['tab']]: reset($tabs); ?>

			<ul class="nav nav-tabs">
				<?php foreach($tabs as $tab): ?>
				<li class="<?php echo $active['slug']==$tab['slug']? 'active': null; ?>"><a href="<?php echo admin_url("/options-general.php?page=520-settings&tab={$tab['slug']}"); ?>"><?php echo $tab['title']; ?></a></li>
				<?php endforeach; ?>
			</ul>

			<div id="tab-content" style="padding:15px;">
				<?php foreach($tabs as $tab) {
					if ($tab['slug']==$active['slug']) {
						call_user_func($tab['callback']);
					}
				} ?>
			</div>

			<div class="panel-footer text-right">
				<input type="submit" name="save" value="Salvar" class="btn btn-primary">
			</div>
		</form>

		<?php
	});
});


add_action('init', function() {
	if (isset($_GET['520-action'])) {
		$json = array('success'=>false, 'error'=>false);
		$params = explode('.', $_GET['520-action']);
		if (sizeof($params)>=3) {
			$class[] = array_shift($params);
			$class[] = array_shift($params);
			$class = implode('\\', $class);
			$method = 'api' . ucfirst(array_shift($params));
			$call = array($class, $method);
			if (is_callable($call)) {
				$class = new $class();
				$call = array($class, $method);
				try {
					$json['success'] = call_user_func_array($call, $params);
				}
				catch(\Exception $e) {
					$json['error'] = $e->getMessage();
				}
			}
		}
		else {
			$json['error'] = 'Parâmetros insuficientes';
		}

		die(json_encode($json));
	}



	if (isset($_GET['search_post'])) {
		$posts = get_posts(array(
			'post_type' => 'any',
			's' => $_GET['search_post'],
		));
		echo json_encode($posts); die;
	}


	// Automatic update
	if ($update_time = cdz_option('update_time')) {
		if (time() > $update_time) {
			cdz_update();
		}
	}
	else {
		cdz_option_update('update_time', (time() + (60*60*24)));
	}

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




//wp-admin/admin-ajax.php?action=520&call=Test.Aaa.search
helper_ajax('520', function() {
	$params = explode('.', (isset($_GET['call'])? $_GET['call']: null));

	$call = array();
	$call[] = array_shift($params);
	$call[] = array_shift($params);
	$method = 'api'. ucfirst(array_shift($params));

	
	if ($include = realpath(__DIR__ .'/'. implode('/', $call) .'.php')) {
		$class = implode('\\', $call);
		$class = new $class();
		$call = array($class, $method);
		if (is_callable($call)) {
			$error = false;
			$success = call_user_func_array($call, $params);
			if (property_exists($call[0], 'error')) {
				if (is_array($call[0]->error) AND !empty($call[0]->error)) {
					$error = $call[0]->error;
					$success = false;
				}
				else if (is_string($call[0]->error) AND $call[0]->error) {
					$error = array($call[0]->error);
					$success = false;
				}
			}

			$data = array('success' => $success, 'error' => $error);

			$output = isset($_REQUEST['output'])? $_REQUEST['output']: 'json';
			if ($output=='pre') { dd($data); }
			else { echo json_encode($data); }
			die;
		}
	}
});


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
