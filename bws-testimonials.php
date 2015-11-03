<?php
/*
Plugin Name: Testimonials by BestWebSoft
Plugin URI: http://bestwebsoft.com/products/
Description: Plugin for displaying Testimonials.
Author: BestWebSoft
Text Domain: bws-testimonials
Domain Path: /languages
Version: 0.1.4
Author URI: http://bestwebsoft.com/
License: GPLv3 or later
*/

/*  @ Copyright 2015  BestWebSoft  ( http://support.bestwebsoft.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Add option page in admin menu */
if ( ! function_exists( 'tstmnls_admin_menu' ) ) {
	function tstmnls_admin_menu() {
		global $submenu;
		bws_add_general_menu( plugin_basename( __FILE__ ) );
		$settings = add_submenu_page( 'bws_plugins', __( 'Testimonials Settings', 'bws-testimonials' ), 'Testimonials', 'manage_options', "testimonials.php", 'tstmnls_settings_page' );
		
		if ( isset( $submenu['edit.php?post_type=bws-testimonial'] ) )
			$submenu['edit.php?post_type=bws-testimonial'][] = array( __( 'Settings', 'bws-testimonials' ), 'manage_options', admin_url( 'admin.php?page=testimonials.php' ) );

		add_action( 'load-' . $settings, 'tstmnls_add_tabs' );
		add_action( 'load-post.php', 'tstmnls_add_tabs' );
		add_action( 'load-edit.php', 'tstmnls_add_tabs' );
		add_action( 'load-post-new.php', 'tstmnls_add_tabs' );
	}
}

/**
 * Internationalization
 */
