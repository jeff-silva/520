<?php


cdz_settings_tab('Módulos', '520-settings-modules', function() {

	$actives = cdz_option('modules', array());

	if (isset($_GET['update-core'])) {
		if (cdz_update()) {
			echo '<div class="alert alert-success">Plugin atualizado</div>';
		}
	}

	?>

	<?php if ($update = cdz_need_update()): ?>
	<div class="alert alert-danger">
		Este plugin está atualmente na versão <?php echo $update['current']; ?>. <br>
		A versão <?php echo $update['new']; ?> já está disponível. <br>
		<a href="<?php echo helper_url_merge('?update-core=1'); ?>" class="btn btn-xs btn-default">Atualizar agora</a>
	</div>
	<?php else: ?>
	<div class="text-right">
		<a href="<?php echo helper_url_merge('?update-core=1'); ?>" class="btn btn-xs btn-success text-muted" onclick="return confirm('Forçar atualização?');">Atualizado</a>
		<br><small class="text-muted">Próxima verificação: <?php echo date('d/m/y - H:i:s', cdz_option('update_time')); ?></small>
	</div>
	<?php endif; ?>

	<br>

	<!-- Bugfix -->
	<input type="checkbox" name="modules[]" value="" checked style="display:none;">

	<div class="row">
		<div class="col-xs-6">
			<ul class="list-group">
				<?php foreach(cdz_modules() as $mod): ?>
				<li class="list-group-item">
					<div class="pull-right">
						<input type="checkbox" name="modules[]" value="<?php echo $mod['basename']; ?>" title="Ativo/Inativo" <?php echo in_array($mod['basename'], $actives)? 'checked': null ?>>
					</div>
					<?php echo $mod['basename']; ?>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="col-xs-6">
			--
		</div>
	</div>


	
<?php });

