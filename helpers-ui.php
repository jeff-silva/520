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



	static function uploads($attrs=null, $value=null)
	{
		$id = 'ui-uploads-' . uniqid(rand());
		$value = $value? $value: '[]';
		$attrs = self::_attrs($attrs, array(
			'name' => '',
			'style' => 'display:none;',
		));

		?>
		
		<div id="<?php echo $id; ?>" class="ui-uploads">
			<textarea <?php echo $attrs; ?>>{{ value }}</textarea>
			<button type="button" class="btn btn-default ui-uploads-btn" @click="_modal();">
				<i class="fa fa-fw fa-upload"></i> Upload
			</button>
			<br><br>
			<div class="ui-uploads-sortable">
				<div v-for="(img,i) in value" style="display:inline-block; margin:0px 5px 5px 0px; overflow:hidden;">
					<div class="ui-uploads-sortable-handle" style="position:relative;">
						<img :src="img.sizes.thumbnail.url" alt="">
						<div style="position:absolute; top:0; left:0; width:100%; text-align:right; background:rgba(255,255,255,.8); padding:5px;">
							<a href="javascript:;" class="fa fa-fw fa-pencil" @click="_edit(img, i);"></a>
							<a href="javascript:;" class="fa fa-fw fa-remove" @click="_remove(img);"></a>
						</div>
					</div>
					<div class="popup" :class="'popup-ui-uploads-edit-'+i">
						<div class="panel panel-default" style="min-width:400px;">
							<div class="panel-heading">{{ img.title || '-' }}</div>
							<div class="panel-body">
								<div class="form-group">
									<label>Título</label>
									<input type="text" v-model="img.title" class="form-control" title="Título da imagem">
								</div>

								<div class="form-group">
									<label>Caption</label>
									<input type="text" v-model="img.caption" class="form-control" title="Descrição curta">
								</div>

								<div class="form-group">
									<label>Descrição</label>
									<textarea class="form-control" title="Descrição longa" v-model="img.description"></textarea>
								</div>
							</div>
							<div class="panel-footer text-right">
								<button type="button" class="btn btn-default" data-close-popup>Salvar</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- <pre>{{ $data }}</pre> -->
		</div>

		<?php wp_enqueue_media(); ?>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.13/vue.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.6.0/Sortable.min.js"></script>
		<script type="text/javascript">
		new Vue({
			el: "#<?php echo $id; ?>",
			data: {
				value: <?php echo $value; ?>,
			},
			methods: {
				_modal: function() {
					var app = this;
					var image = wp.media({title:'Upload', multiple: true}).open().on('select', function(e) {
						var selection = JSON.parse(JSON.stringify(image.state().get('selection'), " ", 2));
						for(var i in selection) { app.value.push(selection[i]); }
					});
				},
				_remove: function(img) {
					var app = this;
					var index = app.value.indexOf(img);
					app.value.splice(index, 1);
				},
				_edit: function(img, i) {
					var app=this, $=jQuery;
					$("#<?php echo $id; ?> .popup-ui-uploads-edit-"+i).fadeIn(200);
				},
			},
			mounted: function(){
				var app=this, $=jQuery;
				app.$nextTick(function() {
					var list = $(app.$el).find(".ui-uploads-sortable").get(0);
					var sortable = Sortable.create(list, {
						animation: 150,
						handle: ".ui-uploads-sortable-handle",
						onEnd: function(e) {
							var array = app.value.filter(function(item) { return item; });
							array.splice(e.newIndex, 0, array.splice(e.oldIndex, 1)[0]);
							app.value = [];
							app.$nextTick(function() { app.value = array; });
						}
					});
				});
			},
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


	static function address($attrs=null, $value=null, $content=null) {
		$id = uniqid('ui_address_'.rand());
		$attrs = self::_attrs($attrs, array(
			'id' => $id,
			'name' => '',
			'style' => 'display:none;',
		));

		$value = is_array($value)? $value: json_decode($value, true);
		$value = is_array($value)? $value: array();
		$value = array_merge(array(
			'complement' => '',
			'route' => '',
			'complement' => '',
			'district' => '',
			'city' => '',
			'state' => '',
			'state_short' => '',
			'country' => '',
			'country_short' => '',
			'zipcode' => '',
			'number' => '',
			'lat' => '',
			'lng' => '',
		), $value);

		$content = is_callable($content)? $content: function($value, $id) { ?>
		<div style="display:none;">
			<input type="text" data-addr="state" value="<?php echo $value['state']; ?>" class="form-control" placeholder="Estado">
			<input type="text" data-addr="country" value="<?php echo $value['country']; ?>" class="form-control" placeholder="País">
			<input type="text" data-addr="lat" value="<?php echo $value['lat']; ?>" class="form-control" placeholder="lat">
			<input type="text" data-addr="lng" value="<?php echo $value['lng']; ?>" class="form-control" placeholder="lng">
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-8"><input type="text" data-addr="postal" value="<?php echo $value['postal']; ?>" class="form-control" placeholder="CEP" onchange="ui_address_postal_autocomplete('#<?php echo $id; ?>', this);" data-mask="{mask:'99999-999'}"></div>
			<div class="col-xs-12 col-sm-8"><input type="text" data-addr="route" value="<?php echo $value['route']; ?>" class="form-control ui_address_search" placeholder="Rua" onchange="ui_address_postal_autocomplete('#<?php echo $id; ?>', this);"></div>
			<div class="col-xs-6  col-sm-4"><input type="text" data-addr="number" value="<?php echo $value['number']; ?>" class="form-control" placeholder="Nº"></div>
			<div class="col-xs-6  col-sm-4"><input type="text" data-addr="complement" value="<?php echo $value['complement']; ?>" class="form-control" placeholder="Complemento"></div>
			<div class="col-xs-6  col-sm-8"><input type="text" data-addr="district" value="<?php echo $value['district']; ?>" class="form-control" placeholder="Bairro"></div>
			<div class="col-xs-6  col-sm-8"><input type="text" data-addr="city" value="<?php echo $value['city']; ?>" class="form-control" placeholder="Cidade"></div>
			<div class="col-xs-6  col-sm-2"><input type="text" data-addr="state_short" value="<?php echo $value['state_short']; ?>" class="form-control" placeholder="Estado"></div>
			<div class="col-xs-6  col-sm-2"><input type="text" data-addr="country_short" value="<?php echo $value['country_short']; ?>" class="form-control" placeholder="País"></div>
		</div>
		<small class="text-muted" data-addr="formatted"><?php echo $value['formatted']; ?></small>
		<?php }; ?>
		<div id="<?php echo $id; ?>" class="ui_address">
			<textarea <?php echo $attrs; ?>><?php echo json_encode($value); ?></textarea>
			<?php call_user_func($content, $value, $id); ?>
		</div>
		<style>
		.ui_address .form-control {width:100% !important; margin-bottom:5px;}
		.ui_address .row {padding:0px 13px;}
		.ui_address .row>* {padding:0px 2px;}
		.pac-container {z-index:9999 !important}
		@keyframes ui_address_loading_spinner {to {transform: rotate(360deg);}}
		.ui_address_loading:before {content: ''; box-sizing: border-box; position: absolute; top: 20px; right: 10px; width: 20px; height: 20px; margin-top: -10px; margin-left: -10px; border-radius: 50%; border: 2px solid #ccc; border-top-color: #333; animation: ui_address_loading_spinner .8s ease infinite;}
		</style>
		<script>
		var ui_address_postal_populate = function(parent, addr) {
			var $=jQuery;
			var $parent = $(parent);
			addr.formatted = (addr.route||"");
			if (addr.number) addr.formatted += " Nº "+(addr.number||"");
			if (addr.complement) addr.formatted += ", "+(addr.complement||"");
			if (addr.postal) addr.formatted += ", CEP "+(addr.postal||"");
			if (addr.district) addr.formatted += ", "+(addr.district||"");
			if (addr.city && addr.state_short) addr.formatted += " - "+(addr.city||"")+"/"+(addr.state_short||"");
			$parent.find("textarea").val(JSON.stringify(addr));
			for(var i in addr) { $parent.find("[data-addr="+i+"]").val(addr[i]); }
			$parent.find("[data-addr=formatted]").html(addr.formatted);
		};

		var ui_address_postal_autocomplete = function(parent, el) {
			var $=jQuery;
			$(el).parent().addClass("ui_address_loading");
			$.get("<?php echo site_url("/?ui_address_search&search="); ?>"+el.value, function(resp) {
				$(el).parent().removeClass("ui_address_loading");
				if (resp.error) { alert(resp.error); }
				else ui_address_postal_populate(parent, resp);
			}, "json");
		};

		jQuery(document).ready(function($) {
			var $parent = $("#<?php echo $id; ?>");
			$parent.find("[data-addr]").on("change input", function() {
				var ktype = $(this).attr("data-addr");
				var addr = $parent.find("textarea").val();
				try { eval('addr='+addr); } catch(e) { addr={}; }
				addr[ktype] = this.value;
				ui_address_postal_populate("#<?php echo $id; ?>", addr);
			});
		});
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



	// Ui::address search
	if (isset($_REQUEST['ui_address_search'])) {
		$params = array_merge(array(
			'search' => '',
		), $_REQUEST);

		function places_search($path=null) {
			$parse = parse_url($path);
			$parse = array_merge(array('path'=>null, 'query'=>null), $parse);
			parse_str($parse['query'], $parse['query']);
			$parse['query'] = is_array($parse['query'])? $parse['query']: array();
			$parse['query']['key'] = 'AIzaSyB-Li2nMHdkyiJVLubSOtxZZEqGkmxRpvs';
			$parse['query']['language'] = 'pt-BR';
			$parse['query'] = http_build_query($parse['query']);
			$path = trim("{$parse['path']}?{$parse['query']}", '/');
			$data = helper_content($url = "https://maps.googleapis.com/maps/api/place/{$path}");
			$data = json_decode($data, true);
			$data['url'] = $url;
			return $data;
		}

		function places_extract_parts($json, $result=null) {
			if (is_array($result) AND isset($result['result'])) {
				$json['lat'] = $result['result']['geometry']['location']['lat'];
				$json['lng'] = $result['result']['geometry']['location']['lng'];
				foreach($result['result']['address_components'] as $comp) {
					if ($comp['types'][0]=='postal_code') {
						$json['postal'] = $comp['long_name'];
					}
					else if ($comp['types'][0]=='sublocality_level_1') {
						$json['district'] = $comp['long_name'];
					}
					else if ($comp['types'][0]=='route') {
						$json['route'] = $comp['long_name'];
					}
					else if ($comp['types'][0]=='street_number') {
						$json['number'] = $comp['long_name'];
					}
					else if ($comp['types'][0]=='administrative_area_level_2') {
						$json['city'] = $comp['long_name'];
					}
					else if ($comp['types'][0]=='administrative_area_level_1') {
						$json['state'] = $comp['long_name'];
						$json['state_short'] = $comp['short_name'];
					}
					else if ($comp['types'][0]=='country') {
						$json['country'] = $comp['long_name'];
						$json['country_short'] = $comp['short_name'];
					}
				}
			}

			$json['formatted'] = $json['route'];
			if ($json['number']) $json['formatted'] .= " Nº {$json['number']}";
			if ($json['postal']) $json['formatted'] .= " CEP {$json['postal']}";
			if ($json['district']) $json['formatted'] .= ", {$json['district']}";
			$json['formatted'] .= " - {$json['city']}/{$json['state_short']}";
			return $json;
		}

		$json = array(
			'error'=>null,
			'route'=>null,
			'number'=>null,
			'postal'=>null,
			'district'=>null,
			'city'=>null,
			'state'=>null,
			'state_short'=>null,
			'country'=>null,
			'country_short'=>null,
			'lat'=>null,
			'lng'=>null,
			'formatted'=>null,
		);


		if ($params['search']) {

			// pesquisa por endereço
			if (preg_match('/[a-z]/', $params['search'])) {
				$resp1 = places_search("/textsearch/json?query={$params['search']}");
				if (isset($resp1['results'][0]['place_id'])) {
					$resp2 = places_search("/details/json?placeid={$resp1['results'][0]['place_id']}");
					$json = places_extract_parts($json, $resp2);

					if (! $json['postal']) {
						$resp3 = str_replace(' ', '%20', "https://viacep.com.br/ws/{$json['state_short']}/{$json['city']}/{$json['route']}/json/");
						$resp3 = json_decode(helper_content($resp3), true);
						if (isset($resp3[0]['cep'])) {
							$json['postal'] = $resp3[0]['cep'];
						}
					}

				}
			}

			// pesquisa por cep
			else {
				$resp1 = helper_content("https://viacep.com.br/ws/{$params['search']}/json/");
				$resp1 = json_decode($resp1, true);
				if ($resp1['logradouro'] OR $resp1['bairro'] OR $resp1['uf']) {
					$json['postal'] = $resp1['cep'];
					$json['route'] = $resp1['logradouro'];
					$json['district'] = $resp1['bairro'];
					$resp2 = places_search("/textsearch/json?query={$resp1['logradouro']}+{$resp1['bairro']}+{$resp1['uf']}");
					if (isset($resp2['results'][0]['place_id'])) {
						$resp3 = places_search("/details/json?placeid={$resp2['results'][0]['place_id']}");
						$json = places_extract_parts($json, $resp3);
					}
				}
			}

			// if (! $json['postal']) {
			// 	$url = str_replace(' ', '%20', "https://viacep.com.br/ws/{$json['state_short']}/{$json['city']}/{$json['route']}/json/");
			// 	$resp3 = helper_content($url);
			// 	$resp3 = json_decode($resp3, true);
			// 	if (isset($resp3[0]['cep'])) {
			// 		$json['postal'] = $resp3[0]['cep'];
			// 	}
			// }
		}

		echo json_encode($json); die;
	}

});


