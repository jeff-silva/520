<?php


if (! function_exists('dd')) {
	function dd() {
		foreach(func_get_args() as $data) {
			echo '<pre style="font:11px monospace;">'. print_r($data, true) .'</pre>';
		}
	}
}


function helper_show_hooks() {
	$debug_tags = array();
	add_action('all', function ($tag) {
	    global $debug_tags;
	    if (! headers_sent()) return null;
	    if (in_array($tag, $debug_tags)) return;
	    echo "<small style='font:10px monospace; margin:0px 5px;'>{$tag}</small>";
	    $debug_tags[] = $tag;
	});
}


function helper_query($value, $call=null) {
	global $post;

	$value = is_array($value)? $value: json_decode($value, true);
	$value = is_array($value)? $value: array();

	if (isset($value['order'])) {
		list($value['orderby'], $value['order']) = explode(' ', $value['order']);
	}

	if (isset($value['posts_per_page']) AND empty($value['posts_per_page'])) {
		$value['posts_per_page'] = '-1';
	}

	$pages = array();
	$query = new WP_Query($value);
	$index = 0;
	while($query->have_posts()) {
		$query->the_post();
		$pages[] = $post;
		if (is_callable($call)) {
			call_user_func($call, $index, $post);
		}
		$index++;
	} 
	wp_reset_postdata();
	return $pages;
}




function helper_breadcrumbs() {
	global $post;

	$breads = array(
		array(
			'title' => 'Home',
			'url' => get_site_url(),
		),
	);

	if (is_category()) {}

	else if (is_search()) {
		$breads[] = array(
			'title' => "Pesquisa por <q>{$_GET['s']}</q>",
			'url' => 'javascript:;',
		);
	}

	else if (is_attachment()) {}

	else if (is_page()) {
		if ($post->post_parent) {
			$parent_id  = $post->post_parent;
			$breadcrumbs = array();
			while ($parent_id) {
				$page = get_page($parent_id);
				$breadcrumbs[] = array(
					'title' => get_the_title($page->ID),
					'url' => get_permalink($page->ID),
				);
				$parent_id  = $page->post_parent;
			}
			foreach(array_reverse($breadcrumbs) as $b) {
				$breads[] = $b;
			}
		}
		$breads[] = array(
			'title' => get_the_title(),
			'url' => get_the_permalink(),
		);
	}

	else if (is_404()) {
		$breads[] = array(
			'title' => 'Página não encontrada',
			'url' => '',
		);
	}

	else {
		$type = get_post_type_object($post->post_type);
		$breads[] = array(
			'title' => $type->labels->name,
			'url' => 'javascript:;',
		);

		$breads[] = array(
			'title' => $post->post_title,
			'url' => get_the_permalink($post->ID),
		);
	}

	return $breads;
}


function helper_social_share($except=array(), $echo=true) {
	global $post;
	$share_url = urlencode(get_permalink());
	$share_title = urlencode(get_the_title());
	$socials = array(
		'facebook' => array(
			'name' => 'Facebook',
			'icon' => '<i class="fa fa-fw fa-facebook"></i>',
			'url' => "https://www.facebook.com/sharer.php?u={$share_url}",
		),
		'twitter' => array(
			'name' => 'Twitter',
			'icon' => '<i class="fa fa-fw fa-twitter"></i>',
			'url' => "https://twitter.com/intent/tweet?url={$share_url}&text={$share_title}&via={via}&hashtags={hashtags}",
		),
		'google-plus' => array(
			'name' => 'Google+',
			'icon' => '<i class="fa fa-fw fa-google-plus"></i>',
			'url' => "https://plus.google.com/share?url={$share_url}",
		),
		'linkedin' => array(
			'name' => 'Linkedin',
			'icon' => '<i class="fa fa-fw fa-linkedin"></i>',
			'url' => "https://www.linkedin.com/shareArticle?url={$share_url}&title={$share_title}",
		),
		'whatsapp' => array(
			'name' => 'Whatsapp',
			'icon' => '<i class="fa fa-fw fa-whatsapp"></i>',
			'url' => "https://api.whatsapp.com/send?text={$share_url}",
			// 'url' => "whatsapp://send?text={$share_url}",
		),
	);

	foreach($socials as $id=>$social) {
		if (in_array($id, $except)) {
			unset($socials[$id]);
		}
	}

	if ($echo==true) {
		foreach($socials as $social) {
			echo "<li><a href='{$social['url']}' target='_blank'>{$social['icon']} <span>{$social['name']}</span></a></li>";
		}
	}

	return $socials;
}



