<div class="wrap">
<h1>Post types</h1>

<div id="app">

	<div class="text-right">
		<a href="javascript:;" class="btn btn-primary" data-popup="#modal-new-posttype">
			<i class="fa fa-fw fa-plus"></i> Novo post type
		</a>
	</div>
	<br>
	
	<!-- new posttype -->
	<div class="popup" id="modal-new-posttype">
		<div class="panel panel-default">
			<div class="panel-heading">
				<a href="javascript:;" class="pull-right popup-close">&times;</a>
				Novo Post Type
			</div>
			<div class="panel-body">
				<div class="alert alert-danger" v-if="error.length>0">
					<a href="javascript:;" class="pull-right" @click="error=[];"><i class="fa fa-remove"></i></a>
					<div v-for="err in error">{{ err }}</div>
				</div><br>

				<div class="row">
					<div class="col-xs-12 form-group">
						<label>Post type</label>
						<input type="text" v-model="posttypeNew.post_type" class="form-control">
					</div>
					<div class="col-xs-6 form-group">
						<label>Singular</label>
						<input type="text" v-model="posttypeNew.singular" class="form-control">
					</div>
					<div class="col-xs-6 form-group">
						<label>Plural</label>
						<input type="text" v-model="posttypeNew.plural" class="form-control"><br>
					</div>
				</div>
			</div>
			<div class="panel-footer text-right">
				<button type="button" class="btn btn-default popup-close">Cancelar</button>
				<button type="button" class="btn btn-primary" @click="_posttypeCreateDefault();">Criar</button>
			</div>
		</div>
	</div>
	<!-- new posttype -->

	
	<ul class="list-group">
		<li class="list-group-item text-center" v-if="posttypes.length==0">Nenhum post type registrado</li>

		<li class="list-group-item" v-for="post in posttypes">
			<div class="pull-right">
				<a href="javascript:;" class="btn btn-xs btn-primary" @click="_posttypeEdit(post);"><i class="fa fa-fw fa-pencil"></i></a>
				<a href="javascript:;" class="btn btn-xs btn-danger" @click="_posttypeRemove(post);"><i class="fa fa-fw fa-remove"></i></a>
			</div>
			<span class="dashicons-before" :class="post.post_type_args.menu_icon"></span>
			<strong>{{ post.post_type_args.label }}</strong>
		</li>
	</ul>

	<!-- edit posttype -->
	<div class="popup" id="modal-posttype" v-if="posttypeEdit">
		<div class="panel panel-default">
			<div class="panel-heading">
				<a href="javascript:;" class="pull-right popup-close">&times;</a>
				Editar post type
			</div>

			<div class="panel-body container">
				<div class="alert alert-danger" v-if="error.length>0">
					<a href="javascript:;" class="pull-right" @click="error=[];"><i class="fa fa-remove"></i></a>
					<div v-for="err in error">{{ err }}</div>
				</div><br>

				<div role="tabpanel">

					<div class="tabs">
						
						<!-- Tab basic -->
						<input type="radio" name="posttype-tab" id="posttype-tab-basic" checked>
						<label for="posttype-tab-basic">Basic</label>
						<div>
							<div class="row">
								<div class="col-xs-6 form-group">
									<label>post_type</label>
									<div class="form-control" disabled>{{ posttypeEdit.post_type }}</div>
								</div>
								<div class="col-xs-6 form-group">
									<label>label</label>
									<input type="text" v-model="posttypeEdit.post_type_args.label" class="form-control">
								</div>
								<div class="col-xs-12 form-group">
									<label>description</label>
									<textarea v-model="posttypeEdit.post_type_args.description" class="form-control"></textarea>
								</div>
								<div class="col-xs-6 form-group">
									<label>menu_position</label>
									<input type="text" v-model="posttypeEdit.post_type_args.menu_position" class="form-control">
								</div>
								<div class="col-xs-6 form-group">
									<label>menu_icon</label>
									<div class="input-group">
										<div class="input-group-addon"><div class="dashicons-before" :class="posttypeEdit.post_type_args.menu_icon"></div></div>
										<select v-model="posttypeEdit.post_type_args.menu_icon" class="form-control">
											<option value="">--</option>
											<option :value="icon" v-for="icon in dashicons">{{ icon }}</option>
										</select>
									</div>
								</div>
							</div>
						</div>

						<!-- Tab advanced -->
						<input type="radio" name="posttype-tab" id="posttype-tab-advanced">
						<label for="posttype-tab-advanced">Advanced</label>
						<div>
							<div class="row">
								<div class="col-xs-12 form-group">
									<label>labels.name</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.name" class="form-control">
								</div>

								<div class="col-xs-12 form-group">
									<label>labels.singular_name</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.singular_name" class="form-control">
								</div>

								<div class="col-xs-12 form-group">
									<label>labels.add_new</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.add_new" class="form-control">
								</div>

								<div class="col-xs-12 form-group">
									<label>labels.add_new_item</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.add_new_item" class="form-control">
								</div>

								<div class="col-xs-12 form-group">
									<label>labels.edit_item</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.edit_item" class="form-control">
								</div>

								<div class="col-xs-12 form-group">
									<label>labels.new_item</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.new_item" class="form-control">
								</div>

								<div class="col-xs-12 form-group">
									<label>labels.view_item</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.view_item" class="form-control">
								</div>

								<div class="col-xs-12 form-group">
									<label>labels.view_items</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.view_items" class="form-control">
								</div>

								<div class="col-xs-12 form-group">
									<label>labels.search_items</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.search_items" class="form-control">
								</div>

								<div class="col-xs-12 form-group">
									<label>labels.not_found</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.not_found" class="form-control">
								</div>

								<div class="col-xs-12 form-group">
									<label>labels.not_found_in_trash</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.not_found_in_trash" class="form-control">
								</div>

								<div class="col-xs-12 form-group">
									<label>labels.parent_item_colon</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.parent_item_colon" class="form-control">
								</div>

								<div class="col-xs-12 form-group">
									<label>labels.all_items</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.all_items" class="form-control">
								</div>

								<div class="col-xs-12 form-group">
									<label>labels.archives</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.archives" class="form-control">
								</div>

								<div class="col-xs-12 form-group">
									<label>labels.attributes</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.attributes" class="form-control">
								</div>

								<div class="col-xs-12 form-group">
									<label>labels.insert_into_item</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.insert_into_item" class="form-control">
								</div>

								<div class="col-xs-12 form-group">
									<label>labels.uploaded_to_this_item</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.uploaded_to_this_item" class="form-control">
								</div>

								<div class="col-xs-12 form-group">
									<label>labels.featured_image</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.featured_image" class="form-control">
								</div>

								<div class="col-xs-12 form-group">
									<label>labels.set_featured_image</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.set_featured_image" class="form-control">
								</div>

								<div class="col-xs-12 form-group">
									<label>labels.remove_featured_image</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.remove_featured_image" class="form-control">
								</div>

								<div class="col-xs-12 form-group">
									<label>labels.use_featured_image</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.use_featured_image" class="form-control">
								</div>

								<div class="col-xs-12 form-group">
									<label>labels.menu_name</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.menu_name" class="form-control">
								</div>

								<div class="col-xs-12 form-group">
									<label>labels.filter_items_list</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.filter_items_list" class="form-control">
								</div>

								<div class="col-xs-12 form-group">
									<label>labels.items_list_navigation</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.items_list_navigation" class="form-control">
								</div>

								<div class="col-xs-12 form-group">
									<label>labels.items_list</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.items_list" class="form-control">
								</div>

								<div class="col-xs-12 form-group">
									<label>labels.name_admin_bar</label>
									<input type="text" v-model="posttypeEdit.post_type_args.labels.name_admin_bar" class="form-control">
								</div>
							</div>
						</div>

						<!-- Tab info -->
						<input type="radio" name="posttype-tab" id="posttype-tab-info">
						<label for="posttype-tab-info">Info</label>
						<div>
							<div class="row">
								<div class="col-xs-6 form-group">
									<label>Sets</label><br>
									<label><input type="checkbox" v-model="posttypeEdit.post_type_args.public" class="form-control"> public</label><br>
									<label><input type="checkbox" v-model="posttypeEdit.post_type_args.exclude_from_search" class="form-control"> exclude_from_search</label><br>
									<label><input type="checkbox" v-model="posttypeEdit.post_type_args.publicly_queryable" class="form-control"> publicly_queryable</label><br>
									<label><input type="checkbox" v-model="posttypeEdit.post_type_args.show_ui" class="form-control"> show_ui</label><br>
									<label><input type="checkbox" v-model="posttypeEdit.post_type_args.show_in_nav_menus" class="form-control"> show_in_nav_menus</label><br>
									<label><input type="checkbox" v-model="posttypeEdit.post_type_args.show_in_menu" class="form-control"> show_in_menu</label><br>
									<label><input type="checkbox" v-model="posttypeEdit.post_type_args.show_in_admin_bar" class="form-control"> show_in_admin_bar</label><br>
									<label><input type="checkbox" v-model="posttypeEdit.post_type_args.has_archive" class="form-control"> has_archive</label><br>
									<label><input type="checkbox" v-model="posttypeEdit.post_type_args.can_export" class="form-control"> can_export</label><br>
								</div>
								<div class="col-xs-6 form-group">
									<label>taxonomies</label>
									<div>
										<div v-for="taxo in taxonomies">
											<label>
												<input type="checkbox" v-model="posttypeEdit.post_type_args.taxonomies" :value="taxo" class="form-control">
												{{ taxo }} &nbsp; &nbsp; 
											</label>
										</div>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-xs-6 form-group">
									<label>capability_type</label>
									<input type="text" v-model="posttypeEdit.post_type_args.capability_type" class="form-control">
								</div>
								<div class="col-xs-6 form-group">
									<label>capabilities</label>
									<input type="text" v-model="posttypeEdit.post_type_args.capabilities" class="form-control">
								</div>
								<div class="col-xs-6 form-group">
									<label>map_meta_cap</label>
									<input type="text" v-model="posttypeEdit.post_type_args.map_meta_cap" class="form-control">
								</div>
								<div class="col-xs-6 form-group">
									<label>hierarchical</label>
									<input type="text" v-model="posttypeEdit.post_type_args.hierarchical" class="form-control">
								</div>
								<div class="col-xs-6 form-group">
									<label>supports</label>
									['thumbnail']
									<!-- <input type="text" v-model="posttypeEdit.post_type_args.supports" class="form-control"> -->
								</div>
								<div class="col-xs-6 form-group">
									<label>register_meta_box_cb</label>
									<input type="text" v-model="posttypeEdit.post_type_args.register_meta_box_cb" class="form-control">
								</div>
								<div class="col-xs-6 form-group">
									<label>rewrite</label>
									<input type="text" v-model="posttypeEdit.post_type_args.rewrite" class="form-control">
								</div>
								<div class="col-xs-6 form-group">
									<label>query_var</label>
									<input type="text" v-model="posttypeEdit.post_type_args.query_var" class="form-control">
								</div>
								<div class="col-xs-6 form-group">
									<label>delete_with_user</label>
									<input type="text" v-model="posttypeEdit.post_type_args.delete_with_user" class="form-control">
								</div>
							</div>
						</div>

						<!-- Tab campos -->
						<input type="radio" name="posttype-tab" id="posttype-tab-campos">
						<label for="posttype-tab-campos">Campos</label>
						<div>
							<div class="text-right">
								<a href="javascript:;" class="btn btn-xs btn-primary" @click="_posttypeFieldgroupAdd(posttypeEdit);">
									<i class="fa fa-plus"></i> Add grupo
								</a>
							</div>
							<hr>
							<div class="panel panel-default" v-for="group in posttypeEdit.posttype_fieldgroups">
								<div class="panel-heading">{{ group.name || 'Grupo' }}</div>
								<div class="panel-body">
									<input type="text" v-model="group.name" class="form-control">
									<pre>{{ group }}</pre>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>

			<div class="panel-footer text-right">
				<button type="button" class="btn btn-default popup-close">Fechar</button>
				<button type="button" class="btn btn-primary" @click="_posttypeUpdate();">Salvar</button>
			</div>
		</div>
	</div>
	<!-- edit posttype -->

	<!-- <pre>{{ $data|json }}</pre> -->
