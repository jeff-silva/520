<?php

if (isset($_POST['save'])) {
	unset($_POST['save']);

	if (isset($_POST['modules'])) $_POST['modules'] = array_filter($_POST['modules'], 'strlen');

	$settings = get_option('cdz_options');
	$settings = is_array($settings)? $settings: array();
	foreach($_POST as $key=>$val) $settings[$key] = $val;
	update_option('cdz_options', $settings);
	die("<script>location.href='{$_SERVER['HTTP_REFERRER']}';</script>");
}


cdz_header();
?>

<br>


<?php $user = wp_get_current_user(); if ($user->ID==1): ?>
<?php cdz_settings_tab('Módulos', '520-settings-modules', function() {

	$actives = cdz_option('modules', array());

	?>
	<div class="text-right">
		<a href="<?php echo admin_url("/options-general.php?page=520-settings&tab=520-settings-modules&update=1"); ?>" class="btn btn-xs btn-default">
			<i class="fa fa-fw fa-refresh"></i> Update 520
		</a>
	</div>
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





<form action="" method="post">
	
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
