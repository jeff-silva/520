<br>
<div id="app">
	<div class="row">
		<div class="col-sm-6">
			<strong>Orçamento:</strong> R${{ project_budget | currency }}
			&nbsp; | &nbsp;
			<strong>Recebido:</strong> R${{ project_budget_paid | currency }}
			&nbsp; | &nbsp;
			<strong>Faltam:</strong> R${{ project_budget_miss | currency }}
			<br>

			<?php $statuses = (new Projects\Projects())->statuses();
			foreach($statuses as $status): ?>
			<label>
				<input type="checkbox" class="form-control" v-model="search.statuses" value="<?php echo $status['id']; ?>" @change="_projectsList();">
				<?php echo $status['name']; ?>
			</label>
			<?php endforeach; ?>
		</div>
		<div class="col-sm-6 text-right">
			<a href="javascript:;" class="btn btn-xs btn-default" @click="_projectsList();"><i class="fa fa-fw fa-refresh" :class="{'fa-spin':loading}"></i></a>
		</div>
	</div>

	<hr>

	<div class="row">
		<div v-for="(row, i) in projects">
			<div class="col-xs-12 col-sm-6 col-md-4" data-live-filter="">
				<div class="panel panel-default">
					<div class="panel-heading">
						<div class="pull-right">
						</div>
						<a :href="'post.php?post='+row.ID+'&action=edit'">{{ row.post_title }}</a>
					</div>
					<div class="panel-body">
						<div class="row">
							<!-- <div class="col-xs-12 form-group">
								<label>Status</label>
								<select v-model="row.project_status" class="form-control">
									<option value=""></option>
									<option :value="st.id" v-for="st in statuses">{{ st.name }}</option>
								</select>
							</div> -->

							<div class="col-xs-12">
								<div class="pull-right">{{ row.project_status_name }}</div>
								<strong><small>Andamento: {{ row.project_percent }}%</small></strong><br>
								<div v-if="row.project_start && row.project_final">
									<div class="progress" style="margin:0;">
										<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" :style="'width:'+row.project_percent+'%'"></div>
									</div>
									<div class="pull-left"><small class="text-muted">{{ row.project_start }}</small></div>
									<div class="pull-right"><small class="text-muted">{{ row.project_final }}</small></div>
									<div class="clearfix"></div>
								</div>
							</div>
						</div>
						<br>

						<div class="pull-right" v-if="row.project_logins.length>0">
							<div class="btn-group" role="group">
								<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<small>Acessos <span class="caret"></span></small>
								</a>
								<ul class="dropdown-menu pull-right">
									<li v-for="login in row.project_logins">
										<a :href="login.link" target="_blank">{{ login.name }}</a>
									</li>
								</ul>
							</div> &nbsp; 
						</div>

						<div class="pull-right" v-if="row.project_uploads.length>0">
							<div class="btn-group" role="group">
								<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<small>Anexos <span class="caret"></span></small>
								</a>
								<ul class="dropdown-menu pull-right">
									<li v-for="upl in row.project_uploads">
										<a :href="upl.url" target="_blank">{{ upl.title }}</a>
									</li>
								</ul>
							</div> &nbsp; 
						</div>


						<div v-if="row.post_content" class="pull-left">
							<a href="javascript:;" onclick="jQuery(this).closest('.panel-body').find('.target-blank').slideToggle(200);">
								<small>Detalhes <span class="caret"></span></small>
							</a>  &nbsp; 
						</div>

						<div class="clearfix"></div>
						<div class="target-blank pull-left" v-html="row.post_content" style="display:none; white-space:pre-line;"></div>
					</div>
				</div>
			</div>

			<div v-if="(i+1)%3==0" class="col-md-12 visible-md"></div>
			<div v-if="(i+1)%3==0" class="col-lg-12 visible-lg"></div>
		</div>
	</div>
	<pre>{{ $data }}</pre>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.4.4/vue.min.js"></script>
<script>
var app = new Vue({
	el: "#app",
	data: {
		loading: false,
		search: {
			statuses: [],
		},
		project_budget: 0,
		project_budget_paid: 0,
		project_budget_miss: 0,
		projects: [],
		statuses: <?php echo json_encode( (new Projects\Projects())->statuses() ); ?>,
	},
	methods: {
		_projectsList: function() {
			var app=this, $=jQuery;
			app.loading = true;
			$.post("admin-ajax.php?action=520&call=Projects.Projects.listing", app.search, function(response) {
				app.loading = false;
				app.projects = response.success.posts;
				app.project_budget = response.success.project_budget;
				app.project_budget_paid = response.success.project_budget_paid;
				app.project_budget_miss = response.success.project_budget_miss;
			}, "json");
		},
	},

	mounted: function() {
		var app=this, $=jQuery;
		app._projectsList();
		setInterval(app._projectsList, 60000);
	},
});
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/3.3.7/flatly/bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="<?php echo plugins_url('520'); ?>/assets/bs3-ui.css">

<script>
jQuery(document).ready(function($) {
	$("body").on("click", ".target-blank a", function() {
		$(this).attr("target", "_blank");
	});
});
</script>