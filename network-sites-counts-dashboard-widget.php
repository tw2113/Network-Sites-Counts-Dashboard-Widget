<?php
/**
 * Plugin Name: Network Sites Counts Dashboard Widget
 * Plugin URI:  https://michaelbox.net
 * Description: Display a list of post counts for all your sites in a network.
 * Version:     1.0.0
 * Author:      Michael Beckwith
 * Author URI:  https://michaelbox.net
 * License:     GPLv2+
 * Text Domain: network-sites-counts-dashboard-widget
 * Requires PHP: 7.4
 */

/**
 * Copyright (c) 2014 WebDevStudios (email : contact@webdevstudios.com)
 * Copyright (c) 2023 Michael Beckwith (email : tw2113@gmail.com)
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

	const VERSION = '1.0.0';

	public function hooks() {
		add_action( 'init', [ $this, 'init' ] );
		add_action( 'admin_init', [ $this, 'admin_hooks' ] );
		add_action( 'wp_insert_site', [ $this, 'flush_transient_on_new_site' ] );
	}

	/**
	 * Init hooks.
	 *
	 * @since 0.1.0
	 */
	public function init() {
		load_plugin_textdomain( 'network-sites-counts-dashboard-widget' );
	}

	/**
	 * Hooks for the Admin.
	 *
	 * @since 0.1.0
	 */
	public function admin_hooks() {
		add_action( 'wp_network_dashboard_setup', [ $this, 'network_dashboard_widget' ] );
	}

	/**
	 * Register our dashboard widget.
	 *
	 * @since 0.1.0
	 */
	function network_dashboard_widget() {
		$title = apply_filters( 'network_sites_counts_widget_title', __( 'Network Posts Count', 'network-sites-counts-dashboard-widget' ) );
		wp_add_dashboard_widget( 'network_sites_counts_dashboard_widget', $title, [ $this, 'dashboard_widget' ] );
	}

	/**
	 * Flush our all site transient on new site add.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site $new_site
	 *
	 * @return WP_Site
	 */
	public function flush_transient_on_new_site( WP_Site $new_site ) : WP_Site {
		global $wpdb;
		$transient_partial = 'all_sites_post_count';
		$transients = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->sitemeta} WHERE `meta_key` LIKE %s",
				'%' . $wpdb->esc_like( $transient_partial ) . '%'
			),
			ARRAY_A
		);
		if ( ! empty( $transients ) && is_array( $transients ) ) {
			foreach ( $transients as $transient ) {
				delete_site_option( $transient['meta_key'] );
			}
		}
		return $new_site;
	}

	/**
	 * Handles rendering the widget.
	 *
	 * @since 0.1.0
	 */
	function dashboard_widget() {

		global $wpdb;
		require_once 'includes/Network_Sites_Counts_Data.php';
		$network_info       = new Network_Sites_Counts_Data( $wpdb );
		$all_network_counts = $network_info->all_sites_post_count();

		if ( empty( $all_network_counts ) ) {
			echo '<p>'. esc_html__( 'No network sites!', 'network-sites-counts-dashboard-widget' ) .'</p>';
			return;
		}

		$post_type      = get_post_type_object( $network_info->args( 'post_type' ) );
		$post_type_name = $post_type->labels->name ?? esc_html__( 'Post', 'network-sites-counts-dashboard-widget' );

		$total_published = $total_drafts = 0;
		?>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Site', 'network-sites-counts-dashboard-widget' ); ?></th>
					<th><?php printf(
						esc_html__( 'Published %s', 'network-sites-counts-dashboard-widget' ),
						$post_type_name
						); ?>
					</th>
					<th><?php printf(
						esc_html__( 'Draft %s', 'network-sites-counts-dashboard-widget' ),
						$post_type_name
						); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th><?php esc_html_e( 'Site', 'network-sites-counts-dashboard-widget' ); ?></th>
					<th><?php
						printf(
							esc_html__( 'Published %s', 'network-sites-counts-dashboard-widget' ),
							$post_type_name
						); ?>
					</th>
					<th><?php
						printf(
							esc_html__( 'Draft %s', 'network-sites-counts-dashboard-widget' ),
							$post_type_name
						); ?>
					</th>
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
					<td><b><?php esc_html_e( 'Total count', 'network-sites-counts-dashboard-widget' ); ?></b></td>
					<td><?php echo $total_published; ?></td>
					<td><?php echo $total_drafts; ?></td>
				</tr>
			</tbody>
		</table>
		<?php
	}
}
$Network_Sites_Counts_Dashboard_Widget = new Network_Sites_Counts_Dashboard_Widget();
$Network_Sites_Counts_Dashboard_Widget->hooks();

