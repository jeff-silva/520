<?php namespace Posttypes;

class Posttypes extends \Db
{

	public function apiSearch()
	{
		return $this->search();
	}


	public function apiAdd()
	{
		$post_type = isset($_REQUEST['post_type'])? $_REQUEST['post_type']: NULL;
		$singular = isset($_REQUEST['singular'])? $_REQUEST['singular']: NULL;
		$plural = isset($_REQUEST['plural'])? $_REQUEST['plural']: NULL;
		return $this->add($post_type, $singular, $plural);
	}


	public function apiPosttypeUpdate()
	{
		$posttypes = isset($_REQUEST['posttypes'])? $_REQUEST['posttypes']: array();
		$posttypes = is_array($posttypes)? $posttypes: array();
		return $this->posttypeUpdate($posttypes);
	}



	public function add($post_type, $singular, $plural)
	{
		if (!$post_type OR !$singular OR !$plural) {
			return $this->error("post_type, singular or plural is empty");
		}

		$posttypes = get_option('520-posttypes');
		$posttypes = is_array($posttypes)? $posttypes: array();
		foreach($posttypes as $posttype) {
			if ($posttype['post_type']==$post_type) {
				return $this->error("Post type {$post_type} already exists");
			}
		}

		$posttypes[] = $this->createDefault($post_type, $singular, $plural);
		update_option('520-posttypes', $posttypes, true);
		return $posttypes;
	}



	public function posttypeUpdate($posttypes)
	{
		$taxonomies = $this->taxonomies();

		$posttypes = is_array($posttypes)? $posttypes: array();
		foreach($posttypes as $i=>$posttype) {
			foreach($posttype['post_type_args'] as $arg=>$val) {
				if ($val=='true') $posttype['post_type_args'][$arg] = true;
				else if ($val=='false') $posttype['post_type_args'][$arg] = false;
				else if ($val=='') $posttype['post_type_args'][$arg] = null;
			}

			$posttype['post_type_args']['taxonomies'] = isset($posttype['post_type_args']['taxonomies'])? $posttype['post_type_args']['taxonomies']: array();
			$posttype['post_type_args']['taxonomies'] = is_array($posttype['post_type_args']['taxonomies'])? $posttype['post_type_args']['taxonomies']: array();

			$posttype['post_type_args']['menu_position'] = intval($posttype['post_type_args']['menu_position']);

			// posttype_fieldgroups
			$posttype['posttype_fieldgroups'] = isset($posttype['posttype_fieldgroups'])? $posttype['posttype_fieldgroups']: array();
			foreach($posttype['posttype_fieldgroups'] as $ii=>$fieldgroup) {
				$fieldgroup['name'] = isset($fieldgroup['name'])? $fieldgroup['name']: 'Grupo';
				$fieldgroup['fields'] = isset($fieldgroup['fields'])? $fieldgroup['fields']: array();
				$posttype['posttype_fieldgroups'][$ii] = $fieldgroup;
			}

			$posttypes[$i] = $posttype;
		}
		update_option('520-posttypes', $posttypes, true);
		return $posttypes;
	}




