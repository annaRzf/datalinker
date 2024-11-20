<?php
if( !defined( 'DATALINKER_VERSION' ) )
    define( 'DATALINKER_VERSION', '1.0.0' );

require_once DATALINKER__PLUGIN_DIR . 'includes/trait/trait-dl-html-render.php';
require_once DATALINKER__PLUGIN_DIR . 'includes/trait/trait-dl-helpers.php';
require_once DATALINKER__PLUGIN_DIR . 'includes/class/class-dl-export.php';
require_once DATALINKER__PLUGIN_DIR . 'includes/class/class-dl-filters.php';
final class DataLinkeRGeneral
{
    static $instance = false;
    static private $datalinker_pages = array('dl-export','dl-import');

    private function __construct()
    {
        add_action('admin_menu', array($this,'admin_menu') );
        add_action( 'admin_enqueue_scripts' , array($this,"enqueue_scripts") );
        add_filter('script_loader_tag', array($this,'initialize_module_tag') , 10, 3);

        // Initialize DataLinkeRFilters
        new DataLinkeRFilters();
    }
    
    public function enqueue_scripts()
    {
        $page = isset( $_GET[ 'page' ] ) ? sanitize_text_field( $_GET[ 'page' ] ) : "";

        $version = time();
        // include the styles only on the plugin pages
        if ( !in_array($page, self::$datalinker_pages) ) {
            return;
        }
        // add font awesome
        wp_enqueue_script( 'font-awesome', 'https://kit.fontawesome.com/feb19a29e3.js');
        // add select2 library
        wp_enqueue_script( 'select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js');
        wp_enqueue_style( 'select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',);
        // add style
        wp_enqueue_style( 'dl-style', plugins_url( '../../assets/css/style.css', __FILE__ ), array(), $version );
        // index script
        wp_enqueue_script( 'dl-index', plugins_url( '../../assets/js/index.js', __FILE__ ), array('jquery'), $version, true );
        // localize with the version
        wp_localize_script('dl-index', 'dl_object', array(
            'version' => $version
        ));
        // export script
        wp_enqueue_script( 'dl-export', plugins_url( '../../assets/js/dl-export.js', __FILE__ ), array('jquery'), $version, true );
        wp_localize_script( 'dl-export', 'pl_export_object', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
        ) );
        // import script
        wp_enqueue_script( 'dl-import', plugins_url( '../../assets/js/dl-import.js', __FILE__ ), array('jquery'), $version, true );
        wp_localize_script( 'dl-import', 'pl_import_object', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
        ) );
        // general script
        wp_enqueue_script( 'dl-general', plugins_url( '../../assets/js/dl-general.js', __FILE__ ), array('jquery'), $version, true );
    }

    public function initialize_module_tag($tag, $handle, $src) {
        // if not your script, do nothing and return original $tag
        $scirpt_modules = array('dl-export','dl-import','dl-general');
        if ( !in_array($handle,$scirpt_modules) ) {
            return $tag;
        }
        // change the script tag by adding type="module" and return it.
        $tag = '<script type="module" src="' . esc_url( $src ) . '"></script>';
        return $tag;
    }

    public function admin_menu()
    {
        // add top level menu page
        add_menu_page(
            'DataLinkeR', // page title
            'DataLinkeR', // menu title
            'manage_options', // capability
            'dl-export', // menu slug
            array( $this, 'export_menu'), // function
            'dashicons-admin-generic', // icon url
            54 // position
        );
    
        // add submenu pages
        add_submenu_page(
            'dl-export', // parent slug
            'Export Data', // page title
            'Export Data', // menu title
            'manage_options', // capability
            'dl-export', // menu slug
            array( $this, 'export_menu') // function
        );
    
        add_submenu_page(
            'dl-export', // parent slug
            'Import Data', // page title
            'Import Data', // menu title
            'manage_options', // capability
            'dl-import', // menu slug
            array( $this, 'import_menu') // function
        );
    }

    public function export_menu()
    {
        // Display export form
        require_once( DATALINKER__PLUGIN_DIR . 'includes/partials/export-form.php' );
    }

    public function import_menu()
    {
        // Display import form
        require_once( DATALINKER__PLUGIN_DIR . 'includes/partials/import-form.php' );
    }

    public static function getInstance() 
    {
		if ( !self::$instance )
			self::$instance = new self;
		return self::$instance;
	}
}