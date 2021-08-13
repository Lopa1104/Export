<?php
/**
 * The plugin bootstrap file.
 *
 * @link              https://github.com/Lopa1104/
 * @since             1.0.0
 * @package           Core_Functions
 *
 * @wordpress-plugin
 * Plugin Name:       Export post Plugin
 * Plugin URI:        https://github.com/Lopa1104/
 * Description:       This plugin is responsible for Export post data.
 * Version:           1.0.0
 * Author:            Lopa
 * Author URI:        https://github.com/Lopa1104/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       core-functions
 */
class PluginExport {
	/**
	 * Currently plugin version.
	 * Start wp actions.
	 */
	public function __construct() {
		add_action( 'admin_head', array( $this, 'custom_js_to_head' ) );
		add_action( 'wp_ajax_nopriv_my_posts', array( $this, 'my_posts' ) );
		add_action( 'wp_ajax_my_posts', array( $this, 'my_posts' ) );
	}
	/**
	 * Call plugin script.
	 * Call JS scripts.
	 */
	public function custom_js_to_head() {
		wp_localize_script( 'export-plugin', 'exportScript', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_script( 'export-plugin', plugins_url( 'js/export.js', __FILE__ ), array( 'jquery' ), '1.0', true );
	}
	/**
	 * Code to create folder in upload.
	 * Create json file.
	 */
	public function my_posts() {
		global $wpdb;
		$upload     = wp_upload_dir();
		$upload_dir = $upload['basedir'];
		$upload_dir = $upload_dir . '/export';
		if ( ! is_dir( $upload_dir ) ) {
			mkdir( $upload_dir, 0777 );
		}
		$data         = array();
		$limit_volume = -1;
		$offset       = 0;
		$post_status  = 'publish';
		$orderby      = 'ID';
		$order        = 'ASC';
		$post_type    = 'post';
		$args         = array(
			'post_type'        => $post_type,
			'orderby'          => $orderby,
			'order'            => $order,
			'offset'           => $offset,
			'posts_per_page'   => $limit_volume,
			'post_status'      => $post_status,
			'suppress_filters' => false,
		);
		$allposts     = array();
		$posts        = new WP_Query( $args );
		if ( $posts->posts ) {
			foreach ( $posts->posts as $post ) {
				$feat_image = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) );
				if ( ! empty( $feat_image ) ) {
					$post->image = $feat_image;
				}
				$post_categories = wp_get_post_categories( $post->ID );
				if ( $post_categories ) {
					$cats = array();
					foreach ( $post_categories as $c ) {
							$cat    = get_category( $c );
							$cats[] = $cat->name;
					}
					if ( ! empty( $cats ) ) {
						$post->categories = implode( ',', $cats );
					}
				}
				$data[] = $post;
			}
		}
		$date = gmdate( 'Ymdhis' );
		WP_Filesystem();
		global $wp_filesystem;
		$formdata = "{$upload_dir}/file-{$date}.json";
		$wp_filesystem->put_contents( $formdata, wp_json_encode( $data ) );
		$formurl = home_url( "/wp-content/uploads/export/file-{$date}.json" );
		echo esc_attr( $formurl );
		die();
	}

}

$export = new PluginExport();