if ( ! function_exists( 'tstmnls_plugins_loaded' ) ) {
	function tstmnls_plugins_loaded() {
		load_plugin_textdomain( 'bws-testimonials', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

if ( ! function_exists ( 'tstmnls_init' ) ) {
	function tstmnls_init() {
		global $tstmnls_plugin_info;		

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );
		
		if ( empty( $tstmnls_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$tstmnls_plugin_info = get_plugin_data( __FILE__ );
		}

		/* Function check if plugin is compatible with current WP version  */
		bws_wp_min_version_check( plugin_basename( __FILE__ ), $tstmnls_plugin_info, '3.8', '3.5' );

		tstmnls_register_testimonial_post_type();
	}
}

if ( ! function_exists ( 'tstmnls_admin_init' ) ) {
	function tstmnls_admin_init() {
		global $bws_plugin_info, $tstmnls_plugin_info, $pagenow, $bws_shortcode_list;

		if ( ! isset( $bws_plugin_info ) || empty( $bws_plugin_info ) )
			$bws_plugin_info = array( 'id' => '180', 'version' => $tstmnls_plugin_info["Version"] );

		add_meta_box( 'custom-metabox', __( 'Testimonials Info', 'bws-testimonials' ), 'tstmnls_custom_metabox', 'bws-testimonial', 'normal', 'high' );

		/* Call register settings function */
		if ( 'widgets.php' == $pagenow || 'plugins.php' == $pagenow || ( isset( $_REQUEST['page'] ) && 'testimonials.php' == $_REQUEST['page'] ) )
			tstmnls_register_settings();

		/* add Testimonials to global $bws_shortcode_list  */
		$bws_shortcode_list['tstmnls'] = array( 'name' => 'Testimonials' );
	}
}

if ( ! function_exists ( 'tstmnls_register_testimonial_post_type' ) ) {
	function tstmnls_register_testimonial_post_type() {
		$args = array(
			'label'				=>	__( 'Testimonials', 'bws-testimonials' ),
			'singular_label'	=>	__( 'Testimonial', 'bws-testimonials' ),
			'public'			=>	true,
			'show_ui'			=>	true,
			'capability_type' 	=>	'post',
			'hierarchical'		=>	false,
			'rewrite'			=>	true,
			'supports'			=>	array( 'title', 'editor' ),
			'labels'			=>	array(
				'add_new_item'			=>	__( 'Add a new testimonial', 'bws-testimonials' ),
				'edit_item'				=>	__( 'Edit testimonials', 'bws-testimonials' ),
				'new_item'				=>	__( 'New testimonial', 'bws-testimonials' ),
				'view_item'				=>	__( 'View testimonials', 'bws-testimonials' ),
				'search_items'			=>	__( 'Search testimonials', 'bws-testimonials' ),
				'not_found'				=>	__( 'No testimonials found', 'bws-testimonials' ),
				'not_found_in_trash'	=>	__( 'No testimonials found in Trash', 'bws-testimonials' )
			)			
		);
		register_post_type( 'bws-testimonial' , $args );
	}
}

/**
	* Register settings for plugin 
	*/
if ( ! function_exists( 'tstmnls_register_settings' ) ) {
	function tstmnls_register_settings() {
		global $tstmnls_options, $tstmnls_plugin_info, $tstmnls_option_defaults;

		$tstmnls_option_defaults = array(
			'plugin_option_version' 	=> $tstmnls_plugin_info["Version"],
			'widget_title'				=>	__( 'Testimonials', 'bws-testimonials' ),
			'count'						=>	'5',
			'display_settings_notice'	=>	1
		);

		/* Install the option defaults */
		if ( ! get_option( 'tstmnls_options' ) )
			add_option( 'tstmnls_options', $tstmnls_option_defaults );

		$tstmnls_options = get_option( 'tstmnls_options' );

		if ( ! isset( $tstmnls_options['plugin_option_version'] ) || $tstmnls_options['plugin_option_version'] != $tstmnls_plugin_info["Version"] ) {
			$tstmnls_option_defaults['display_settings_notice'] = 0;
			$tstmnls_options = array_merge( $tstmnls_option_defaults, $tstmnls_options );
			$tstmnls_options['plugin_option_version'] = $tstmnls_plugin_info["Version"];
			update_option( 'tstmnls_options', $tstmnls_options );
		}		
	}
}

/**
	* Add settings page in admin area
	*/
if ( ! function_exists( 'tstmnls_settings_page' ) ) {
	function tstmnls_settings_page(){ 
		global $title, $tstmnls_options, $tstmnls_plugin_info, $tstmnls_option_defaults;
		$message = $error = ''; 
		
		if ( isset( $_POST['tstmnls_form_submit'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'tstmnls_check_field' ) ) {

			$tstmnls_options['widget_title'] = stripslashes( esc_html( $_POST['tstmnls_widget_title'] ) );
			$tstmnls_options['count'] = intval( $_POST['tstmnls_count'] );

			update_option( 'tstmnls_options', $tstmnls_options ); 
			$message = __( 'Settings saved', 'bws-testimonials' );
		}
		/* Add restore function */
		if ( isset( $_REQUEST['bws_restore_confirm'] ) && check_admin_referer( plugin_basename(__FILE__), 'bws_settings_nonce_name' ) ) {
			$tstmnls_options = $tstmnls_option_defaults;
			update_option( 'tstmnls_options', $tstmnls_options );
			$message = __( 'All plugin settings were restored.', 'bws-testimonials' );
		} /* end */ ?>
		<div class="wrap">
			<h2><?php echo $title; ?></h2>
			<?php bws_show_settings_notice(); ?>
			<div class="updated fade" <?php if ( $message == "" || "" != $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<div class="error" <?php if ( "" == $error ) echo "style=\"display:none\""; ?>><p><?php echo $error; ?></p></div>
			<?php if ( ! isset( $_GET['action'] ) ) { 
				if ( isset( $_REQUEST['bws_restore_default'] ) && check_admin_referer( plugin_basename(__FILE__), 'bws_settings_nonce_name' ) ) {
					bws_form_restore_default_confirm( plugin_basename(__file__) );
				} else { ?>
					<form id="tstmnls_settings_form" class="bws_form" method='post' action=''>
						<p><?php printf(
								'%1$s "<strong>%2$s</strong>" %3$s.',
								__( 'If you would like to display testimonials with a widget, you need to add the widget', 'bws-testimonials' ),
								__( 'Testimonials Widget', 'bws-testimonials' ),
								__( 'on the Widgets tab', 'bws-testimonials' )
							); ?>
						</p>
						<div><?php printf( 
							__( "If you would like to add testimonials to your page or post, please use %s button", 'bws-testimonials' ), 
							'<span class="bws_code"><img style="vertical-align: sub;" src="' . plugins_url( 'bws_menu/images/shortcode-icon.png', __FILE__ ) . '" alt=""/></span>' ); ?> 
							<div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help">
								<div class="bws_hidden_help_text" style="min-width: 180px;">
									<?php printf( 
										__( "You can add testimonials to your page or post by clicking on %s button in the content edit block using the Visual mode. If the button isn't displayed, please use the shortcode %s", 'bws-testimonials' ), 
										'<code><img style="vertical-align: sub;" src="' . plugins_url( 'bws_menu/images/shortcode-icon.png', __FILE__ ) . '" alt="" /></code>',
										'<code>[bws_testimonials]</code>'
									); ?>
								</div>
							</div>
						</div>
						<p>
							<?php _e( "Also, you can paste the following strings into the template source code", 'bws-testimonials' ); ?> 
							<code>
								&lt;?php if ( has_action( 'tstmnls_show_testimonials' ) ) {
									do_action( 'tstmnls_show_testimonials' );
								} ?&gt;
							</code>
						</p>
						<table class="form-table">
							<tbody>
								<tr>
									<th scope="row"><?php _e( 'Widget title', 'bws-testimonials' ); ?></th>
									<td>
										<input type="text" class="text" maxlength="250" value="<?php echo $tstmnls_options['widget_title']; ?>" name="tstmnls_widget_title"/>
									</td>
								</tr>
								<tr>
									<th scope="row"><?php _e( 'Number of testimonials to be displayed', 'bws-testimonials' ); ?></th>
									<td>
										<input type="number" class="text" min="1" max="10000" value="<?php echo $tstmnls_options['count']; ?>" name="tstmnls_count" />
									</td>
								</tr>
							</tbody>
						</table>
						<p class="submit">
							<input id="bws-submit-button" type="submit" value="<?php _e( 'Save Changes', 'bws-testimonials' ); ?>" class="button button-primary" name="tstmnls_submit">
							<input type="hidden" name="tstmnls_form_submit" value="submit" />
							<?php wp_nonce_field( plugin_basename( __FILE__ ), 'tstmnls_check_field' ) ?>
						</p>
					</form>
					<?php bws_form_restore_default_settings( plugin_basename(__file__) );
				}
			}
			bws_plugin_reviews_block( $tstmnls_plugin_info["Name"], 'bws-testimonials' ); ?>
		</div>
	<?php }
}

if ( ! function_exists( 'tstmnls_custom_metabox' ) ) {
	function tstmnls_custom_metabox() {
		global $post;
		$testimonials_info = get_post_meta( $post->ID, '_testimonials_info', true ); ?>
		<p>
			<label for="tstmnls_author"><?php _e( 'Author', 'bws-testimonials' ); ?>:<br />
			<input type="text" id="tstmnls_author" name="tstmnls_author" value="<?php if ( ! empty( $testimonials_info['author'] ) ) echo $testimonials_info['author']; ?>"/></label>
		</p>
		<p>
			<label for="tstmnls_company_name"><?php _e( 'Company Name', 'bws-testimonials' ); ?>:</label><br />
			<input type="text" id="tstmnls_company_name" name="tstmnls_company_name" value="<?php if ( ! empty( $testimonials_info['company_name'] ) ) echo $testimonials_info['company_name']; ?>"/>
		</p>
	<?php }
}

if ( ! function_exists( 'tstmnls_save_postdata' ) ) {
	function tstmnls_save_postdata( $post_id ) {
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */
		/* If this is an autosave, our form has not been submitted, so we don't want to do anything. */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;
		/* Check if our nonce is set. */
		if ( get_post_type( $post_id ) != 'bws-testimonial' )
			return $post_id;
		else {
			if ( isset( $_POST[ 'tstmnls_author' ] ) ) {
				$testimonials_info = array();
				$testimonials_info['author'] = esc_js( $_POST[ 'tstmnls_author' ] );
				$testimonials_info['company_name'] = esc_js( $_POST[ 'tstmnls_company_name' ] );
				/* Update the meta field in the database. */
				update_post_meta( $post_id, '_testimonials_info', $testimonials_info );
			}
		}
	}
}

/**
 * Remove shortcode from the content of the testimonial
 */
if ( ! function_exists ( 'tstmnls_content_save_pre' ) ) {
	function tstmnls_content_save_pre( $content ) {
		global $post;
		if ( isset( $post ) && "bws-testimonial" == $post->post_type && ! wp_is_post_revision( $post->ID ) && ! empty( $_POST ) ) {
			/* remove shortcode */
			$content = str_replace( '[bws_testimonials]', '', $content );
		}
		return $content;
	}
}

if ( ! class_exists( 'Testimonials' ) ) {
	class Testimonials extends WP_Widget {

		function Testimonials() {
			/* Instantiate the parent object */
			parent::__construct( 
				'tstmnls_testimonails_widget', 
				__( 'Testimonials Widget', 'bws-testimonials' ),
				array( 'description' => __( 'Widget for displaying Testimonials.', 'bws-testimonials' ) )
			);
		}

		function widget( $args, $instance ) {
			global $tstmnls_options;
			if ( empty( $tstmnls_options ) )
				$tstmnls_options = get_option( 'tstmnls_options' );
			$widget_title   = isset( $instance['widget_title'] ) ? stripslashes( esc_html( $instance['widget_title'] ) ) : $tstmnls_options['widget_title'];
			$count  		= isset( $instance['count'] ) ? intval( $instance['count'] ) : $tstmnls_options['count'];
			echo $args['before_widget'];
			if ( ! empty( $widget_title ) ) { 
				echo $args['before_title'] . $widget_title . $args['after_title'];
			} 
			tstmnls_show_testimonials( $count );		
			echo $args['after_widget'];
		}

		function form( $instance ) {
			global $tstmnls_options;
			if ( empty( $tstmnls_options ) )
				$tstmnls_options = get_option( 'tstmnls_options' );
			$widget_title  	= isset( $instance['widget_title'] ) ? stripslashes( esc_html( $instance['widget_title'] ) ) : $tstmnls_options['widget_title'];
			$count  		= isset( $instance['count'] ) ? intval( $instance['count'] ) : $tstmnls_options['count']; ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'widget_title' ); ?>"><?php _e( 'Widget Title', 'bws-testimonials' ); ?>: </label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'widget_title' ); ?>" name="<?php echo $this->get_field_name( 'widget_title' ); ?>" type="text" maxlength="250" value="<?php echo esc_attr( $widget_title ); ?>"/>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Number of testimonials to be displayed', 'bws-testimonials' ); ?>: </label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="number" min="1" max="10000" value="<?php echo esc_attr( $count ); ?>"/>
			</p>
		<?php }

		function update( $new_instance, $old_instance ) {
			global $tstmnls_options;
			if ( empty( $tstmnls_options ) )
				$tstmnls_options = get_option( 'tstmnls_options' );
			$instance = array();
			$instance['widget_title']	= ( isset( $new_instance['widget_title'] ) ) ? stripslashes( esc_html( $new_instance['widget_title'] ) ) : $tstmnls_options['widget_title'];
			$instance['count']			= ( ! empty( $new_instance['count'] ) ) ? intval( $new_instance['count'] ) : $tstmnls_options['count'];
			return $instance;
		}
	}
}

/**
 * Display Featured Post
 * @return echo Featured Post block
 */
if ( ! function_exists( 'tstmnls_show_testimonials' ) ) {
	function tstmnls_show_testimonials( $count = false ) {
		if ( ! $count ) {
			global $tstmnls_options;
			if ( empty( $tstmnls_options ) )
				$tstmnls_options = get_option( 'tstmnls_options' );
			$count = $tstmnls_options['count'];
		}
		$query_args = array(
			'post_type'			=>	'bws-testimonial',
			'post_status'		=>	'publish',
			'posts_per_page'	=>	$count
		);
		query_posts( $query_args ); ?>
		<div class="bws-testimonials">
			<?php while ( have_posts() ) {
				the_post(); 
				global $post;
				$testimonials_info = get_post_meta( $post->ID, '_testimonials_info', true ); ?>
				<div class="testimonials_quote">
					<blockquote><?php the_content(); ?></blockquote>
					<div class="testimonial_quote_footer">
						<div class="testimonial_quote_author"><?php echo $testimonials_info['author']; ?></div>
						<span><?php echo $testimonials_info['company_name']; ?></span>
					</div>
				</div>
			<?php } 
			wp_reset_query(); ?>
		</div><!-- .bws-testimonials -->
	<?php }
}

if ( ! function_exists ( 'tstmnls_admin_head' ) ) {
	function tstmnls_admin_head() {
		wp_enqueue_style( 'tstmnls_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
	}
}

if ( ! function_exists ( 'tstmnls_wp_head' ) ) {
	function tstmnls_wp_head() {
		wp_enqueue_style( 'tstmnls_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
	}
}

/**
 * Function to handle action links
 */
if ( ! function_exists( 'tstmnls_plugin_action_links' ) ) {
	function tstmnls_plugin_action_links( $links, $file ) {
		if ( ! is_network_admin() ) {
			/* Static so we don't call plugin_basename on every plugin row. */
			static $this_plugin;
			if ( ! $this_plugin )
				$this_plugin = plugin_basename(__FILE__);

			if ( $file == $this_plugin ) {
				$settings_link = '<a href="admin.php?page=testimonials.php">' . __( 'Settings', 'bws-testimonials' ) . '</a>';
				array_unshift( $links, $settings_link );
			}
		}
		return $links;
	}
}

if ( ! function_exists ( 'tstmnls_register_plugin_links' ) ) {
	function tstmnls_register_plugin_links( $links, $file ) {
		$base = plugin_basename(__FILE__);
		if ( $file == $base ) {
			if ( ! is_network_admin() )
				$links[] = '<a href="admin.php?page=testimonials.php">' . __( 'Settings', 'bws-testimonials' ) . '</a>';
			$links[] = '<a href="http://wordpress.org/plugins/bws-testimonials/faq/" target="_blank">' . __( 'FAQ', 'bws-testimonials' ) . '</a>';
			$links[] = '<a href="http://support.bestwebsoft.com">' . __( 'Support', 'bws-testimonials' ) . '</a>';
		}
		return $links;
	}
}

/* add admin notices */
if ( ! function_exists ( 'tstmnls_admin_notices' ) ) {
	function tstmnls_admin_notices() {
		global $hook_suffix, $tstmnls_plugin_info;
		if ( 'plugins.php' == $hook_suffix && ! is_network_admin() ) {
			bws_plugin_banner_to_settings( $tstmnls_plugin_info, 'tstmnls_options', 'bws-testimonials', 'admin.php?page=testimonials.php', 'post-new.php?post_type=bws-testimonial', __( 'Testimonial', 'bws-testimonials' ) );
		}
	}
}

if ( ! function_exists ( 'tstmnls_register_widgets' ) ) {
	function tstmnls_register_widgets() {		
		register_widget( 'Testimonials' );
	}
}

/* add help tab  */
if ( ! function_exists( 'tstmnls_add_tabs' ) ) {
	function tstmnls_add_tabs() {
		$screen = get_current_screen();
		if ( ( ! empty( $screen->post_type ) && 'bws-testimonial' == $screen->post_type ) ||
			( isset( $_GET['page'] ) && $_GET['page'] == 'testimonials.php' ) ) {
			$args = array(
				'id' 			=> 'tstmnls',
				'section' 		=> '200897195'
			);
			bws_help_tab( $screen, $args );
		}
	}
}

/* add shortcode content  */
if ( ! function_exists( 'tstmnls_shortcode_button_content' ) ) {
	function tstmnls_shortcode_button_content( $content ) { ?>
		<div id="tstmnls" style="display:none;">
			<fieldset>				
				<?php _e( 'Add testimonials to your page or post', 'bws-testimonials' ); ?>
			</fieldset>
			<input class="bws_default_shortcode" type="hidden" name="default" value="[bws_testimonials]" />
			<div class="clear"></div>
		</div>
	<?php }
}

/**
 * Delete plugin options
 */
if ( ! function_exists( 'tstmnls_plugin_uninstall' ) ) {
	function tstmnls_plugin_uninstall() {
		global $wpdb;
		/* Delete options */
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			$old_blog = $wpdb->blogid;
			/* Get all blog ids */
			$blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );
				delete_option( 'tstmnls_options' );
			}
			switch_to_blog( $old_blog );
		} else {
			delete_option( 'tstmnls_options' );
		}
	}
}

add_action( 'admin_menu', 'tstmnls_admin_menu' );
		
add_action( 'init', 'tstmnls_init' );
add_action( 'admin_init', 'tstmnls_admin_init' );
add_action( 'widgets_init', 'tstmnls_register_widgets' );
add_action( 'plugins_loaded', 'tstmnls_plugins_loaded' );

add_action( 'save_post', 'tstmnls_save_postdata' );
add_filter( 'content_save_pre', 'tstmnls_content_save_pre', 10, 1 );
/* Display Featured Post */
add_action( 'tstmnls_show_testimonials', 'tstmnls_show_testimonials' );
/* custom filter for bws button in tinyMCE */
add_filter( 'bws_shortcode_button_content', 'tstmnls_shortcode_button_content' );
add_shortcode( 'bws_testimonials', 'tstmnls_show_testimonials' );
/* Add style for admin page */
add_action( 'admin_enqueue_scripts', 'tstmnls_admin_head' );
add_action( 'wp_enqueue_scripts', 'tstmnls_wp_head' );
/* add admin notices */
add_action( 'admin_notices', 'tstmnls_admin_notices' );
/* Additional links on the plugin page */
add_filter( 'plugin_action_links', 'tstmnls_plugin_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'tstmnls_register_plugin_links', 10, 2 );

register_uninstall_hook( __FILE__, 'tstmnls_plugin_uninstall' );