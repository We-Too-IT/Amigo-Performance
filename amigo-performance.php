<?php

/**
 * @package amigo-performance
 * @author Amigo Dheena
 
 * Plugin Name:       Amigo Performance
 * Plugin URI:        https://github.com/AmigoDheena/Amigo-Performance
 * Description:       A WordPress Plugin to Optimize Website Performance and improve Site Score.
 * Version:           0.1
 * Author:            Amigo Dheena
 * Author URI:        https://www.amigodheena.xyz
 * Text Domain:       amigo-performance
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

if (!defined('ABSPATH')) {
    die;
}

include_once ( ABSPATH . 'wp-admin/includes/file.php' ); // to get get_home_path() function work
include_once ( ABSPATH . 'wp-admin/includes/plugin.php' ); // to is_plugin_active()() function work
    
// Define plugin version for future releases
if (!defined('AMIGOPERF_PLUGIN_VERSION')) {
    define('AMIGOPERF_PLUGIN_VERSION', '0.1');
}

class AmigoPerformancePlugin{
    public $amigoPerf_hfn = 'amigoPerf_hfn'; //hidden field name
    public $amigoPerf_PluginName = 'Amigo Performance';
    public $amigoPerf_PluginVersion;
    public $amigoPerf_rqs;
    public $amigoPerf_remoji;
    public $amigoPerf_defer;
    public $amigoPerf_iframelazy;
    
    function amigoperformance_activate()
    {
        update_option( $this->amigoPerf_update_checker, AMIGOPERF_PLUGIN_VERSION );
        return AMIGOPERF_PLUGIN_VERSION;
    }

    function amigoperformance_deactivate()
    {
        flush_rewrite_rules();
    }

    // Check plugin versioin
    function amigoPerf_update_checker() {        
        $version = get_option( $this->amigoPerf_PluginVersion ); 
        if( version_compare($version, AMIGOPERF_PLUGIN_VERSION , '<')) {
            // Do some special things when we update to 2.0.0.
        }
    }

    function amigoPerf_is_current_version(){
        return version_compare($this->version, AMIGOPERF_PLUGIN_VERSION, '=') ? true : false;
    }

    // Enqueue Style sheets and Scripts
    function amigoperformance_enqueue_style(){
        wp_enqueue_style('amigoperf_style', plugins_url('assets/css/style.css',__FILE__));
    }

    // Register Style sheets and Scripts
    function amigoperformance_register(){
        add_action('admin_enqueue_scripts', array($this , 'amigoperformance_enqueue_style'));
    }

    public function amigoPerf_Default(){
        global $amigoPerf_rqs_opt, $amigoPerf_remoji_opt, $amigoPerf_defer_opt, $amigoPerf_iframelazy_opt;

        $this->amigoPerf_PluginVersion = ( get_option($this->amigoPerf_PluginVersion) ? get_option($this->amigoPerf_PluginVersion) : AMIGOPERF_PLUGIN_VERSION );

        $this->amigoPerf_rqs_opt = ( FALSE !== get_option($this->amigoPerf_rqs) ? get_option($this->amigoPerf_rqs) : TRUE  ); 
        $this->amigoPerf_rqs = 'amigoPerf_rqs';
        $this->amigoPerf_rqs_val = $amigoPerf_rqs_opt;

        $this->amigoPerf_remoji_opt = ( FALSE !== get_option($this->amigoPerf_remoji) ? get_option($this->amigoPerf_remoji) : TRUE  ); 
        $this->amigoPerf_remoji = 'amigoPerf_remoji';
        $this->amigoPerf_remoji_val = $amigoPerf_remoji_opt;
        
        $this->amigoPerf_defer_opt = ( FALSE !== get_option($this->amigoPerf_defer) ? get_option($this->amigoPerf_defer) : TRUE  ); 
        $this->amigoPerf_defer = 'amigoPerf_defer';
        $this->amigoPerf_defer_val = $amigoPerf_defer_opt;
        
        $this->amigoPerf_iframelazy_opt = ( FALSE !== get_option($this->amigoPerf_iframelazy) ? get_option($this->amigoPerf_iframelazy) : TRUE  ); 
        $this->amigoPerf_iframelazy = 'amigoPerf_iframelazy';
        $this->amigoPerf_iframelazy_val = $amigoPerf_iframelazy_opt;
    }
        
    public function amigoperf_hiddenField(){
        if (isset($_POST[$this->amigoPerf_hfn]) && $_POST[$this->amigoPerf_hfn] === 'Y') {

            $this->amigoPerf_rqs_val = ( FALSE!== isset($_POST[$this->amigoPerf_rqs]) ? FALSE!== $_POST[$this->amigoPerf_rqs] : FALSE);
            if (is_bool($this->amigoPerf_rqs_val)) {
                update_option( $this->amigoPerf_rqs, $this->amigoPerf_rqs_val );
            }

            $this->amigoPerf_remoji_val = (FALSE!== isset($_POST[$this->amigoPerf_remoji]) ? FALSE!== $_POST[$this->amigoPerf_remoji] : FALSE);
            if (is_bool($this->amigoPerf_remoji_val)) {
                update_option( $this->amigoPerf_remoji, $this->amigoPerf_remoji_val );
            }
            
            $this->amigoPerf_defer_val = (FALSE!== isset($_POST[$this->amigoPerf_defer]) ? FALSE!== $_POST[$this->amigoPerf_defer] : FALSE);
            if (is_bool($this->amigoPerf_defer_val)) {
                update_option( $this->amigoPerf_defer, $this->amigoPerf_defer_val );
            }
            
            $this->amigoPerf_iframelazy_val = (FALSE!== isset($_POST[$this->amigoPerf_iframelazy]) ? FALSE!== $_POST[$this->amigoPerf_iframelazy] : FALSE);
            if (is_bool($this->amigoPerf_iframelazy_val)) {
                update_option( $this->amigoPerf_iframelazy, $this->amigoPerf_iframelazy_val );
            }

            flush_rewrite_rules();
        }
    }

    public function amigoPerf_rqs_query($src)
    {
        if(strpos( $src, '?ver=' ))
        $src = remove_query_arg( 'ver', $src );
        return $src;
    }

    public function amigoPerf_rqs_operation()
    {
        if($this->amigoPerf_rqs_opt == get_option($this->amigoPerf_rqs)) {
            if(!is_admin()) {
                add_filter( 'style_loader_src', array($this,'amigoPerf_rqs_query'), 10, 2 );
                add_filter( 'script_loader_src', array($this,'amigoPerf_rqs_query'), 10, 2 );
            }
        }
    }

    public function amigoPerf_remoji_operation()
    {
        if($this->amigoPerf_remoji_opt == get_option($this->amigoPerf_remoji)) {
            remove_action( 'wp_head', 'print_emoji_detection_script', 7 ); 
            remove_action( 'admin_print_scripts', 'print_emoji_detection_script' ); 
            remove_action( 'wp_print_styles', 'print_emoji_styles' ); 
            remove_action( 'admin_print_styles', 'print_emoji_styles' );
        }
    }

    public function amigoPerf_defer_operation(){
        if($this->amigoPerf_defer_opt == get_option($this->amigoPerf_defer)) {
            if(!is_admin()) {
                add_filter( 'script_loader_tag', function ( $tag, $handle ) {
                    if(is_front_page()) {
                        if ( 'jquery-core' == $handle){ return $tag; } 
                    } else {
                    return $tag;
                    }
                    return str_replace( ' src', ' defer="defer" src', $tag );
                }, 10, 2 );
            }
        }
    }

    public function amigoPerf_iframelazy_operation()
    {
        //Inline Footer
        function inline_footer(){ 
            $lazyscript = '
            <script>
                /* 
                URL: https://github.com/AmigoDheena/amigolazy
                Author: Amigo Dheena
                */
                let amframe = document.querySelectorAll(".amigolazy");
                window.onload = function(){
                    for(let i=0; i<amframe.length;i++){
                        let amsrc = amframe[i];
                        let amdata = amsrc.getAttribute("data-src");
                        let datanew = amsrc.getAttribute("lazy");
                        if(datanew === null){
                        datanew = 1500;
                        }
                        setTimeout(function(){
                        amframe[i].setAttribute("src",amdata);
                        console.info(datanew + "ms Lazyloaded " + amframe[i].src);
                        }, datanew);
                    }
                }
            </script>';
            _e($lazyscript, 'amigo-performance');
        }
        //Inline Footer
        add_action('wp_footer', 'inline_footer', 100);
    }

    public function link_rel_buffer_callback($buffer) {
        $buffer = preg_replace('/iframe(.src)/m', 'iframe class="amigolazy" lazy="3000" data-src', $buffer);
        return $buffer;
    }

    public function link_rel_buffer_start() {
        ob_start(array($this,'link_rel_buffer_callback'));
    }

    public function link_rel_buffer_end() {
        ob_flush();
    } 

    public function amigoPerf_iframelazy_execute(){
        if($this->amigoPerf_iframelazy_opt == get_option($this->amigoPerf_iframelazy)) {

            // Inline amigolazy script at footer
            $this->amigoPerf_iframelazy_operation();

            //str replace
            if (!is_admin()) {
                add_action('template_redirect', array($this,'link_rel_buffer_start'));
                add_action('get_header', array($this,'link_rel_buffer_start'));
                add_action('wp_footer', array($this,'link_rel_buffer_end'));
            }
        }
    }
    
   // Register Menu Page
    public function amigoperformance_add_pages() {
        add_menu_page(
            'Amigo Performance Plugin', //Page title
            'Amigo Perf', //Menu title
            'manage_options', //capability
            'amigo_performance', //menu_slug
            array($this, 'amigoPerf_newpage'), //function
            'dashicons-buddicons-activity' //icon url
        );
    }

    public function amigoPerf_newpage(){
        require_once plugin_dir_path(__FILE__).'admin.php';
    }

    function amigoPerf_reg_menu(){
        add_action('admin_menu', array($this, 'amigoperformance_add_pages'));
    }

}

