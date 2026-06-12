<?php

class Ech_Encyclopedia_Virtual_Pages extends Ech_Encyclopedia_Public
{
    /************************************************************************
     * To avoid the error "generated X characters of unexpected output" ocurred during plugin activation,
     * initialize_createVP function is called in define_public_hooks, add_action('init')
     * (folder: includes/class-ech-blog.php)
     * initialize_createVP() fires after WordPress has finished loading, but before any headers are sent.
     ************************************************************************/
    public static function encyclopedia_initialize_createVP()
    {
        // add an option to make use encyclopedia_setupVP is triggered once per VP. Delete this option once all VP are created.
        add_option('encyclopedia_run_init_createVP', 1);
    }


    public function encyclopedia_createVP()
    {
        if (get_option('encyclopedia_run_init_createVP') == 1) {
            $this->encyclopedia_setupVP('Encyclopedia Content', 'encyclopedia-content', '[ech_encyclopedia_single_post_output]');

            // Delete this option once all VP are created.
            delete_option('encyclopedia_run_init_createVP');
        }
    }

    private static function encyclopedia_setupVP($pageTitle, $pageSlug, $pageShortcode)
    {
        // Get parent page and get its id
        $get_parent_page = get_page_by_path('encyclopedia');
        $v_page = array(
            'post_type' => 'page',
            'post_title' => $pageTitle,
            'post_name' => $pageSlug,
            'post_content' => $pageShortcode,  // shortcode from this plugin
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
            'post_parent' => $get_parent_page->ID,
        );

        wp_insert_post($v_page, true);
    } // encyclopedia_setupVP




    /******************************** VP SHORTCODE ********************************/

    public function ech_encyclopedia_single_post_output($atts)
    {
        if (!get_option('encyclopedia_run_init_createVP')) {
            global $wp;

            $atts = shortcode_atts(array(
                'postid'    => isset($_GET['postid']) ? sanitize_key($_GET['postid']) : '',
            ), $atts);


            $postID  = $atts['postid'];

            if (!isset($postID) || empty($postID)) {
                echo '<script>window.location.replace("/encyclopedia");</script>';
            }

            $args = array(
                'postid' => $postID,
            );

            $api_link = parent::gen_post_api_link($args);
            $get_post_json = parent::wp_remote_get_encyclopedia_json($api_link);
            $json_arr = json_decode($get_post_json, true);

            if (!isset($json_arr['id']) || empty($json_arr['id'])) {
                echo '<script>window.location.replace("/encyclopedia");</script>';
            }

            $post = $json_arr;
            $post_title = $post['title'];
            $post_content = parent::echoLang([$post['acf']['content_en'], $post['acf']['content_zn'], $post['acf']['content_sc']]);
            $encyclopedia_cat_id = [];
            $encyclopedia_cat_name = [];
            $brand_category_id = [];
            $brand_category_name = [];
            $spec_cat_id = [];
            $spec_cat_name = [];

            foreach ($post['encyclopedia_category'] as $cat) {
                array_push($encyclopedia_cat_id, $cat['id']);
                array_push($encyclopedia_cat_name, parent::echoLang([$cat['name_en'],$cat['name_zh'],$cat['name_sc']]));
            }

            foreach ($post['brand_category'] as $brand) {
                array_push($brand_category_id, $brand['id']);
                array_push($brand_category_name, parent::echoLang([$brand['name_en'],$brand['name_zh'],$brand['name_sc']]));
            }

            foreach ($post['spec_category'] as $spec) {
                array_push($spec_cat_id, $spec['id']);
                array_push($spec_cat_name, $this->echoLang([$spec['name_en'],$spec['name_zh'],$spec['name_sc']]));
            }

            $html = '';

            $html .= '<div class="ech-encyclopedia-single-post-container" data-post="' . $post['id'] . '" data-cat="' . implode(',', $encyclopedia_cat_id) . '" data-brand="' . implode(',', $brand_category_id) . '">';
            
            $html .= '<div class="post-content-container">';
            $html .= '<h1 class="encyclopedia-heading-title">' . $post_title . '</h1>';
            $html .= '<div class="post-info">';
            $html .= '<ul>';
            $html .= '<li class="post-date"><i aria-hidden="true" class="fas fa-calendar"></i> ' . date('d m月, Y', strtotime($post['published_date'])) . '</li>';
            if($encyclopedia_cat_name){
                $html .= '<li class="post-cat"><i aria-hidden="true" class="fas fa-tags"></i> ' . implode(', ', $encyclopedia_cat_name) . '</li>';
            }
            if($spec_cat_name){
                $html .= '<li class="post-spec"><i aria-hidden="true" class="fas fa-stethoscope"></i> ' . implode(', ', $spec_cat_name) . '</li>';
            }
            $html .= '</ul>';

            $html .= '<div class="back-to-encyclopedia-list">';
            $html .= '<a href="'. site_url() .'/encyclopedia/"> < ' . parent::echoLang(['Back to Medical Health Encyclopedia', '返回醫思健康百科', '返回医思健康百科']) . '</a>';
            $html .= '</div>'; //.back-to-encyclopedia-list
            $html .= '</div>'; // .post-info

            $html .= '<div class="encyclopedia-content-container">';
            $html .= '<div class="post-content">' . $post_content . '</div>'; // .post_content
            
            $html .='<small class="post-tnc">'.parent::echoLang([$post['acf']['t&c_en'], $post['acf']['t&c_zn'], $post['acf']['t&c_sc']]).'</small>';

            $html .= '</div>'; // .encyclopedia-content-container

            $html .= '</div>'; // .post-content-container
            $html .= '<div class="related-encyclopedia">';
            $html .= '<h3 class="related-encyclopedia-title">' . parent::echoLang(['Related Posts', '相關文章', '相关文章']) . '</h3>';
            $html .= '<ul class="related-encyclopedia-list">';
            if ($post['acf']['related_posts']) {
                foreach ($post['acf']['related_posts'] as $related_post) {
                    $html .= '<li><h6>'. $related_post['post_title'].'</h6>';
                    $html .= '<a href="'. site_url() .'/encyclopedia/encyclopedia-content/?postid='. $related_post['id'].'">' . parent::echoLang(['Read More','閱讀更多','阅读更多']) . '>></a>';
                    $html .= '</li>';
                }
            }else {
                $html .= '<li>' .parent::echoLang(['No Related Posts','無相關文章','无相关文章']) .'</li>';
            }
            $html .= '</ul>';
            $html .= '</div>';
            $html .= '</div>'; // .ech-encyclopedia-single-post-container

            return $html;
        } // if encyclopedia_run_init_createVP
    }  //--end ech_news_single_post_output()

    /******************************** (end) VP SHORTCODE ********************************/



} // class
