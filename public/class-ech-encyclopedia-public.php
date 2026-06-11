<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://127.0.0.1
 * @since      1.0.0
 *
 * @package    Ech_Encyclopedia
 * @subpackage Ech_Encyclopedia/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ech_Encyclopedia
 * @subpackage Ech_Encyclopedia/public
 * @author     Rowan Chang <rowanchang@prohaba.com>
 */
class Ech_Encyclopedia_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ech_Encyclopedia_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ech_Encyclopedia_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ech-encyclopedia-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ech_Encyclopedia_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ech_Encyclopedia_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ech-encyclopedia-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script($this->plugin_name . '_pagination', plugin_dir_url(__FILE__) . 'js/ech-encyclopedia-pagination.js', array('jquery'), $this->version, false);			

	}

	public function ech_encyclopedia_func($atts)
    {
        // per page post
        $ppp = get_option('ech_encyclopedia_ppp');

        // check if specific filters are set.
        $filterCatID = get_option('ech_encyclopedia_cat_filter');
        $filterBrandID = get_option('ech_encyclopedia_brand_filter');

        $api_args = array(
            'ppp' => $ppp,
            'encyclopedia_cat' => $filterCatID,
            'brand' => $filterBrandID,
        );
        $api_link = $this->gen_encyclopedia_link_api_link($api_args);
        $get_encyclopedia_json = $this->wp_remote_get_encyclopedia_json($api_link);
        $json_arr = json_decode($get_encyclopedia_json, true);

        $output = '';

        /*********** POST LIST ************/
        $output .= '<div class="ech-encyclopedia-container" >';

        $output .= '<div class="encyclopedia-list" data-ajaxurl="' . get_admin_url(null, 'admin-ajax.php') . '" data-ppp="' . $ppp . '" data-page="1" data-cat="' . $filterCatID . '" data-brand="' . $filterBrandID . '">';
        foreach ($json_arr['posts_data'] as $post) {
            $output .= $this->load_post_card_template($post);
        }
        $output .= '</div>'; //encyclopedia-list

		/*** pagination ***/
        $total_posts = $json_arr['count'];
		$max_page = ceil($total_posts / $ppp);
		$output .= '<div class="ech-encyclopedia-pagination" data-current-page="1" data-max-page="' . $max_page . '" data-topage="" data-cat="' . $filterCatID . '" data-brand-id="' . $filterBrandID . '" data-ajaxurl="' . get_admin_url(null, 'admin-ajax.php') . '"></div>';
		/*** (end) pagination ***/



        $output .= '</div>'; //ech-encyclopedia-container

        /*********** (END) POST LIST ************/
        return $output;
    } // ech_encyclopedia_func()
    public function load_post_card_template($post)
    {
        $html = '';
        $encyclopedia_cat_id = [];
        $encyclopedia_cat_name = [];
        $brand_category_id = [];
        $brand_category_name = [];
        $featured_image = ($post['featured_image']['has_featured_image']) ? $post['featured_image']['url'] : get_option('ech_dmn_default_post_featured_img');
        foreach ($post['encyclopedia_category'] as $cat) {
            array_push($encyclopedia_cat_id, $cat['id']);
            array_push($encyclopedia_cat_name, $this->echoLang([$cat['name_en'],$cat['name_zh'],$cat['name_sc']]));
        }

        foreach ($post['brand_category'] as $brand) {
            array_push($brand_category_id, $brand['id']);
            array_push($brand_category_name, $this->echoLang([$brand['name_en'],$brand['name_zh'],$brand['name_sc']]));
        }

        $html .= '<div class="encyclopedia-card" data-post="' . $post['id'] . '" data-cat="' . implode(',', $encyclopedia_cat_id) . '" data-brand="' . implode(',', $brand_category_id) . '">';
        if($post['featured_image']['has_featured_image']) {
            $html .= '<div class="featured-image">';
            $html .= '<img src="' . $featured_image . '" alt="' . $post['featured_image']['alt_text'] . '">';
            $html .= '</div>';
        }
        $html .= '<div class="encyclopedia-info">';
        $html .= '<div class="encyclopedia-title"><a href="' . site_url() . '/encyclopedia/encyclopedia-content/?postid=' . $post['id'] . '"><h1>' . $post['title'] . '</h1></a></div>';
        $html .= '<h4 class="encyclopedia-cat"><i aria-hidden="true" class="fas fa-tags"></i> ' . implode(' ', $encyclopedia_cat_name) . '</h4>';
        $html .= '<a href="' . site_url() . '/encyclopedia/encyclopedia-content/?postid=' . $post['id'] . '">' . $this->echoLang(['Read More','閱讀更多','阅读更多']) . '</a>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
    /**************************** API ****************************/

    /***********************************************************
     * Get API domain
     ***********************************************************/
    public function getAPIDomain()
    {
        $domain = get_option('ech_encyclopedia_domain_url');
        return $domain;
    }
	/****************************************
     * Filter and merge value and return a full API Encyclopedia List link.
     * Array key: ppp, page, encyclopedia_cat, brand
     ****************************************/
		public function gen_encyclopedia_link_api_link(array $args)
    {
        $full_api = $this->getAPIDomain() . '/wp-json/am-api/v1/encyclopedia_list?';

        if(!empty($args)) {
            if(isset($args['ppp']) && !empty($args['ppp'])) {
                $full_api .= 'ppp=' . $args['ppp'];
            }
            if(isset($args['page']) && !empty($args['page'])) {
                $full_api .= '&page=' . $args['page'];
            }
            if(isset($args['encyclopedia_cat']) && !empty($args['encyclopedia_cat'])) {
              $full_api .= '&encyclopedia_cat=' . $args['encyclopedia_cat'];
            }

            if(isset($args['brand']) && !empty($args['brand'])){
            	$full_api .='&brand='.$args['brand'];
            }
        }
        return $full_api;
    }
		public function gen_encyclopedia_categories_api_link()
    {
        $full_api = $this->getAPIDomain() . '/wp-json/am-api/v1/encyclopedia_categories';

        return $full_api;
    }
		public function gen_brand_api_link()
    {
        $full_api = $this->getAPIDomain() . '/wp-json/am-api/v1/brand_categories';

        return $full_api;
    }

		/****************************************
     * Filter and merge value and return a full API Post Content link.
     * Array key: postid
     ****************************************/
    public function gen_post_api_link(array $args)
    {
        $full_api = $this->getAPIDomain() . '/wp-json/am-api/v1/single_post?';

        if (!empty($args['postid'])) {
            $full_api .= '&';
            $full_api .= 'postid=' . $args['postid'];
        }
        return $full_api;
    }

    /**************************** (end)API ****************************/

	/****************************************
     * Get Encyclopedia JSON Using API
     ****************************************/
		public function wp_remote_get_encyclopedia_json($api_link)
    {
        $getAccessToken = get_option('ech_encyclopedia_access_token');
        $api_headers = array(
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $getAccessToken,
        );

        $response = wp_remote_get($api_link, array(
            'headers' => $api_headers,
            'timeout' => 15,
        ));

        if (is_wp_error($response)) {
            return 'Error: ' . $response->get_error_message();
        }

        $result = wp_remote_retrieve_body($response);

        return $result;
    }

	/****************************************
     * DISPLAY SPECIFIC LANGUAGE
     ****************************************/
    public function echoLang($stringArr)
    {
        global $TRP_LANGUAGE;

        switch ($TRP_LANGUAGE) {
            case 'zh_HK':
                $langString = $stringArr[1];
                break;
            case 'zh_CN':
                $langString = $stringArr[2];
                break;
            default:
                $langString = $stringArr[0];
        }

        if (empty($langString) || $langString == '' || $langString == null) {
            $langString = $stringArr[1]; //zh_HK
        }

        return $langString;
    }
    /********** (END)DISPLAY SPECIFIC LANGUAGE **********/

}
