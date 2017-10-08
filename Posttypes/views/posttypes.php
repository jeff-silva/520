<div class="wrap">
<h1>Post types</h1>

<div id="app">
	<div class="row">
		<div class="col-xs-4 form-group">
			<label>Post type</label>
			<input type="text" v-model="posttypeNew.post_type" class="form-control">
		</div>
		<div class="col-xs-4 form-group">
			<label>Singular</label>
			<input type="text" v-model="posttypeNew.singular" class="form-control">
		</div>
		<div class="col-xs-4 form-group">
			<label>Plural</label>
			<input type="text" v-model="posttypeNew.plural" class="form-control"><br>
			<button type="button" class="btn btn-default" @click="_posttypeCreateDefault();">Go</button>
		</div>
	</div>
	<pre>{{ $data|json }}</pre>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.4.2/vue.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/3.3.7/flatly/bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<script>
var app = new Vue({
	el: "#app",
	data: {
		loading: false,
		posttypeNew: {post_type:"", singular:"", plural:""},
		posttypes: [],
	},
	methods: {
		_posttypeCreateDefault: function() {
			var app=this, $=jQuery;
			$.post("<?php echo admin_url('admin-ajax.php?action=520&call=Posttypes.Posttypes.add'); ?>", app.posttypeNew, function(response) {
				app.posttypes.push(response.success);
			}, "json");
		},
	},
	mounted: function() {
		var app=this, $=jQuery;
		$.get("<?php echo admin_url('admin-ajax.php?action=520&call=Posttypes.Posttypes.search'); ?>", function(response) {
			app.posttypes = response.success;
		}, "json");
	},
});
</script>