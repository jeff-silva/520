<?php
$project_budget = get_post_meta($post->ID, 'project_budget', true);
$project_budget_paid = get_post_meta($post->ID, 'project_budget_paid', true);
$project_start = get_post_meta($post->ID, 'project_start', true);
$project_final = get_post_meta($post->ID, 'project_final', true);
$project_text = get_post_meta($post->ID, 'project_text', true);
$project_status = get_post_meta($post->ID, 'project_status', true);
$project_statuses = json_encode(project_statuses());

$project_clients = get_post_meta($post->ID, 'project_clients', true);
$project_clients = $project_clients? $project_clients: '[]';

$project_tasks = get_post_meta($post->ID, 'project_tasks', true);
$project_tasks = $project_tasks? $project_tasks: '[]';

$project_logins = get_post_meta($post->ID, 'project_logins', true);
$project_logins = $project_logins? $project_logins: '[]';

$project_uploads = get_post_meta($post->ID, 'project_uploads', true);
$project_uploads = $project_uploads? $project_uploads: '[]';
?>


<br>
<div id="app_250_project">
	<div role="tabpanel">
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li class="active"><a href="#project-info" data-toggle="tab">Info</a></li>
			<li><a href="#project-logins" data-toggle="tab">Logins</a></li>
			<li><a href="#project-uploads" data-toggle="tab">Anexos</a></li>
		</ul>
		<br>
	
		<div class="tab-content">
			
			<!-- Info -->
			<div role="tabpanel" class="tab-pane active" id="project-info">
				<div class="row">
					<div class="col-xs-6">
						<div class="form-group">
							<label>Orçamento</label>
							<input type="text" v-model="project_budget" name="postmeta[project_budget]" value="<?php echo $project_budget; ?>" class="form-control">
						</div>

						<div class="form-group">
							<label>Valor pago</label>
							<input type="text" v-model="project_budget_paid" name="postmeta[project_budget_paid]" value="<?php echo $project_budget_paid; ?>" class="form-control">
						</div>

						<div class="form-group">
							<label>Data de início</label>
							<input type="text" v-model="project_start" name="postmeta[project_start]" value="<?php echo $project_start; ?>" class="form-control" data-datetime='{}'>
						</div>

						<div class="form-group">
							<label>Data de fim</label>
							<input type="text" v-model="project_final" name="postmeta[project_final]" value="<?php echo $project_final; ?>" class="form-control" data-datetime='{}'>
						</div>
						<div class="form-group">
							<label>Status</label>
							<select name="postmeta[project_status]" v-model="project_status" class="form-control">
								<option value="">Selecione</option>
								<option :value="st.id" v-for="st in project_statuses">{{ st.name }}</option>
							</select>
						</div>
					</div>
					<div class="col-xs-6">
						<div class="form-group">
							<label>Texto</label>
							<textarea name="postmeta[project_text]" class="form-control" style="height:300px;"><?php echo $project_text; ?></textarea>
						</div>
					</div>
				</div>
			</div>


			<!-- Logins -->
			<div role="tabpanel" class="tab-pane" id="project-logins">
				<div class="text-right">
					<button type="button" class="btn btn-primary" @click="_projectLoginAdd();">Novo</button>
				</div>

				<table class="table table-hover">
					<thead>
						<tr>
							<th>Nome</th>
							<th>Host</th>
							<th>User</th>
							<th>Pass</th>
							<th>Port</th>
							<th>Type</th>
							<th>-</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="row in project_logins">
							<td><input type="text" v-model="row.name" class="form-control"></td>
							<td><input type="text" v-model="row.host" class="form-control"></td>
							<td><input type="text" v-model="row.user" class="form-control"></td>
							<td><input type="text" v-model="row.pass" class="form-control"></td>
							<td><input type="text" v-model="row.port" class="form-control"></td>
							<td>
								<select v-model="row.type" class="form-control">
									<option value="">Outro</option>
									<option :value="opt.id" v-for="opt in _projectLoginTypes()">{{ opt.name }}</option>
								</select>
							</td>
							<td><a href="javascript:;" class="btn btn-danger" @click="_projectLoginRemove(row);"><o class="fa fa-fw fa-remove"></o></a></td>
						</tr>
					</tbody>
				</table>
			</div>


			<!-- Logins -->
			<div role="tabpanel" class="tab-pane" id="project-uploads">
				<div class="text-right">
					<button type="button" class="btn btn-primary" @click="_projectUploadsAdd();">
						<i class="fa fa-fw fa-upload"></i> Upload
					</button>
				</div>
				<table class="table table-hover">
					<thead>
						<tr>
							<th>Título</th>
							<th>File</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="row in project_uploads">
							<td><input type="text" v-model="row.title" class="form-control"></td>
							<td>
								<div class="input-group" v-if="row.url">
									<div class="form-control" style="white-space:nowrap; overflow:hiden;">
										<a :href="row.url" :download="row.filename">{{ row.filename }}</a>
									</div>
									<div class="input-group-btn">
										<button type="button" class="btn btn-default" @click="row.url='';">
											<i class="fa fa-fw fa-remove"></i>
										</button>
									</div>
								</div>
								<div class="input-group" v-else>
									<div class="form-control" disabled>Upload</div>
									<div class="input-group-btn">
										<button type="button" class="btn btn-primary" @click="_projectUploadsUpload(row);">
											<i class="fa fa-fw fa-upload"></i>
										</button>
									</div>
								</div>
							</td>
							<td><a href="javascript:;" class="btn btn-danger" @click="_projectUploadRemove(row);"><i class="fa fa-fw fa-remove"></i></a></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<textarea name="postmeta[project_logins]" style="display:none;">{{ project_logins|json }}</textarea>
	<textarea name="postmeta[project_uploads]" style="display:none;">{{ project_uploads|json }}</textarea>
	<!-- <pre>{{ $data }}</pre> -->
