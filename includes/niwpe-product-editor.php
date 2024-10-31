<?php
if ( ! defined( 'ABSPATH' ) ) { exit;}
if( !class_exists( 'NiWPE_Product_Editor' ) ) {  
	include_once("niwpe-function.php");
	class NiWPE_Product_Editor extends NiWPE_Function{
		var $per_page = 100;
		function __construct(){
		}
		function get_niwpe_page(){
			$input_type = "text";
			$input_type = "hidden";	
		
			$start_date = $this->get_request("start_date",date_i18n("Y-m-d"));
			$end_date = $this->get_request("end_date",date_i18n("Y-m-d"));
		
			$product_type =	isset($_REQUEST["product_type"])?$_REQUEST["product_type"] : 'simple_product';
			$page =	isset($_REQUEST["page"])?$_REQUEST["page"] : '';
			$page_titles 				= array(
						'simple_product'			=> __('Simple Product',		'nisalesreportpro')
						,'variable_product'		 	=> __('Variable Product',	'nisalesreportpro')
						,'variation_product'		=> __('Variation Product',	'nisalesreportpro')				
					);
			?>
			<h2 class="nav-tab-wrapper woo-nav-tab-wrapper hide_for_print">
			<div class="responsive-menu"><a href="#" id="menu-icon"></a></div>
			<?php            	
			   foreach ( $page_titles as $key => $value ) {
					echo '<a href="'.admin_url( 'admin.php?page='.$page.'&product_type=' . urlencode( $key ) ).'" class="nav-tab ';
					if ( $product_type == $key ) echo 'nav-tab-active';
					echo '">' . esc_html( $value ) . '</a>';
			   }
			?>
			</h2>
          <div class="niwpe_container">
            <div class="niwpe_content">
                <div class="niwpe_search_form">
                    <form id="frm_niwpe_product_editor" name="frm_niwpe_product_editor">
                       
                        <div class="niwpe_search_row">
                            <div class="niwpe_field_wrapper">
                                <label for="product_name">Product Name</label>
                                <select id="product_name" name="product_name" class="">
                                    <option value="-1" selected="selected">--Select One--</option>
                                    <?php 
        
                                 $data = array();
                                 if ($product_type == "simple_product"){
                                    $data = $this->get_simple_product();
                                 }
                                 if ($product_type == "variable_product"){
                                    $data = $this->get_variable_product();
                                 }
                                 if ($product_type == "variation_product"){
                                    $data = $this->get_variation_product();
        
                                 }
                                 foreach($data as $key=>$value){
                                 ?>
                                        <option value="<?php echo $key; ?>">
                                            <?php echo $value; ?>
                                        </option>
                                        <?php
                                 }
                                 ?>
                                </select>
                            </div>
                            <div class="niwpe_field_wrapper">
                                <label for="backorders">Backorders:</label>
                                <select id="backorders" name="backorders" class="">
                                    <option value="-1" selected="selected">--Select One--</option>
                                    <option value="no">Do not allow</option>
                                    <option value="notify">Allow, but notify customer</option>
                                    <option value="yes">Allow</option>
                                </select>
                            </div>
                            <div style="clear:both"></div>
                        </div>
                        
                        <div class="niwpe_search_row">
                            <div class="niwpe_field_wrapper">
                                <label for="search_product">Search  product</label>
                                <input type="text" name="search_product" id="search_product" />
                            </div>
                            <div class="niwpe_field_wrapper">
                                <label for="product_sku">Product SKU</label>
                                 <input type="text" name="product_sku" id="product_sku" />
                            </div>
                            <div style="clear:both"></div>
                        </div>
                        <div style="clear:both"></div>
                        <div class="niwpe_search_row">
                            <div style="padding:5px; padding-right:23px;">
                                <input type="submit" value="Search" class="niwpe_button" />
                                <div style="clear:both"></div>
                            </div>
                            <div style="clear:both"></div>
                        </div>
                        <input type="<?php echo $input_type; ?>" name="per_page" value="<?php echo $this->per_page; ?>" />
                        <input type="<?php echo $input_type; ?>" name="p" value="0" />
                        <input type="<?php echo $input_type; ?>" name="page" value="<?php echo $_REQUEST["page"]; ?>" />
                        <input type="<?php echo $input_type; ?>" name="action" value="niwpe_product_editor" />
                        <input type="hidden" name="product_type" value="<?php echo $product_type; ?>" />
                        <input type="hidden" name="sub_action"  value="niwpe_product_editor"/>
                    </form>
                </div>
                <div class="_ajax_niwpe_save ni-success-msg" style="display:none">
                </div>
                <div style="margin-top:20px;">
                    <div class="_ajax_niwpe_content"></div>
                </div>
                
             </div>
           </div>
		
		<?php	
		}
		function get_simple_product_query($type="row"){
		
			global $wpdb;
			$start = 0;
		    $per_page   			= $this->get_request("per_page");
		    $stock_status			= $this->get_request("stock_status");
			$backorders				= $this->get_request("backorders");
			$manage_stock			= $this->get_request("manage_stock");
		    $product_name			= $this->get_request("product_name");
			
			$search_product			= $this->get_request("search_product");
			$product_sku			= $this->get_request("product_sku");
		 	
			
			$meta_key				=  $this->get_item_meta_key_list() ;
			//$meta_key				= array();
			$p   					= $this->get_request("p");
			if($p > 1){	$start = ($p - 1) * $per_page;}
			
			
			
			$query  = "";
			
			
			$query .=" SELECT    ";
			$query .=" post.ID as product_id ";
			$query .=", post.post_title as product_name ";
		
			$query .=" FROM {$wpdb->prefix}posts as post  ";
			
			$query .=" LEFT JOIN {$wpdb->prefix}term_relationships as relationships ON relationships.object_id=post.ID ";
			
			$query .=" LEFT JOIN {$wpdb->prefix}term_taxonomy as term_taxonomy ON term_taxonomy.term_taxonomy_id=relationships.term_taxonomy_id ";
			
			$query .=" LEFT JOIN {$wpdb->prefix}terms as terms ON terms.term_id=term_taxonomy.term_id ";
			
			
			
			if ($backorders !="-1"){
				$query .=" LEFT JOIN {$wpdb->prefix}postmeta as backorders ON backorders.post_id=post.ID ";
			}
			
			if ($product_sku !==''){
				$query .=" LEFT JOIN {$wpdb->prefix}postmeta as sku ON sku.post_id=post.ID ";
			}
			
			
			
			$query .=" WHERE 1=1 ";
			$query .=" AND post.post_type='product'";
			$query .=" AND post.post_status='publish'";
			$query .=" AND terms.name='simple'";
			
			
			if ($backorders !="-1"){
				$query .=" AND backorders.meta_key='_backorders'";
				$query .=" AND backorders.meta_value='{$backorders}'";
			}
			
			
			if ($product_sku !==''){
				$query .=" AND sku.meta_key='_sku'";
				$query .=" AND sku.meta_value LIKE '%{$product_sku}%'";
			}
			
			
			if ($product_name	 !="-1"){
				$query .=" AND post.ID='{$product_name}'";
			}
			if ($search_product	 !==''){
				$query .=" AND post.post_title LIKE '%{$search_product}%'";
			}
			
		
			
			
			$query .=" ORDER BY post.post_title ";
			
			if ($type=="count"){
				$row = $wpdb->get_results( $query);			
				 $row = count($row);
			}
			elseif($type=="export"){
				$row = $wpdb->get_results( $query);		
			}else{
				$query .= "LIMIT {$start} , {$per_page}";
				$row = $wpdb->get_results( $query);			
			}
		
			if ($type =="row" || $type=="export"){
				
				foreach($row as $key=>$value){
					$product_id =$value->product_id ;
					$all_meta = $this->get_order_postmeta($product_id,$meta_key);
					foreach($all_meta as $k=>$v){
						$row[$key]->$k =$v;
					}
				}
			}
			//echo $query ;
			//$this->print_data($row);	
			return  $row;
		}
		function get_variable_product_query($type ="row"){
			
			global $wpdb;
			$start = 0;
		    $per_page   			= $this->get_request("per_page");
			
			$stock_status			= $this->get_request("stock_status");
			$backorders				= $this->get_request("backorders");
			$manage_stock			= $this->get_request("manage_stock");
		    $product_name			= $this->get_request("product_name");
			
			$search_product			= $this->get_request("search_product");
			$product_sku			= $this->get_request("product_sku");
			
			$meta_key				=  $this->get_item_meta_key_list() ;
			
			$p   					= $this->get_request("p");
			if($p > 1){	$start = ($p - 1) * $per_page;}
			
			$query  = "";
			
			$query .=" SELECT    ";
			$query .=" post.ID as product_id ";
			$query .=", post.post_title as product_name ";
			
			$query .=" FROM {$wpdb->prefix}posts as post  ";
			
			$query .=" LEFT JOIN {$wpdb->prefix}term_relationships as relationships ON relationships.object_id=post.ID ";
			
			$query .=" LEFT JOIN {$wpdb->prefix}term_taxonomy as term_taxonomy ON term_taxonomy.term_taxonomy_id=relationships.term_taxonomy_id ";
			
			$query .=" LEFT JOIN {$wpdb->prefix}terms as terms ON terms.term_id=term_taxonomy.term_id ";
			
			
			if ($backorders !="-1"){
				$query .=" LEFT JOIN {$wpdb->prefix}postmeta as backorders ON backorders.post_id=post.ID ";
			}
			
			if ($product_sku !==''){
				$query .=" LEFT JOIN {$wpdb->prefix}postmeta as sku ON sku.post_id=post.ID ";
			}
			
			
			$query .=" WHERE 1=1 ";
			$query .=" AND post.post_type='product'";
			$query .=" AND post.post_status='publish'";
			$query .=" AND terms.name='variable'";

			if ($backorders !="-1"){
				$query .=" AND backorders.meta_key='_backorders'";
				$query .=" AND backorders.meta_value='{$backorders}'";
			}
			
			if ($product_sku !==''){
				$query .=" AND sku.meta_key='_sku'";
				$query .=" AND sku.meta_value LIKE '%{$product_sku}%'";
			}
			
			if ($product_name	 !="-1"){
				$query .=" AND post.ID='{$product_name}'";
			}
			if ($search_product	 !==''){
				$query .=" AND post.post_title LIKE '%{$search_product}%'";
			}
			$query .=" ORDER BY post.post_title ";
			
			if ($type=="count"){
				$row = $wpdb->get_results( $query);			
				 $row = count($row);
			}
			elseif($type=="export"){
				$row = $wpdb->get_results( $query);		
			}else{
				$query .= "LIMIT {$start} , {$per_page}";
				$row = $wpdb->get_results( $query);			
			}
			
			if ($type =="row" || $type=="export"){
				foreach($row as $key=>$value){
					$product_id =$value->product_id ;
					$all_meta = $this->get_order_postmeta($product_id,$meta_key);
					foreach($all_meta as $k=>$v){
						$row[$key]->$k =$v;
					}
				}
			}
			
			return  $row;
		}
		function get_variation_product_query($type ="row"){
			
			global $wpdb;
			$start = 0;
		    $per_page   			= $this->get_request("per_page");
			
			$stock_status			= $this->get_request("stock_status");
			$backorders				= $this->get_request("backorders");
			$manage_stock			= $this->get_request("manage_stock");
		    $product_name			= $this->get_request("product_name");
			
			$search_product			= $this->get_request("search_product");
			$product_sku			= $this->get_request("product_sku");
			
			$meta_key				=  $this->get_item_meta_key_list() ;
			
			$p   					= $this->get_request("p");
			if($p > 1){	$start = ($p - 1) * $per_page;}
			
			$query  = "";
			
			$query  = "";
			$query .=" SELECT    ";
			$query .=" post.ID as product_id ";
			$query .=", post.post_title as product_name ";
			//$query .=", post_parent.post_title as product_name ";
			$query .=", post_parent.ID as parent_product_id ";
			
			$query .=" FROM {$wpdb->prefix}posts as post  ";
			
			$query .=" LEFT JOIN {$wpdb->prefix}posts as post_parent ON post_parent.ID=post.post_parent ";
			
			if ($backorders !="-1"){
				$query .=" LEFT JOIN {$wpdb->prefix}postmeta as backorders ON backorders.post_id=post.ID ";
			}
			
			if ($product_sku !==''){
				$query .=" LEFT JOIN {$wpdb->prefix}postmeta as sku ON sku.post_id=post.ID ";
			}
			
			$query .=" WHERE 1=1 ";
			$query .=" AND post.post_type='product_variation'";
			$query .=" AND post.post_status='publish'";
			
			
			if ($backorders !="-1"){
				$query .=" AND backorders.meta_key='_backorders'";
				$query .=" AND backorders.meta_value='{$backorders}'";
			}
			
			if ($product_sku !==''){
				$query .=" AND sku.meta_key='_sku'";
				$query .=" AND sku.meta_value LIKE '%{$product_sku}%'";
			}
			if ($search_product	 !==''){
				$query .=" AND post.post_title LIKE '%{$search_product}%'";
			}
			if ($product_name	 !="-1"){
				$query .=" AND post.ID='{$product_name}'";
			}
			$query .=" ORDER BY post.post_title ";
			
			if ($type=="count"){
				$row = $wpdb->get_results( $query);			
				 $row = count($row);
			}
			elseif($type=="export"){
				$row = $wpdb->get_results( $query);		
			}else{
				$query .= "LIMIT {$start} , {$per_page}";
				$row = $wpdb->get_results( $query);			
			}
			
			if ($type =="row" || $type=="export"){
				foreach($row as $key=>$value){
					$product_id =$value->product_id ;
					$all_meta = $this->get_order_postmeta($product_id,$meta_key);
					foreach($all_meta as $k=>$v){
						$row[$key]->$k =$v;
					}
				}
			}
			//echo $query ;
			//$this->print_data($row);	
			return  $row;
		}
		function get_niwpe_ajax(){
			
			
			if (isset($_REQUEST["sub_action"])){
				$sub_action = $_REQUEST["sub_action"];
				if ($sub_action == "niwpe_product_editor"){
					$this->get_niwpe_grid();
				}
				if ($sub_action == "niwpe_product_update"){
					$this->get_niwpe_product_update();
				}
			}
			//echo json_encode($_REQUEST);
			die;
		}
		function get_niwpe_grid(){
			$limit	 	  =	$this->get_request("per_page");
			$p   	 	  = $this->get_request("p");
			$product_name = $this->get_request("product_name");
			$backorders   = $this->get_request("backorders");
			$columns = $this->get_default_columns(); 
			$row =  array();
			$count = 0;
			$product_type = $_REQUEST['product_type'];
			if ($product_type =="simple_product"){
				$row = $this->get_simple_product_query("row");
				$count = $this->get_simple_product_query("count");
			}
			if ($product_type =="variable_product"){
				$row = $this->get_variable_product_query("row");
				$count = $this->get_variable_product_query("count");
			}
			if ($product_type =="variation_product"){
				$row = $this->get_variation_product_query("row");
				$count = $this->get_variation_product_query("count");
			}	
			?>
			<?php  $this->get_table($columns,$row ); ?>
			 <form id="frm_niwpe_product_editor_pagination" method="post">
				<input type="hidden" name="product_name" id="product_name" value="<?php echo $product_name;  ?>" />
				<input type="hidden" name="backorders" id="backorders" value="<?php echo $backorders;  ?>" />
			 </form>
			<?php
			
			echo  $this->pagination($count,$limit,$p,$url='?');	
		}
		function get_niwpe_product_update(){
			$product_id = isset($_REQUEST["product_id"])?$_REQUEST["product_id"]:0;
		    $ni_product_name  = isset($_REQUEST["ni_product_name"])?$_REQUEST["ni_product_name"]:'';
			$ni_sku 		  = isset($_REQUEST["ni_sku"])?$_REQUEST["ni_sku"]:'';
			$ni_sale_price    = isset($_REQUEST["ni_sale_price"])?$_REQUEST["ni_sale_price"]:0;
			$ni_regular_price = isset($_REQUEST["ni_regular_price"])?$_REQUEST["ni_regular_price"]:0;
			$ni_stock_status  = isset($_REQUEST["ni_stock_status"])?$_REQUEST["ni_stock_status"]: 'instock';
			$ni_stock_qty     = isset($_REQUEST["ni_stock_qty"])?$_REQUEST["ni_stock_qty"]: 0;
			$ni_backorders    = isset($_REQUEST["ni_backorders"])?$_REQUEST["ni_backorders"]:'no';
			$ni_manage_stock  = isset($_REQUEST["ni_manage_stock"])?$_REQUEST["ni_manage_stock"]:'no';
			
			 wp_update_post( array( 'ID'=> $product_id,'post_title'=> $ni_product_name));
			update_post_meta($product_id, '_sku', $ni_sku); 
			update_post_meta($product_id, '_sale_price', $ni_sale_price); 
			update_post_meta($product_id, '_regular_price', $ni_regular_price); 
			update_post_meta($product_id, '_stock_status', $ni_stock_status); 
			update_post_meta($product_id, '_stock', $ni_stock_qty); 
			update_post_meta($product_id, '_backorders', $ni_backorders); 
			//update_post_meta($product_id, '_manage_stock', $ni_stock_status); 
			update_post_meta($product_id, '_stock_status', $ni_stock_status); 
			update_post_meta($product_id, '_manage_stock', $ni_manage_stock); 
			
			echo "Done";
		}
	}
}
?>