	public function createDefault($post_type, $singular, $plural)
	{
		if (!$post_type OR !$singular OR !$plural) {
			return $this->error("post_type, singular or plural is empty");
		}

		return array(
			'post_type' => $post_type,
			'post_type_args' => array(
				'label' => $plural,
				'labels' => array(
					'name' => $plural,
					'singular_name' => $singular,
					'add_new' => "Criar {$singular}",
					'add_new_item' => "Criar {$singular}",
					'edit_item' => "Editar {$singular}",
					'new_item' => "Criar {$singular}",
					'view_item' => "Ver {$singular}",
					'view_items' => "Ver {$plural}",
					'search_items' => "Pesquisar {$plural}",
					'not_found' => "Nenhum {$singular} encontrado",
					'not_found_in_trash' => "Nenhum {$singular} encontrado",
					'parent_item_colon' => "parent_item_colon",
					'all_items' => $plural,
					'archives' => "archives",
					'attributes' => "Atributos",
					'insert_into_item' => "Inserir em {$singular}",
					'uploaded_to_this_item' => "Fazer upload para {$singular}",
					'featured_image' => "Imagem principal",
					'set_featured_image' => "Alterar imagem principal",
					'remove_featured_image' => "Remover imagem principal",
					'use_featured_image' => "Usar imagem principal",
					'menu_name' => $plural,
					'filter_items_list' => "Filtrar items",
					'items_list_navigation' => "items_list_navigation",
					'items_list' => "Lista de {$plural}",
					'name_admin_bar' => $plural,
				),
				'description' => "Gerenciamento de {$plural}",
				'public' => true,
				'exclude_from_search' => true,
				'publicly_queryable' => true,
				'show_ui' => true,
				'show_in_nav_menus' => true,
				'show_in_menu' => true,
				'show_in_admin_bar' => true,
				'menu_position' => 5,
				'menu_icon' => 'dashicons-menu',
				'capability_type' => 'post',
				'capabilities' => array(),
				'map_meta_cap' => null,
				'hierarchical' => null,
				'supports' => array('thumbnail'),
				'register_meta_box_cb' => false,
				'taxonomies' => array('category', 'post_tag'),
				'has_archive' => false,
				'rewrite' => false,
				'query_var' => false,
				'can_export' => true,
				'delete_with_user' => null,
			),
		);
	}


	public function taxonomies()
	{
		$taxonomies = get_option('520-taxonomies');
		$taxonomies = is_array($taxonomies)? $taxonomies: array();
		foreach(get_taxonomies() as $taxo) {
			$taxonomies[] = $taxo;
		}
		return $taxonomies;
	}


	public function search()
	{
		$posttypes = get_option('520-posttypes');
		$posttypes = is_array($posttypes)? $posttypes: array();
		return $posttypes;
	}


	public function register()
	{
		$posttypes = $this->search();
		foreach($posttypes as $posttype) {
			$posttype['post_type_args']['supports'] = isset($posttype['post_type_args']['supports'])? $posttype['post_type_args']['supports']: array();
			$posttype['post_type_args']['supports'][] = 'title';
			$posttype['post_type_args']['supports'][] = 'editor';
			$posttype['post_type_args']['supports'][] = 'thumbnail';
			$posttype['post_type_args']['rewrite'] = array();
			// dd($posttype); die;
			register_post_type($posttype['post_type'], $posttype['post_type_args']);
		}

		$thumbs = array('post', 'page');
		foreach($posttypes as $posttype) $thumbs[] = $posttype['post_type'];
		add_theme_support('post-thumbnails', $thumbs);
		// set_post_thumbnail_size( 140, 140, true );
	}


