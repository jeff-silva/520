<?php


class Ui
{

	static function _attrs($atts=null, $defs=null)
	{
		if (is_string($atts)) parse_str($atts, $atts);
		$atts['class'] = isset($atts['class'])? $atts['class']: null;
		$atts['class'] = explode(' ', $atts['class']);

		if (is_string($defs)) parse_str($defs, $defs);
		$defs['class'] = isset($defs['class'])? $defs['class']: null;
		$defs['class'] = explode(' ', $defs['class']);
		foreach($defs['class'] as $cl) $atts['class'][] = $cl;
		unset($defs['class']);
		$atts['class'] = implode(' ', array_filter($atts['class'], 'strlen'));

		$atts = array_merge($defs, $atts);
		return implode(' ', array_map(function($key, $val) {
			return "{$key}=\"{$val}\"";
		}, array_keys($atts), $atts));
	}

	static function uploader($attr=null)
	{
		$id = uniqid(rand(), true);
		?>
		<div class="helper-ui-uploader" id="<?php echo $id; ?>">
			<div class="input-group">
				<input type="text" <?php echo $attr; ?> class="form-control helper-ui-uploader-input">
				<div class="input-group-btn">
					<button type="button" class="btn btn-default helper-ui-uploader-btn">Upload</button>
				</div>
			</div>
		</div>
		<?php wp_enqueue_media(); ?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			$("#<?php echo $id; ?> .helper-ui-uploader-btn").on("click", function(ev) {
				var $parent = $("#<?php echo $id; ?>");
				var image = wp.media({title:'Upload', multiple: false}).open().on('select', function(e) {
					var uploaded_image = image.state().get('selection').first();
					var image_url = uploaded_image.toJSON().url;
					$parent.find("img").attr("src", image_url);
					$parent.find(".helper-ui-uploader-input").val(image_url);
					$parent.find(".helper-ui-uploader-cover").css({"background-image":'url('+image_url+')'});
				});
			});
		});
		</script>
	<?php
	}



	static function upload($attrs=null, $value=null)
	{
		$attrs = self::_attrs($attrs, array(
			'type' => 'text',
			'class' => 'form-control ui-upload-input',
			'value' => $value,
		));

		$id = uniqid('ui-upload-');

		?>
		<div class="input-group ui-upload" id="<?php echo $id; ?>">
			<input <?php echo $attrs; ?>>
			<input type="file" class="ui-upload-file" style="display:none;">
			<div class="input-group-btn">
				<button type="button" class="btn btn-default ui-upload-btn">
					<i class="fa fa-fw fa-upload"></i>
				</button>
			</div>
			<div class="ui-upload-progress" style=""></div>
		</div>
		<script>
		jQuery(document).ready(function($) {
			var $parent = $("#<?php echo $id; ?>");
			var $input = $parent.find(".ui-upload-input");
			var $file = $parent.find(".ui-upload-file");
			var $btn = $parent.find(".ui-upload-btn");
			var $progress = $parent.find(".ui-upload-progress");

			$btn.on("click", function() {
				$file.click();
			});

			$file.on("change", function() {
				if (this.files[0]) {
					var formdata = new FormData();
					formdata.append('upload', this.files[0]);
					jQuery.ajax({
						url: "<?php echo admin_url('admin-ajax.php?helper_upload=true'); ?>",
						type: "post",
						dataType: "json",
						contentType: false,
						processData: false,
						data: formdata,
						xhr: function() {
							var xhr = $.ajaxSettings.xhr();
							if(xhr.upload) {
								xhr.upload.addEventListener('progress', function(e) {
									if(e.lengthComputable) {
										var percent = (e.loaded * 100)/e.total;
										$progress.css({width:percent+'%'});
									}
								}, false);
							}
							return xhr;
						},
						success: function(response) {
							$progress.css({width:0});
							if (response.url) {
								$input.val(response.url);
							}
						},
						error: function(response) {
							$progress.css({width:0});
						},
					});
				}
			});


			$parent.on("drag dragstart dragover dragenter", function(ev) {
				ev.preventDefault();
				ev.stopPropagation();
				$(this).addClass("ui-upload-dnd");
			});

			$parent.on("dragend dragleave drop", function(ev) {
				ev.preventDefault();
				ev.stopPropagation();
				$(this).removeClass("ui-upload-dnd");
				$file[0].files = ev.originalEvent.dataTransfer.files;
				$file.trigger("change");
			});
		});
		</script>
		<style>
		.ui-upload {position:relative;}
		.ui-upload * {transition:all 100ms ease;}
		.ui-upload-dnd {box-shadow:0px 0px 0px 15px rgba(36, 164, 193, 0.78), 0px 0px 0px 5px rgba(36, 164, 193, 0.78)}
		.ui-upload-dnd * {background:rgba(36, 164, 193, 0.78) !important; border:none !important; box-shadow:none !important; border-radius:0}
		.ui-upload-progress {position:absolute; top:100%; left:0px; display:block; display:block; border:solid 1px green}
		</style>
		<?php
	}



	static function posts($name, $value=null)
	{
		
		$id = 'helper_ui_query_'. rand(99, 99999);

		$value = json_decode($value, true);
		$value = is_array($value)? $value: array();
		$value = array_merge(array(
			'post_type' => null,
			'order' => null,
			'posts_per_page' => null,
		), $value);

		?>
		<div id="<?php echo $id; ?>">
			<div class="row">
				<div class="col-sm-4 form-group">
					<label>Tipo de página</label>
					<select v-model="query.post_type" class="form-control">
						<option value="">Selecione</option>
						<?php foreach(get_post_types(array('public'=>true), 'objects') as $type): ?>
						<option value="<?php echo $type->name; ?>"><?php echo $type->labels->name; ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="col-sm-4 form-group">
					<label>Ordenar por</label>
					<select class="form-control" v-model="query.order">
						<option value="">Selecione</option>
						<option value="ID DESC">Último > Primeiro</option>
						<option value="ID ASC">Primeiro > Último</option>
						<option value="post_title ASC">A-Z</option>
						<option value="post_title DESC">Z-A</option>
						<option value="ID RAND">Aleatório</option>
					</select>
				</div>


				<div class="col-sm-4 form-group">
					<label>Limite</label>
					<input type="text" v-model="query.posts_per_page" class="form-control">
				</div>
			</div>
			<textarea name="<?php echo $name; ?>" style="display:none;">{{ query|json }}</textarea>
			<pre>{{ $data|json }}</pre>
		</div>
		<script>
		var app_<?php echo $id; ?> = new Vue({
			el: "#<?php echo $id; ?>",
			data: {
				query: <?php echo json_encode($value); ?>,
			},
			methods: {},
		});
		</script>
		<?php
	}



	static function cep($attrs=null) {
		$id = uniqid('ui-cep-'.rand());
		$attrs = self::_attrs($attrs, array(
			'class' => 'form-control',
			'value' => '',
			'type' => 'text',
			'id' => $id,
		)); ?>
		<input <?php echo $attrs; ?>>
	<?php }



	static function postform($call=null) {
		$id = uniqid('ui_postform_'.rand());
		$attrs = self::_attrs($attrs, array(
			'method' => 'post',
			'id' => $id,
			'submit' => "return {$id}_submit(this);",
		)); ?>
		<form <?php echo $attrs; ?>>
			<?php call_user_func($call); ?>
		</form>
		<script>
		<?php echo "function {$id}_submit(form) {"; ?> 
			var $form = $(this);
			$form.attr("autocomplete", "off");
			var post = {postform:1};
			$.map($form.serializeArray(), function(n, i) { post[n['name']] = n['value']; });
			$form.find(".ajax-response").empty();
			$.post("<?php echo site_url(); ?>", post, function(response) {
				$(form).find(".ajax-response").html(response);
			});
			return false;
		<?php echo "}"; ?> 
		</script>
		<?php
	}

}



add_action('init', function() {
	
	// UI Ajax upload
	if (isset($_REQUEST['helper_upload']) AND !empty($_REQUEST['helper_upload'])) {

		$return = array();

		$return['url'] = false;
		$return['path'] = false;
		$return['upload'] = false;

		if ($_FILES['upload']['tmp_name']) {
			
			$wp_upload_dir = wp_upload_dir();
			$return['path'] = isset($_REQUEST['path'])? trim($_REQUEST['path'], '/'): null;
			$return['path'] = "{$wp_upload_dir['basedir']}/{$return['path']}";

			$_FILES['upload']['name'] = preg_replace('/[^a-zA-Z0-9-_.]/', '', $_FILES['upload']['name']);
			$return['upload'] = $_FILES['upload'];
			if (move_uploaded_file($_FILES['upload']['tmp_name'], "{$return['path']}/{$_FILES['upload']['name']}")) {
				$return['url'] = "{$wp_upload_dir['baseurl']}/{$_FILES['upload']['name']}";
			}
		}

		echo json_encode($return, JSON_PRETTY_PRINT); die;
	}

});


