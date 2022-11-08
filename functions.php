<?php
add_filter( 'loop_shop_per_page', 'new_loop_shop_per_page', 20 );
function new_loop_shop_per_page( $cols ) {
  $cols = 100;
  return $cols;
}
//change Add to Cart to Read more
add_action( 'woocommerce_after_shop_loop_item', 'my_woocommerce_template_loop_add_to_cart', 10 );

function my_woocommerce_template_loop_add_to_cart() {
    global $product;
    echo '<a href="';
	echo the_permalink();
	echo '"><div class="viewmore">View more</div></a>';
}


//remove related products
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
	
	//HTML in category descriptions
	foreach ( array( 'pre_term_description' ) as $filter ) {
remove_filter( $filter, 'wp_filter_kses' );
}
foreach ( array( 'term_description' ) as $filter ) {
remove_filter( $filter, 'wp_kses_data' );
}

	
	// Add RSS links to <head> section
	automatic_feed_links();
	
	
	// Clean up the <head>
	function removeHeadLinks() {
    	remove_action('wp_head', 'rsd_link');
    	remove_action('wp_head', 'wlwmanifest_link');
    }
    add_action('init', 'removeHeadLinks');
    remove_action('wp_head', 'wp_generator');
    
	// Declare sidebar widget zone
    if (function_exists('register_sidebar')) {
    	register_sidebar(array(
    		'name' => 'Sidebar Widgets',
    		'id'   => 'sidebar-widgets',
    		'description'   => 'These are widgets for the sidebar.',
    		'before_widget' => '<div id="%1$s" class="widget %2$s">',
    		'after_widget'  => '</div>',
    		'before_title'  => '<h2>',
    		'after_title'   => '</h2>'
    	));
    }
	

// Declare Woocommerce support	
	add_theme_support( 'woocommerce' );
	
	
//Disable woo styles	
//	if ( version_compare( WOOCOMMERCE_VERSION, "2.1" ) >= 0 ) {
//add_filter( 'woocommerce_enqueue_styles', '__return_false' );
//} else {
//define( 'WOOCOMMERCE_USE_CSS', false );
//}
	
	
	
	//add_action('template_redirect','custom_theme_files');
 
//function custom_theme_files() {
//    wp_enqueue_script( 'jquery' );
//}
	
	
	
	
	function string_limit_words($string, $word_limit)
{
  $words = explode(' ', $string, ($word_limit + 1));
  if(count($words) > $word_limit)
  array_pop($words);
  return implode(' ', $words);
}	

	
	
		if ( function_exists( 'add_theme_support' ) ) { 
  add_theme_support( 'post-thumbnails' ); 
  set_post_thumbnail_size( 200, 150 ); 

}


if ( function_exists( 'add_image_size' ) ) { 
	add_image_size( 'cat-thumb', 390, 390, false ); 
	add_image_size( 'home-thumb', 382, 229, false );
	add_image_size( 'banner-thumb', 457, 296, true );
}
add_action( 'wp_scheduled_delete', 'delete_expired_db_transients' );

function delete_expired_db_transients() {

    global $wpdb, $_wp_using_ext_object_cache;

    if( $_wp_using_ext_object_cache )
        return;

    $time = isset ( $_SERVER['REQUEST_TIME'] ) ? (int)$_SERVER['REQUEST_TIME'] : time() ;
    $expired = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout%' AND option_value < {$time};" );

    foreach( $expired as $transient ) {

        $key = str_replace('_transient_timeout_', '', $transient);
        delete_transient($key);
    }
}


add_action( 'pre_get_posts', 'custom_pre_get_posts_query' );
 
function custom_pre_get_posts_query( $q ) {
 
if ( ! $q->is_main_query() ) return;
if ( ! $q->is_post_type_archive() ) return;
if ( ! is_admin() && is_shop() ) {
 
$q->set( 'tax_query', array(array(
'taxonomy' => 'product_cat',
'field' => 'slug',
'terms' => array( 'trade-in-specials' ), // Don't display products in the knives category on the shop page
'operator' => 'NOT IN'
)));
}
 
remove_action( 'pre_get_posts', 'custom_pre_get_posts_query' );
 
}

 
 

register_sidebar(array(
	'name' => 'hitcounter',
    'before_widget' => '',
    'after_widget' => '',
	'before_title' => '',
    'after_title' => '',
    ));
	
	
// Change number or products per row to 3
add_filter('loop_shop_columns', 'loop_columns');
if (!function_exists('loop_columns')) {
	function loop_columns() {
		return 3; // 3 products per row
	}
}


//Remove Woo tabs

add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

function woo_remove_product_tabs( $tabs ) {

    unset( $tabs['reviews'] );  	// Remove the Reviews tab

    return $tabs;

}


add_action( 'after_setup_theme', 'yourtheme_setup' );

function yourtheme_setup() {
//add_theme_support( 'wc-product-gallery-zoom' );
add_theme_support( 'wc-product-gallery-lightbox' );
add_theme_support( 'wc-product-gallery-slider' );
}

 //custom sale badge wording
add_filter('woocommerce_sale_flash', 'woocommerce_custom_sale_text', 10, 3);
function woocommerce_custom_sale_text($text, $post, $_product)
{
    //return '<span class="onsale"></br>Black Friday Deal ONLY</span>';
	//return '<span class="onsale"></br>Special Offer!!!</span>';
}

add_action( 'woocommerce_before_shop_loop_item_title', 'saleStatus', 10 );
add_action( 'woocommerce_before_single_product_summary', 'saleStatus', 10 );

function saleStatus(){
	if(get_field('sale_status')) {
?>
	<div class="saleStatusContainer">
	<?php if( get_field('sale_status') == 'N' ) { ?>
			<div class="saleStatusNone"></div>
		<?php } ?> 
		<?php if( get_field('sale_status') == 'SNP' ) { ?>
			<div class="saleStatus">
				<p>Sale In Progress</p>
			</div>
		<?php } ?> 
		
		<?php if( get_field('sale_status') == 'SO' ) { ?>
			<div class="saleStatus">
				<p>Special Offer</p>
			</div>
		<?php } ?> 
		
		<?php if( get_field('sale_status') == 'HDP' ) { ?>
			<div class="saleStatus">
				<p>48 Hour Holding Deposit Paid</p>
			</div>
		<?php } ?> 
	</div>
<?php } }


// CASH PRICE & FINANCE PRICE HACK//

//if( !function_exists("add_custom_text_prices") ) {
 //   function add_custom_text_prices( $price, $product ) {
        // Text
 //       $text_regular_price = __("Cash Price: ");
  //      $text_final_price = __("Finance Price: ");

  //      if ( $product->is_on_sale(56666) ) {
  //          $has_sale_text = array(
    //          '<del>' => '<del>' . $text_regular_price,
     //         '<ins>' => '<br>'.$text_final_price.'<ins>'
      //      );
      //      $return_string = str_replace(
      //          array_keys( $has_sale_text ), 
      //          array_values( $has_sale_text ), 
       //         $price
       //     );

       //     return $return_string;
      //  }
     //   return $text_regular_price . $price;
   // }
  //  add_filter( 'woocommerce_get_price_html', 'add_custom_text_prices', 100, 2 );
// }
// 