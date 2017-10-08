<?php
/*
	Plugin Name: Midia 520 - Gerenciamento
	Plugin URI: -
	Description: Gerenciamento empresarial da Midia 520
	Version: 1.1.4
	Author: ARI Soft
	Author URI: http://www.ari-soft.com
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


define('_520VERSION_', '0.0.1');
define('_520RELEASE_', '--');


include __DIR__ . '/libs/Db.php';
include __DIR__ . '/helpers.php';
include __DIR__ . '/helpers-ui.php';
include __DIR__ . '/hooks.php';


function cdz_modules() {
	global $cdz_modules;

	$actives = get_option('520-modules');
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


function cdz_module_toggle($name) {
	$actives = get_option('520-modules');
	$actives = is_array($actives)? $actives: array();

	if (in_array($name, $actives)) {
		foreach($actives as $i=>$active) {
			if ($active==$name) unset($actives[$i]);
		}
	}
	else {
		$actives[] = $name;
	}

	return update_option('520-modules', $actives, true);
}


foreach(cdz_modules() as $mod) {
	if ($mod['active']) include $mod['init'];
}


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



add_action('init', function() {

	add_action('admin_head', function() { ?>
	<style>/* CSS style | Bugfix */
	select.form-control {height:auto!important; padding:9px;}
	</style>
	<?php });

});


/* Gerenciador de módulos 520 */
add_action('admin_menu', function() {
	add_menu_page('520 Modulos', '520 Modulos', 'manage_options', '520_modules', function() {

	if (isset($_GET['cdz_module_toggle'])) {
		cdz_module_toggle($_GET['cdz_module_toggle']);
		wp_redirect($_SERVER['HTTP_REFERER']); die;
	}

	?>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/3.3.7/flatly/bootstrap.min.css">

	<br>
	<ul class="list-group">
		<?php foreach(cdz_modules() as $mod): ?>
		<li class="list-group-item">
			<div class="row">
				<div class="col-xs-6"><?php echo $mod['basename']; ?></div>
				<div class="col-xs-6 text-right">
					<a href="admin.php?page=520_modules&cdz_module_toggle=<?php echo $mod['basename']; ?>">
						<?php if ($mod['active']==1): ?>Desativar
						<?php else: ?>Ativar<?php endif; ?>
					</a>
				</div>
			</div>
		</li>
		<?php endforeach; ?>
	</ul>
	<?php }, 'dashicons-admin-users', 1);
});