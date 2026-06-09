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
		if (strpos($_SERVER['REQUEST_URI'], "encyclopedia") !== false) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ech-encyclopedia-public.css', array(), $this->version, 'all' );
		}

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
		if (strpos($_SERVER['REQUEST_URI'], "encyclopedia") !== false) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ech-encyclopedia-public.js', array( 'jquery' ), $this->version, false );
		}

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
            'dr' => $filterDrID,
            'encyclopedia_cat' => $filterCatID,
            'brand' => $filterBrandID,
        );
        $api_link = $this->ECHD_gen_news_link_api_link($api_args);
        $get_news_json = $this->ECHD_wp_remote_get_news_json($api_link);
        $json_arr = json_decode($get_news_json, true);

        $output = '';

        // *********** Custom styling ***************/
        if (!empty(get_option('ech_dmn_submitBtn_color')) || !empty(get_option('ech_dmn_submitBtn_hoverColor') || !empty(get_option('ech_dmn_submitBtn_text_color')) || !empty(get_option('ech_dmn_submitBtn_text_hoverColor')))) {
            $output .= '<style>';

            $output .= '.ech-dmn-dr-news-container .news-btn button { ';
            (!empty(get_option('ech_dmn_submitBtn_color'))) ? $output .= 'background:' . get_option('ech_dmn_submitBtn_color') . ';' : '';
            (!empty(get_option('ech_dmn_submitBtn_text_color'))) ? $output .= 'color:' . get_option('ech_dmn_submitBtn_text_color') . ';' : '';
            $output .= '}';

            $output .= '.ech-dmn-dr-news-container .news-btn button:hover { ';
            (!empty(get_option('ech_dmn_submitBtn_hoverColor'))) ? $output .= 'background:' . get_option('ech_dmn_submitBtn_hoverColor') . ';' : '';
            (!empty(get_option('ech_dmn_submitBtn_text_hoverColor'))) ? $output .= 'color:' . get_option('ech_dmn_submitBtn_text_hoverColor') . ';' : '';
            $output .= '}';

            $output .= '</style>';
        }
        // *********** (END) Custom styling ****************/

        $output .= '<div class="ech-dmn-dr-news-container">';

        /***** Filters *****/

        $output .= '<div class="ech-dmn-filter-container">';

        if($enableFilterDrID) {
            $output .= $this->filters->ECHD_get_dr_categories_list($filterDrID);
        }
        if($enableFilterSpecID) {
            $output .= $this->filters->ECHD_get_specialty_categories_list($filterSpecID);
        }
        if($enableFilterBrandID) {
            $output .= $this->filters->ECHD_get_brand_categories_list($filterBrandID);
        }


        $output .= '<div class="news-btn">';
        $output .= '<button id="newsSearchBtn" type="button" disabled>' . $this->ECHD_echolang(['Search', '搜尋', '搜寻']) . '</button>';
        $output .= '<button id="resetBtn" type="button" disabled>' . $this->ECHD_echolang(['Reset', '清除', '清除']) . '</button>';
        $output .= '</div>';
        $output .= '</div>'; //ech-dmn-filter-container
        /***** (end)Filters *****/


        /*********** POST LIST ************/
        $output .= '<div class="ech-dmn-news-container" >';

        $output .= '<div class="news-list" data-ajaxurl="' . get_admin_url(null, 'admin-ajax.php') . '" data-ppp="' . $ppp . '" data-page="1" data-specialties="' . $filterSpecID . '" data-dr="' . $filterDrID . '" data-brand="' . $filterBrandID . '">';
        foreach ($json_arr['posts_data'] as $post) {
            $output .= $this->ECHD_load_post_card_template($post);
        }
        $output .= '</div>'; //news-list

        /*** loading div ***/
        $output .= '<div class="loading-news">' . $this->ECHD_echolang(['Loading...', '載入中...', '载入中...']) . '</div>';
        /*** (end) loading div ***/
        $output .= '<div class="news-btn">';
        $output .= '<button id="moreNewsBtn" type="button">' . $this->ECHD_echolang(['More articles', '更多文章', '更多文章']) . '</button>';
        $output .= '</div>';


        $output .= '</div>'; //ech-dmn-news-container

        /*********** (END) POST LIST ************/

        $output .= '</div>'; //ech-dmn-dr-news-container


        return $output;
    } // ech_encyclopedia_func()
		/**************************** API ****************************/

    /***********************************************************
     * Get API domain
     ***********************************************************/
    public function getAPIDomain()
    {
        $domain = get_option('ech_dmn_domain_url');
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
            if(isset($args['id']) && !empty($args['id'])) {
                $full_api .= '&id=' . $args['id'];
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
