<?php
/*
Plugin Name: Testimonials by BestWebSoft
Plugin URI: https://bestwebsoft.com/products/wordpress/plugins/testimonials/
Description: Add testimonials and feedbacks from your customers to WordPress posts, pages and widgets.
Author: BestWebSoft
Text Domain: bws-testimonials
Domain Path: /languages
Version: 1.0.0
Author URI: https://bestwebsoft.com/
License: GPLv3 or later
*/

/*  @ Copyright 2019  BestWebSoft  ( https://support.bestwebsoft.com )

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

		$settings = add_submenu_page( 'edit.php?post_type=bws-testimonial', __( 'Testimonials Settings', 'bws-testimonials' ), __( 'Settings', 'bws-testimonials' ), 'manage_options', "testimonials.php", 'tstmnls_settings_page' );
		add_submenu_page( 'edit.php?post_type=bws-testimonial', 'BWS Panel', 'BWS Panel', 'manage_options', 'tstmnls-bws-panel', 'bws_add_menu_render' );

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
		global $tstmnls_plugin_info, $pagenow;

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );

		if ( empty( $tstmnls_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$tstmnls_plugin_info = get_plugin_data( __FILE__ );
		}

		/* Call register settings function */
		if ( ! is_admin() || 'widgets.php' == $pagenow || 'plugins.php' == $pagenow || ( isset( $_REQUEST['page'] ) && 'testimonials.php' == $_REQUEST['page'] ) ) {
			tstmnls_register_settings();
		}

		/* Function check if plugin is compatible with current WP version */
		bws_wp_min_version_check( plugin_basename( __FILE__ ), $tstmnls_plugin_info, '3.9' );

		tstmnls_register_testimonial_post_type();

		tstmnls_show_testimonials_form();

		/* Redirect if testimonials form send successfully */
		if ( ! isset( $_POST["tstmnls_testimonial_author"],
			$_POST["tstmnls_testimonial_title"],
			$_POST["tstmnls_testimonial_comment"] ) && isset( $_POST["redirect_once"] ) ) {
			$_POST["redirect_once"] = FALSE;
			$url = add_query_arg( 'message', 'true', $_SERVER['REQUEST_URI'] );
			header( "Location: " . $url );
			exit;
		}
	}
}

if ( ! function_exists ( 'tstmnls_admin_init' ) ) {
	function tstmnls_admin_init() {
		global $bws_plugin_info, $tstmnls_plugin_info, $bws_shortcode_list;

		if ( empty( $bws_plugin_info ) )
			$bws_plugin_info = array( 'id' => '180', 'version' => $tstmnls_plugin_info["Version"] );

		add_meta_box( 'custom-metabox', __( 'Testimonials Info', 'bws-testimonials' ), 'tstmnls_custom_metabox', 'bws-testimonial', 'normal', 'high' );

		/* add Testimonials to global $bws_shortcode_list */
		$bws_shortcode_list['tstmnls'] = array( 'name' => 'Testimonials', 'js_function' => 'tstmnls_shortcode_init' );
	}
}

if ( ! function_exists ( 'tstmnls_register_testimonial_post_type' ) ) {
	function tstmnls_register_testimonial_post_type() {
		$args = array(
			'label'				=> __( 'Testimonials', 'bws-testimonials' ),
			'singular_label'	=> __( 'Testimonial', 'bws-testimonials' ),
			'public'			=> true,
			'show_ui'			=> true,
			'capability_type'	=> 'post',
			'hierarchical'		=> false,
			'rewrite'			=> true,
			'supports'			=> array( 'title', 'editor', 'thumbnail' ),
			'labels'			=> array(
				'add_new'				=> __( 'Add New', 'bws-testimonials' ),
				'add_new_item'			=> __( 'Add a new testimonial', 'bws-testimonials' ),
				'edit_item'				=> __( 'Edit testimonials', 'bws-testimonials' ),
				'new_item'				=> __( 'New testimonial', 'bws-testimonials' ),
				'view_item'				=> __( 'View testimonials', 'bws-testimonials' ),
				'search_items'			=> __( 'Search testimonials', 'bws-testimonials' ),
				'not_found'				=> __( 'No testimonials found', 'bws-testimonials' ),
				'not_found_in_trash'	=> __( 'No testimonials found in Trash', 'bws-testimonials' ),
				'filter_items_list'		=> __( 'Testimonials list filter', 'bws-testimonials' ),
				'items_list_navigation' => __( 'Testimonials list navigation', 'bws-testimonials' ),
				'items_list'			=> __( 'Testimonials list', 'bws-testimonials' )
			),
		);
		register_post_type( 'bws-testimonial' , $args );
	}
}

/**
 * @return array Default plugin options
 * @since 0.2.4
 */
if ( ! function_exists( 'tstmnls_get_option_defaults' ) ) {
	function tstmnls_get_option_defaults() {
		global $tstmnls_plugin_info;

		if ( ! $tstmnls_plugin_info ) {
			$tstmnls_plugin_info = get_plugin_data( __FILE__ );
		}

		$option_defaults = array(
			'plugin_option_version'		=> $tstmnls_plugin_info["Version"],
			'widget_title'				=> __( 'Testimonials', 'bws-testimonials' ),
			'count'						=> '5',
			'display_settings_notice'	=> 1,
			'order_by'					=> 'date',
			'order'						=> 'DESC',
			'suggest_feature_banner'	=> 1,
			'permissions'				=> 'all',
			'auto_publication'			=> 0,
			/* form labels */
			'gdpr_text'					=> '',
			'gdpr_link'					=> '',
			'gdpr_tm_name'				=> __( 'I consent to having this site collect my personal data.', 'bws-testimonials' ),
			/* To email settings */
			'gdpr'						=> 0,
			'recaptcha_cb'				=> 0,
		);
		return $option_defaults;
	}
}

