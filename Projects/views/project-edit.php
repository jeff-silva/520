<?php
// update_post_meta($post->ID, 'meta_logins', '');
cdz_header();
wp_enqueue_media();
$post = new \Cdz\Projects\Project($post);
$post->tasks();
$post->logins();

?>

<br>
<div id="app-520-project2">
	<div class="panel panel-default" v-if="infos">
		<div class="panel-body">
			<div v-for="info in infos">
				<strong>{{ info.name }}</strong>

				<span v-if="info.type=='$'">{{ info.value }}</span>

				<div class="progress" style="margin:0;" v-if="info.type=='%'">
					<div class="progress-bar progress-bar-striped active" :style="{width:info.value+'%'}">
						{{ info.value }}% Complete
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">Logins</div>
		<div class="panel-body">
			<div class="text-right">
				<a href="javascript:;" class="btn btn-xs btn-default" @click="_loginAdd();"><i class="fa fa-fw fa-plus"></i></a>
			</div><br>

			<ul class="list-group">
				<li class="list-group-item" v-for="(login, i) in post.meta_logins">
					<div class="pull-right">
						<a href="javascript:;" class="fa fa-fw fa-pencil" :data-popup="'.popup-edit-'+i"></a>
						<a href="javascript:;" class="fa fa-fw fa-remove" @click="_loginRemove(login);"></a>
					</div>

					<a :href="login.url" target="_blank">
						{{ login.name }} <br>
						<small>{{ login.url }}</small>
					</a>

					<div :class="'popup popup-edit-'+i">
						<div class="panel panel-default" style="width:400px;">
							<div class="panel-heading">Editar</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-xs-12 form-group">
										<label>Name</label>
										<input type="text" v-model="login.name" placeholder="login.name" class="form-control">
									</div>
									<div class="col-xs-12 form-group">
										<label>Host</label>
										<input type="text" v-model="login.host" placeholder="login.host" class="form-control">
									</div>
									<div class="col-xs-6 form-group">
										<label>User</label>
										<input type="text" v-model="login.user" placeholder="login.user" class="form-control">
									</div>
									<div class="col-xs-6 form-group">
										<label>Pass</label>
										<input type="text" v-model="login.pass" placeholder="login.pass" class="form-control">
									</div>
									<div class="col-xs-6 form-group">
										<label>Port</label>
										<input type="text" v-model="login.port" placeholder="login.port" class="form-control">
									</div>
									<div class="col-xs-6 form-group">
										<label>Type</label>
										<select v-model="login.type" class="form-control">
											<option value="">Selecione</option>
											<option :value="lt.id" v-for="lt in loginTypes">{{ lt.name }}</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</li>
			</ul>
		</div>
	</div>


	<div class="panel panel-default">
		<div class="panel-heading">Tasks</div>
		<div class="panel-body">
			<ul class="list-grou">
				<li class="list-group-item" style="padding:0;">
					<a href="javascript:;" class="btn btn-lg btn-block btn-primary" style="margin:0;" @click="_taskAdd();">Add task</a>
				</li>
				<li class="list-group-item text-center text-muted" v-if="post.meta_tasks.length==0">
					Nenhuma task criada
				</li>
				<li class="list-group-item" v-for="(task, i) in post.meta_tasks">
					<div class="pull-right">
						<a href="javascript:;" class="fa fa-fw fa-pencil" :data-popup="'.popup-edit-task-'+i"></a>
					</div>
					<input type="checkbox" v-model="task.closed" true-value="1" false-value="0">
					<strong>{{ task.title }}</strong><br>
					<small v-if="task.date_start || task.date_final">
						{{ task.date_start }} - {{ task.date_final }}
					</small>

					<div :class="'popup popup-edit-task-'+i">
						<div class="panel panel-default">
							<div class="panel-body">
								<div class="row">
									<div class="col-xs-6 form-group">
										<label>Title</label>
										<input type="text" v-model="task.title" class="form-control">
									</div>
									<div class="col-xs-6 form-group">
										<label>Status</label>
										<input type="text" v-model="task.status" class="form-control">
									</div>
									<div class="col-xs-6 form-group">
										<label>Date start</label>
										<input type="text" v-model="task.date_start" class="form-control" data-flatpickr="{}">
									</div>
									<div class="col-xs-6 form-group">
										<label>Date final</label>
										<input type="text" v-model="task.date_final" class="form-control" data-flatpickr="{}">
									</div>
									<div class="col-xs-6 form-group">
										<label>Budget</label>
										<input type="text" v-model="task.budget" class="form-control">
									</div>
									<div class="clearfix"></div>
									<div class="col-xs-12 form-group">
										<label>Description</label>
										<textarea class="form-control" v-model="task.description"></textarea>
									</div>
								</div>
							</div>
						</div>
					</div>
				</li>
			</ul>
		</div>
	</div>
	
	<!-- <pre>{{ $data }}</pre> -->
	<textarea name="520-project-data" style="display:none;">{{ post }}</textarea>
</div>
<script>
var app = new Vue({
	el: "#app-520-project2",
	data: {
		loading: null,
		ajax: "<?php echo site_url('/wp-admin/admin-ajax.php'); ?>",
		post: <?php echo json_encode($post); ?>,
		loginTypes: <?php echo json_encode($post->loginTypes()); ?>,
		infos: null,
	},
	methods: {
		_ajax: function(cdz, params, callback, method) {
			var app=this, $=jQuery;
			params.cdz = cdz;
			method = method||"get";
			callback = typeof callback=="function"? callback: function() {};

			app.loading = true;
			$.ajax({
				url: app.ajax,
				data: params,
				dataType: "json",
				method: method,
				success: function(response) {
					console.log(response);
					app.loading = false;
					if (response.success) {
						for(var i in response.success) {
							console.log(i, response.success[i]);
							Vue.set(app, i, response.success[i]);
						}
					}
					callback.call(app, response);
				},
			});
		},
		_save: function(callback) {
			var app=this, $=jQuery;
			app._ajax("Cdz.Projects.Project.save", {post:app.post}, callback, "post");
		},
		_loginAdd: function() {
			var app=this, $=jQuery;
			app.post.meta_logins.push({id:"", name:"", host:"", user:"", pass:"", port:"", type:""});
		},
		_loginRemove: function(login) {
			if (!confirm("Deseja deletar este login?")) return false;
			var app=this, $=jQuery;
			var index = app.post.meta_logins.indexOf(login);
			app.post.meta_logins.splice(index, 1);
		},
		_taskAdd: function() {
			var app=this, $=jQuery;
			app.post.meta_tasks.push({id:"", title:"", description:"", status:"", date_start:"", date_final:"", closed:"0"});
		},
		_infos: function() {
			var app=this, $=jQuery;
			app._ajax("Cdz.Projects.Project.infos", {post:app.post.ID});
		},
	},
	mounted: function() {
		var app=this, $=jQuery;
		app._infos();
	},
});
</script>



<?php /* ?>
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
<?php */ ?>
