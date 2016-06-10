<?php
/**
 * Plugin Name: Network Sites Counts Dashboard Widget
 * Plugin URI:  http://webdevstudios.com
 * Description: Display a list of post counts for all your sites in a network.
 * Version:     0.1.1
 * Author:      WebDevStudios
 * Author URI:  http://webdevstudios.com
 * Donate link: http://webdevstudios.com
 * License:     GPLv2+
 * Text Domain: n_s_c_d_widget
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2014 WebDevStudios (email : contact@webdevstudios.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

class Network_Sites_Counts_Dashboard_Widget {

	const VERSION = '0.1.1';

	public function hooks() {

		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'admin_hooks' ) );

	}

	/**
	 * Init hooks
	 *
	 * @since 0.1.0
	 */
	public function init() {

		$locale = apply_filters( 'plugin_locale', get_locale(), 'n_s_c_d_widget' );
		load_textdomain( 'n_s_c_d_widget', WP_LANG_DIR . '/n_s_c_d_widget/n_s_c_d_widget-' . $locale . '.mo' );
		load_plugin_textdomain( 'n_s_c_d_widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	}

	/**
	 * Hooks for the Admin
	 *
	 * @since 0.1.0
	 */
	public function admin_hooks() {

		// Add network dashboard to display data.
		add_action( 'wp_network_dashboard_setup', array( $this, 'network_dashboard_widget' ) );

	}

	/**
	 * Register our dashboard widget
	 *
	 * @since 0.1.0
	 */
	function network_dashboard_widget() {

		// Filter dasboard widget title.
		$title = apply_filters( 'n_s_c_d_widget_title', __( 'Network Posts Count', 'n_s_c_d_widget' ) );
		wp_add_dashboard_widget( 'network_sites_counts_dashboard_widget', $title, array( $this, 'dashboard_widget' ) );

	}

	/**
	 * Handles rendering the widget
	 *
	 * @since 0.1.0
	 */
	function dashboard_widget() {

		require_once 'includes/N_S_C_Data.php';
		$network_info = new N_S_C_Data();
		$all_network_counts = $network_info->all_sites_post_count();

		if ( empty( $all_network_counts ) ) {
			echo '<p>'. esc_html__( 'No network sites!', 'n_s_c_d_widget' ) .'</p>';
			return;
		}

		$post_type = get_post_type_object( $network_info->args( 'post_type' ) );
		$post_type_name = isset( $post_type->labels->name ) ? $post_type->labels->name : __( 'Post' );

		$total_published = 0;
		$total_drafts = 0;
		?>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Site', 'n_s_c_d_widget' ); ?></th>
					<th><?php printf( esc_html__( 'Published %s', 'n_s_c_d_widget' ), $post_type_name ); ?></th>
					<th><?php printf( esc_html__( 'Draft %s', 'n_s_c_d_widget' ), $post_type_name ); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th><?php esc_html_e( 'Site', 'n_s_c_d_widget' ); ?></th>
					<th><?php printf( esc_html__( 'Published %s', 'n_s_c_d_widget' ), $post_type_name ); ?></th>
					<th><?php printf( esc_html__( 'Draft %s', 'n_s_c_d_widget' ), $post_type_name ); ?></th>
				</tr>
			</tfoot>
			<tbody>
			<?php

		foreach ( $all_network_counts as $path => $counts ) {
	    	$total_published += $counts->publish;
	    	$total_drafts += $counts->draft;
	    	$path = explode( ':', $path );
				?>
				<tr>
					<td><?php echo $path[1]; ?></td>
					<td><?php echo $counts->publish; ?></td>
					<td><?php echo $counts->draft; ?></td>
				</tr>
				<?php
		}
				?>
				<tr>
					<td><b><?php esc_html_e( 'Total count', 'n_s_c_d_widget' ); ?></b></td>
					<td><?php echo $total_published; ?></td>
					<td><?php echo $total_drafts; ?></td>
				</tr>
			</tbody>
		</table>
		<?php
	}
}

// Init our class.
$Network_Sites_Counts_Dashboard_Widget = new Network_Sites_Counts_Dashboard_Widget();
$Network_Sites_Counts_Dashboard_Widget->hooks();

