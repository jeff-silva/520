<?php

if (isset($_REQUEST['cdz-form'])) {
	add_action('init', function() {
		$post = array_merge(array(
			'email' => '',
		), $_REQUEST);
		unset($post['cdz-form']);
		$resp = array('success'=>false, 'error'=>false);

		if (! filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
			$resp['error'] .= "E-mail inválido\n";
		}

		echo json_encode($resp); die;
	});
}



add_shortcode('cdz-form', function($atts=null, $content=null) {
	ob_start();
	
	$atts = shortcode_atts(array(
		'type' => 'contact',
	), $atts);

	$formid = 'cdz-form-'.uniqid();
	
	?>
	<form id="<?php echo $formid; ?>" onsubmit="return _cdzFormSubmit(this);">
		<div class="cdz-form-error"></div>
		<div class="cdz-form-success"></div>
		<?php echo $content; ?> 
	</form>
	<script>
	var _cdzFormSubmit = function(form) {
		var $=jQuery;
		var $form=$(form);
		var $error = $form.find(".cdz-form-error");
		var $success = $form.find(".cdz-form-success");
		var post = {"cdz-form":true, "cdz-form-type":"<?php echo $atts['type']; ?>"};
		$.map($form.serializeArray(), function(n, i){ post[n['name']] = n['value']; });

		$error.empty();
		$form.css({opacity:.5});

		$.post("<?php echo site_url('/wp-admin/admin-ajax.php'); ?>", post, function(resp) {
			$form.css({opacity:1});
			if (resp.error) {
				$error.html('<div class="alert alert-danger">'+ resp.error +'</div>');
				return false;
			}
			$success.html('<div class="alert alert-success">Mensagem enviada</div>');
			form.reset();
		}, "json");

		return false;
	};
	</script>
	<?php

	return ob_get_clean();
});