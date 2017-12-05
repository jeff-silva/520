<div class="wrap">
<h1>Post types</h1>


<div id="app">

	<div class="text-right">
		<a class="btn btn-xs btn-primary" data-toggle="modal" href='#modal-new-posttype'>
			<i class="fa fa-fw fa-plus"></i> Novo post type
		</a>
	</div>
	<br>
	
	<!-- new posttype -->
	<div class="modal fade" id="modal-new-posttype">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Novo post type</h4>
				</div>
				<div class="modal-body">
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
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					<button type="button" class="btn btn-primary" @click="_posttypeCreateDefault();">Criar</button>
				</div>
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
	<div class="modal fade" id="modal-posttype">
		<div class="modal-dialog">
			<div class="modal-content" v-if="posttypeEdit">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Editar post type</h4>
				</div>
				<div class="modal-body">
					<div class="alert alert-danger" v-if="error.length>0">
						<a href="javascript:;" class="pull-right" @click="error=[];"><i class="fa fa-remove"></i></a>
						<div v-for="err in error">{{ err }}</div>
					</div><br>

					<div role="tabpanel">
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li role="presentation" class="active">
								<a href="#modal-posttype-basic" role="tab" data-toggle="tab">Basic</a>
							</li>
							<li role="presentation">
								<a href="#modal-posttype-advanced" role="tab" data-toggle="tab">Advanced</a>
							</li>
							<li role="presentation">
								<a href="#modal-posttype-info" role="tab" data-toggle="tab">Info</a>
							</li>
							<li role="presentation">
								<a href="#modal-posttype-fieldgroups" role="tab" data-toggle="tab">Campos</a>
							</li>
						</ul>
						<br>
					
						<!-- Tab panes -->
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active" id="modal-posttype-basic">
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

							<div role="tabpanel" class="tab-pane" id="modal-posttype-advanced">
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

							<div role="tabpanel" class="tab-pane" id="modal-posttype-info">
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
										<input type="text" v-model="posttypeEdit.post_type_args.supports" class="form-control">
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

							<div role="tabpanel" class="tab-pane" id="modal-posttype-fieldgroups">
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
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
					<button type="button" class="btn btn-primary" @click="_posttypeUpdate();">Salvar</button>
				</div>
			</div>
		</div>
	</div>
	<!-- edit posttype -->

	<!-- <pre>{{ $data|json }}</pre> -->
</div>


<link rel="stylesheet" href="<?php echo plugins_url('520-master'); ?>/assets/bs3-ui.css">

<script>
var app = new Vue({
	el: "#app",
	data: {
		loading: false,
		error: [],
		taxonomies: <?php echo json_encode( (new Posttypes\Posttypes())->taxonomies() ); ?>,
		posttypeNew: {post_type:"", singular:"", plural:""},
		posttypeEdit: null,
		posttypes: <?php echo json_encode( (new Posttypes\Posttypes())->search() ); ?>,
		dashicons: <?php echo json_encode( (new Posttypes\Posttypes())->dashicons() ); ?>,
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
					$("#modal-new-posttype").modal('hide');
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
			$("#modal-posttype").modal('show');
		},

		_posttypeUpdate: function() {
			var app=this, $=jQuery;
			var post = {"posttypes":app.posttypes};
			$.post("<?php echo admin_url('admin-ajax.php?action=520&call=Posttypes.Posttypes.posttypeUpdate'); ?>", post, function(response) {
				app.error = response.error;
				if (response.success) {
					$("#modal-posttype").modal('hide');
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