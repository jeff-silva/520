<?php


// Register posttypes
add_action('init', function() {
	$posttypes = new \Cdz\Posttypes\Posttypes();
	$posttypes = $posttypes->posttypeSearch();
	foreach($posttypes as $posttype) {
		//$posttype['posttype_data']['menu_icon'] = 'aaa';
		//dd($posttype); die;
		register_post_type($posttype['posttype_slug'], $posttype['posttype_data']);
	}
});


add_action('520-settings', function() {
	cdz_tab('Post types', function() {
		include __DIR__ . '/views/posttypes.php';
	});
});


add_action('admin_footer', function() {
	$posttypes = new \Cdz\Posttypes\Posttypes();
	$posttypes = $posttypes->posttypeSearch();
	?>
	<script>
	jQuery(document).ready(function($) {
		<?php foreach($posttypes as $posttype): ?>
		$(".menu-icon-<?php echo $posttype['posttype_slug']; ?> .wp-menu-image").html('<i class="<?php echo $posttype['posttype_data']['menu_icon']; ?>"></i>');
		<?php endforeach; ?>
	});
	</script>
	<style>a .wp-menu-image i {font-size:17px; margin:8px 0;}</style>
<?php });