if (class_exists('AmigoPerformancePlugin')) {
    $amigoperformanceplugin = new AmigoPerformancePlugin();
    $amigoperformanceplugin -> amigoperformance_register();

    $amigoPerfDefault = new AmigoPerformancePlugin();
    $amigoPerfDefault -> amigoPerf_Default();
    $amigoPerfDefault -> amigoperf_hiddenField();
    $amigoPerfDefault -> amigoPerf_rqs_query('details');
    $amigoPerfDefault -> amigoPerf_rqs_operation(); //Remove Query Strings Operation
    $amigoPerfDefault -> amigoPerf_remoji_operation(); //Remove Emoji Operation
    $amigoPerfDefault -> amigoPerf_defer_operation(); //Defer parsing of JavaScript
    $amigoPerfDefault -> amigoPerf_iframelazy_execute(); //Iframe Lazyload  
    $amigoPerfDefault -> amigoPerf_reg_menu(); //Register Menu
    $amigoPerfDefault -> amigoPerf_update_checker(); //Update checker
}
// Activation
register_activation_hook(__FILE__,array($amigoperformanceplugin,'amigoperformance_activate'));

// Deactivation
register_deactivation_hook(__FILE__,array($amigoperformanceplugin,'amigoperformance_deactivate'));

// Uninstall