<?php namespace Cdz\Posttypes;

class Posttypes
{

	public function apiData()
	{
		$data['posttypes'] = $this->posttypeSearch();
		$data['taxonomies'] = $this->taxonomySearch();
		return $data;
	}

	public function apiPosttypeSearch() {}

	public function apiPosttypeSave() {
		$this->posttypeSave($_REQUEST['data']);
		return $this->apiData();
	}

	public function apiPosttypeDelete() {
		$this->posttypeDelete($_REQUEST['id']);
		return $this->apiData();
	}

	public function apiTaxonomySearch() {}

	public function apiTaxonomySave() {
		$this->taxonomySave($_REQUEST['data']);
		return $this->apiData();
	}

	public function apiTaxonomyDelete() {
		$this->taxonomyDelete($_REQUEST['id']);
		return $this->apiData();
	}


	public function posttypeDefault($data=array())
	{
		if (!$data['posttype_slug'] OR !$data['posttype_plural'] OR !$data['posttype_singular']) {
			throw new \Exception('Campos posttype_slug, posttype_plural e posttype_singular são obrigatórios');
		}
		$data = array_merge(array(
			'posttype_id' => '',
			'posttype_slug' => $data['posttype_slug'],
			'posttype_plural' => $data['posttype_plural'],
			'posttype_singular' => $data['posttype_singular'],
			'posttype_data' => array(
				'label' => $data['posttype_plural'],
				'labels' => array(
					'name' => $data['posttype_plural'],
					'singular_name' => $data['posttype_singular'],
					'add_new' => "Criar {$data['posttype_singular']}",
					'add_new_item' => "Criar {$data['posttype_singular']}",
					'edit_item' => "Editar {$data['posttype_singular']}",
					'new_item' => "Criar {$data['posttype_singular']}",
					'view_item' => "Ver {$data['posttype_singular']}",
					'view_items' => "Ver {$data['posttype_plural']}",
					'search_items' => "Pesquisar {$data['posttype_plural']}",
					'not_found' => "Nenhum {$data['posttype_singular']} encontrado",
					'not_found_in_trash' => "Nenhum {$data['posttype_singular']} encontrado",
					'parent_item_colon' => "parent_item_colon",
					'all_items' => $data['posttype_plural'],
					'archives' => "archives",
					'attributes' => "Atributos",
					'insert_into_item' => "Inserir em {$data['posttype_singular']}",
					'uploaded_to_this_item' => "Fazer upload para {$data['posttype_singular']}",
					'featured_image' => "Imagem principal",
					'set_featured_image' => "Alterar imagem principal",
					'remove_featured_image' => "Remover imagem principal",
					'use_featured_image' => "Usar imagem principal",
					'menu_name' => $data['posttype_plural'],
					'filter_items_list' => "Filtrar items",
					'items_list_navigation' => "items_list_navigation",
					'items_list' => "Lista de {$data['posttype_plural']}",
					'name_admin_bar' => $data['posttype_plural'],
				),
				'description' => "Gerenciamento de {$data['posttype_plural']}",
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
				'supports' => array(),
				'register_meta_box_cb' => false,
				'taxonomies' => array(),
				'has_archive' => false,
				'rewrite' => false,
				'query_var' => false,
				'can_export' => true,
				'delete_with_user' => null,
			),
		), $data);
		$data['posttype_id'] = $data['posttype_id']? $data['posttype_id']: uniqid();

		foreach($data['posttype_data'] as $key=>$val) {
			if ($val=='true') $val=true;
			else if ($val=='false') $val=false;
			$data['posttype_data'][$key] = $val;
		}

		return $data;
	}


