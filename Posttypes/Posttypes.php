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



	public function posttypeSearch()
	{
		$posttypes = cdz_option('cdz-posttypes', array());
		$posttypes = is_array($posttypes)? $posttypes: array();
		return $posttypes;
	}

	public function posttypeSave($data) {
		if (!$data['posttype_slug'] OR !$data['posttype_plural'] OR !$data['posttype_singular']) {
			throw new \Exception('Campos slug, plural e singular são obrigatórios');
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
