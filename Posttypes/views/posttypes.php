<div id="app">
	<div class="row">
		<div class="col-xs-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="pull-right">
						<a href="javascript:;" class="fa fa-fw fa-plus" data-popup=".popup-posttype" @click="_reset('posttype');"></a>
					</div>
					<strong>POST TYPES</strong>
				</div>
				<div class="panel-body">
					<ul class="list-group">
						<li class="list-group-item text-center" v-if="posttypes.length==0">
							<small class="text-muted">Nenhum posttype encontrado</small>
						</li>
						<li class="list-group-item" v-for="item in posttypes">
							<div class="pull-right">
								<a href="javascript:;" class="fa fa-remove text-danger" @click="_posttypeDelete(item);"></a>
							</div>
							<a href="javascript:;" @click="posttype=item" data-popup=".popup-posttype">
								<i :class="item.posttype_data.menu_icon"></i>
								{{ item.posttype_plural }}
							</a>
						</li>
					</ul>

					<div class="popup popup-posttype">
						<div class="panel panel-default">
							<div class="panel-heading">
								<strong>POST TYPE</strong>
							</div>
							<div class="panel-body">
								<div role="tabpanel">
									<!-- Nav tabs -->
									<ul class="nav nav-tabs" role="tablist">
										<li class="active"><a href="#posttype-basic" data-toggle="tab">Basico</a></li>
										<li><a href="#posttype-taxonomies" data-toggle="tab">Taxonomias</a></li>
									</ul><br>
								
									<!-- Tab panes -->
									<div class="tab-content">
										<div class="tab-pane active" id="posttype-basic">
											<div class="row">
												<div class="col-xs-12 form-group">
													<label>Slug</label>
													<div class="input-group">
														<input type="text" v-model="posttype.posttype_slug" class="form-control">
														<div class="input-group-addon" style="cursor:pointer;" onclick="jQuery('.icon-selector').fadeToggle(200);" :title="posttype.posttype_data.menu_icon">
															<i :class="posttype.posttype_data.menu_icon"></i>
														</div>
													</div>
													<div class="icon-selector" style="display:none;">
														<div style="padding:5px; background:#eee;">
															<input type="text" class="form-control" placeholder="Filtrar ícones" @keyup="_iconsFilter($event);">
														</div>
														<div style="max-height:300px; overflow:auto;">
															<i style="margin:3px;" :class="icon" :title="icon" :data-icon="icon" :style="icon==posttype.posttype_data.menu_icon? 'background:#ffd;': ''" v-for="icon in icons" @click="posttype.posttype_data.menu_icon=icon" onclick="jQuery('.icon-selector').fadeOut(200);"></i>
														</div>
													</div>
												</div>

												<div class="col-xs-6 form-group">
													<label>Plural</label>
													<input type="text" v-model="posttype.posttype_plural" class="form-control">
												</div>

												<div class="col-xs-6 form-group">
													<label>Singular</label>
													<input type="text" v-model="posttype.posttype_singular" class="form-control">
												</div>

												<div class="col-xs-12 form-group">
													<label><input type="checkbox" class="form-control" value="thumbnail" v-model="posttype.posttype_data.supports"> thumbnail</label>
													<label><input type="checkbox" class="form-control" value="title" v-model="posttype.posttype_data.supports"> title</label>
													<label><input type="checkbox" class="form-control" value="editor" v-model="posttype.posttype_data.supports"> editor</label>
												</div>
											</div>
										</div>

										<div class="tab-pane" id="posttype-taxonomies" v-if="posttype.posttype_data.taxonomies">
											<div class="row">
												<div class="col-xs-6">
													<ul class="list-group">
														<li class="list-group-item" v-for="tax1 in posttype.posttype_data.taxonomies">
															<a href="javascript:;">{{ tax1 }}</a>
														</li>
													</ul>
												</div>
												<div class="col-xs-6">
													<ul class="list-group">
														<li class="list-group-item" v-for="tax2 in taxonomies">
															<a href="javascript:;" @click="posttype.posttype_data.taxonomies.push(tax2.taxonomy_slug);">
																{{ tax2.taxonomy_slug }}
															</a>
														</li>
													</ul>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="panel-footer text-right">
								<input type="button" value="Salvar" class="btn btn-primary" @click="_posttypeSave();">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="pull-right">
						<a href="javascript:;" class="fa fa-fw fa-plus" data-popup=".popup-taxonomy" @click="_reset('taxonomy');"></a>
					</div>
					<STRONG>TAXONOMIES</strong>
				</div>
				<div class="panel-body">
					<ul class="list-group">
						<li class="list-group-item text-center" v-if="taxonomies.length==0">
							<small class="text-muted">Nenhuma taxonomia encontrada</small>
						</li>
						<li class="list-group-item" v-for="item in taxonomies">
							<div class="pull-right">
								<a href="javascript:;" class="fa fa-remove text-danger" @click="_taxonomyDelete(item);"></a>
							</div>
							<a href="javascript:;" @click="taxonomy=item" data-popup=".popup-taxonomy">{{ item.taxonomy_plural }}</a>
						</li>
					</ul>

					<div class="popup popup-taxonomy">
						<div class="panel panel-default">
							<div class="panel-heading">
								<strong>TAXONOMY</strong>
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-xs-12 form-group">
										<label>taxonomy_slug</label>
										<input type="text" v-model="taxonomy.taxonomy_slug" class="form-control">
									</div>
									<div class="col-xs-6 form-group">
										<label>taxonomy_plural</label>
										<input type="text" v-model="taxonomy.taxonomy_plural" class="form-control">
									</div>
									<div class="col-xs-6 form-group">
										<label>taxonomy_singular</label>
										<input type="text" v-model="taxonomy.taxonomy_singular" class="form-control">
									</div>
								</div>
							</div>
							<div class="panel-footer text-right">
								<input type="button" value="Salvar" class="btn btn-primary" @click="_taxonomySave();">
							</div>
						</div>
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
		ajax: "<?php echo site_url('/wp-admin/admin-ajax.php'); ?>",
		posttype: {
			posttype_id: null,
			posttype_slug: null,
			posttype_plural: null,
			posttype_singular: null,
			posttype_data: [],
		},
		posttypes: [],
		taxonomy: {
			taxonomy_id: null,
			taxonomy_slug: null,
			taxonomy_plural: null,
			taxonomy_singular: null,
			taxonomy_data: [],
		},
		taxonomies: [],
		icons: <?php echo json_encode(cdz_icons()); ?>,
	},
	methods: {
		_ajax: function(method, params, call) {
			var app=this, $=jQuery;
			method = method||"get";
			app.loading=true;
			$.ajax({"url":app.ajax, "method":method, "dataType":"json", "data":params, success: function(resp) {
				app.loading=false;
				if (resp.error) { return alert(resp.error); }
				for(var i in resp.success) { Vue.set(app, i, resp.success[i]); }
				if (typeof call=="function") call.call(this, resp);
				$(".popup").fadeOut(200);
			}});
		},

		_reset: function(keyname) {
			var item = JSON.parse(JSON.stringify(this[keyname]));
			for(var i in item) {
				var val = item[i];
				if (! val) continue;
				if (typeof val=="object") { item[i] = Array.isArray(val)? []: {}; }
				else item[i] = null;
			}
			Vue.set(app, keyname, item);
		},

		_posttypeSave: function() {
			var app=this, $=jQuery;
			app._ajax("post", {
				"cdz": "Posttypes.Posttypes.posttypeSave",
				"data": app.posttype,
			});
		},

		_posttypeDelete: function(posttype) {
			var app=this, $=jQuery;
			if (!confirm("Tem certeza que deseja deletar este posttype?")) return false;
			app._ajax("post", {
				"cdz": "Posttypes.Posttypes.posttypeDelete",
				"id": posttype.posttype_id,
			});
		},

		_taxonomySave: function() {
			var app=this, $=jQuery;
			app._ajax("post", {
				"cdz": "Posttypes.Posttypes.taxonomySave",
				"data": app.taxonomy,
			});
		},

		_taxonomyDelete: function(taxonomy) {
			var app=this, $=jQuery;
			if (!confirm("Tem certeza que deseja deletar esta taxonomia?")) return false;
			app._ajax("post", {
				"cdz": "Posttypes.Posttypes.taxonomyDelete",
				"id": taxonomy.taxonomy_id,
			});
		},
		
		_iconsFilter: function($ev) {
			var app=this, $=jQuery;
			var $icons = $(app.$el).find(".icon-selector i");
			var search = ($ev.target.value||"").toLowerCase();
			
			if (search) {
				$icons.hide();
				$icons.each(function() {
					var value = ($(this).attr("data-icon")||"").toLowerCase();
					if (value.indexOf(search)!==-1) $(this).show();
				});
			}
			
			else {
				$icons.show();
			}
		},
	},
	mounted: function() {
		var app=this, $=jQuery;
		app._ajax("get", {"cdz":"Posttypes.Posttypes.data"});
	},
});
</script>