	public function posttypeSearch()
	{
		$posttypes = cdz_option('cdz-posttypes', array());
		$posttypes = is_array($posttypes)? $posttypes: array();

		//default value
		//$posttypes = array();
		//$unserial = unserialize('a:5:{i:0;a:3:{s:9:"post_type";s:8:"projetos";s:14:"post_type_args";a:22:{s:5:"label";s:15:"Projetos de lei";s:6:"labels";a:26:{s:4:"name";s:15:"Projetos de lei";s:13:"singular_name";s:14:"Projeto de lei";s:7:"add_new";s:20:"Criar Projeto de lei";s:12:"add_new_item";s:20:"Criar Projeto de lei";s:9:"edit_item";s:21:"Editar Projeto de lei";s:8:"new_item";s:20:"Criar Projeto de lei";s:9:"view_item";s:18:"Ver Projeto de lei";s:10:"view_items";s:19:"Ver Projetos de lei";s:12:"search_items";s:25:"Pesquisar Projetos de lei";s:9:"not_found";s:32:"Nenhum Projeto de lei encontrado";s:18:"not_found_in_trash";s:32:"Nenhum Projeto de lei encontrado";s:17:"parent_item_colon";s:17:"parent_item_colon";s:9:"all_items";s:15:"Projetos de lei";s:8:"archives";s:8:"archives";s:10:"attributes";s:9:"Atributos";s:16:"insert_into_item";s:25:"Inserir em Projeto de lei";s:21:"uploaded_to_this_item";s:32:"Fazer upload para Projeto de lei";s:14:"featured_image";s:16:"Imagem principal";s:18:"set_featured_image";s:24:"Alterar imagem principal";s:21:"remove_featured_image";s:24:"Remover imagem principal";s:18:"use_featured_image";s:21:"Usar imagem principal";s:9:"menu_name";s:15:"Projetos de lei";s:17:"filter_items_list";s:13:"Filtrar items";s:21:"items_list_navigation";s:21:"items_list_navigation";s:10:"items_list";s:24:"Lista de Projetos de lei";s:14:"name_admin_bar";s:15:"Projetos de lei";}s:11:"description";s:32:"Gerenciamento de Projetos de lei";s:6:"public";b:1;s:19:"exclude_from_search";b:1;s:18:"publicly_queryable";b:1;s:7:"show_ui";b:1;s:17:"show_in_nav_menus";b:1;s:12:"show_in_menu";b:1;s:17:"show_in_admin_bar";b:1;s:13:"menu_position";i:20;s:9:"menu_icon";s:21:"dashicons-businessman";s:15:"capability_type";s:4:"post";s:12:"map_meta_cap";N;s:12:"hierarchical";N;s:20:"register_meta_box_cb";b:0;s:10:"taxonomies";a:2:{i:0;s:8:"category";i:1;s:8:"post_tag";}s:11:"has_archive";b:1;s:7:"rewrite";b:0;s:9:"query_var";b:0;s:10:"can_export";b:1;s:16:"delete_with_user";N;}s:20:"posttype_fieldgroups";a:0:{}}i:1;a:3:{s:9:"post_type";s:8:"noticias";s:14:"post_type_args";a:23:{s:5:"label";s:9:"Notícias";s:6:"labels";a:26:{s:4:"name";s:9:"Notícias";s:13:"singular_name";s:8:"Notícia";s:7:"add_new";s:14:"Criar Notícia";s:12:"add_new_item";s:14:"Criar Notícia";s:9:"edit_item";s:15:"Editar Notícia";s:8:"new_item";s:14:"Criar Notícia";s:9:"view_item";s:12:"Ver Notícia";s:10:"view_items";s:13:"Ver Notícias";s:12:"search_items";s:19:"Pesquisar Notícias";s:9:"not_found";s:26:"Nenhum Notícia encontrado";s:18:"not_found_in_trash";s:26:"Nenhum Notícia encontrado";s:17:"parent_item_colon";s:17:"parent_item_colon";s:9:"all_items";s:9:"Notícias";s:8:"archives";s:8:"archives";s:10:"attributes";s:9:"Atributos";s:16:"insert_into_item";s:19:"Inserir em Notícia";s:21:"uploaded_to_this_item";s:26:"Fazer upload para Notícia";s:14:"featured_image";s:16:"Imagem principal";s:18:"set_featured_image";s:24:"Alterar imagem principal";s:21:"remove_featured_image";s:24:"Remover imagem principal";s:18:"use_featured_image";s:21:"Usar imagem principal";s:9:"menu_name";s:9:"Notícias";s:17:"filter_items_list";s:13:"Filtrar items";s:21:"items_list_navigation";s:21:"items_list_navigation";s:10:"items_list";s:18:"Lista de Notícias";s:14:"name_admin_bar";s:9:"Notícias";}s:11:"description";s:26:"Gerenciamento de Notícias";s:6:"public";b:1;s:19:"exclude_from_search";b:1;s:18:"publicly_queryable";b:1;s:7:"show_ui";b:1;s:17:"show_in_nav_menus";b:1;s:12:"show_in_menu";b:1;s:17:"show_in_admin_bar";b:1;s:13:"menu_position";i:20;s:9:"menu_icon";s:31:"dashicons-welcome-widgets-menus";s:15:"capability_type";s:4:"post";s:12:"map_meta_cap";N;s:12:"hierarchical";N;s:8:"supports";a:1:{i:0;s:9:"thumbnail";}s:20:"register_meta_box_cb";b:0;s:10:"taxonomies";a:2:{i:0;s:8:"category";i:1;s:8:"post_tag";}s:11:"has_archive";b:0;s:7:"rewrite";b:0;s:9:"query_var";b:0;s:10:"can_export";b:1;s:16:"delete_with_user";N;}s:20:"posttype_fieldgroups";a:0:{}}i:2;a:3:{s:9:"post_type";s:6:"frases";s:14:"post_type_args";a:23:{s:5:"label";s:13:"Frases do dia";s:6:"labels";a:26:{s:4:"name";s:13:"Frases do dia";s:13:"singular_name";s:12:"Frase do dia";s:7:"add_new";s:18:"Criar Frase do dia";s:12:"add_new_item";s:18:"Criar Frase do dia";s:9:"edit_item";s:19:"Editar Frase do dia";s:8:"new_item";s:18:"Criar Frase do dia";s:9:"view_item";s:16:"Ver Frase do dia";s:10:"view_items";s:17:"Ver Frases do dia";s:12:"search_items";s:23:"Pesquisar Frases do dia";s:9:"not_found";s:30:"Nenhum Frase do dia encontrado";s:18:"not_found_in_trash";s:30:"Nenhum Frase do dia encontrado";s:17:"parent_item_colon";s:17:"parent_item_colon";s:9:"all_items";s:13:"Frases do dia";s:8:"archives";s:8:"archives";s:10:"attributes";s:9:"Atributos";s:16:"insert_into_item";s:23:"Inserir em Frase do dia";s:21:"uploaded_to_this_item";s:30:"Fazer upload para Frase do dia";s:14:"featured_image";s:16:"Imagem principal";s:18:"set_featured_image";s:24:"Alterar imagem principal";s:21:"remove_featured_image";s:24:"Remover imagem principal";s:18:"use_featured_image";s:21:"Usar imagem principal";s:9:"menu_name";s:13:"Frases do dia";s:17:"filter_items_list";s:13:"Filtrar items";s:21:"items_list_navigation";s:21:"items_list_navigation";s:10:"items_list";s:22:"Lista de Frases do dia";s:14:"name_admin_bar";s:13:"Frases do dia";}s:11:"description";s:30:"Gerenciamento de Frases do dia";s:6:"public";b:1;s:19:"exclude_from_search";b:1;s:18:"publicly_queryable";b:1;s:7:"show_ui";b:1;s:17:"show_in_nav_menus";b:1;s:12:"show_in_menu";b:1;s:17:"show_in_admin_bar";b:1;s:13:"menu_position";i:20;s:9:"menu_icon";s:22:"dashicons-format-quote";s:15:"capability_type";s:4:"post";s:12:"map_meta_cap";N;s:12:"hierarchical";N;s:8:"supports";a:1:{i:0;s:9:"thumbnail";}s:20:"register_meta_box_cb";b:0;s:11:"has_archive";b:0;s:7:"rewrite";b:0;s:9:"query_var";b:0;s:10:"can_export";b:1;s:16:"delete_with_user";N;s:10:"taxonomies";a:0:{}}s:20:"posttype_fieldgroups";a:0:{}}i:3;a:3:{s:9:"post_type";s:6:"albums";s:14:"post_type_args";a:23:{s:5:"label";s:6:"Albums";s:6:"labels";a:26:{s:4:"name";s:6:"Albums";s:13:"singular_name";s:5:"Album";s:7:"add_new";s:11:"Criar Album";s:12:"add_new_item";s:11:"Criar Album";s:9:"edit_item";s:12:"Editar Album";s:8:"new_item";s:11:"Criar Album";s:9:"view_item";s:9:"Ver Album";s:10:"view_items";s:10:"Ver Albums";s:12:"search_items";s:16:"Pesquisar Albums";s:9:"not_found";s:23:"Nenhum Album encontrado";s:18:"not_found_in_trash";s:23:"Nenhum Album encontrado";s:17:"parent_item_colon";s:17:"parent_item_colon";s:9:"all_items";s:6:"Albums";s:8:"archives";s:8:"archives";s:10:"attributes";s:9:"Atributos";s:16:"insert_into_item";s:16:"Inserir em Album";s:21:"uploaded_to_this_item";s:23:"Fazer upload para Album";s:14:"featured_image";s:16:"Imagem principal";s:18:"set_featured_image";s:24:"Alterar imagem principal";s:21:"remove_featured_image";s:24:"Remover imagem principal";s:18:"use_featured_image";s:21:"Usar imagem principal";s:9:"menu_name";s:6:"Albums";s:17:"filter_items_list";s:13:"Filtrar items";s:21:"items_list_navigation";s:21:"items_list_navigation";s:10:"items_list";s:15:"Lista de Albums";s:14:"name_admin_bar";s:6:"Albums";}s:11:"description";s:23:"Gerenciamento de Albums";s:6:"public";b:1;s:19:"exclude_from_search";b:1;s:18:"publicly_queryable";b:1;s:7:"show_ui";b:1;s:17:"show_in_nav_menus";b:1;s:12:"show_in_menu";b:1;s:17:"show_in_admin_bar";b:1;s:13:"menu_position";i:20;s:9:"menu_icon";s:22:"dashicons-format-image";s:15:"capability_type";s:4:"post";s:12:"map_meta_cap";N;s:12:"hierarchical";N;s:8:"supports";a:1:{i:0;s:9:"thumbnail";}s:20:"register_meta_box_cb";b:0;s:11:"has_archive";b:0;s:7:"rewrite";b:0;s:9:"query_var";b:0;s:10:"can_export";b:1;s:16:"delete_with_user";N;s:10:"taxonomies";a:0:{}}s:20:"posttype_fieldgroups";a:0:{}}i:4;a:3:{s:9:"post_type";s:14:"materias-frias";s:14:"post_type_args";a:23:{s:5:"label";s:15:"Matérias frias";s:6:"labels";a:26:{s:4:"name";s:15:"Matérias frias";s:13:"singular_name";s:13:"Matéria fria";s:7:"add_new";s:19:"Criar Matéria fria";s:12:"add_new_item";s:19:"Criar Matéria fria";s:9:"edit_item";s:20:"Editar Matéria fria";s:8:"new_item";s:19:"Criar Matéria fria";s:9:"view_item";s:17:"Ver Matéria fria";s:10:"view_items";s:19:"Ver Matérias frias";s:12:"search_items";s:25:"Pesquisar Matérias frias";s:9:"not_found";s:31:"Nenhum Matéria fria encontrado";s:18:"not_found_in_trash";s:31:"Nenhum Matéria fria encontrado";s:17:"parent_item_colon";s:17:"parent_item_colon";s:9:"all_items";s:15:"Matérias frias";s:8:"archives";s:8:"archives";s:10:"attributes";s:9:"Atributos";s:16:"insert_into_item";s:24:"Inserir em Matéria fria";s:21:"uploaded_to_this_item";s:31:"Fazer upload para Matéria fria";s:14:"featured_image";s:16:"Imagem principal";s:18:"set_featured_image";s:24:"Alterar imagem principal";s:21:"remove_featured_image";s:24:"Remover imagem principal";s:18:"use_featured_image";s:21:"Usar imagem principal";s:9:"menu_name";s:13:"Matéria fria";s:17:"filter_items_list";s:13:"Filtrar items";s:21:"items_list_navigation";s:21:"items_list_navigation";s:10:"items_list";s:24:"Lista de Matérias frias";s:14:"name_admin_bar";s:15:"Matérias frias";}s:11:"description";s:32:"Gerenciamento de Matérias frias";s:6:"public";b:1;s:19:"exclude_from_search";b:1;s:18:"publicly_queryable";b:1;s:7:"show_ui";b:1;s:17:"show_in_nav_menus";b:1;s:12:"show_in_menu";b:1;s:17:"show_in_admin_bar";b:1;s:13:"menu_position";i:20;s:9:"menu_icon";s:22:"dashicons-format-aside";s:15:"capability_type";s:4:"post";s:12:"map_meta_cap";N;s:12:"hierarchical";N;s:8:"supports";a:1:{i:0;s:9:"thumbnail";}s:20:"register_meta_box_cb";b:0;s:11:"has_archive";b:0;s:7:"rewrite";b:0;s:9:"query_var";b:0;s:10:"can_export";b:1;s:16:"delete_with_user";N;s:10:"taxonomies";a:0:{}}s:20:"posttype_fieldgroups";a:0:{}}}');
		//update_option('520-posttypes', $unserial);
		

		// Old post types bugfix (remove at next versions)
		$oldposttypes = get_option('520-posttypes');
		if (is_array($oldposttypes) AND !empty($oldposttypes)) {
			foreach($oldposttypes as $posttype) {
				$posttype['posttype_slug'] = $posttype['post_type'];
				$posttype['posttype_plural'] = $posttype['post_type_args']['labels']['name'];
				$posttype['posttype_singular'] = $posttype['post_type_args']['labels']['singular_name'];
				$posttype = $this->posttypeDefault($posttype);
				$posttypes[ $posttype['posttype_id'] ] = $posttype;
			}
			cdz_option_update('cdz-posttypes', $posttypes);
			update_option('520-posttypes', '');
		}


		foreach($posttypes as $i=>$posttype) {
			$posttype = $this->posttypeDefault($posttype);
			$posttypes[$i] = $posttype;
		}

		return $posttypes;
	}

