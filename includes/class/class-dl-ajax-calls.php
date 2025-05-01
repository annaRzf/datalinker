<?php

class DataLinkeRAjax
{
    use DataLinkeRHelpers;
    use DataLinkeRHTMLRender;

    public function __construct()
    {
        add_action('wp_ajax_dl_get_posts', [$this, 'get_posts']);
        add_action('wp_ajax_dl_get_filters', [$this, 'get_filters']);
        add_action('wp_ajax_dl_get_rules', [$this, 'get_rules']);
    }

    public function get_posts()
    {
        // check security nonce
        if( !check_ajax_referer('dl-export-nonce', 'security') ) {
            wp_send_json_error(['message' => 'Invalid security nonce']);
        }

        $post_type = sanitize_text_field($_POST['post_type']);
        $taxonomy = isset($_POST['taxonomy']) ? sanitize_text_field($_POST['taxonomy']) : '';
        $this->log_data($post_type);
        $this->log_data($taxonomy);
        
    }

    public function get_filters()
    {
        // check security nonce
        if( !check_ajax_referer('dl-export-nonce', 'security') ) {
            wp_send_json_error(['message' => 'Invalid security nonce']);
        }

        $post_type = sanitize_text_field($_POST['post_type']);
        $taxonomy = isset($_POST['taxonomy']) ? sanitize_text_field($_POST['taxonomy']) : '';
        // get the filters
        $dl_export = new DataLinkeRExport();
        $filters = $dl_export->get_post_filters($post_type, $taxonomy);
        $this->log_data($filters);
        ob_start();
        include DATALINKER__PLUGIN_DIR . 'includes/partials/sections/filters.php';
        $filters_html = ob_get_clean();
        ob_end_clean();
        wp_send_json_success(['filters' => $filters_html]);
    }

    public function get_rules()
    {
        // check security nonce
        if( !check_ajax_referer('dl-export-nonce', 'security') ) {
            wp_send_json_error(['message' => 'Invalid security nonce']);
        }

        $field = sanitize_text_field($_POST['field']);
        // get the filters
        $dl_export = new DataLinkeRExport();
        $rules = $dl_export->get_post_rules($field);
        // TODO: render the rules dropdown properly
        ob_start();
        $dl_export->render_dropdown('export_rules',[]);
        $rules_html = ob_get_clean();
        wp_send_json_success(['rules' => $rules_html]);
    }
}