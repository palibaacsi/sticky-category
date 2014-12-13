<?php
/*
Plugin Name: Sticky Category Bar	
**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   Plugin_Name
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 *
 * @wordpress-plugin
 * 
 * Plugin URI:        @TODO
 * Description:       @TODO
 * Version:           1.0.0
 * Author:            @TODO
 * Author URI:        @TODO
 * Text Domain:       plugin-name-locale
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/<owner>/<repo>
 */
 
 
 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/* plugin is activated */
register_activation_hook(__FILE__,'pb_floating_sticky_install'); 

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'pb_floating_sticky_remove' );

function pb_floating_sticky_install() {
/* Storing Category */
add_option("pb_floating_sticky_cat", 'uncategorized', '', 'yes');
}
function pb_floating_sticky_remove() {
/* Deletes the category from database */
delete_option('pb_floating_sticky_cat');
}
?>
<?php 
function pb_close_button_script() {  
?> 
<script type='text/javascript'> 
//<![CDATA[ 
var stickyfooter_arr = new Array(); 
var stickyfooter_clear = new Array(); 
function stickyfooterFloat(stickyfooter) { 
stickyfooter_arr[stickyfooter_arr.length] = this; 
var stickyfooterpointer = eval(stickyfooter_arr.length-1); 
this.pagetop = 0; 
this.cmode = (document.compatMode && document.compatMode!="BackCompat") ? document.documentElement : document.body; 
this.stickyfootersrc = document.all? document.all[stickyfooter] : document.getElementById(stickyfooter); 
this.stickyfootersrc.height = this.stickyfootersrc.offsetHeight; 
this.stickyfooterheight = this.cmode.clientHeight; 
this.stickyfooteroffset = stickyfooterGetOffsetY(stickyfooter_arr[stickyfooterpointer]); 
var stickyfooterbar = 'stickyfooter_clear['+stickyfooterpointer+'] = setInterval("stickyfooterFloatInit(stickyfooter_arr['+stickyfooterpointer+'])",1);'; 
stickyfooterbar = stickyfooterbar; 
eval(stickyfooterbar); 
} 
function stickyfooterGetOffsetY(stickyfooter) { 
var mtaTotOffset = parseInt(stickyfooter.stickyfootersrc.offsetTop); 
var parentOffset = stickyfooter.stickyfootersrc.offsetParent; 
while ( parentOffset != null ) { 
stickyfooterTotOffset += parentOffset.offsetTop; 
parentOffset = parentOffset.offsetParent; 
} 
return stickyfooterTotOffset; 
} 
function stickyfooterFloatInit(stickyfooter) { 
stickyfooter.pagetop = stickyfooter.cmode.scrollTop; 
stickyfooter.stickyfootersrc.style.top = stickyfooter.pagetop - stickyfooter.stickyfooteroffset + "px"; 
} 
function closeTopAds() { 
document.getElementById("closestick").style.visibility = "hidden"; 
} 
//]]> 
</script> 
    <?php 
} 
 
add_action('wp_head', 'pb_close_button_script' ); 
 
//  ================================ 
//  ====== ProCategories Widget ============= 
//  ================================ 

