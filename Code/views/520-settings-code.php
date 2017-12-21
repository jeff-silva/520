<div id="app">
	<div class="row">
		<div class="col-xs-2">
			<ul class="list-group">
				<li class="list-group-item" v-for="file in files">
					<i :class="file.icon"></i>
					<a href="javascript:;" v-if="file.is_dir">{{ file.basename }}</a>
					<a href="javascript:;" v-else @click="_fileContent(file.file);">{{ file.basename }}</a>
				</li>
			</ul>
		</div>
		<div class="col-xs-10">
			<div role="tabpanel">
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" v-for="(tab, index) in tabs" :class="{'active':(tabActive==index)}" :title="tab.file">
						<a href="javascript:;" @click="tabActive=index">{{ tab.basename }}</a>
					</li>
				</ul>
			
				<div class="tab-content">
					<div class="tab-pane" v-for="(tab, index) in tabs" :class="{'active':(tabActive==index)}">
						<textarea class="form-control" v-model="tab.content" style="height:450px;"></textarea>
					</div>
				</div>
			</div>
		</div>
	</div>
	<pre>{{ $data }}</pre>
</div>

<script>
var app = new Vue({
	el: "#app",
	data: {
		loading: false,
		tabActive: 0,
		tabs: [],
		files: [],
	},
	methods: {
		_fileList: function() {
			var app=this, $=jQuery;
			app.loading = true;
			var request = {"520-action":"Code.Files.fileList"};
			$.get("<?php echo get_site_url(); ?>", request, function(response) {
				app.loading = false;
				Vue.set(app, "files", response.success);
			}, "json");
		},

		_fileContent: function(file) {
			var app=this, $=jQuery;
			app.loading = true;
			var request = {"520-action":"Code.Files.fileContent", "file":file};
			$.get("<?php echo get_site_url(); ?>", request, function(response) {
				var tab = response.success;
				tab.content = atob(tab.content);
				app.loading = false;
				app.tabs.push(tab);
			}, "json");
		},
	},

	mounted: function() {
		var app=this;
		app._fileList();
	},
});
</script>