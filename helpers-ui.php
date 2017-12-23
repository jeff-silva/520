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

}