</div>


<link rel="stylesheet" href="<?php echo plugins_url('520-master'); ?>/assets/bs3-ui.css">

<?php $Posttypes = new Posttypes\Posttypes(); ?>
<script>
var app = new Vue({
	el: "#app",
	data: {
		loading: false,
		error: [],
		taxonomies: <?php echo json_encode( $Posttypes->taxonomies() ); ?>,
		posttypeNew: {post_type:"", singular:"", plural:""},
		posttypeEdit: null,
		posttypes: <?php echo json_encode( $Posttypes->search() ); ?>,
		dashicons: <?php echo json_encode( $Posttypes->dashicons() ); ?>,
	},
	methods: {
		_posttypeSearch: function() {
			var app=this, $=jQuery;
			$.post("<?php echo admin_url('admin-ajax.php?action=520&call=Posttypes.Posttypes.search'); ?>", function(response) {
				app.posttypes = response.success;
			}, "json");
		},

		_posttypeCreateDefault: function() {
			var app=this, $=jQuery;
			$.post("<?php echo admin_url('admin-ajax.php?action=520&call=Posttypes.Posttypes.add'); ?>", app.posttypeNew, function(response) {
				app.error = response.error;
				if (response.success) {
					$("#modal-new-posttype").fadeOut(200);
					app._posttypeSearch();
				}
			}, "json");
		},

		_posttypeRemove: function(post) {
			if (! confirm("Confirmar ação?")) return false;
			var app=this, $=jQuery;
			var index = app.posttypes.indexOf(post);
			app.posttypes.splice(index, 1);
			app._posttypeUpdate();
		},

		_posttypeEdit: function(post) {
			var app=this, $=jQuery;
			app.posttypeEdit = post;
			$("#modal-posttype").fadeIn(200);
		},

		_posttypeUpdate: function() {
			var app=this, $=jQuery;
			var post = {"posttypes":app.posttypes};
			$.post("<?php echo admin_url('admin-ajax.php?action=520&call=Posttypes.Posttypes.posttypeUpdate'); ?>", post, function(response) {
				app.error = response.error;
				if (response.success) {
					$("#modal-posttype").fadeOut(200);
					app.posttypes = response.success;
				}
			}, "json");
		},


		_posttypeFieldgroupAdd: function(posttype) {
			var app=this, $=jQuery;
			posttype.posttype_fieldgroups.push({
				name: null,
				fields: [],
			});
		},
	},
	mounted: function() {},
});
</script>