</div>




<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/3.3.7/flatly/bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/3.0.7/flatpickr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/3.0.7/flatpickr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.4.2/vue.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<?php wp_enqueue_media(); ?>

<script>
var App520Project = new Vue({
	el: "#app_250_project",
	data: {
		post: <?php echo json_encode($post); ?>,
		project_budget: "<?php echo $project_budget; ?>",
		project_budget_paid: "<?php echo $project_budget_paid; ?>",
		project_start: "<?php echo $project_start; ?>",
		project_final: "<?php echo $project_final; ?>",
		project_status: "<?php echo $project_status; ?>",
		project_statuses: <?php echo $project_statuses; ?>,
		project_clients: <?php echo $project_clients; ?>,
		project_tasks: <?php echo $project_tasks; ?>,
		project_logins: <?php echo $project_logins; ?>,
		project_uploads: <?php echo $project_uploads; ?>,
	},

	methods: {
		_projectLoginAdd: function() {
			var app=this;
			app.project_logins.push({
				name: "",
				host: "",
				user: "",
				pass: "",
				port: "",
				type: "",
			});
		},

		_projectLoginRemove: function(row) {
			if (! confirm("Tem certeza que deseja deletar este dado de login?")) return;
			var index = this.project_logins.indexOf(row);
			this.project_logins.splice(index, 1);
		},

		_projectLoginTypes: function() {
			return [
				{"id":"cpanel", "name":"CPanel"},
				{"id":"ftp", "name":"FTP"},
				{"id":"wordpress", "name":"Wordpress"},
			];
		},

		_projectUploadsAdd: function() {
			var app=this, $=jQuery;
			app.project_uploads.unshift({
				title: "",
				url: "",
				filename: "",
			});
			app._projectUploadsUpload(app.project_uploads[0]);
		},


		_projectUploadsUpload: function(row) {
			var app=this, $=jQuery;
			var image = wp.media({title:'Upload', multiple: false}).open().on('select', function(e) {
				var upload = image.state().get('selection').first();
				upload = upload.toJSON();
				row.title = upload.uploadedToTitle;
				row.url = upload.url;
				row.filename = upload.filename;
			});
		},

		_projectUploadRemove: function(row) {
			if (! confirm("Tem certeza que deseja deletar este anexo?")) return;
			var index = this.project_logins.indexOf(row);
			this.project_uploads.splice(index, 1);
		},
	},

	mounted: function() {
		var $=jQuery;

		$("[data-datetime]").each(function() {
			var opts = $(this).attr("data-datetime");
			try { eval('opts='+opts); } catch(e) { opts={}; }
			opts = $.extend(opts, {
				dateFormat: "Y-m-d H:i:S",
			});
			$(this).flatpickr(opts);
		});
	},
});
</script>

<style>
select.form-control {height:auto!important; padding:10px 8px;}
</style>