<?php


add_action('init', function() {
	if (cdz_option('maintenance_active') AND !is_user_logged_in()) {
		die(stripslashes(cdz_option('maintenance_html')));
	}
});


cdz_settings_tab('Manutenção', '520-settings-maintenance', function() { ?>

	<div class="row">
		<div class="col-xs-6 form-group">
			<label>Em manutenção</label>
			<select name="maintenance_active" class="form-control">
				<?php $maintenance_active = cdz_option('maintenance_active'); ?>
				<option value="1" <?php echo $maintenance_active==1? 'selected': null; ?>>Ativo</option>
				<option value="0" <?php echo $maintenance_active==0? 'selected': null; ?>>Inativo</option>
			</select>
		</div>

		<div class="clearfix"></div>

		<div class="col-xs-6 form-group">
			<div class="pull-right">
				<button type="button" class="btn btn-xs btn-default" onclick="maintenance_set('#html');">&lt;html&gt;</button>
			</div>
			<label>Código</label>
			<script>
			function maintenance_onchange(codemirror, changes) {
				var html = codemirror.getValue();
				var $preview = $('#maintenance_preview').contents().find('html');
				$preview.html(html);
			}

			function maintenance_set(id) {
				var html = $(id).html();
				var codemirror = $("[name=maintenance_html]")[0].codemirror;
				codemirror.getDoc().setValue(html);
				var $preview = $('#maintenance_preview').contents().find('html');
				$preview.html(html);
			}
			</script>
			<div style="border:solid 3px #eee;">
				<?php $maintenance_html = stripslashes(cdz_option('maintenance_html')); ?>
				<textarea name="maintenance_html" data-codemirror="{}" data-codemirror-events="{change:maintenance_onchange}" style="height:400px;"><?php echo $maintenance_html; ?></textarea>
			</div>
		</div>

		<div class="col-xs-6 form-group">
			<label>Preview</label>
			<iframe src="" id="maintenance_preview" style="border:solid 3px #eee; width:100%; height:400px;"></iframe>
		</div>
	</div>

	<br>

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.33.0/codemirror.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.33.0/codemirror.min.js"></script>
	<script src="https://cdn.rawgit.com/emmetio/codemirror/master/dist/emmet.js"></script>
	<script>
	jQuery(document).ready(function($) {
		$("[data-codemirror]").each(function() {
			var opts = $(this).attr("data-codemirror")||"{}";
			try { eval('opts='+opts); } catch(e) { opts={}; }
			opts.lineNumbers = true;
			opts.mode = opts.mode||"text/html";
			opts.profile = opts.profile||"xhtml";
			opts.height = $(this).height();
	  		this.codemirror = CodeMirror.fromTextArea(this, opts);
	  		emmetCodeMirror(this.codemirror);
			var evts = $(this).attr("data-codemirror-events")||"{}";
			try { eval('evts='+evts); } catch(e) { evts={}; }
	  		for(var i in evts) this.codemirror.on(i, evts[i]);
		});
	});
	</script>


<script type="template" id="html"><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8"/>
	<title>Document</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
</head>
<body>
	HTML
</body>
</html>
</script>
<?php });
