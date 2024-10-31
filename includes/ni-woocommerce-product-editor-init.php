<?php
if ( ! defined( 'ABSPATH' ) ) { exit;}
if( !class_exists( 'Ni_WooCommerce_product_Editor_Init' ) ) { 
	class Ni_WooCommerce_product_Editor_Init{
		var $niwpe_constant = array(); 
		public function __construct($niwpe_constant = array()){
			$this->niwpe_constant = $niwpe_constant; 
			add_action('admin_menu', 		array($this,'admin_menu'));	
			add_action('admin_enqueue_scripts',  array(&$this,'admin_enqueue_scripts' ));
			add_action('wp_ajax_niwpe_product_editor',  array(&$this,'ajax_niwpe_product_editor' )); 
		}
		function admin_menu(){
			
			add_menu_page( 'Ni Product Editor', 'Ni Product Editor', $this->niwpe_constant['manage_options'], $this->niwpe_constant['menu'], array( $this, 'add_page'), 'dashicons-edit', "58.6361" );
			add_submenu_page($this->niwpe_constant["menu"],"Dashboard","Dashboard", $this->niwpe_constant['manage_options'],$this->niwpe_constant["menu"], array( $this, 'add_page'));
			add_submenu_page($this->niwpe_constant["menu"],"Ni Product Editor","Ni Product Editor",  $this->niwpe_constant['manage_options'],'niwpe-product-editor', array( $this, 'add_page'));
			
	
		
		}
		function admin_enqueue_scripts(){
			if (isset($_REQUEST["page"])){
				$page = $_REQUEST["page"];
				if ($page =="niwpe-product-editor"){
					wp_enqueue_script('niwpe-ajax-pe', plugins_url( '../assets/js/script.js', __FILE__ ), array('jquery') );
					wp_localize_script('niwpe-ajax-pe','niwpe_pe_ajax_object',array('niwpe_pe_ajax_object_ajaxurl'=>admin_url('admin-ajax.php') ) );
					wp_enqueue_script('niwpe-product-editor-script', plugins_url( '../assets/js/niwpe-product-editor.js', __FILE__ ) );
					
					wp_register_style('niwpe-style', plugins_url( '../assets/css/niwpe-style.css', __FILE__ ));
					wp_enqueue_style( 'niwpe-style');
					
					
				}
				if ($page =="niwpe-product-dashboard"){
					wp_register_style('niwpe-dashboard-style', plugins_url( '../assets/css/niwpe-dashboard.css', __FILE__ ));
					wp_enqueue_style( 'niwpe-dashboard-style');
				}
				//niwpe-dashboard
				
			}
		}
		function ajax_niwpe_product_editor(){
			if (isset($_REQUEST["sub_action"])){
				$sub_action = $_REQUEST["sub_action"];
				if ($sub_action == "niwpe_product_editor"){
					include_once("niwpe-product-editor.php");
					$obj =  new NiWPE_Product_Editor();
					$obj->get_niwpe_ajax();
				}
				if ($sub_action == "niwpe_product_update"){
					include_once("niwpe-product-editor.php");
					$obj =  new NiWPE_Product_Editor();
					$obj->get_niwpe_ajax();
				}
			}
			die;
		}
		function add_page(){
			if (isset($_REQUEST["page"])){
				$page = $_REQUEST["page"];
				if ($page == "niwpe-product-editor"){
					include_once("niwpe-product-editor.php");
					$obj =  new NiWPE_Product_Editor();
					$obj->get_niwpe_page();
				}
				if ($page == "niwpe-product-dashboard"){
					include_once("niwpe-dashboard.php");
					$obj =  new NiWPE_Dashboard();
					$obj->get_niwpe_page();
				}
				//
			}
		}
	}
}
?>