function helper_ajax_response($success, $error=null) {
	if ($error) {
		$error = is_array($error)? $error: array($error);
		$error = array_filter($error, 'strlen');
		if (!empty($error)) $success=false;
	}

	echo json_encode(array(
		'success' => $success,
		'error' => implode('<br>', $error),
	), (defined('JSON_PRETTY_PRINT')? JSON_PRETTY_PRINT: false)); wp_die();
}


/* Ajax JSON response */
function helper_ajax($action, $call, $nopriv=false) {
	if ($nopriv==false OR $nopriv=='all') add_action("wp_ajax_{$action}", $call);
	if ($nopriv==true  OR $nopriv=='all') add_action("wp_ajax_nopriv_{$action}", $call);
}



function helper_posttypes($save=null) {
	if (is_array($save)) {
		foreach($save as $i=>$data) {
			$data['options']['public'] = !!$data['options']['public'];
			$data['options']['show_ui'] = !!$data['options']['show_ui'];
			$data['options']['show_in_menu'] = !!$data['options']['show_in_menu'];
			$data['options']['show_in_nav_menus'] = !!$data['options']['show_in_nav_menus'];
			$data['options']['publicly_queryable'] = !!$data['options']['publicly_queryable'];
			$data['options']['exclude_from_search'] = !!$data['options']['exclude_from_search'];
			$data['options']['has_archive'] = !!$data['options']['has_archive'];
			$data['options']['query_var'] = !!$data['options']['query_var'];
			$data['options']['can_export'] = !!$data['options']['can_export'];
			$data['options']['rewrite'] = !!$data['options']['rewrite'];
			$save[$i] = $data;
		}
		file_put_contents(__DIR__ . '/posttypes.json', json_encode($save));
	}
	if ($file = realpath(__DIR__ . '/posttypes.json')) {
		$posttypes = json_decode(file_get_contents(__DIR__ . '/posttypes.json'), true);
		$posttypes = is_array($posttypes)? $posttypes: array();
		return $posttypes;
	}
	return array();
}



function helper_posts($query, $callback) {
	global $post;

	if (is_string($query)) {
		parse_str($query, $query);
	}

	$query = new WP_Query($query);

	if ($query->have_posts() AND is_callable($callback)) {
		$index = 0;
		while ($query->have_posts()) {
			$query->the_post();
			call_user_func($callback, $post, $index);
			$index++;
		}
		wp_reset_postdata();
		return $query;
	}

	return false;
}


function helper_post($id, $callback) {
	global $post;

	if (! $id) $page = $post;
	else $page = get_post($id);

	$return = false;
	if (is_callable($callback) AND $page) {
		$return = $page;
		setup_postdata($page);
		call_user_func($callback, $page);
		wp_reset_postdata();
	}
	return $return;
}






// Remove window._wpemojiSettings from HTML
add_action('init', function() {
	// global $wp_filter;
	// print_r(array_keys($wp_filter)); die;

	// all actions related to emojis
	remove_action('admin_print_styles', 'print_emoji_styles');
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
	remove_filter('the_content_feed', 'wp_staticize_emoji');
	remove_filter('comment_text_rss', 'wp_staticize_emoji');

	// filter to remove TinyMCE emojis
	add_filter( 'tiny_mce_plugins', function($plugins) {
		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, array( 'wpemoji' ) );
		}
		return array();
	});
});

