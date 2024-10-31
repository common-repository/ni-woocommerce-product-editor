<?php
/*
Plugin Name: Ni WooCommerce Bulk Product Editor
Description: Ni WooCommerce bulk product edit plugin provides a list of all product simple, variation and variation products  where store admin can edit or update the product details.
Author: anzia
Version: 1.4.5
Author URI: http://naziinfotech.com/
Plugin URI: https://wordpress.org/plugins/ni-woocommerce-product-editor/
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/agpl-3.0.html
Requires at least: 4.7
Tested up to: 6.5.3
WC requires at least: 3.0.0
WC tested up to: 8.9.1
Last Updated Date: 31-May-2024
Requires PHP: 7.0
*/
if ( ! defined( 'ABSPATH' ) ) { exit;}
if( !class_exists( 'Ni_WooCommerce_Product_Editor' ) ) {
	class Ni_WooCommerce_Product_Editor{
		var $niwpe_constant = array(); 
		function __construct()
		 {
			 $this->niwpe_constant = array(
			 	"prefix" 		  => "niwpe-",
				 "manage_options" => "manage_options",
				 "menu"   		  => "niwpe-product-dashboard",
				);
			include("includes/ni-woocommerce-product-editor-init.php");
			$obj_niwpe_init =  new Ni_WooCommerce_product_Editor_Init($this->niwpe_constant);
		 } 
	}
	$obj_niwpe = new Ni_WooCommerce_Product_Editor();
}
?>