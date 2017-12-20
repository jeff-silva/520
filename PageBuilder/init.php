<?php


function cdz_contentbuilder_snippets($callback=null) {
	global $cdz_contentbuilder_snippets;
	$cdz_contentbuilder_snippets = is_array($cdz_contentbuilder_snippets)? $cdz_contentbuilder_snippets: array();
	if (is_callable($callback)) {
		$cdz_contentbuilder_snippets[] = $callback;
	}
	return $cdz_contentbuilder_snippets;
}


cdz_contentbuilder_snippets(function() { ?>
<div data-thumb="http://via.placeholder.com/260x150?text=Panel">
	<div class="container">
	    <div class="panel panel-default">
	    	<div class="panel-heading"><p>Title</p></div>
	    	<div class="panel-body">
	    		<p>Basic panel example</p>
	    	</div>
	    </div>
	</div>
</div>
<?php });




add_action('init', function() {
	if (isset($_GET['520-contentbuilder-snippets'])) {
		foreach(cdz_contentbuilder_snippets() as $call) {
			call_user_func($call);
		}
		die;
	}


	if (isset($_GET['520-contentbuilder-filepicker'])) {
		die('filepicker');
	}


	if (isset($_GET['pagebuilder_save'])) {
		$save = $_GET;
		$save['success'] = wp_update_post($save);
		die(json_encode($save));
	}
});




add_filter('the_content', function($content) {
	global $post;
	if (! current_user_can('administrator')) return $content;

	ob_start(); ?>
	
	<div class="pagebuilder-actions"></div>
	<div id="editor-area">
		<?php echo $content; ?>
	</div>
	<div class="pagebuilder-actions"></div>
	<div class="pagebuilder-editor">
		Lol
	</div>

	<div class="pagebuilder-widget" title="Panel">
		<div class="panel panel-default">
			<div class="panel-heading"><p>Title</p></div>
			<div class="panel-body">
				<p>Basic panel example</p>
			</div>
		</div>
	</div>

	<div class="pagebuilder-widget" title="jumbotron">
		<div class="jumbotron">
			<h2>Jumbotron</h2>
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque nunc est, tempus et venenatis id, ornare sed lorem.Curabitur sed ipsum et odio interdum pharetra nec sed libero. Sed velit massa, consectetur in mollis eget, elementum eu diam.</p>
			<a class="btn btn-primary btn-lg">Learn more</a>
		</div>
	</div>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.6.0/Sortable.min.js"></script>
	<script>
	jQuery(document).ready(function($) {
		Sortable.create(document.getElementById("editor-area"), {
			animation: 150,
			handle: ".pagebuilder-section-handle",
		});

		var _appendActions = function() {
			$(".pagebuilder-widget").each(function(i) {
				var title = $(this).attr("title")||("Widget "+(i+1));
				var html = $(this).html();
				var $action = $('<a href="javascript:;">'+ title +'</a>');
				$(".pagebuilder-actions").append($action);
				$action.on("click", function() {
					var $wrapper = $('#editor-area');
					$section = $('<div class="pagebuilder-section"><div class="pagebuilder-section-handle">::</div><div class="pagebuilder-section-content container">'+html+'</div></div>');
					$wrapper.append($section);
					$section.find("p, a, h1, h2, h3, h4, h5, h6").attr("contenteditable", "true");
					$section.on("click", function(ev) {
						$(".pagebuilder-section-selected").removeClass("pagebuilder-section-selected");
						$(this).addClass("pagebuilder-section-selected");
						var $selected = $(".pagebuilder-section-selected");
						
						var $editor = $(".pagebuilder-editor");
						$editor.html('<br /><br /><br />');

						$editor.append('<div><a href="javascript:;" class="pagebuilder-toggle-container">Toggle container</a></div>');
						$(".pagebuilder-toggle-container").on("click", function() {
							$selected.find(".pagebuilder-section-content").toggleClass("container");
						});

						var backgroundColor = $selected.css("backgroundColor");
						$editor.append('<div>Background color <br /><input type="text" value="'+backgroundColor+'" class="pagebuilder-background-color" /></div>');
						$(".pagebuilder-background-color").on("keyup", function() {
							$selected.css("background-color", this.value);
						});

						var elStyle = $(ev.target).attr("style")||"";
						$editor.append('<div>Element style <br /><input type="text" value="'+elStyle+'" class="pagebuilder-el-style" /></div>');
						$(".pagebuilder-el-style").on("keyup", function() {
							$(ev.target).attr("style", this.value);
						});
					});
					$section.trigger("click");
				});
			});
		};


		$(window).on("keyup", function(ev) {
			if(ev.keyCode==46) {
				if (confirm("Deseja deletar o elemento selecionado?")) {
					$(".pagebuilder-section-selected").remove();
				}
			}
		});

		$(".pagebuilder-actions").append('<a href="javascript:;" style="display:block;" class="pagebuilder-save">Salvar</a>');
		$(".pagebuilder-save").on("click", function() {
			var send = <?php echo json_encode($post); ?>;
			send.post_content = $("#editor-area").html();
			send.pagebuilder_save = $("#editor-area").html();
			$.get("<?php echo get_site_url(); ?>", send, function(response) {
				if (response.success) alert("Dados salvos");
			}, "json");
		});

		_appendActions.call();
	});
	</script>

	<style>
	.pagebuilder-section {border:dotted 1px #ddd}
	.pagebuilder-section-selected {border-color:blue;}
	.pagebuilder-section-handle {position:absolute; display:inline; float:left; cursor:pointer; background:#eee; padding:5px; color:#222; z-index:9; opacity:.5;}
	.pagebuilder-section-content {}
	.pagebuilder-widget {display:none;}
	.pagebuilder-actions {text-align:center;}
	.pagebuilder-actions a {display:inline-block; padding:3px 8px;}
	.pagebuilder-editor {position:fixed; top:0; right:0; width:300px; height:100%; background:#eee;}
	</style>

	<?php
	return ob_get_clean();
});


/*<script src="<?php echo plugin_dir_url(__FILE__); ?>assets/contentbuilder/jquery-ui.min.js"></script>
<script src="<?php echo plugin_dir_url(__FILE__); ?>assets/contentbuilder/contentbuilder.js"></script>
<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__); ?>assets/contentbuilder/contentbuilder.css">
<script type="text/javascript">
jQuery(document).ready(function ($) {
    $("#contentarea").contentbuilder({
        zoom: 1,
        snippetOpen: true,
        toolbar: 'left',
		snippetFile: '<?php echo get_site_url(); ?>?520-contentbuilder-snippets=1',
		imageselect: '<?php echo get_site_url(); ?>?520-contentbuilder-filepicker=1',
		fileselect: '<?php echo get_site_url(); ?>?520-contentbuilder-filepicker=1',
    });
});

function _getHtml() {
    var sHTML = $('#contentarea').data('contentbuilder').viewHtml();
    //alert(sHTML);
}
</script>*/