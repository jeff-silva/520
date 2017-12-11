<?php

function helper_snippet_add($class) {
	global $helper_snippets;
	$helper_snippets = isset($helper_snippets)? $helper_snippets: array();
	$helper_snippets[ $class ] = $class;
	return $helper_snippets;
}


function helper_snippets() {
	global $helper_snippets;
	$helper_snippets = isset($helper_snippets)? $helper_snippets: array();
	$return = array();
	foreach($helper_snippets as $i=>$snippet) {
		$snippet = new $snippet();
		$info = $snippet->info();
		$info['title'] = isset($info['title'])? $info['title']: 'No title';
		$return[] = array(
			'title' => $info['title'],
			'instance' => $snippet,
		);
	}
	return $return;
}


class Helper_Snippet
{
	public function info()
	{
		return array(
			'title' => '',
		);
	}


	public function settings()
	{
		return array();
	}

	public function editor()
	{
		echo '<div>none</div>';
	}

	public function render()
	{
		echo '<div>none</div>';
	}
}



class BS3_Panel extends Helper_Snippet
{
	public function info()
	{
		return array(
			'title' => 'BS3 Panel',
			'text' => 'Aaaa',
		);
	}


	public function settings()
	{
		return array(
			'title' => 'Default title',
			'content' => 'Default <strong>content</strong>',
			'btn_close' => 'Close',
			'btn_save' => 'Save changes',
		);
	}


	public function editor($settings) { ?>
	<div class="row">
		<div class="col-xs-6 form-group">
			<label>Title</label>
			<input type="text" name="title" value="<?php echo $settings['title']; ?>" class="form-control">
		</div>

		<div class="col-xs-6 form-group">
			<label>Content</label>
			<input type="text" name="content" value="<?php echo $settings['content']; ?>" class="form-control">
		</div>
	</div>
	<?php }


	public function render($settings) { ?>
	<div class="panel panel-primary">
		<div class="panel-heading"><?php echo $settings['title']; ?></div>
		<div class="panel-body">
			<?php echo $settings['content']; ?>
		</div>
	</div>
	<?php }
}


helper_snippet_add('BS3_Panel');


/* add_action('the_content', function($content) {
	global $post, $helper_snippets;

	$contents = json_decode($post->post_content);
	$contents = is_array($contents)? $contents: array();
	$contents = array(
		array(
			'snippet' => 'BS3_Panel',
			'settings' => array(
				'title' => 'Default title',
				'content' => 'Default <strong>content</strong>',
			),
		),

		array(
			'snippet' => 'BS3_Panel',
			'settings' => array(
				'title' => 'Title 02',
				'content' => 'Aaa bbb ccc ddd',
			),
		),
	);



	ob_start(); ?>
	<div class="snippets-wrapper">
		<?php foreach($contents as $content):
		$snippet = new $content['snippet']();
		$id = rand(0, 99999); ?>

		<!-- Snippet: <?php echo $content['snippet']; ?> -->
		<div class="snippets-wrapper-each" data-snippet-content='<?php echo json_encode($content); ?>'>
			
			<!-- Snippet edit: <?php echo $content['snippet']; ?> -->
			<div class="snippets-wrapper-each-actions">
				<a class="btn btn-primary" data-toggle="modal" href='<?php echo "#modal-snippet-edit-{$id}"; ?>'>Edit</a>
				<div class="modal fade" id="<?php echo "modal-snippet-edit-{$id}"; ?>">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title">Modal title</h4>
							</div>
							<div class="modal-body">
								<?php $snippet->editor($content['settings']); ?>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								<button type="button" class="btn btn-primary">Save changes</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- Snippet edit: <?php echo $content['snippet']; ?> -->

			<?php $snippet->render($content['settings']); ?>
		</div>
		<!-- Snippet: <?php echo $content['snippet']; ?> -->
		<?php endforeach; ?>
	</div>

	<div>
		<?php foreach(helper_snippets() as $snippet): ?>
		<a href="javascript:;"><?php echo $snippet['title']; ?></a>
		<?php endforeach; ?>
	</div>

	<script>
	jQuery(document).ready(function($) {
		var sort = Sortable.create( $(".snippets-wrapper")[0] , {
			animation: 150,
			// handle: ".snippets-wrapper-each-handle",
			onUpdate: function (ev){
				alert('change');
			},
		});
	});
	</script>
	<?php return ob_get_clean(); 
}); */


add_action('wp_enqueue_scripts', function() {
	wp_enqueue_style('bootstrap', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css');
	wp_enqueue_script('jquery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js');
	wp_enqueue_script('bootstrap', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js');
	wp_enqueue_script('sortable', 'https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.6.0/Sortable.min.js');
});


$action = 'helper_snippet';
function helper_test_callback() {
	echo '{"success":true}'; die;
}
add_action("wp_ajax_{$action}", 'helper_test_callback');
add_action("wp_ajax_nopriv_{$action}", 'helper_test_callback');


// the_content?add
// the_content?update
// the_content?remove