/**
* Register settings for plugin
*/
if ( ! function_exists( 'tstmnls_register_settings' ) ) {
	function tstmnls_register_settings() {
		global $tstmnls_options, $tstmnls_plugin_info;

		$tstmnls_option_defaults = tstmnls_get_option_defaults();

		/* Install the option defaults */
		if ( ! get_option( 'tstmnls_options' ) ) {
			add_option( 'tstmnls_options', $tstmnls_option_defaults );
		}

		$tstmnls_options = get_option( 'tstmnls_options' );

		if ( ! isset( $tstmnls_options['plugin_option_version'] ) || $tstmnls_options['plugin_option_version'] != $tstmnls_plugin_info["Version"] ) {

			tstmnls_plugin_activate();

			$tstmnls_option_defaults['display_settings_notice'] = 0;
			$tstmnls_options = array_merge( $tstmnls_option_defaults, $tstmnls_options );
			$tstmnls_options['plugin_option_version'] = $tstmnls_plugin_info["Version"];
			update_option( 'tstmnls_options', $tstmnls_options );
		}
	}
}

/**
 * Function for activation
 */
if ( ! function_exists( 'tstmnls_plugin_activate' ) ) {
	function tstmnls_plugin_activate() {
		/* registering uninstall hook */
		if ( is_multisite() ) {
			switch_to_blog( 1 );
			register_uninstall_hook( __FILE__, 'tstmnls_plugin_uninstall' );
			restore_current_blog();
		} else {
			register_uninstall_hook( __FILE__, 'tstmnls_plugin_uninstall' );
		}
	}
}

/**
 * Function to retrieve related plugin status information
 * @param	string	$plugin_name 		The name of related plugin
 * @return	array	$status 			An array with the following key=>value data: 'installed' => bool, 'active' => 'free'|'pro'|'outdated'|false, 'enabled' => bool
 */
if ( ! function_exists( 'tstmnls_get_related_plugin_status' ) ) {
	function tstmnls_get_related_plugin_status( $plugin_name = '' ) {
		$related_plugins = array(
			'recaptcha'		=> array(
				'link_slug'	=> array(
					'free'	=> 'google-captcha/google-captcha.php',
					'pro'	=> 'google-captcha-pro/google-captcha-pro.php'
				),
				'options_name'	=> 'gglcptch_options'
			),
		);

		$status = array(
			'installed'		=> false,
			'active'		=> false,
			'enabled'		=> false
		);

		if ( empty( $plugin_name ) || ! array_key_exists( $plugin_name, $related_plugins ) ) {
			return $status;
		}

		$plugin = $related_plugins[ $plugin_name ];

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		$all_plugins = get_plugins();

		foreach ( $plugin['link_slug'] as $link_slug ) {
			if ( array_key_exists( $link_slug, $all_plugins ) ) {
				$is_installed = true;
				break;
			}
		}

		if ( ! isset( $is_installed ) ) {
			return $status;
		}

		$status['installed'] = true;

		foreach ( $plugin['link_slug'] as $key => $link_slug ) {
			if ( is_plugin_active( $link_slug ) ) {
				$version = $key;
				break;
			}
		}

		if ( ! isset( $version ) ) {
			return $status;
		}

		$status['active'] = $version;

		if ( is_multisite() ) {
			if ( get_site_option( $plugin['options_name'] ) ) {
				$plugin_options = get_site_option( $plugin['options_name'] );
				if ( ! ( isset( $plugin_options['network_apply'] ) && 'all' == $plugin_options['network_apply'] ) ) {
					if ( get_option( $plugin['options_name'] ) ) {
						$plugin_options = get_option( $plugin['options_name'] );
					}
				}
			} elseif ( get_option( $plugin['options_name'] ) ) {
				$plugin_options = get_option( $plugin['options_name'] );
			}
		} else {
			if ( get_option( $plugin['options_name'] ) ) {
				$plugin_options = get_option( $plugin['options_name'] );
			}
		}

		if ( empty( $plugin_options ) ) {
			return $status;
		}

		return $status;
	}
}

