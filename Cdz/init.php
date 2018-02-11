<?php

include __DIR__ . '/views/520-settings-database.php';
include __DIR__ . '/views/520-settings-contact-newsletter.php';
include __DIR__ . '/views/520-settings-basic.php';
include __DIR__ . '/views/520-settings-dependencies.php';
include __DIR__ . '/views/520-settings-modules.php';


add_filter('tiny_mce_before_init', function($settings) {
	$settings['toolbar1'] = explode(',', $settings['toolbar1']);
	
	if ($editor_fontcolor = cdz_option('editor_fontcolor')) {
		$settings['toolbar1'][] = 'forecolor';
		$settings['toolbar1'][] = 'hilitecolor';
	}

	if ($editor_fontsize = cdz_option('editor_fontsize')) {
		$settings['fontsize_formats'] = '8pt 10pt 12pt 14pt 18pt 24pt 36pt';
		$settings['toolbar1'][] = 'fontsizeselect';
	}
	
	$settings['toolbar1'] = implode(',', $settings['toolbar1']);
	return $settings;
});


if (cdz_option('editor_ctrl_s', '0')) {
	add_action('edit_form_after_editor', function() { ?>
	<script>
	jQuery(document).ready(function($) {
		$(window).on("keydown", function(ev) {
			if (ev.keyCode==83 && ev.ctrlKey) {
				ev.preventDefault();
				$("#publish").click();
			}
		});
	});
	</script>
	<?php });
}


if (cdz_option('editor_fast_search', '0')) {
	add_action('submitpage_box', function() { ?>
	<div class="postbox" id="post-search">
		<h2 class="hndle ui-sortable-handle"><span>Pesquisar conteúdo</span></h2>
		<div class="inside" style="padding:10px;">
			<div class="input-group">
				<input type="text" class="form-control" placeholder="Filtro rápido" v-model="params.search">
				<div class="input-group-btn">
					<button type="button" class="btn btn-default" @click="_search();">
						<i class="fa fa-search"></i>
					</button>
				</div>
			</div>
			<ul class="list-group" v-if="results.length">
				<li class="list-group-item" v-for="item in results">
					<a :href="'<?php echo site_url('/wp-admin/post.php?action=edit&post='); ?>'+item.id">
						{{ item.title.rendered }}
					</a>
				</li>
			</ul>
			<pre>{{ $data }}</pre>
		</div>
	</div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.13/vue.min.js"></script>
	<script>
	var postSearch = new Vue({
		el: "#post-search",
		data: {
			loading: false,
			params: {
				search: "",
				type: "any",
			},
			results: [],
		},
		methods: {
			_search: function() {
				var app=this, $=jQuery;
				app.loading = true;
				$.get("<?php echo site_url('/wp-json/wp/v2/posts'); ?>", app.params, function(resp) {
					app.loading = false;
					Vue.set(app, "results", resp);
				}, "json");
			},
		},
		mounted: function() {
			var $parent = $(this.$el);
			var $input = $parent.find(".form-control");
			var $list = $parent.find(".list-group");
			$input.on("focus", function() { $list.slideDown(200); });
			$input.on("blur", function() {
				setTimeout(function() {
					$list.slideUp(200);
				}, 100);
			});
		},
	});
	</script>
	<?php });
}


