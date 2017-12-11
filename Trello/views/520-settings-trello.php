<div id="app-trello">
	
	<div class="alert" style="background:#eee;">
		<p><a href="htp://trello.com" target="_blank">Trello</a> é um gerenciador de projetos amplo, e com funcionalidades que atendem a qualquer tipo de trabalho.</p>
		<p>Este módulo trabalha com a API do Trello para que o cliente possa ter uma visão do andamento do projeto.</p>
	</div>

	<div class="row">
		<div class="col-sm-4">
			<div class="form-group">
				<label>API Key</label>
				<input type="text" name="trello_apikey" value="<?php echo cdz_option('trello_apikey'); ?>" class="form-control">
			</div>

			<div class="form-group">
				<label>Token</label>
				<input type="text" name="trello_token" value="<?php echo cdz_option('trello_token'); ?>" class="form-control">
			</div>

			<ol>
				<li><a href="https://trello.com/app-key" target="_blank">Clique aqui</a> e faça login se necessário para pegar sua API Key;</li>
				<li>Informe-a no campo "API Key" e clique em "salvar";</li>

				<?php if ($trello_apikey = cdz_option('trello_apikey')): ?>
				<li>Acesse <a href="https://trello.com/1/authorize?expiration=never&scope=read,write,account&response_type=token&name=Server%20Token&key=<?php echo $trello_apikey; ?>" target="_blank">este link</a> para gerar o token</li>
				<li>Copie o token informado na URL acima, cole em "Token" e depois em "salvar".</li>
				<?php endif; ?>
			</ol>
		</div>

		<div class="col-sm-8">
			<div class="form-group">
				<label>Board ID</label>
				<div class="input-group">
					<input type="text" v-model="boardId" class="form-control" placeholder="Board ID">
					<div class="input-group-btn">
						<button type="button" class="btn btn-default" @click="_loadBoards();">
							<i class="fa fa-fw fa-spin fa-spinner" v-if="loading"></i>
							<span v-else>Ok</span>
						</button>
					</div>
				</div>
			</div>
			<br>

			<div v-if="board.id">
				<div class="panel panel-default">
					<div class="panel-heading">{{ board.name }}</div>
					<div class="panel-body">

						<div class="panel panel-default" v-for="card in cards">
							<div class="panel-heading">{{ card.name }}</div>
							<div class="panel-body">
								<div style="white-space:pre;">{{ card.desc }}</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- <pre>{{ $data }}</pre> -->
</div>

<script>
var app = new Vue({
	el: "#app-trello",
	data: {
		loading: false,
		boardId: "",
		board: {},
		cards: [],
	},
	methods: {
		_trello: function(trello_path, data, call) {
			var $=jQuery;
			data = typeof data=="object"? data: {};
			call = typeof call=="function"? call: function() {};
			data['key'] = "<?php echo cdz_option('trello_apikey'); ?>";
			data['token'] = "<?php echo cdz_option('trello_token'); ?>";
			$.get("https://api.trello.com/1"+trello_path, data, call);
		},

		_loadBoards: function() {
			var $=jQuery, app=this;
			app.loading = true;
			app._trello("/boards/"+app.boardId, {}, function(response) {
				app.board = response;
				app._trello("/boards/"+app.boardId+"/cards", {}, function(response) {
					app.loading = false;
					app.cards = response;
				});
			});
		},
	},
});
</script>