	public function dashicons()
	{
		return array(
			'62259' => 'dashicons-menu',
			'62233' => 'dashicons-admin-site',
			'61990' => 'dashicons-dashboard',
			'61700' => 'dashicons-admin-media',
			'61701' => 'dashicons-admin-page',
			'61697' => 'dashicons-admin-comments',
			'61696' => 'dashicons-admin-appearance',
			'61702' => 'dashicons-admin-plugins',
			'61712' => 'dashicons-admin-users',
			'61703' => 'dashicons-admin-tools',
			'61704' => 'dashicons-admin-settings',
			'61714' => 'dashicons-admin-network',
			'61713' => 'dashicons-admin-generic',
			'61698' => 'dashicons-admin-home',
			'61768' => 'dashicons-admin-collapse',
			'62774' => 'dashicons-filter',
			'62784' => 'dashicons-admin-customizer',
			'62785' => 'dashicons-admin-multisite',
			'61699' => 'dashicons-admin-links',
			'61705' => 'dashicons-admin-post',
			'61736' => 'dashicons-format-image',
			'61793' => 'dashicons-format-gallery',
			'61735' => 'dashicons-format-audio',
			'61734' => 'dashicons-format-video',
			'61733' => 'dashicons-format-chat',
			'61744' => 'dashicons-format-status',
			'61731' => 'dashicons-format-aside',
			'61730' => 'dashicons-format-quote',
			'61721' => 'dashicons-welcome-write-blog',
			'61747' => 'dashicons-welcome-add-page',
			'61717' => 'dashicons-welcome-view-site',
			'61718' => 'dashicons-welcome-widgets-menus',
			'61719' => 'dashicons-welcome-comments',
			'61720' => 'dashicons-welcome-learn-more',
			'61797' => 'dashicons-image-crop',
			'62769' => 'dashicons-image-rotate',
			'61798' => 'dashicons-image-rotate-left',
			'61799' => 'dashicons-image-rotate-right',
			'61800' => 'dashicons-image-flip-vertical',
			'61801' => 'dashicons-image-flip-horizontal',
			'62771' => 'dashicons-image-filter',
			'61809' => 'dashicons-undo',
			'61810' => 'dashicons-redo',
			'61952' => 'dashicons-editor-bold',
			'61953' => 'dashicons-editor-italic',
			'61955' => 'dashicons-editor-ul',
			'61956' => 'dashicons-editor-ol',
			'61957' => 'dashicons-editor-quote',
			'61958' => 'dashicons-editor-alignleft',
			'61959' => 'dashicons-editor-aligncenter',
			'61960' => 'dashicons-editor-alignright',
			'61961' => 'dashicons-editor-insertmore',
			'61968' => 'dashicons-editor-spellcheck',
			'61969' => 'dashicons-editor-expand',
			'62726' => 'dashicons-editor-contract',
			'61970' => 'dashicons-editor-kitchensink',
			'61971' => 'dashicons-editor-underline',
			'61972' => 'dashicons-editor-justify',
			'61973' => 'dashicons-editor-textcolor',
			'61974' => 'dashicons-editor-paste-word',
			'61975' => 'dashicons-editor-paste-text',
			'61976' => 'dashicons-editor-removeformatting',
			'61977' => 'dashicons-editor-video',
			'61984' => 'dashicons-editor-customchar',
			'61985' => 'dashicons-editor-outdent',
			'61986' => 'dashicons-editor-indent',
			'61987' => 'dashicons-editor-help',
			'61988' => 'dashicons-editor-strikethrough',
			'61989' => 'dashicons-editor-unlink',
			'62240' => 'dashicons-editor-rtl',
			'62580' => 'dashicons-editor-break',
			'62581' => 'dashicons-editor-code',
			'62582' => 'dashicons-editor-paragraph',
			'62773' => 'dashicons-editor-table',
			'61749' => 'dashicons-align-left',
			'61750' => 'dashicons-align-right',
			'61748' => 'dashicons-align-center',
			'61752' => 'dashicons-align-none',
			'61792' => 'dashicons-lock',
			'62760' => 'dashicons-unlock',
			'61765' => 'dashicons-calendar',
			'62728' => 'dashicons-calendar-alt',
			'61815' => 'dashicons-visibility',
			'62768' => 'dashicons-hidden',
			'61811' => 'dashicons-post-status',
			'62564' => 'dashicons-edit',
			'61826' => 'dashicons-post-trash',
			'62775' => 'dashicons-sticky',
			'62724' => 'dashicons-external',
			'61762' => 'dashicons-arrow-up',
			'61760' => 'dashicons-arrow-down',
			'61761' => 'dashicons-arrow-left',
			'61753' => 'dashicons-arrow-right',
			'62274' => 'dashicons-arrow-up-alt',
			'62278' => 'dashicons-arrow-down-alt',
			'62272' => 'dashicons-arrow-left-alt',
			'62276' => 'dashicons-arrow-right-alt',
			'62275' => 'dashicons-arrow-up-alt2',
			'62279' => 'dashicons-arrow-down-alt2',
			'62273' => 'dashicons-arrow-left-alt2',
			'62277' => 'dashicons-arrow-right-alt2',
			'61993' => 'dashicons-leftright',
			'61782' => 'dashicons-sort',
			'62723' => 'dashicons-randomize',
			'61795' => 'dashicons-list-view',
			'61796' => 'dashicons-excerpt-view',
			'62729' => 'dashicons-grid-view',
			'62789' => 'dashicons-move',
			'62216' => 'dashicons-hammer',
			'62217' => 'dashicons-art',
			'62224' => 'dashicons-migrate',
			'62225' => 'dashicons-performance',
			'62595' => 'dashicons-universal-access',
			'62727' => 'dashicons-universal-access-alt',
			'62598' => 'dashicons-tickets',
			'62596' => 'dashicons-nametag',
			'62593' => 'dashicons-clipboard',
			'62599' => 'dashicons-heart',
			'62600' => 'dashicons-megaphone',
			'62601' => 'dashicons-schedule',
			'61728' => 'dashicons-wordpress',
			'62244' => 'dashicons-wordpress-alt',
			'61783' => 'dashicons-pressthis',
			'62563' => 'dashicons-update',
			'61824' => 'dashicons-screenoptions',
			'61812' => 'dashicons-cart',
			'61813' => 'dashicons-feedback',
			'61814' => 'dashicons-cloud',
			'62246' => 'dashicons-translation',
			'62243' => 'dashicons-tag',
			'62232' => 'dashicons-category',
			'62592' => 'dashicons-archive',
			'62585' => 'dashicons-tagcloud',
			'62584' => 'dashicons-text',
			'62721' => 'dashicons-media-archive',
			'62720' => 'dashicons-media-audio',
			'62617' => 'dashicons-media-code',
			'62616' => 'dashicons-media-default',
			'62615' => 'dashicons-media-document',
			'62614' => 'dashicons-media-interactive',
			'62613' => 'dashicons-media-spreadsheet',
			'62609' => 'dashicons-media-text',
			'62608' => 'dashicons-media-video',
			'62610' => 'dashicons-playlist-audio',
			'62611' => 'dashicons-playlist-video',
			'62754' => 'dashicons-controls-play',
			'62755' => 'dashicons-controls-pause',
			'62745' => 'dashicons-controls-forward',
			'62743' => 'dashicons-controls-skipforward',
			'62744' => 'dashicons-controls-back',
			'62742' => 'dashicons-controls-skipback',
			'62741' => 'dashicons-controls-repeat',
			'62753' => 'dashicons-controls-volumeon',
			'62752' => 'dashicons-controls-volumeoff',
			'61767' => 'dashicons-yes',
			'61784' => 'dashicons-no',
			'62261' => 'dashicons-no-alt',
			'61746' => 'dashicons-plus',
			'62722' => 'dashicons-plus-alt',
			'62787' => 'dashicons-plus-alt2',
			'62560' => 'dashicons-minus',
			'61779' => 'dashicons-dismiss',
			'61785' => 'dashicons-marker',
			'61781' => 'dashicons-star-filled',
			'62553' => 'dashicons-star-half',
			'61780' => 'dashicons-star-empty',
			'61991' => 'dashicons-flag',
			'62280' => 'dashicons-info',
			'62772' => 'dashicons-warning',
			'62007' => 'dashicons-share1',
			'62016' => 'dashicons-share-alt',
			'62018' => 'dashicons-share-alt2',
			'62209' => 'dashicons-twitter',
			'62211' => 'dashicons-rss',
			'62565' => 'dashicons-email',
			'62566' => 'dashicons-email-alt',
			'62212' => 'dashicons-facebook',
			'62213' => 'dashicons-facebook-alt',
			'62245' => 'dashicons-networking',
			'62562' => 'dashicons-googleplus',
			'62000' => 'dashicons-location',
			'62001' => 'dashicons-location-alt',
			'62214' => 'dashicons-camera',
			'62002' => 'dashicons-images-alt',
			'62003' => 'dashicons-images-alt2',
			'62004' => 'dashicons-video-alt',
			'62005' => 'dashicons-video-alt2',
			'62006' => 'dashicons-video-alt3',
			'61816' => 'dashicons-vault',
			'62258' => 'dashicons-shield',
			'62260' => 'dashicons-shield-alt',
			'62568' => 'dashicons-sos',
			'61817' => 'dashicons-search',
			'61825' => 'dashicons-slides',
			'61827' => 'dashicons-analytics',
			'61828' => 'dashicons-chart-pie',
			'61829' => 'dashicons-chart-bar',
			'62008' => 'dashicons-chart-line',
			'62009' => 'dashicons-chart-area',
			'62215' => 'dashicons-groups',
			'62264' => 'dashicons-businessman',
			'62262' => 'dashicons-id',
			'62263' => 'dashicons-id-alt',
			'62226' => 'dashicons-products',
			'62227' => 'dashicons-awards',
			'62228' => 'dashicons-forms',
			'62579' => 'dashicons-testimonial',
			'62242' => 'dashicons-portfolio',
			'62256' => 'dashicons-book',
			'62257' => 'dashicons-book-alt',
			'62230' => 'dashicons-download',
			'62231' => 'dashicons-upload',
			'62241' => 'dashicons-backup',
			'62569' => 'dashicons-clock',
			'62265' => 'dashicons-lightbulb',
			'62594' => 'dashicons-microphone',
			'62578' => 'dashicons-desktop',
			'62791' => 'dashicons-laptop',
			'62577' => 'dashicons-tablet',
			'62576' => 'dashicons-smartphone',
			'62757' => 'dashicons-phone',
			'62248' => 'dashicons-smiley',
			'62736' => 'dashicons-index-card',
			'62737' => 'dashicons-carrot',
			'62738' => 'dashicons-building',
			'62739' => 'dashicons-store',
			'62740' => 'dashicons-album',
			'62759' => 'dashicons-palmtree',
			'62756' => 'dashicons-tickets-alt',
			'62758' => 'dashicons-money',
			'62761' => 'dashicons-thumbs-up',
			'62786' => 'dashicons-thumbs-down',
			'62776' => 'dashicons-layout',
			'62790' => 'dashicons-paperclip',
			'61706' => 'dashicons-email-alt2',
			'61707' => 'dashicons-menu-alt',
			'61708' => 'dashicons-plus-light',
			'61709' => 'dashicons-trash',
			'61710' => 'dashicons-heading',
			'61711' => 'dashicons-insert',
			'61715' => 'dashicons-saved',
			'61716' => 'dashicons-align-full-width',
			'61722' => 'dashicons-button',
			'61723' => 'dashicons-align-wide',
			'61724' => 'dashicons-ellipsis',
			'62546' => 'dashicons-buddicons-activity',
			'62536' => 'dashicons-buddicons-buddypress-logo',
			'62547' => 'dashicons-buddicons-community',
			'62537' => 'dashicons-buddicons-forums',
			'62548' => 'dashicons-buddicons-friends',
			'62550' => 'dashicons-buddicons-groups',
			'62551' => 'dashicons-buddicons-pm',
			'62545' => 'dashicons-buddicons-replies',
			'62544' => 'dashicons-buddicons-topics',
			'62549' => 'dashicons-buddicons-tracking',
			'62583' => 'dashicons-buddipress-bbpress-logo',
			'61725' => 'dashicons-admin-site-alt',
			'61726' => 'dashicons-admin-site-alt2',
			'61727' => 'dashicons-admin-site-alt3',
			'61729' => 'dashicons-html',
		);
	}
}
