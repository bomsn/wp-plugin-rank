<?php
if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('WP_Plugin_Rank')) {
	class WP_Plugin_Rank
	{

		public $version = '1.0.0';
		public function __construct()
		{

			$this->load_dependencies();
			$this->define_hooks();
		}

		/**
		 * Load all dependencies here.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function load_dependencies()
		{
			require plugin_dir_path(__FILE__) . 'partials/helper-functions.php';
		}
		/**
		 * Register all of the hooks related to the admin and public functionality
		 * of the plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function define_hooks()
		{

			# Scripts and styles hooks
			add_action('admin_enqueue_scripts', array($this, 'enqueue_styles_admin'));
			add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts_admin'));


			# Add necessary dashboard menus
			add_action('admin_menu', array($this, 'admin_menus'));
		}

		/**
		 * Enqueue style and javascript files
		 * of the plugin.
		 *
		 * @since    1.0.0
		 * @access   public
		 */
		public function enqueue_styles_admin($hook)
		{

			wp_enqueue_style('wp-plugin-rank-css', plugin_dir_url(__FILE__) . 'assets/css/wp-plugin-rank-admin.css', array(), $this->version, 'all');
		}
		public function enqueue_scripts_admin($hook)
		{
			wp_enqueue_script('wp-plugin-rank-js', plugin_dir_url(__FILE__) . 'assets/js/wp-plugin-rank-admin.js', array('jquery'), $this->version, false);
		}

		/**
		 * Add dashboard menus
		 *
		 * - Branches -> tools
		 * 
		 * @since    1.0.0
		 */
		public function admin_menus()
		{
			// Tools sub menu
			add_submenu_page(
				'tools.php',
				'Plugin Rank',
				'Plugin Rank',
				'manage_options',
				'wp_plugin_rank',
				array($this, 'plugin_rank_details')
			);
		}

		/**
		 * Tools dashboard page handler
		 *
		 * @since    1.0.0
		 */
		public function plugin_rank_details()
		{

			if (!function_exists('plugins_api')) {
				require_once(ABSPATH . '/wp-admin/includes/plugin-install.php');
			}

			$plugin_data = wppr_get_plugin_data();

			if (!isset($_GET['fresh'])) {
				printf('<p><strong>Note:</strong> The data is stored for 24 hours to reduce API calls on each visit, if you want to see fresh data, use this <a href="%s">link</a></p>', add_query_arg('fresh', true, menu_page_url('wp_plugin_rank', false)));
			}

			# Start building the page markup
			echo '<div class="wrap mfr-wrap">';
			echo '<div class="clearfix"></div>';

			wppr_display_plugin_data($plugin_data);
			wppr_display_plugin_ranking($plugin_data);

			echo '<div class="clearfix"></div>';
			echo '</div>';
			echo '<p><strong>Made by:</strong> <a href="https://alikhallad.com">Ali Khallad</a></p>';
		}
	}
}
