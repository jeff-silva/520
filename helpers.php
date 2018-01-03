<?php

if(! session_id()) session_start();

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




function helper_breadcrumb($callback=null) {
	global $post;

	$callback = is_callable($callback)? $callback: function($breads) { ?>
		<ul class="breadcrumb">
			<?php foreach($breads as $bread): ?>
			<li><a href="<?php echo $bread['url']; ?>"><?php echo $bread['title']; ?></a></li>
			<?php endforeach; ?>
		</ul>
	<?php };

	$breads = array(
		array(
			'title' => 'Home',
			'url' => get_site_url(),
		),
	);

	if (is_category()) {}

	else if (is_search()) {
		$breads[] = array(
			'title' => "Pesquisa</q>",
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
			'url' => home_url("?s=+&post_type={$type->name}"),
		);

		$breads[] = array(
			'title' => $post->post_title,
			'url' => get_the_permalink($post->ID),
		);
	}

	return call_user_func($callback, $breads);
}




function helper_category_tree($taxonomy, $params=array(), $callback=null) {
	$callback = is_callable($callback)? $callback: function($taxonomy, $params, $terms, $callback) { ?>
		<ul class="list-group">
			<?php foreach($terms as $term): ?>
			<li class="list-group-item">
				<a href="<?php echo get_term_link($term); ?>"><?php echo $term->name; ?></a>
				<?php $params['parent'] = $term->term_id;
				helper_category_tree($taxonomy, $params, $callback); ?>
			</li>
			<?php endforeach; ?>
		</ul>
	<?php };

	if (! is_array($params)) parse_str($params, $params);
	$params = array_merge(array(
		'hierarchical' => 1,
		'hide_empty' => 0,
		'parent' => 0,
	), $params);
	$terms = get_terms($taxonomy, $params);
	if (empty($terms)) return false;
	return call_user_func($callback, $taxonomy, $params, $terms, $callback);
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
	if ($id) $post = get_post($id);
	$return = false;
	if (is_callable($callback) AND $post) {
		$return = $post;
		setup_postdata($post);
		call_user_func($callback, $post);
		wp_reset_postdata();
	}
	return $return;
}




function helper_thumbnail($post, $default=null) {
	$thumbnail = get_the_post_thumbnail_url($post, 'full');
	$default = $default===true? (plugin_dir_url(__FILE__) . 'assets/nophoto.jpg'): $default;
    return $thumbnail? $thumbnail: $default;
}



function helper_content($url, $post=null) {
	if (is_array($post)) {
		die("Curl Post: " . json_encode($post));
	}

	if ($realpath = realpath($url)) {
		ob_start();
		include $realpath;
		return ob_get_clean();
	}

	$return = file_get_contents($url);

	if (! $return) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		$return = curl_exec($ch);
		curl_close($ch);
	}

	return $return;
}


function helper_url_merge($url) {
	$url = parse_url($url);
	$url['query'] = isset($url['query'])? $url['query']: null;
	parse_str($url['query'], $url['query']);
	$url['query'] = array_merge($_GET, $url['query']);
	$url2 = (isset($_SERVE['HTTPS']) AND $_SERVE['HTTPS']=='on')? 'https://': 'http://';
	$url2 .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$url2 = parse_url($url2);
	parse_str($url2['query'], $url2['query']);
	$url['query'] = array_merge($url['query'], $url2['query']);
	$url['query'] = http_build_query($url['query']);
	$url2['query'] = http_build_query($url2['query']);
	$url = array_merge($url2, $url);
	return "{$url['scheme']}://{$url['host']}{$url['path']}" . ($url['query']? "?{$url['query']}": null);
}


function cdz_flash($class=null, $text=null) {
	$_SESSION['cdz_flash'] = isset($_SESSION['cdz_flash'])? $_SESSION['cdz_flash']: array();

	if ($class AND $text) {
		$_SESSION['cdz_flash'][$class] = isset($_SESSION['cdz_flash'][$class])? $_SESSION['cdz_flash'][$class]: array();
		$_SESSION['cdz_flash'][$class][] = $text;
	}

	else if ($class AND !$text) {
		return isset($_SESSION['cdz_flash'][$class])? $_SESSION['cdz_flash'][$class]: null;
	}

	return $_SESSION['cdz_flash'];
}



function cdz_flash_render() {
	if (! empty($classes = cdz_flash())) { ?>
	<div class="cdz-flash" style="position:fixed; top:40px; left:20%; width:60%; z-index:99999999 !important;">
		<?php foreach($classes as $class=>$flashes): ?>
		<div class="alert alert-success">
			<a href="javascript:;" style="float:right; color:#222;" onclick="jQuery(this).closest('.cdz-flash').fadeOut(200);">&times;</a>
			<?php echo implode('<br>', $flashes); ?>
		</div>
		<?php endforeach; ?>
	</div>
	<?php if(! session_id()) session_start();
		$_SESSION['cdz_flash'] = array();
	}
}
add_action('admin_footer', 'cdz_flash_render');
add_action('wp_footer', 'cdz_flash_render');




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


function helper_redirect($url) {
	echo "<script>location.href='{$url}';</script>"; die;
}