/**
* Add settings page in admin area
*/
if ( ! function_exists( 'tstmnls_settings_page' ) ) {
	function tstmnls_settings_page() {
		global $title, $tstmnls_options, $tstmnls_plugin_info, $wp_version;
		$message = $error = '';

		if ( isset( $_POST['tstmnls_form_submit'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'tstmnls_check_field' ) ) {

			$tstmnls_options['widget_title']		= isset( $_POST['tstmnls_widget_title'] ) ? stripslashes( esc_html( $_POST['tstmnls_widget_title'] ) ) : __( 'Testimonials', 'bws-testimonials' );
			$tstmnls_options['count']				= isset( $_POST['tstmnls_count'] ) ? intval( $_POST['tstmnls_count'] ) : '5';
			$tstmnls_options['order_by']			= isset( $_POST['tstmnls_order_by'] ) ? $_POST['tstmnls_order_by'] : 'date';
			$tstmnls_options['order']				= isset( $_POST['tstmnls_order'] ) ? $_POST['tstmnls_order'] : 'DESC';
			$tstmnls_options['permissions']			= isset( $_POST['tstmnls_permission'] ) ? $_POST['tstmnls_permission'] : 'all';
			$tstmnls_options['auto_publication'] = isset( $_POST['tstmnls_auto_publication'] ) ? 1 : 0;

			$tstmnls_options['gdpr_tm_name']		= isset( $_POST['tstmnls_gdpr_tm_name'] ) ? esc_html( $_POST['tstmnls_gdpr_tm_name'] ) : $tstmnls_options['gdpr_tm_name'];
			$tstmnls_options['gdpr_text']			= isset( $_POST['tstmnls_gdpr_text'] ) ? esc_html( $_POST['tstmnls_gdpr_text'] ) : $tstmnls_options['gdpr_text'];
			$tstmnls_options['gdpr_link']			= isset( $_POST['tstmnls_gdpr_link'] ) ? esc_html( $_POST['tstmnls_gdpr_link'] ) : $tstmnls_options['gdpr_link'];
			$tstmnls_options['gdpr']				= isset( $_POST['tstmnls_gdpr'] ) ? 1 : 0;
			$tstmnls_options['recaptcha_cb']		= isset( $_POST['tstmnls_enable_recaptcha'] ) ? 1 : 0;

			update_option( 'tstmnls_options', $tstmnls_options );
			$message = __( 'Settings saved.', 'bws-testimonials' );
		}

		$related_plugins = array(
			'recaptcha' => array(
				'name'				=> 'Google Captcha ( reCAPTCHA ) by BestWebSoft',
				'short_name'		=> 'Google Captcha',
				'download_link'		=> 'https://bestwebsoft.com/products/wordpress/plugins/google-captcha/?k=8b945710c30a24dd837c9c53c0aed0f8&amp;pn=180&v=' . $tstmnls_plugin_info["Version"] . '&amp;wp_v=' . $wp_version,
				'status'			=> tstmnls_get_related_plugin_status( 'recaptcha' )
			),

		);

		/* Updating reCAPTCHA options and status on form submit */
			foreach ( $related_plugins as $plugin_slug => $plugin_data ) {
				if ( ! empty( $plugin_data['status']['active'] ) && 'outdated' != $plugin_data['status']['active'] ) {
					$is_enabled = isset( $_POST["tstmnls_enable_{$plugin_slug}"] ) ? true : false;
					$related_plugins[ $plugin_slug ]['status']['enabled'] = $is_enabled;
					if ( 'recaptcha' == $plugin_slug && get_option( 'gglcptch_options' )  ) {
						$gglcptch_options = get_option( 'gglcptch_options' );
						$gglcptch_options['testimonials_form'] = $is_enabled ? 1 : 0;
						update_option( 'gglcptch_options', $gglcptch_options );
					}
				}
			}

		/* Add restore function */
		if ( isset( $_REQUEST['bws_restore_confirm'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'bws_settings_nonce_name' ) ) {
			$tstmnls_options = tstmnls_get_option_defaults();
			update_option( 'tstmnls_options', $tstmnls_options );
			$message = __( 'All plugin settings were restored.', 'bws-testimonials' );
		} /* end */ ?>
		<div class="wrap">
			<h1><?php echo $title; ?></h1>
			<noscript>
            	<div class="error below-h2">
                	<p><strong><?php _e( 'WARNING', 'bws-testimonials' ); ?>
                    	    :</strong> <?php _e( 'The plugin works correctly only if JavaScript is enabled.', 'bws-testimonials' ); ?>
                	</p>
            	</div>
        	</noscript>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab<?php if ( ! isset( $_GET['action'] ) || ( isset( $_GET['action'] ) && 'custom_code' != $_GET['action'] ) ) echo ' nav-tab-active'; ?>" href="edit.php?post_type=bws-testimonial&page=testimonials.php"><?php _e( 'Settings', 'bws-testimonials' ); ?></a>
				<a class="nav-tab <?php if ( isset( $_GET['action'] ) && 'custom_code' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="edit.php?post_type=bws-testimonial&page=testimonials.php&amp;action=custom_code"><?php _e( 'Custom code', 'bws-testimonials' ); ?></a>
			</h2>
			<?php bws_show_settings_notice(); ?>
			<div class="updated fade below-h2" <?php if ( "" == $message || "" != $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<div class="error below-h2" <?php if ( "" == $error ) echo "style=\"display:none\""; ?>><p><?php echo $error; ?></p></div>
			<?php if ( ! isset( $_GET['action'] ) || ( isset( $_GET['action'] ) && 'custom_code' != $_GET['action'] ) ) {
				if ( isset( $_REQUEST['bws_restore_default'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'bws_settings_nonce_name' ) ) {
					bws_form_restore_default_confirm( plugin_basename( __FILE__ ) );
				} else { ?>
					<form id="tstmnls_settings_form" class="bws_form" method='post' action=''>
						<p><?php printf(
								'%1$s "<strong>%2$s</strong>" %3$s.',
								__( 'If you would like to display testimonials in widget, you need to add ', 'bws-testimonials' ),
								__( 'Testimonials Widget', 'bws-testimonials' ),
								__( 'on the Widgets page', 'bws-testimonials' )
							); ?>
						</p>
						<div><?php printf(
							__( "If you would like to add testimonials or testimonials form to your page or post, please use %s button", 'bws-testimonials' ),
							'<span class="bws_code"><span class="bwsicons bwsicons-shortcode"></span></span>' );
							echo bws_add_help_box( sprintf(
								__( "You can add testimonials to your page or post by clicking on %s button in the content edit block using the Visual mode. If the button isn't displayed, please use the shortcode %s or use %s shortcode to add testimonials form.", 'bws-testimonials' ),
								'<code><span class="bwsicons bwsicons-shortcode"></span></code>',
								'<code>[bws_testimonials]</code>',
								'<code>[bws_testimonials_form]</code>'
							) ); ?>
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
									<th scope="row"><?php _e( 'Widget Title', 'bws-testimonials' ); ?></th>
									<td>
										<input type="text" class="text" maxlength="250" value="<?php echo $tstmnls_options['widget_title']; ?>" name="tstmnls_widget_title"/>
									</td>
								</tr>
								<tr>
									<th scope="row"><?php _e( 'Number of Testimonials to be Displayed', 'bws-testimonials' ); ?></th>
									<td>
										<input type="number" required class="text" min="1" max="10000" value="<?php echo $tstmnls_options['count']; ?>" name="tstmnls_count" />
									</td>
								</tr>
								<tr>
									<th scope="row"><?php _e( 'Sort Testimonials by', 'bws-testimonials' ); ?></th>
									<td>
										<fieldset>
											<label>
												<input type="radio" name="tstmnls_order_by" value="ID" <?php checked( 'ID', $tstmnls_options["order_by"] ); ?> /> <?php _e( 'Testimonial ID', 'bws-testimonials' ); ?>
											</label>
											<br />
											<label>
												<input type="radio" name="tstmnls_order_by" value="title" <?php checked( 'title', $tstmnls_options["order_by"] ); ?> /> <?php _e( 'Testimonial title', 'bws-testimonials' ); ?>
											</label>
											<br />
											<label>
												<input type="radio" name="tstmnls_order_by" value="date" <?php checked( 'date', $tstmnls_options["order_by"] ); ?> /> <?php _e( 'Date', 'bws-testimonials' ); ?>
											</label>
											<br />
											<label>
												<input type="radio" name="tstmnls_order_by" value="rand" <?php checked( 'rand', $tstmnls_options["order_by"] ); ?> /> <?php _e( 'Random', 'bws-testimonials' ); ?>
											</label>
										</fieldset>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( 'Testimonials Sorting', 'bws-testimonials' ); ?> </th>
									<td><fieldset>
										<label><input type="radio" name="tstmnls_order" value="ASC" <?php checked( 'ASC', $tstmnls_options["order"] ); ?> /> <?php _e( 'ASC ( ascending order from lowest to highest values - 1, 2, 3; a, b, c )', 'bws-testimonials' ); ?></label><br />
										<label><input type="radio" name="tstmnls_order" value="DESC" <?php checked( 'DESC', $tstmnls_options["order"] ); ?> /> <?php _e( 'DESC ( descending order from highest to lowest values - 3, 2, 1; c, b, a )', 'bws-testimonials' ); ?></label>
									</fieldset></td>
								</tr>
								<tr>
									<th scope="row"><?php _e( 'Publication Permissions', 'bws-testimonials' ); ?></th>
									<td>
										<fieldset>
											<label>
												<input type="radio" name="tstmnls_permission" value="logged" <?php checked( 'logged', $tstmnls_options["permissions"] ); ?> /> <?php _e( 'Logged', 'bws-testimonials' ); ?>
											</label>
											<br />
											<label>
												<input type="radio" name="tstmnls_permission" value="all" <?php checked( 'all', $tstmnls_options["permissions"] ); ?> /> <?php _e( 'All', 'bws-testimonials' ); ?>
											</label>
										</fieldset>
									</td>
								</tr>
								<tr>
									<th scope="row"><?php _e( 'Publish Automatically', 'bws-testimonials' ); ?></th>
									<td>
										<input type="checkbox" name="tstmnls_auto_publication" id="tstmnls_auto_publication" <?php checked( 1, $tstmnls_options['auto_publication'] ); ?> />
										<label><?php _e( 'Enable to publish new testimonials automatically. Otherwise, new testimonials will be saved as drafts.', 'bws-testimonials' ); ?></label>
									</td>
								</tr>
								<tr>
									<?php /* Display reCAPTCHA settings */
									foreach ( $related_plugins as $plugin_slug => $plugin_data ) { ?>
										<tr valign="top">
											<th scope="row">
												<label for="<?php echo "tstmnls-enable-{$plugin_slug}"; ?>">
													<?php printf(
														__( 'Add %s', 'bws-testimonials' ),
														$plugin_data['short_name']
													); ?>
												</label>
											</th>
											<td>
												<label for="tstmnls-enable-<?php echo $plugin_slug; ?>">
													<input type="checkbox"
														name="tstmnls_enable_<?php echo $plugin_slug; ?>"
														id="<?php echo "tstmnls-enable-{$plugin_slug}"; ?>"
														<?php checked( $plugin_data['status']['installed'] && ! empty( $tstmnls_options['recaptcha_cb'] ) );
														disabled(
															! $plugin_data['status']['installed'] ||
															! $plugin_data['status']['active'] ||
															'outdated' == $plugin_data['status']['active']
														); ?>
														value="1" >&nbsp;
													<span class="bws_info">
														<?php if ( ! $plugin_data['status']['installed'] ) {
															printf(
																'<a href="%1$s" target="_blank">%2$s</a> %3$s.',
																$plugin_data['download_link'],
																__( 'Download', 'bws-testimonials' ),
																$plugin_data['name']
															);
														} elseif ( ! $plugin_data['status']['active'] ) {
															printf(
																'<a href="%1$s" target="_blank">%2$s</a> %3$s.',
																network_admin_url( 'plugins.php' ),
																__( 'Activate', 'bws-testimonials' ),
																$plugin_data['name']
															);
														} else {
															if ( 'outdated' != $plugin_data['status']['active'] ) {
																printf(
																	__( 'Enable to use %s for Testimonials form.', 'bws-testimonials' ),
																	$plugin_data['name']
																);
															} else {
																printf(
																	__( 'Your %s plugin is outdated. Please update it to the latest version.', 'bws-testimonials' ),
																	$plugin_data['name']
																);
															}
														} ?>
													</span>
												</label>
											</td>
										</tr>
									<?php } ?>
								</tr>
								<tr valign="top">
									<th scope="row">
										<label for="tstmnls-gdpr"><?php _e( 'GDPR Compliance', 'bws-testimonials' ); ?></label>
									</th>
									<td class="tstmnls_gdpr_td">
										<fieldset>
											<input type="checkbox" id="tstmnls_gdpr" name="tstmnls_gdpr" value="1" <?php checked( '1', $tstmnls_options['gdpr'] ); ?> />
											<div id="tstmnls_gdpr_link_options" >
												<label class="tstmnls_privacy_policy_text" >
													<?php _e( 'Checkbox label', 'bws-testimonials' ); ?>
													<input type="text" id="tstmnls_gdpr_tm_name" size="29" name="tstmnls_gdpr_tm_name" value="<?php echo $tstmnls_options['gdpr_tm_name']; ?>"/>
												</label>
												<label class="tstmnls_privacy_policy_text" >
													<?php _e( "Link to Privacy Policy Page", 'bws-testimonials' ); ?>
													<input type="url" id="tstmnls_gdpr_link"  placeholder="http://" name="tstmnls_gdpr_link" value="<?php echo $tstmnls_options['gdpr_link']; ?>" />
												</label>
												<label class="tstmnls_privacy_policy_text" >
													<?php _e( "Text for Privacy Policy Link", 'bws-testimonials' ); ?>
													<input type="text" id="tstmnls_gdpr_text" name="tstmnls_gdpr_text" value="<?php echo $tstmnls_options['gdpr_text']; ?>" />
												</label>
											</div>
										</fieldset>
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
					<?php bws_form_restore_default_settings( plugin_basename( __FILE__ ) );
				}
			} elseif ( 'custom_code' == $_GET['action'] ) {
				bws_custom_code_tab();
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
		function __construct() {
			/* Instantiate the parent object */
			parent::__construct( 'tstmnls_testimonails_widget',
				__( 'Testimonials Widget', 'bws-testimonials' ),
				array( 'description' => __( 'Widget for displaying Testimonials.', 'bws-testimonials' ) )
			);
		}

		function widget( $args, $instance ) {
			global $tstmnls_options;
			if ( empty( $tstmnls_options ) ) {
				$tstmnls_options = get_option( 'tstmnls_options' );
			}
			$widget_title	= isset( $instance['widget_title'] ) ? apply_filters( 'widget_title', $instance['widget_title'], $instance, $this->id_base ) : $tstmnls_options['widget_title'];
			$count			= isset( $instance['count'] ) ? intval( $instance['count'] ) : $tstmnls_options['count'];
			echo $args['before_widget'];
			if ( ! empty( $widget_title ) ) {
				echo $args['before_title'] . $widget_title . $args['after_title'];
			}
			tstmnls_show_testimonials( $count );
			echo $args['after_widget'];
		}

		function form( $instance ) {
			global $tstmnls_options;
			if ( empty( $tstmnls_options ) ) {
				$tstmnls_options = get_option( 'tstmnls_options' );
			}
			$widget_title = isset( $instance['widget_title'] ) ? stripslashes( esc_html( $instance['widget_title'] ) ) : $tstmnls_options['widget_title'];
			$count = isset( $instance['count'] ) ? intval( $instance['count'] ) : $tstmnls_options['count']; ?>

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
			if ( empty( $tstmnls_options ) ) {
				$tstmnls_options = get_option( 'tstmnls_options' );
				$instance = array();
				$instance['widget_title'] = ( isset( $new_instance['widget_title'] ) ) ? stripslashes( esc_html( $new_instance['widget_title'] ) ) : $tstmnls_options['widget_title'];
				$instance['count'] = ( ! empty( $new_instance['count'] ) ) ? intval( $new_instance['count'] ) : $tstmnls_options['count'];
			}
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
		echo tstmnls_show_testimonials_shortcode( array( 'count' => $count ) );
	}
}

if ( ! function_exists( 'tstmnls_show_testimonials_shortcode' ) ) {
	function tstmnls_show_testimonials_shortcode( $attr ) {
		global $tstmnls_options, $wp_query, $post;
		$old_query = $wp_query;
		if ( empty( $tstmnls_options ) ) {
			$tstmnls_options = get_option( 'tstmnls_options' );
		}

		$shortcode_attributes = shortcode_atts( array( 'count' => '' ), $attr );

		if ( empty( $shortcode_attributes['count'] ) ) {
			$shortcode_attributes['count'] = $tstmnls_options['count'];
		}

		$query_args = array(
			'post_type'			=> 'bws-testimonial',
			'post_status'		=> 'publish',
			'posts_per_page'	=> $shortcode_attributes['count'],
			'orderby'			=> $tstmnls_options['order_by'],
			'order'				=> $tstmnls_options['order']
		);

		$content = '<div class="bws-testimonials">';
		$tstmnl_query = new WP_Query( $query_args );

		while ( $tstmnl_query->have_posts() ) {
			$tstmnl_query->the_post();
			$testimonials_info = get_post_meta( $post->ID, '_testimonials_info', true );
			$testimonial_thumbnail = has_post_thumbnail() ? '<div class="tstmnls-thumbnail">' . get_the_post_thumbnail( $post->ID, 'thumbnail' ) . '</div>' : '';
			$content .= '<div class="testimonials_quote">
							<blockquote>' .
								$testimonial_thumbnail;

			$testimonial_content = get_the_content();
			/* insteed 'the_content' filter we use its functions to compability with social buttons */
			/* Hack to get the [embed] shortcode to run before wpautop() */
			require_once( ABSPATH . WPINC . '/class-wp-embed.php' );
			$wp_embed = new WP_Embed();
			$testimonial_content = $wp_embed->run_shortcode( $testimonial_content );
			$testimonial_content = $wp_embed->autoembed( $testimonial_content );
			$testimonial_content = wptexturize( $testimonial_content );
			$testimonial_content = convert_smilies( $testimonial_content );
			$testimonial_content = wpautop( $testimonial_content );
			$testimonial_content = shortcode_unautop( $testimonial_content );
			if ( function_exists( 'wp_make_content_images_responsive' ) ) {
				$testimonial_content = wp_make_content_images_responsive( $testimonial_content );
			}
			$testimonial_content = do_shortcode( $testimonial_content ); /* AFTER wpautop() */
			$testimonial_content = str_replace( ']]>', ']]&gt;', $testimonial_content );

			$content .= $testimonial_content;
			$content .= '</blockquote>';
			if ( is_rtl() ) {
				$content .= '<div class="rtl_testimonial_quote_footer">';
			} else {
				$content .= '<div class="testimonial_quote_footer">';
			}
			$content .=			'<div class="testimonial_quote_author">' . $testimonials_info['author'] . '</div>
									<span>' . $testimonials_info['company_name'] . '</span>
							</div>
						</div>';
		}
		wp_reset_postdata();
		wp_reset_query();
		$wp_query = $old_query;
		$content .= '</div><!-- .bws-testimonials -->';
		return $content;
	}
}
/**
 * Add recaptcha support
 */

if ( ! function_exists( 'tstmnls_add_recaptcha_forms' ) ) {
	function tstmnls_add_recaptcha_forms( $forms ) {
		$forms['testimonials'] = array( "form_name" => __( 'Testimonials Form', 'bws-testimonials' ) );
		return $forms;
	}
}

/**
 * Display testimonials form
 */

if ( ! function_exists( 'tstmnls_show_testimonials_form' ) ) {
	function tstmnls_show_testimonials_form () {
		global $wp_version;
		$content = $subm_result = $form_error = $captcha_error_messages = '';
		$form_author = $form_company = $form_title = $form_content = '';
		$tstmnls_options = get_option( 'tstmnls_options' );

		$status = tstmnls_get_related_plugin_status( 'recaptcha' );

		/* Check if plugin Google Captcha Pro is activated */
		if ( ('free' == $status['active'] || 'pro' == $status['active'] ) && ! empty( $tstmnls_options['recaptcha_cb'] ) && ! gglcptch_is_hidden_for_role() ) {
			$tstmnsl_for_recaptcha = get_option( 'gglcptch_options' );
			$tstmnsl_for_recaptcha = $tstmnsl_for_recaptcha['testimonials'];
		}

		if (
			isset( $_POST['tstmnls_submit_testimonial'] ) &&
			isset( $_POST['tstmnls_field'] ) &&
			wp_verify_nonce( $_POST['tstmnls_field'], 'tstmnls_action' )
		) {

			if ( function_exists( 'gglcptch_check' ) ) {
				$gglcptch_check = gglcptch_check();
			}

			if ( ! empty( $tstmnsl_for_recaptcha ) ) {
				$check = $gglcptch_check['response'];
			} else {
				$check = true;
			}

			$form_author = stripslashes( esc_html( trim( $_POST['tstmnls_testimonial_author'] ) ) );
			$form_company = stripslashes( esc_html( trim( $_POST['tstmnls_testimonial_company_name'] ) ) );
			$form_title = stripslashes( esc_html( trim( $_POST['tstmnls_testimonial_title'] ) ) );
			$form_content = stripslashes( esc_html( trim( $_POST['tstmnls_testimonial_comment'] ) ) );

			if ( true === $check &&
				! empty( $form_author ) &&
				! empty( $form_title ) &&
				! empty( $form_content ) ) {
				if ( ( 'all' == $tstmnls_options['permissions'] ) ){
					$admin_email = get_option( 'admin_email' );
					$admin_user = get_user_by( 'email', $admin_email );
					$post_author=$admin_user->ID;
				} elseif ( 'logged' == $tstmnls_options['permissions'] ) {
					$post_author = get_current_user_id();
				}

				$post = array(
					'post_type'	=> 'bws-testimonial',
					'post_title'	=> $form_title,
					'post_content'	=> $form_content,
					'post_author'	=> $post_author,
					'meta_input'	=> array(
											'_testimonials_info'	=> array(
											'author'				=> $form_author,
											'company_name'			=> $form_company
										)
					),
				);

				if ( ! empty( $tstmnls_options['auto_publication'] ) ) {
					$post['post_status'] = 'publish';
				} else {
					$post['post_status'] = 'draft';
				}

				$lastid = wp_insert_post( $post );
				if ( $wp_version < "4.4.0" ) {
					add_post_meta( $lastid, '_testimonials_info', $post['meta_input']['_testimonials_info'] );
				}
				unset( $_POST["tstmnls_testimonial_author"],
				$_POST["tstmnls_testimonial_company_name"],
				$_POST["tstmnls_testimonial_title"],
				$_POST["tstmnls_testimonial_comment"],
				$_POST["tstmnls_submit_testimonial"],
				$_POST["tstmnls_GDPR"],
				$_POST["g-recaptcha-response"],
				$_POST["tstmnls_field"],
				$_POST["_wp_http_referer"] );
				$form_author = '';
				$form_company = '';
				$form_title = '';
				$form_content = '';
				/* the variable is used in tstmnls_init function */
				$_POST["redirect_once"] = TRUE;

			} elseif ( false === $check && ! empty( $tstmnls_options['recaptcha_cb'] ) && ! empty( $tstmnsl_for_recaptcha ) ) {
				$captcha_error_messages = gglcptch_get_message();
				$captcha_error_messages = '<p class="tstmnls_error">' . $captcha_error_messages . '</p>';
				$form_error = '<p class="tstmnls_error_form">' . __( 'Please make corrections below and try again.', 'bws-testimonials' ) . '</p>';
			}
		}
		if ( ! is_user_logged_in() && 'logged' == $tstmnls_options["permissions"] ) {
			$content =
			'<div class = "tstmnls_form_div">
				<p>' . __( 'This form is available only for logged in users. Please', 'bws-testimonials' ) . '<a href="' . wp_login_url() . '"> ' .
				__( 'log in', 'bws-testimonials' ) . '</a> ' . __( 'or', 'bws-testimonials' ) .
				'<a href="' . wp_registration_url() . '"> ' . __( 'register', 'bws-testimonials' ) . '</a> ' . __( 'on our site', 'bws-testimonials' ) . '</p>
			</div>';
		} else {
			if ( isset( $_GET['message'] ) ) {
				if ( ! empty( $tstmnls_options['auto_publication'] ) ) {
					$subm_result = '<p class="tstmnls_result">' . __( 'Your testimonial has been published!', 'bws-testimonials' ) . '</p>';
				} else {
					$subm_result = '<p class="tstmnls_result">' . __( 'Your testimonial has been sent to administration!', 'bws-testimonials' ) . '</p>';
				}
			}
			$content =
			'<div class = "tstmnls_form_div">' . $subm_result . '
				<form method="post" name="tstmnls_form_name" id="tstmnls_form_name" action="">
					<h2>' . __( 'Leave your testimonial', 'bws-testimonials' ) . '</h2>
					' . $form_error . '
					<div class="tstmnls_field_form">
						<label for="tstmnls_testimonial_author">' . __( 'Your name:', 'bws-testimonials' ) . '
							<span class="tstmnls_required_symbol"> * </span>
						</label>
						<input type="text" required name="tstmnls_testimonial_author" id="tstmnls_testimonial_author" value="'. $form_author . '" >
					</div>
					<div class="tstmnls_field_form">
						<label for="tstmnls_testimonial_company_name">'. __( 'Your company:', 'bws-testimonials' ) .'</label>
						<input type="text" name="tstmnls_testimonial_company_name" id="tstmnls_testimonial_company_name" value="'. $form_company .'" >
					</div>
					<div class="tstmnls_field_form">
						<label for="tstmnls_testimonial_title">' . __( 'Testimonial title:', 'bws-testimonials' ) . '
							<span class="tstmnls_required_symbol"> * </span>
						</label>
						<input type="text" required name="tstmnls_testimonial_title" id="tstmnls_testimonial_title" value="' . $form_title . '" >
					</div>
					<div class="tstmnls_field_form">
						<label for="tstmnls_testimonial_title">' . __( 'Testimonial:', 'bws-testimonials' ) . '
							<span class="tstmnls_required_symbol"> * </span>
						</label>
						<textarea name="tstmnls_testimonial_comment" required class="tstmnls_testimonial_comment" rows="8" cols="80">' . $form_content . '</textarea>
						<input type="hidden" name="tstmnls_submit_testimonial" id="tstmnls_submit_testimonial" value="1">
					</div>';
					if( ! empty( $tstmnls_options['gdpr'] ) ) {
						$content .= '<div class="tstmnls_field_form">
							<p class="tstmnls-GDPR-wrap">
								<label>
									<input id="tstmnls-GDPR-checkbox" required type="checkbox" name="tstmnls_GDPR" style="vertical-align: middle;"/>'
									. $tstmnls_options['gdpr_tm_name'];
									if( ! empty( $tstmnls_options['gdpr_link'] ) ) {
										$content .= ' ' . '<a href="' . $tstmnls_options['gdpr_link'] . '" target="_blank">' . $tstmnls_options['gdpr_text'] . '</a>';
									} else {
										$content .= '<span>' . ' ' . $tstmnls_options['gdpr_text'] . '</span>';
									}
								$content .= '</label>
							</p>
						</div>';
					}
					if ( ! empty( $tstmnsl_for_recaptcha ) ) {
						$content .= $captcha_error_messages;
						$content .= apply_filters( 'gglcptch_display_recaptcha', '', 'testimonials' );
					}
					$content .= wp_nonce_field( 'tstmnls_action', 'tstmnls_field', true, false ) . '
					<input type="submit" value="' . __( 'Publish', 'bws-testimonials' ) . '">
				</form>
			</div>';
		}
		return $content;
	}
}

/**
 * Add styles for admin page and widget
 */
if ( ! function_exists ( 'tstmnls_admin_head' ) ) {
	function tstmnls_admin_head() {
		global $tstmnls_plugin_info;
		wp_enqueue_style( 'tstmnls_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
		wp_enqueue_script( 'tstmnls_script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery' ), $tstmnls_plugin_info['Version'] );

		if ( isset( $_GET['page'] ) && "testimonials.php" == $_GET['page'] ) {
			bws_enqueue_settings_scripts();
			bws_plugins_include_codemirror();
		}
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
				$this_plugin = plugin_basename( __FILE__ );

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
		$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			if ( ! is_network_admin() )
				$links[] = '<a href="admin.php?page=testimonials.php">' . __( 'Settings', 'bws-testimonials' ) . '</a>';
				$links[] = '<a href="https://support.bestwebsoft.com/hc/en-us/sections/200897195" target="_blank">' . __( 'FAQ', 'bws-testimonials' ) . '</a>';
				$links[] = '<a href="https://support.bestwebsoft.com">' . __( 'Support', 'bws-testimonials' ) . '</a>';
		}
		return $links;
	}
}

/* add admin notices */
if ( ! function_exists ( 'tstmnls_admin_notices' ) ) {
	function tstmnls_admin_notices() {
		global $hook_suffix, $tstmnls_plugin_info;
		if ( 'plugins.php' == $hook_suffix && ! is_network_admin() ) {
			bws_plugin_banner_to_settings( $tstmnls_plugin_info, 'tstmnls_options', 'bws-testimonials', 'admin.php?page=testimonials.php', 'post-new.php?post_type=bws-testimonial' );
		}

		if ( isset( $_REQUEST['page'] ) && 'testimonials.php' == $_REQUEST['page'] ) {
			bws_plugin_suggest_feature_banner( $tstmnls_plugin_info, 'tstmnls_options', 'bws-testimonials' );
		}
	}
}

if ( ! function_exists ( 'tstmnls_register_widgets' ) ) {
	function tstmnls_register_widgets() {
		register_widget( 'Testimonials' );
	}
}

/* add help tab */
if ( ! function_exists( 'tstmnls_add_tabs' ) ) {
	function tstmnls_add_tabs() {
		$screen = get_current_screen();
		if ( ( ! empty( $screen->post_type ) && 'bws-testimonial' == $screen->post_type ) ||
			( isset( $_GET['page'] ) && 'testimonials.php' == $_GET['page'] ) ) {
			$args = array(
				'id'			=> 'tstmnls',
				'section'		=> '200897195'
			);
			bws_help_tab( $screen, $args );
		}
	}
}

/* add shortcode content */
if ( ! function_exists( 'tstmnls_shortcode_button_content' ) ) {
	function tstmnls_shortcode_button_content( $content ) { ?>
		<div id="tstmnls" style="display:none;">
			<fieldset>
				<label>
					<input type="radio" name="tstmnls_select" value="bws_testimonials" checked="checked">
					<span><?php _e( 'Add testimonials to your page or post', 'bws-testimonials' ); ?></span>
				</label>
				<label>
					<input type="radio" name="tstmnls_select" value="bws_testimonials_form">
					<span><?php _e( 'Leave testimonials', 'bws-testimonials' ); ?></span>
				</label>
			</fieldset>
			<input class="bws_default_shortcode" type="hidden" name="default" value="[bws_testimonials]" />
			<script type="text/javascript">
				function tstmnls_shortcode_init() {
					( function( $ ) {
						$( '.mce-reset input[name="tstmnls_select"]' ).on( 'change', function() {
							var shortcode = $( '.mce-reset input[name="tstmnls_select"]:checked' ).val();
							$( '.mce-reset #bws_shortcode_display' ).text( '[' + shortcode + ']' );
						} );
					} )( jQuery );
				}
			</script>
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
				delete_option( 'widget_tstmnls_testimonails_widget' );
			}
			switch_to_blog( $old_blog );
		} else {
			delete_option( 'tstmnls_options' );
			delete_option( 'widget_tstmnls_testimonails_widget' );
		}

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );
		bws_delete_plugin( plugin_basename( __FILE__ ) );
	}
}

/* Plugin uninstall function */
register_activation_hook( __FILE__, 'tstmnls_plugin_activate' );

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
add_shortcode( 'bws_testimonials', 'tstmnls_show_testimonials_shortcode' );
add_shortcode( 'bws_testimonials_form', 'tstmnls_show_testimonials_form' );
/* Add style for admin page */
add_action( 'admin_enqueue_scripts', 'tstmnls_admin_head' );

/* Add style for widget */
add_action( 'wp_enqueue_scripts', 'tstmnls_wp_head' );
/* Add admin notices */
add_action( 'admin_notices', 'tstmnls_admin_notices' );
/* Additional links on the plugin page */
add_filter( 'plugin_action_links', 'tstmnls_plugin_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'tstmnls_register_plugin_links', 10, 2 );
