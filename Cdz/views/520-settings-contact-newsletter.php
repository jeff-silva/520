<?php


add_action('520-settings', function() {
	cdz_tab('Contato/Newsletter', function() { ?>
	
<div class="alert" style="background:#eee;">
	A instrução básica de uso está descrita abaixo. <br>
	A input name=email é obrigatória sempre dentro do form. <br>
	O atributo data-action ativa a ação em ajax para o form, e seus valores podem ser "newsletter" ou "contact". <br>
	<code style="white-space:pre;">&lt;form data-action="newsletter"&gt;
	&lt;input type="text" name="email"&gt;
&lt;/form&gt;</code>
</div>

	<div class="row">
		<div class="col-sm-6 form-group">
			<label>Formulário de contato</label>
			<?php $contact_active = cdz_option('contact_active', '0'); ?>
			<select name="contact_active" class="form-control">
				<option value="1" <?php echo $contact_active==1? 'selected': null; ?> >Ativado</option>
				<option value="0" <?php echo $contact_active==0? 'selected': null; ?> >Desativado</option>
			</select>
		</div>
		<div class="col-sm-6 form-group">
			<label>Formulário de Newsletter</label>
			<?php $newsletter_active = cdz_option('newsletter_active', '0'); ?>
			<select name="newsletter_active" class="form-control">
				<option value="1" <?php echo $newsletter_active==1? 'selected': null; ?> >Ativado</option>
				<option value="0" <?php echo $newsletter_active==0? 'selected': null; ?> >Desativado</option>
			</select>
		</div>
		<div class="col-sm-6 form-group">
			<label>E-mails de contato</label>
			<textarea name="contact_emails" class="form-control"><?php echo cdz_option('contact_emails'); ?></textarea>
		</div>
	</div>
	<?php });
});





add_action('init', function() {
	global $wpdb;

	if (isset($_REQUEST['_action'])) {
		$action = $_REQUEST['_action'];
		$message = isset($_REQUEST['message'])? $_REQUEST['message']: '';
		unset($_REQUEST['_action']);

		$email = isset($_REQUEST['email'])? $_REQUEST['email']: null;
		$error = array();

		if (! $action) {
			$error[] = 'Action indefinida';
		}

		// Validando e-mail (obrigatório em todos os casos)
		if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$error[] = 'E-mail inválido';
		}

		// Verificando se e-mail existe em action=newsletter
		if ($action=='newsletter' AND $email) {
			$exists = $wpdb->get_results(" select * from {$wpdb->prefix}postmeta where meta_value like '%\"{$email}\"%' ");
			if (sizeof($exists) > 0) $error[] = 'Este e-mail já está cadastrado em nossa newsletter';
		}

		if (! empty($error)) {
			die('<div class="alert alert-danger">'. implode('<br>', $error) .'</div>');
		}

		else {
			$post_title = (ucwords($action) .': '. $email);
			$id = wp_insert_post(array(
				'post_title' => $post_title,
				'post_type' => $action,
				'post_content' => $message,
			), true);
			if ($id) update_post_meta($id, 'meta-action', $_REQUEST);

			if ($action=='newsletter') {
				die('<div class="alert alert-success">Obrigado por assinar nossa newsletter.</div>');
			}

			if ($action=='contact') {
				$to = implode(';', explode("\n", cdz_option('contact_emails')));
				wp_mail($to, $post_title, $message, array('Content-Type: text/html; charset=UTF-8'));
				die('<div class="alert alert-success">Obrigado por entrar em contato. <br>Responderemos em breve.</div>');
			}
		}

		die;
	}
});



add_action('wp_footer', function() { ?>
<script>
jQuery(document).ready(function($) {
	$("form[data-action]").submit(function(ev) {
		ev.preventDefault();
		var $form = $(this);
		$form.attr("autocomplete", "off");
		var post = {_action: $form.attr("data-action")};
		$.map($form.serializeArray(), function(n, i) { post[n['name']] = n['value']; });
		$form.find(".ajax-response").empty();
		$.get("<?php echo get_site_url(); ?>", post, function(response) {
			$form.find(".ajax-response").html(response);
		});
	});
});
</script>
<?php });



add_action('admin_menu', function() {

	if (cdz_option('newsletter_active', '0')==1) {
		add_menu_page('Newsletter', 'Newsletter', 'manage_options', '520-newsletter', function() {
			include __DIR__ . '/views/520-admin-newsletter.php';
		}, 'dashicons-admin-users', 10);
	}

	if (cdz_option('contact_active', '0')==1) {
		add_menu_page('Contato', 'Contato', 'manage_options', '520-contacts', function() {
			include __DIR__ . '/views/520-admin-contact.php';
		}, 'dashicons-admin-users', 10);
	}
});

