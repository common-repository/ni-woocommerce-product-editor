<?php
if ( ! defined( 'ABSPATH' ) ) { exit;}
if( !class_exists( 'NiWPE_Function' ) ) {  
	class NiWPE_Function{
		function __construct(){
		}
		function get_simple_product(){
			global $wpdb;
			$simple_product =  array();
			$query  = "";
			$query .=" SELECT    ";
			$query .=" post.ID as product_id ";
			$query .=", post.post_title as product_name ";
			$query .=" FROM {$wpdb->prefix}posts as post  ";
			
			$query .=" LEFT JOIN {$wpdb->prefix}term_relationships as relationships ON relationships.object_id=post.ID ";
			
			$query .=" LEFT JOIN {$wpdb->prefix}term_taxonomy as term_taxonomy ON term_taxonomy.term_taxonomy_id=relationships.term_taxonomy_id ";
			
			$query .=" LEFT JOIN {$wpdb->prefix}terms as terms ON terms.term_id=term_taxonomy.term_id ";
			$query .=" WHERE 1=1 ";
			$query .=" AND post.post_type='product'";
			$query .=" AND post.post_status='publish'";
			$query .=" AND terms.name='simple'";
			$query .=" ORDER BY post.post_title  ";
			
			$row = $wpdb->get_results( $query);	
			foreach($row as $key=>$value){
				$simple_product[$value->product_id] = $value->product_name;
			}
			
			//$this->print_data($simple_product);
			
			return $simple_product;
		}
		function get_variable_product(){
			global $wpdb;
			$variable_product =  array();
			$query  = "";
			$query .=" SELECT    ";
			$query .=" post.ID as product_id ";
			$query .=", post.post_title as product_name ";
			$query .=" FROM {$wpdb->prefix}posts as post  ";
			
			$query .=" LEFT JOIN {$wpdb->prefix}term_relationships as relationships ON relationships.object_id=post.ID ";
			
			$query .=" LEFT JOIN {$wpdb->prefix}term_taxonomy as term_taxonomy ON term_taxonomy.term_taxonomy_id=relationships.term_taxonomy_id ";
			
			$query .=" LEFT JOIN {$wpdb->prefix}terms as terms ON terms.term_id=term_taxonomy.term_id ";
			$query .=" WHERE 1=1 ";
			$query .=" AND post.post_type='product'";
			$query .=" AND post.post_status='publish'";
			$query .=" AND terms.name='variable'";
			$query .=" ORDER BY post.post_title  ";
			
			$row = $wpdb->get_results( $query);	
			foreach($row as $key=>$value){
				$variable_product[$value->product_id] = $value->product_name;
			}
			
			//$this->print_data($simple_product);
			
			return $variable_product;
		}
		function get_variation_product(){
			global $wpdb;
			$row               =  array();
			$variation_product =  array();
			$query  = "";
			$query .=" SELECT    ";
			$query .=" post.ID as variation_id ";
			$query .=", post.post_parent as post_parent_id ";
			$query .=", post.post_title as product_name ";
			$query .=", posts_parent.post_title as parent_product_name ";
			$query .=" FROM {$wpdb->prefix}posts as post  ";
			
			$query .=" LEFT JOIN {$wpdb->prefix}posts as posts_parent ON posts_parent.ID=post.post_parent ";
			
		
			
			$query .=" WHERE 1=1 ";
			$query .=" AND post.post_type='product_variation'";
			$query .=" AND post.post_status='publish'";
			$query .=" ORDER BY post.post_title  ";
			
			$row = $wpdb->get_results( $query);	
			foreach($row as $key=>$value){
				$variation_product[$value->variation_id] = $value->product_name;
			}
			return $variation_product;
		}
		function print_data($data){
			print "<pre>";
			print_r($data);
			print "</pre>";
		}
		function get_request($name,$default = NULL,$set = false){
			if(isset($_REQUEST[$name])){
				$newRequest = $_REQUEST[$name];
				
				if(is_array($newRequest)){
					$newRequest = implode(",", $newRequest);
					//$newRequest = implode("','", $newRequest);
				}else{
					$newRequest = trim($newRequest);
				}
				
				if($set) $_REQUEST[$name] = $newRequest;
				
				return $newRequest;
			}else{
				if($set) 	$_REQUEST[$name] = $default;
				return $default;
			}
		}
		function get_item_meta_key_list(){
			$meat_key = array("_sku","_manage_stock","_stock","_backorders","_visibility","_regular_price","_sale_price","_price","_stock_status");
			return $meat_key;
		}
		/*Get Order  Meta*/
		function get_order_postmeta($order_id, $mata_key = array()){
			global $wpdb;
			$postmeta = array();
			$query = " SELECT * ";
			$query .= " FROM {$wpdb->prefix}postmeta as postmeta ";
			$query .= " WHERE 1 =1 ";
			
			$query .= " AND postmeta.post_id={$order_id}";
			if (count($mata_key)){
				$mata_key_string=  implode ( "', '", $mata_key );
				$query .= " AND postmeta.meta_key IN ('{$mata_key_string}')";
			}
			
			
			$row = $wpdb->get_results( $query);		
			
			foreach($row as   $key=>$value){
				if(isset($value->meta_key)){
					$postmeta[substr($value->meta_key,1)] =   $value->meta_value;
				}
			}
			//$this->prettyPrint($postmeta );	
			return $postmeta;			
		}
		function get_country_name($code){
				$name = "";
				if (strlen($code)>0){
					$name= WC()->countries->countries[ $code];	
					$name  = isset($name) ? $name : $code;
				}
				return $name;
		}
		function get_price($price=0){
			return wc_price($price);
		}
		
		function pagination($total_row,$per_page=10,$page=1,$url='?'){   
			$total = $total_row;
			$adjacents = "2"; 
			  
			$prevlabel = "&lsaquo; Prev";
			$nextlabel = "Next &rsaquo;";
			$lastlabel = "Last &rsaquo;&rsaquo;";
			  
			$page = ($page == 0 ? 1 : $page);  
			$start = ($page - 1) * $per_page;                               
			  
			$prev = $page - 1;                          
			$next = $page + 1;
			  
			$lastpage = ceil($total/$per_page);
			  
			$lpm1 = $lastpage - 1; // //last page minus 1
			  
			$pagination = "";
			if($lastpage > 1){   
				$pagination .= "<ul class='niwpe_pagination'>";
				$pagination .= "<li class='page_info'>Page {$page} of {$lastpage}</li>";
					  
					if ($page > 1) $pagination.= "<li><a data-page={$prev} href='{$url}page={$prev}'>{$prevlabel}</a></li>";
					  
				if ($lastpage < 7 + ($adjacents * 2)){   
					for ($counter = 1; $counter <= $lastpage; $counter++){
						if ($counter == $page)
							//$pagination.= "<li><a class='current'>{$counter}</a></li>";
							$pagination.= "<li><span class='current'>{$counter}</span></li>";
						else
							$pagination.= "<li><a data-page={$counter} href='{$url}page={$counter}'>{$counter}</a></li>";                    
					}
				  
				} elseif($lastpage > 5 + ($adjacents * 2)){
					  
					if($page < 1 + ($adjacents * 2)) {
						  
						for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++){
							if ($counter == $page)
								//$pagination.= "<li><a data-page={$counter} class='current'>{$counter}</a></li>";
								$pagination.= "<li><span class='current'>{$counter}</span></li>";
							else
								$pagination.= "<li><a data-page={$counter}  href='{$url}page={$counter}'>{$counter}</a></li>";                    
						}
						$pagination.= "<li class='dot'>...</li>";
						$pagination.= "<li><a data-page={$lpm1} href='{$url}page={$lpm1}'>{$lpm1}</a></li>";
						$pagination.= "<li><a data-page={$lastpage} href='{$url}page={$lastpage}'>{$lastpage}</a></li>";  
							  
					} elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
						  
						$pagination.= "<li><a data-page=1 href='{$url}page=1'>1</a></li>";
						$pagination.= "<li><a data-page=2 href='{$url}page=2'>2</a></li>";
						$pagination.= "<li class='dot'>...</li>";
						for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
							if ($counter == $page)
								//$pagination.= "<li><a data-page={$counter} class='current'>{$counter}</a></li>";
								$pagination.= "<li><span class='current'>{$counter}</span></li>";
							else
								$pagination.= "<li><a data-page={$counter} href='{$url}page={$counter}'>{$counter}</a></li>";                    
						}
						$pagination.= "<li class='dot'>..</li>";
						$pagination.= "<li><a data-page={$lpm1} href='{$url}page={$lpm1}'>{$lpm1}</a></li>";
						$pagination.= "<li><a data-page={$lastpage} href='{$url}page={$lastpage}'>{$lastpage}</a></li>";      
						  
					} else {
						  
						$pagination.= "<li><a data-page=1 href='{$url}page=1'>1</a></li>";
						$pagination.= "<li><a data-page=2 href='{$url}page=2'>2</a></li>";
						$pagination.= "<li class='dot'>..</li>";
						for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
							if ($counter == $page)
								//$pagination.= "<li><a data-page={$counter} class='current'>{$counter}</a></li>";
								$pagination.= "<li><span class='current'>{$counter}</span></li>";
							else
								$pagination.= "<li><a data-page={$counter} href='{$url}page={$counter}'>{$counter}</a></li>";                    
						}
					}
				}
				  
					if ($page < $counter - 1) {
						$pagination.= "<li><a data-page={$next} href='{$url}page={$next}'>{$nextlabel}</a></li>";
						$pagination.= "<li><a data-page={$lastpage} href='{$url}page=$lastpage'>{$lastlabel}</a></li>";
					}
				  
				$pagination.= "</ul>";        
			}
			  
			return $pagination;
		}
		function get_default_columns(){
			$column = array();
			$column["product_name"] 		= __("Product Name","textdomain_wooreport");
			$column["sku"]					= __("SKU","textdomain_wooreport");
			$column["sale_price"]			= __("Sale Price","textdomain_wooreport");
			$column["regular_price"]		= __("Regular Price","textdomain_wooreport");
			//$column["price"]				= __("Price","textdomain_wooreport");
			$column["stock_status"]			= __("Stock Status","textdomain_wooreport");
			$column["stock"]				= __("Qty","textdomain_wooreport");
			$column["backorders"]			= __("Backorders","textdomain_wooreport");
			$column["manage_stock"]			= __("Manage Stock","textdomain_wooreport");
			//$column["visibility"]			= __("Visibility","textdomain_wooreport");
			$column["update"]				= __("Update","textdomain_wooreport");
			
			
			return $column;
		}
		function get_table($columns =array(), $row  =array()){
		?>
        <div style="overflow-x:auto;">
			<table class="niwpe_default_table">
				<tr>
					 <thead>
				<?php foreach($columns as $key=>$value): ?>
					<th><?php echo $value; ?></th>
				<?php endforeach; ?>
					</thead>
				</tr>
                <?php if (count($row)==0):?>
                <tr>
                	<td colspan="<?php echo count($columns); ?>">No record found</td>
                </tr>
                <?php endif; ?>
                	<tbody>
				<?php foreach ($row as $rk => $rv): ?>
					<tr>
						<?php foreach ($columns as $ck => $cv): ?>	
							<?php 
							switch ($ck) {
								case 'product_name':
								case 'sku':
									 $value  = isset ($rv->$ck)?$rv->$ck:"";
									 ?>
                                     <td><input type="text" value="<?php echo $value ; ?>" name="<?php echo $ck; ?>" class="_<?php echo $ck; ?>" /> </td>
                                     <?php	
									 break;
								case "price":
								case "sale_price":
								case 'regular_price':
								case 'stock':
									 $value  = isset ($rv->$ck)?$rv->$ck:"";
									 ?>
                                     <td><input type="text" value="<?php echo $value ; ?>" name="<?php echo $ck; ?>"  class="_<?php echo $ck; ?>"  style="width:60px; text-align:right" /> </td>
                                     <?php	
									 break;
								case 'stock_status':
									  $value  = isset ($rv->$ck)?$rv->$ck:'';
									  ?>
                                      <td>
                                      <select name="<?php echo $ck; ?>" id="stock_status" class="_<?php echo $ck; ?>">
                                      	<option <?php if ($value == 'instock') echo 'selected="selected"'; ?> value="instock">In stock</option>
                                        <option <?php if ($value == 'outofstock') echo 'selected="selected"'; ?> value="outofstock">Out of stock</option>
                                      </select>
                                      </td>
                                      <?php
									  break;
								case 'manage_stock':
									 $value  = isset ($rv->$ck)?$rv->$ck:'no';
									 ?>
                                     <td>
                                      <select name="<?php echo $ck; ?>"  class="_<?php echo $ck; ?>">
                                      	<option <?php if ($value == 'yes') echo 'selected="selected"'; ?> value="yes">Yes</option>
                                        <option <?php if ($value == 'no') echo 'selected="selected"'; ?> value="no">No</option>
                                      </select>
                                     </td>
                                     <?php		
									 break;	
								case 'backorders':
									 $value  = isset ($rv->$ck)?$rv->$ck:'no';
									?>
                                    <td>
                                      <select name="<?php echo $ck; ?>" class="_<?php echo $ck; ?>">
                                      	<option <?php if ($value == 'no') echo 'selected="selected"'; ?> value="no">Do not allow</option>
                                        <option <?php if ($value == 'notify') echo 'selected="selected"'; ?> value="notify">Allow, but notify customer</option>
                                        <option <?php if ($value == 'yes') echo 'selected="selected"'; ?> value="yes">Allow</option>	
                                      </select>
                                     </td>
                                    <?php
									break;	   	 
								case 'update':
									?>
                                  
                                    <td>  
                                    <input type="button" value="Update" class="_niwpe_product_update niwpe_button" data-product-id ="<?php echo $rv->product_id; ?>" />
                                    </td>
                                    <?php	
									break;	
								default:
								$value  = isset ($rv->$ck)?$rv->$ck:"";
								?>
								<td><?php echo $value; ?></td>
								<?php		
							}
							?>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
					</tbody>
			</table>
			</div>
        <?php	
		}	 
	}
}
?>