class PB_Categories_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_categories', 'description' => __( "A list or dropdown of categories" ) );
		parent::__construct('pB_categories_widget', __('PB Categories Widget'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Categories' ) : $instance['title'], $instance, $this->id_base);
		$exclude = empty( $instance['exclude'] ) ? '' : $instance['exclude'];
		
		$c = ! empty( $instance['count'] ) ? '1' : '0';
		$h = ! empty( $instance['hierarchical'] ) ? '1' : '0';
		$d = ! empty( $instance['dropdown'] ) ? '1' : '0';

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

		$cat_args = array('orderby' => 'name', 'show_count' => $c, 'hierarchical' => $h,'exclude' => $exclude);

		if ( $d ) {
			$cat_args['show_option_none'] = __('Select Category');
			wp_dropdown_categories(apply_filters('widget_categories_dropdown_args', $cat_args));
?>

<script type='text/javascript'>
/* <![CDATA[ */
	var dropdown = document.getElementById("cat");
	function onCatChange() {
		if ( dropdown.options[dropdown.selectedIndex].value > 0 ) {
			location.href = "<?php echo home_url(); ?>/?cat="+dropdown.options[dropdown.selectedIndex].value;
		}
	}
	dropdown.onchange = onCatChange;
/* ]]> */
</script>

<?php
		} else {
?>
		<ul>
<?php
		$cat_args['title_li'] = '';
		wp_list_categories(apply_filters('widget_categories_args', $cat_args));
?>
		</ul>
<?php
		}

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['exclude'] = strip_tags($new_instance['exclude']);
		$instance['count'] = !empty($new_instance['count']) ? 1 : 0;
		$instance['hierarchical'] = !empty($new_instance['hierarchical']) ? 1 : 0;
		$instance['dropdown'] = !empty($new_instance['dropdown']) ? 1 : 0;

		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '','exclude' => '') );
		$title = esc_attr( $instance['title'] );
		$exclude = esc_attr( $instance['exclude'] );
		$count = isset($instance['count']) ? (bool) $instance['count'] :false;
		$hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
		$dropdown = isset( $instance['dropdown'] ) ? (bool) $instance['dropdown'] : false;
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e( 'Exclude :' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('exclude'); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" type="text" value="<?php echo $exclude; ?>" />
		
		<br>Enter a comma separated category ID.<br>ex : <code>2,3</code> &nbsp;&nbsp;(This widget will display all of your categories except these categories).</p>

		<p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('dropdown'); ?>" name="<?php echo $this->get_field_name('dropdown'); ?>"<?php checked( $dropdown ); ?> />
		<label for="<?php echo $this->get_field_id('dropdown'); ?>"><?php _e( 'Display as dropdown' ); ?></label><br />

		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>"<?php checked( $count ); ?> />
		<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e( 'Show post counts' ); ?></label><br />

		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('hierarchical'); ?>" name="<?php echo $this->get_field_name('hierarchical'); ?>"<?php checked( $hierarchical ); ?> />
		<label for="<?php echo $this->get_field_id('hierarchical'); ?>"><?php _e( 'Show hierarchy' ); ?></label></p>
<?php
	}

}   
/* class PB_Categories_Widget

// register PB_Categories_Widget widget
 add_action( 'widgets_init', create_function( '', 'register_widget( "PB_Categories_Widget" );' ) );
register_deactivation_hook(__FILE__, 'pb_categories_widget_deactivate');

function pb_categories_widget_deactivate ()
{
 unregister_widget('PB_Categories_Widget');
}  * /
?>
<style>
div {
    width: 100px;
    height: 100px;
    background: red;
    /* For Safari 3.1 to 6.0 * /
    -webkit-transition-property: width;
    -webkit-transition-duration: 1s;
    -webkit-transition-timing-function: linear;
    -webkit-transition-delay: 2s;
    /* Standard syntax * /
    transition-property: width;
    transition-duration: 1s;
    transition-timing-function: linear;
    transition-delay: 2s;
}

div:hover {
    width: 200px;
}
</style>
<?php // ===============================
    /**
     * Register with hook 'wp_enqueue_scripts', which can be used for front end CSS and JavaScript
     */
    add_action( 'wp_enqueue_scripts', 'prefix_add_my_stylesheet1' );

    /**
     * Enqueue plugin style-file
     */
    function prefix_add_my_stylesheet1() {
        // Respects SSL, Style.css is relative to the current file
        wp_register_style( 'prefix-style', plugins_url('style.css', __FILE__) );
        wp_enqueue_style( 'prefix-style' );
    }
?>
<?php
if ( is_admin() ){

/*creating admin menu */
add_action('admin_menu', 'pb_floating_sticky_admin_menu');

function pb_floating_sticky_admin_menu() {
add_options_page('Sticky Category Bar', 'Sticky Category Bar', 'administrator',
'floating-sticky-bar', 'pb_floating_sticky_html_page');
}
}
?>
<?php
function pb_floating_sticky_html_page() {
?>
<div class='wrap'>

<div id="icon-tools" class="icon32"></div><h2>WP Category Footer Bar options</h2><br>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>

<table class="widefat" width="510">
<tr valign="top">
<th width="92" scope="row">Enter Category Name:</th>
<td width="406">
<input name="pb_floating_sticky_cat" type="text" id="pb_floating_sticky_cat"
value="<?php echo get_option('pb_floating_sticky_cat'); ?>" />
(ex. WordPress)</td>
</tr>
</table>

<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="pb_floating_sticky_cat" />

<p><br>
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>

</form>
<? 
}

/*Getting Random Article From The Category */
 function random_article() { 
query_posts(array(
  'orderby' => 'rand', 
  'category_name'  => get_option( 'pb_floating_sticky_cat' ),
  'posts_per_page' => 1
)); 
if (have_posts()) : while (have_posts()) : the_post(); ?>
<? /* <div id="closestick"> */ ?>
<div id="closestick">
 <div class="fixedbar">
<img border='0' onClick='closeTopAds();return false;' src='<?php echo plugins_url(); ?>/pb-sticky-category-bar/images/cancel.png'  style='cursor:hand;cursor:pointer;position:absolute;top:5px;right:5px;'/>
 <div class="floatingbox">
 <ul id="tips">
 <li>Santa</li>
 <li><a href='<?php the_permalink(); ?>' title='<?php the_title(); ?>'><?php the_title(); ?> </a></li>
 <li>	<form id="category-select" class="category-select" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">

		<?php
		$args = array(
			'show_option_none' => __( 'Select category' ),
			'show_count'       => 0,
			'orderby'          => 'name',
			'echo'             => 0,
		);
		?>

		<?php $select  = wp_dropdown_categories( $args ); ?>
		<?php $replace = "<select$1 onchange='return this.form.submit()'>"; ?>
		<?php $select  = preg_replace( '#<select([^>]*)>#', $replace, $select ); ?>

		<?php echo $select; ?>

		<noscript>
			<input type="submit" value="View" />
		</noscript>

	</form></li>
	<li>mmm</li>
 </ul>
 </div>
 	<div id="pbsearchform"><?php the_widget( 'WP_Widget_Search' ); ?></div>
	

 </div>
 </div>
<?php endwhile; endif; wp_reset_query(); 
}
add_action('wp_footer', 'random_article');
?>