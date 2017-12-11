<?php
/*
	Plugin Name: Midia 520 - Gerenciamento
	Plugin URI: -
	Description: Gerenciamento empresarial da Midia 520
	Version: 0.0.10
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

spl_autoload_register(function($class) {
	$class = preg_replace('/[^a-zA-Z0-9]/', DIRECTORY_SEPARATOR, $class);
	if ($class = realpath(__DIR__ . DIRECTORY_SEPARATOR . $class . '.php')) {
		include $class;
	}
});


define('__520DIR__', __DIR__);


include __DIR__ . '/libs/Db.php';
include __DIR__ . '/helpers.php';
include __DIR__ . '/helpers-ui.php';
include __DIR__ . '/helper-snippets.php';


function cdz_option($key=null, $default=null) {
	$settings = get_option('cdz_options');
	$settings = is_array($settings)? $settings: array();
	if ($key) {
		return isset($settings[$key])? $settings[$key]: $default;
	}
	return $settings;
}


function cdz_modules() {
	global $cdz_modules;

	$actives = cdz_option('modules', array());
	$actives = is_array($actives)? $actives: array();

	if (! isset($cdz_modules)) {
		$cdz_modules = array();
		foreach(glob(__DIR__ . '/*') as $file) {
			if (is_dir($file) AND $init = realpath("{$file}/init.php")) {
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

	// Download zip
	include __DIR__ . '/libs/PclZip.php';
	if (file_exists(__DIR__ . '/download.zip')) unlink(__DIR__ . '/download.zip');
	file_put_contents(__DIR__ . '/download.zip', fopen('https://github.com/jeff-silva/520/archive/master.zip', 'r'));


	// Delete all, except zip
	if (!function_exists('cdz_delete_all_files')) {
		function cdz_delete_all_files($glob, $files=array(), $level=0) {
			foreach(glob($glob) as $file) {
				if (is_dir($file)) {
					$files = cdz_delete_all_files($file . '/*', $files, $level+1);
				}
				$files[] = $file;
			}
			if ($level==0) {
				foreach($files as $file) {
					if ($file == __DIR__ . DIRECTORY_SEPARATOR. 'download.zip') continue;
					if (is_dir($file)) rmdir($file);
					else unlink($file);
				}
			}
			return $files;
		}
	}
	cdz_delete_all_files(__DIR__ . '/*');


	// Extract zip
	$zip = new PclZip(__DIR__ . '/download.zip');
	$zip->extract(PCLZIP_OPT_PATH, __DIR__, PCLZIP_OPT_REMOVE_PATH, '520-master', PCLZIP_OPT_REPLACE_NEWER);


	// Send e-mail to 520
	$site_url = get_site_url();
	$admin_email = get_option('admin_email');
	$body = "O site {$site_url} atualizou o plugin da 520. <br>";
	$body .= "Para entrar em contato com o administrador, envie um e-mail para {$admin_email}";
	wp_mail('lampejo520@gmail.com', 'Atualização 520', $body, array('Content-Type: text/html; charset=UTF-8'));


	// Delete zip
	unlink(__DIR__ . '/download.zip');

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
	if ($mod['active']) include $mod['init'];
}


add_action('wp_login', function() {
	$json1 = json_decode(file_get_contents(__DIR__ . '/info.json'), true);
	$json2 = json_decode(file_get_contents('https://raw.githubusercontent.com/jeff-silva/520/master/info.json'), true);
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
			die("<script>location.href='{$_SERVER['HTTP_REFERRER']}';</script>");
		}

		include __DIR__ . '/views/520-settings-modules.php';
		include __DIR__ . '/views/520-settings-dependencies.php';
		?>

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
	if (isset($_GET['search_post'])) {
		$posts = get_posts(array(
			'post_type' => 'any',
			's' => $_GET['search_post'],
		));
		echo json_encode($posts); die;
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

			// Validate prices
			if (in_array($key, array('project_budget'))) {
				$value = preg_replace('/[^0-9]/', '.', $value);
			}

			// Validate datetime
			else if (in_array($key, array('project_start', 'project_final'))) {
				if (strtotime($value)===false) {
					$value = false;
				}
			}

			update_post_meta($_POST['post_ID'], $key, $value);
		}
	}
}, 10, 2);




//wp-admin/admin-ajax.php?action=520&call=Test.Aaa.search
helper_ajax('520', function() {
	$call = explode('.', (isset($_GET['call'])? $_GET['call']: null));
	$method = array_pop($call);
	$method = 'api'.ucfirst($method);
	$call = array_map('ucfirst', $call);
	
	if ($include = realpath(__DIR__ .'/'. implode('/', $call) .'.php')) {
		$class = implode('\\', $call);
		$class = new $class();
		$call = array($class, $method);
		if (is_callable($call)) {
			$error = false;
			$success = call_user_func($call);
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


