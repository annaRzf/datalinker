<?php

class CM_Importer_Exporter
{
    static $instance = false;

    private function __construct()
    {
        $this->initiliaze_ajax_actions();
        add_action('admin_menu', array($this,'admin_menu') );
        add_action( 'admin_enqueue_scripts' , array($this,"enqueue_scripts") );
        add_filter('script_loader_tag', array($this,'initialize_module_tag') , 10, 3);
    }
    
    public function enqueue_scripts()
    {
        $version = time();
        wp_enqueue_script( 'pl-index', plugins_url( '../../assets/js/index.js', __FILE__ ), array('jquery'), $version, true );
        wp_enqueue_script( 'pl-export', plugins_url( '../../assets/js/pl-export.js', __FILE__ ), array('jquery'), $version, true );
        wp_localize_script( 'pl-export', 'pl_export_object', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
        ) );
        wp_enqueue_script( 'pl-import', plugins_url( '../../assets/js/pl-import.js', __FILE__ ), array('jquery'), $version, true );
        wp_localize_script( 'pl-import', 'pl_import_object', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
        ) );
        // localize with the version
        wp_localize_script('pl-index', 'pl_object', array(
            'version' => $version
        ));
    }

    public function initialize_module_tag($tag, $handle, $src) {
        // if not your script, do nothing and return original $tag
        $scirpt_modules = array('pl-export','pl-import');
        if ( !in_array($handle,$scirpt_modules) ) {
            return $tag;
        }
        // change the script tag by adding type="module" and return it.
        $tag = '<script type="module" src="' . esc_url( $src ) . '"></script>';
        return $tag;
    }

    private function initiliaze_ajax_actions()
    {
        add_action('wp_ajax_pl_exporter_fetch_data', array($this,'fetch_data'));
        add_action('wp_ajax_pl_importer_insert_data', array($this,'insert_data'));
    }

    public function admin_menu()
    {
        // add top level menu page
        add_menu_page(
            'PL Importer/Exporter', // page title
            'PL Importer/Exporter', // menu title
            'manage_options', // capability
            'pl-export', // menu slug
            array( $this, 'export_menu'), // function
            'dashicons-admin-generic', // icon url
            54 // position
        );
    
        // add submenu pages
        add_submenu_page(
            'pl-export', // parent slug
            'Export Data', // page title
            'Export Data', // menu title
            'manage_options', // capability
            'pl-export', // menu slug
            array( $this, 'export_menu') // function
        );
    
        add_submenu_page(
            'pl-export', // parent slug
            'Import Data', // page title
            'Import Data', // menu title
            'manage_options', // capability
            'pl-import', // menu slug
            array( $this, 'import_menu') // function
        );
    }

    public function export_menu()
    {
        // Display export form
        require_once( IMP_EXP__PLUGIN_DIR . 'includes/partials/export-form.php' );
    }

    public function import_menu()
    {
        // Display import form
        require_once( IMP_EXP__PLUGIN_DIR . 'includes/partials/import-form.php' );
    }

    public function fetch_data()
    {
        $post_type = isset( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : 'post';
        
        // Handle export request
        $post_type = isset( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : 'post';

        // Set CSV file name
        $filename = 'export-' . $post_type . '-' . date( 'Ymd' ) . '.csv';

        // Set CSV headers
        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        );

        // Create CSV file
        ob_start();
        $file = fopen( 'php://output', 'w' );

        // Get posts data
        $args = array(
            'post_type' => $post_type,
            'post_status' => 'publish', // Only retrieve published posts
            'posts_per_page' => -1, // Retrieve all posts
            'orderby' => 'title', // Order by post title
            'order' => 'ASC', // Sort in ascending order
        );
        $query = new WP_Query( $args );

        // Write CSV headers
        $csv_headers = array("Title","Content","Featured Image","Categories","ACF Festival Slug");
        fputcsv( $file, $csv_headers );

        // Write CSV rows
        foreach ( $query->posts as $post ) {
            $terms = wp_get_post_terms( $post->ID, "horse-racing-category", array( "fields" => "slugs" ) );
            // get only first terms
            $horse_racing_cat = isset($terms[0]) ? $terms[0] : '';
            // pre treatment of the content
            $content = preg_replace('/stga.punterslounge.com/', 'punterslounge.com', $post->post_content); //change stga link to punterslounge
            $content = preg_replace('/pre.punterslounge.com/', 'punterslounge.com', $post->post_content); //change pre link to punterslounge
            $post_data = array(
                $post->post_title,
                $post->post_content,
                get_the_post_thumbnail_url( $post->ID, 'full' ),
                $horse_racing_cat,
                get_field( 'festival_slug', $post->ID ),
            );
            fputcsv( $file, $post_data );
        }

        // Close CSV file
        fclose( $file );
        $csvData = ob_get_clean();
        // Send CSV file as AJAX response
        wp_send_json_success( array(
            'csv_data' => $csvData,
            'file_name' => $filename,
        ) );
        wp_die();
    }

    public function insert_data()
    {
        $post_type = isset( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : 'post';
        $file = isset( $_FILES['upload_file'] ) ? $_FILES['upload_file'] : '';
        $file_path = $file['tmp_name'];
        $file_name = $file['name'];
        $file_type = $file['type'];
        $file_error = $file['error'];
        $file_size = $file['size'];

        // Check if file is CSV
        if ( $file_type !== 'text/csv' ) {
            wp_send_json_error( array(
                'message' => 'Invalid file type. Please upload a CSV file.',
            ) );
            wp_die();
        }

        // Check if file is uploaded
        if ( $file_error !== UPLOAD_ERR_OK ) {
            wp_send_json_error( array(
                'message' => 'File upload error. Please try again.',
            ) );
            wp_die();
        }

        // Check if file is not empty
        if ( $file_size <= 0 ) {
            wp_send_json_error( array(
                'message' => 'File is empty. Please upload a valid CSV file.',
            ) );
            wp_die();
        }

        // Read CSV file
        $csv = array_map( 'str_getcsv', file( $file_path ) );
        $csv_headers = array_shift( $csv );

        // Insert posts
        $inserted_rows = 0;
        foreach ( $csv as $row ) {
            // check if the post already exists by title
            $args = array(
                'post_type' => $post_type,
                'post_title' => $row[0],
                'posts_per_page' => 1,
            );
            $posts = get_posts( $args );
            if( !empty($posts) ) {
                $post_id = $posts[0]->ID;
                $post_data = array(
                    'ID' => $post_id,
                    'post_content' => $row[1],
                );
                wp_update_post( $post_data );
                // update festival slug
                update_field( 'festival_slug', $row[4], $post_id );
            }else{
                $post_data = array(
                    'post_title' => $row[0],
                    'post_content' => $row[1],
                    'post_type' => $post_type,
                    'post_status' => 'publish',
                );
                $post_id = wp_insert_post( $post_data );
                // set the category
                if ( !empty( $row[3] ) ) {
                    $term = get_term_by( 'slug', $row[3], 'horse-racing-category' );
                    if ( $term ) {
                        wp_set_post_terms( $post_id, $term->term_id, 'horse-racing-category' );
                    }
                }
                // update festival slug
                update_field( 'festival_slug', $row[4], $post_id );
            }

            // Set featured image
            if ( !empty( $row[2] ) ) {
                $image_url = $row[2];
                $image_name = basename( $image_url );
                $upload_dir = wp_upload_dir();
                $image_data = file_get_contents( $image_url );
                $unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name );
                $filename = basename( $unique_file_name );
                if ( wp_mkdir_p( $upload_dir['path'] ) ) {
                    $file = $upload_dir['path'] . '/' . $filename;
                } else {
                    $file = $upload_dir['basedir'] . '/' . $filename;
                }
                file_put_contents( $file, $image_data );
                $wp_filetype = wp_check_filetype( $filename, null );
                $attachment = array(
                    'post_mime_type' => $wp_filetype['type'],
                    'post_title' => sanitize_file_name( $filename ),
                    'post_content' => '',
                    'post_status' => 'inherit',
                );
                $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
                require_once( ABSPATH . 'wp-admin/includes/image.php' );
                $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
                wp_update_attachment_metadata( $attach_id, $attach_data );
                set_post_thumbnail( $post_id, $attach_id );
            }

            $inserted_rows++;
        }
        // Return the number of inserted rows
        wp_send_json_success( array(
            'inserted_rows' => $inserted_rows,
        ) );
        wp_die();
    }

    public static function getInstance() 
    {
		if ( !self::$instance )
			self::$instance = new self;
		return self::$instance;
	}
}