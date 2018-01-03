<?php

add_action('init', function() {
	if (isset($_GET['520-settings-database-data'])) {
		unset($_GET['520-settings-database-data']);
		global $wpdb;

		$params = array_merge(array(
			'table_name' => false,
			'perpage' => 25,
			'page' => 1,
			'pages' => 1,
			'total' => 1,
			'order' => false,
			'table_data' => array(),
		), $_GET);

		$results = $wpdb->get_results($sql = "select * from {$params['table_name']} order by {$params['order']} {$params['orderby']}");
		$params['total'] = sizeof($results);
		$params['pages'] = ceil($params['total']/$params['perpage']);
		$params['page'] = max($params['page'], 1);
		$params['page'] = min($params['page'], $params['pages']);
		$params['table_data'] = array_slice($results, (($params['page']-1) * $params['perpage']), $params['perpage']);

		// dd($sql, $params); die;
		echo json_encode($params); die;
	}
});


cdz_settings_tab('Database', '520-settings-database', function() {
	global $wpdb;

	$tables = DB_NAME;
	$tables = $wpdb->get_results("select TABLE_NAME from information_schema.TABLES where TABLE_SCHEMA='{$tables}' and TABLE_NAME like '{$wpdb->prefix}%' ");
	$tables = array_map(function($row) {
		global $wpdb;
		$table = $row->TABLE_NAME;
		$columns = $wpdb->get_results("show columns from {$table} ");
		return array(
			'table_name' => $table,
			'query' => array(
				'page' => 1,
				'order' => $columns[0]->Field,
				'orderby' => 'desc',
			),
			'pagination_pages' => 1,
			'pagination_total' => 1,
			'table_fields' => $columns,
			'table_data' => array(),
		);
	}, $tables);


	?>
	<div id="database">
		<div class="row">
			<div class="col-xs-3" style="max-height:400px; overflow:auto;">
				<ul class="list-group">
					<li class="list-group-item" v-for="table in tables" :class="{active:(table.table_name==tableCurrent.table_name)}">
						<a href="javascript:;" @click="_tableSelect(table);">{{ table.table_name }}</a>
					</li>
				</ul>
			</div>
			<div class="col-xs-9" style="max-height:400px; overflow:auto;">
				<table class="table table-hover table-striped table-bordered" v-if="tableCurrent" :class="'loading-'+loading">
					<thead>
						<tr>
							<th :colspan="tableCurrent.table_fields.length">
								<div class="text-center" style="text-transform:uppercase;">{{ tableCurrent.table_name }}</div>
							</th>
						</tr>
						<tr>
							<th v-for="field in tableCurrent.table_fields">
								<a href="javascript:;" @click="_fieldOrder(field.Field);">
									<i class="pull-right fa fa-fw fa-sort-desc" v-if="tableCurrent.query.order==field.Field && tableCurrent.query.orderby=='asc'"></i>
									<i class="pull-right fa fa-fw fa-sort-asc"  v-if="tableCurrent.query.order==field.Field && tableCurrent.query.orderby=='desc'"></i>
									{{ field.Field }}
								</a>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr v-if="tableCurrent.table_data.length==0">
							<td class="text-center text-muted" :colspan="tableCurrent.table_fields.length">
								<span v-if="loading">Carregando</span>
								<span v-else="loading">Nenhum dado encontrado</span>
							</td>
						</tr>

						<tr v-for="row in tableCurrent.table_data">
							<td v-for="(value, field) in row">
								{{ value }}
							</td>
						</tr>
					</tbody>
				</table>

				<div class="text-center" v-if="tableCurrent && tableCurrent.pagination_pages>1">
					<select v-model="tableCurrent.query.page" class="form-control" @change="_tableData();">
						<option value="">Selecione uma página</option>
						<option :value="p" v-for="p in (1, tableCurrent.pagination_total)">{{ p }}</option>
					</select>
				</div>
			</div>
		</div>
		<pre>{{ $data }}</pre>
	</div>

	<script>
	var app = new Vue({
		el: "#database",
		data: {
			loading: false,
			tableCurrent: false,
			tables: <?php echo json_encode($tables); ?>
		},
		methods: {
			_tableSelect: function(dbtable) {
				var app=this, $=jQuery;
				for(var i in app.tables) {
					if (app.tables[i]==dbtable || app.tables[i].table_name==dbtable) {
						var table = app.tables[i];
						Vue.set(app, "tableCurrent", table);
						break;
					}
				}
				app._tableData();
			},

			_fieldOrder: function(fieldname) {
				var app=this, $=jQuery;
				if (! app.tableCurrent) return false;
				var table = app.tableCurrent;
				table.query.order = fieldname;
				table.query.orderby = table.query.orderby=="asc"? "desc": "asc";
				app._tableData();
			},

			_tableData: function() {
				var app=this, $=jQuery;
				if (! app.tableCurrent) return false;
				var table = app.tableCurrent;

				var params = {"520-settings-database-data":true};
				params['table_name'] = table.table_name;
				params['order'] = table.query.order;
				params['orderby'] = table.query.orderby;
				params['page'] = table.query.page;

				app.loading = true;
				$.get("<?php echo admin_url(); ?>", params, function(response) {
					app.loading = false;
					table.table_data = response.table_data;
					table.pagination_pages = response.pages;
					table.pagination_total = response.total;
					Vue.set(app, "tableCurrent", table);
				}, "json");
			},
		},
	});
	</script>

	<style>
	.loading-true {opacity:.5;}
	</style>
	<?php
});
