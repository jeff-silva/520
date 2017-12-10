<?php




cdz_header();
?>

<br>


<?php $user = wp_get_current_user(); if ($user->ID==1): ?>
<?php cdz_settings_tab('Módulos', '520-settings-modules', function() {

	$actives = cdz_option('modules', array());

	if (isset($_GET['update-core'])) {
		cdz_update();
	}

	?>
	<div class="text-right">
		<a href="<?php echo admin_url("/options-general.php?page=520-settings&tab=520-settings-modules&update-core=1"); ?>" id="update-verify">
			<div class="btn btn-xs btn-success"><i class="fa fa-fw fa-spin fa-spinner"></i> Verificando</div>
		</a>
	</div>

	<script>
	jQuery(document).ready(function($) {
		var $verify = $("#update-verify");
		$.get("<?php echo get_site_url(); ?>/wp-content/plugins/520-master/info.json?rand=<?php echo rand(0, 999); ?>", function(json1) {
			$.get("https://raw.githubusercontent.com/jeff-silva/520/master/info.json?rand=<?php echo rand(0, 999); ?>", function(json2) {
				if(json1.version==json2.version) {
					$verify.html('<div class="btn btn-xs btn-success"><i class="fa fa-fw fa-check"></i> Atualizado</div>');
				}
				else {
					$verify.html('<div class="btn btn-xs btn-danger"><i class="fa fa-fw fa-cloud-download"></i> Atualizar</div>');
				}
			}, "json");
		}, "json");
	});
	</script>
	<br>

	<!-- Bugfix -->
	<input type="checkbox" name="modules[]" value="" checked style="display:none;">

	<ul class="list-group">
		<?php foreach(cdz_modules() as $mod): ?>
		<li class="list-group-item">
			<div class="row">
				<div class="col-xs-6"><?php echo $mod['basename']; ?></div>
				<div class="col-xs-6 text-right">
					<input type="checkbox" name="modules[]" value="<?php echo $mod['basename']; ?>" title="Ativo/Inativo" <?php echo in_array($mod['basename'], $actives)? 'checked': null ?>>
				</div>
			</div>
		</li>
		<?php endforeach; ?>
	</ul>

	<pre><?php include __520DIR__ . '/info.txt'; ?></pre>
<?php }); ?>
<?php endif; ?>

