<?php

add_action('520-settings', function() {

	cdz_tab('Basico', function() { ?>
	<div class="row">
		<div class="col-xs-4">
			<div class="panel panel-default">
				<div class="panel-heading">Editor</div>
				<div class="panel-body">
					<div class="form-group">
						<label>Cor da fonte</label>
						<?php $editor_fontcolor = cdz_option('editor_fontcolor', '0'); ?>
						<select name="editor_fontcolor" class="form-control">
							<option value="0" <?php echo $editor_fontcolor==0? 'selected': null; ?>>Inativo</option>
							<option value="1" <?php echo $editor_fontcolor==1? 'selected': null; ?>>Ativo</option>
						</select>
					</div>

					<div class="form-group">
						<label>Tamanho da fonte</label>
						<?php $editor_fontsize = cdz_option('editor_fontsize', '0'); ?>
						<select name="editor_fontsize" class="form-control">
							<option value="0" <?php echo $editor_fontsize==0? 'selected': null; ?>>Inativo</option>
							<option value="1" <?php echo $editor_fontsize==1? 'selected': null; ?>>Ativo</option>
						</select>
					</div>

					<div class="form-group">
						<label>Salvar com CTRL+S</label>
						<?php $editor_ctrl_s = cdz_option('editor_ctrl_s', '0'); ?>
						<select name="editor_ctrl_s" class="form-control">
							<option value="0" <?php echo $editor_ctrl_s==0? 'selected': null; ?>>Inativo</option>
							<option value="1" <?php echo $editor_ctrl_s==1? 'selected': null; ?>>Ativo</option>
						</select>
					</div>

					<div class="form-group">
						<label>Pesquisa rápida</label>
						<?php $editor_fast_search = cdz_option('editor_fast_search', '0'); ?>
						<select name="editor_fast_search" class="form-control">
							<option value="0" <?php echo $editor_fast_search==0? 'selected': null; ?>>Inativo</option>
							<option value="1" <?php echo $editor_fast_search==1? 'selected': null; ?>>Ativo</option>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php }); ?>
<?php });
