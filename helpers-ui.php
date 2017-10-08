<?php


function helper_ui_uploader($name, $value=null) {
	$id = 'helper_ui_uploader_' . rand(99, 99999);
	wp_enqueue_media();
	?>
	<div class="helper_ui_uploader" id="<?php echo $id; ?>">
		<button type="button" class="btn btn-default">Upload</button>
		<img src="<?php echo $value; ?>" alt="" style="width:100%;">
		<input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>">
	</div>
	<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#<?php echo $id; ?> button').click(function(e) {
			e.preventDefault();
			var $parent = $(this).parent();
			var image = wp.media({title:'Upload', multiple: false}).open()
			.on('select', function(e) {
				var uploaded_image = image.state().get('selection').first();
				var image_url = uploaded_image.toJSON().url;
				$parent.find("img").attr("src", image_url);
				$parent.find("input").val(image_url);
			});
		});
	});
	</script>
<?php }




function helper_ui_query($name, $value=null) {

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
<?php }