	public function posttypeSave($data) {
		$data = $this->posttypeDefault($data);

		$posttypes = $this->posttypeSearch();
		$posttypes[ $data['posttype_id'] ] = $data;

		return cdz_option_update('cdz-posttypes', $posttypes);
	}

	public function posttypeDelete($slug) {
		$posttypes = cdz_option('cdz-posttypes', array());
		$posttypes = is_array($posttypes)? $posttypes: array();
		if (isset($posttypes[$slug])) unset($posttypes[$slug]);
		return cdz_option_update('cdz-posttypes', $posttypes);
	}

	public function taxonomySearch() {
		$taxonomies = cdz_option('cdz-taxonomies', array());
		$taxonomies = is_array($taxonomies)? $taxonomies: array();
		return $taxonomies;
	}

	public function taxonomySave($data) {
		if (!$data['taxonomy_slug'] OR !$data['taxonomy_plural'] OR !$data['taxonomy_singular']) {
			throw new \Exception('Campos slug, plural e singular são obrigatórios');
		}

		$data = array_merge(array(
			'taxonomy_id' => '',
			'taxonomy_slug' => '',
			'taxonomy_plural' => '',
			'taxonomy_singular' => '',
			'taxonomy_data' => array(
				'labels' => array(
					'name' => $data['taxonomy_plural'],
					'singular_name'              => $data['taxonomy_singular'],
					'search_items'               => "Pesquisar {$data['taxonomy_plural']}",
					'popular_items'              => "Popular {$data['taxonomy_plural']}",
					'all_items'                  => "Todos os(as) {$data['taxonomy_plural']}",
					'parent_item'                => null,
					'parent_item_colon'          => null,
					'edit_item'                  => "Editar {$data['taxonomy_singular']}",
					'update_item'                => "Alterar {$data['taxonomy_singular']}",
					'add_new_item'               => "Adicionar novo {$data['taxonomy_singular']}",
					'new_item_name'              => "Novo nome {$data['taxonomy_singular']}",
					'separate_items_with_commas' => "Separar {$data['taxonomy_plural']} com vírgulas",
					'add_or_remove_items'        => "Adicionar ou remover {$data['taxonomy_plural']}",
					'choose_from_most_used'      => "Escolher {$data['taxonomy_plural']} mais usados",
					'not_found'                  => "Nenhum {$data['taxonomy_singular']} encontrado",
					'menu_name'                  => $data['taxonomy_plural'],
				),
				'hierarchical'          => false,
				'show_ui'               => true,
				'show_admin_column'     => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var'             => true,
				'rewrite'               => array('slug'=>$data['taxonomy_slug']),
			),
		), $data);

		$data['taxonomy_id'] = $data['taxonomy_id']? $data['taxonomy_id']: uniqid();

		$taxonomies = cdz_option('cdz-taxonomies', array());
		$taxonomies = is_array($taxonomies)? $taxonomies: array();
		$taxonomies[ $data['taxonomy_id'] ] = $data;
		return cdz_option_update('cdz-taxonomies', $taxonomies);
	}

	public function taxonomyDelete($id) {
		$taxonomies = cdz_option('cdz-taxonomies', array());
		$taxonomies = is_array($taxonomies)? $taxonomies: array();
		if (isset($taxonomies[$id])) unset($taxonomies[$id]);
		return cdz_option_update('cdz-taxonomies', $taxonomies);
	}



	public function register()
	{
		$posttypes = $this->posttypeSearch();
		foreach($posttypes as $posttype) {
			$posttype['posttype_data']['rewrite'] = array();
			register_post_type($posttype['posttype_slug'], $posttype['posttype_data']);
		}


		$thumbs = array('post', 'page');
		foreach($posttypes as $posttype) $thumbs[] = $posttype['post_type'];
		add_theme_support('post-thumbnails', $thumbs);
		// set_post_thumbnail_size( 140, 140, true );

		
		foreach($this->taxonomySearch() as $tax) {
			// register_taxonomy('writer', 'book', $args);
		}
	}
}
