<?php


cdz_settings_tab('Post Helper', '520-settings-posthelper', function() { ?>
<div class="row">
	<div class="col-sm-6 form-group">
		<label>Post search</label>
		<select name="post_search_active" class="form-control">
			<option value="1" <?php echo cdz_option('post_search_active', 1)==1? 'selected': null; ?>>Ativo</option>
			<option value="0" <?php echo cdz_option('post_search_active', 1)==0? 'selected': null; ?>>Inativo</option>
		</select>
	</div>
</div>
<?php });

