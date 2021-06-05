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
			// General check for user permissions.
			if (!current_user_can('manage_options')) {
				wp_die(__('You do not have sufficient pilchards to access this page.'));
			}

			if (!function_exists('plugins_api')) {
				require_once(ABSPATH . '/wp-admin/includes/plugin-install.php');
			}


			$call_api = get_transient('wppr_data');
			if ($call_api === false || isset($_GET['fresh'])) {
				/** Prepare data query */
				$call_api = plugins_api(
					'plugin_information',
					array(
						'slug' => WPPR_Slug,
						'description' => false,
						'short_description' => false,
						'donate_link' => false,
						'sections' => false,
						'homepage' => false,
						'added' => false,
						'last_updated' => false,
						'compatibility' => false,
						'tested' => false,
						'requires' => false,
						'downloadlink' => false,
					)
				);
				set_transient('wppr_data', $call_api, DAY_IN_SECONDS);
			}

			# Start building the page
			echo '<div class="wrap mfr-wrap">';

			/** Check for Errors & Display the results */
			if (is_wp_error($call_api)) {
				echo '<pre>' . print_r($call_api->get_error_message(), true) . '</pre>';
			} elseif (isset($call_api->tags)) {

				// General Info
				echo '<h3><b>' . WPPR_Name . ' Details</b></h3>';
				echo '<table class="wppr-plugin-details"><tbody>';

				echo '<tr>';
				echo '<th>Name</th>';
				echo '<td><a href="https://wordpress.org/plugins/' . $call_api->slug . '" target="_blank">' . $call_api->name . '</a></td>';
				echo '</tr>';

				echo '<tr>';
				echo '<th>Slug</th>';
				echo '<td>' . $call_api->slug . '</td>';
				echo '</tr>';

				echo '<tr>';
				echo '<th>Rating</th>';
				echo '<td>' . $call_api->rating . '%</td>';
				echo '</tr>';

				echo '<tr>';
				echo '<th>Downloads</th>';
				echo '<td>' . $call_api->downloaded . '</td>';
				echo '</tr>';

				echo '</tbody></table>';


				// Ranking Info

				echo '<h3><b>' . WPPR_Name . ' Rank</b></h3>';
				echo '<table class="wppr-plugin-rank">';
				// Titles
				echo '<thead>';
				echo '<tr>';
				echo '<th>Search tag</th>';
				echo '<th>Page</th>';
				echo '<th>Position</th>';
				echo '<th>Top 5 competitors</th>';
				echo '<th>Top competitor tags</th>';
				echo '</tr>';
				echo '</thead>';

				$ranking_data = get_transient('wppr_ranking_data');
				if ($ranking_data === false || isset($_GET['fresh'])) {
					$ranking_data = array();
					foreach ($call_api->tags as $tag_slug => $tag) {
						$ranking_data[$tag_slug] = wppr_get_tag_results($tag);
					}
					set_transient('wppr_ranking_data', $ranking_data, DAY_IN_SECONDS);
				}

				// Data
				echo '<tbody>';

				foreach ($ranking_data as $key => $data) {
					echo '<tr>';
					// Search tag
					echo '<td class="wppr-search-tag">' . $data['tag'] ?? $key . '</td>';
					// Page
					echo '<td class="wppr-page">' . $data['page'] . '</td>';
					// Position
					echo '<td class="wppr-position">' . $data['position'] . '</td>';
					// Top competitors
					echo '<td class="wppr-top-competitors">';
					if (!empty($data['top_competitors'])) {
						echo '<ul>';
						foreach ($data['top_competitors'] as $competitor) {
							echo '<li>';
							echo $competitor['name'] . ' <strong>(' . $competitor['slug'] . ')</strong><br>';
							echo 'Downloads ( <strong>' . wppr_get_formatted_number($competitor['downloaded']) . '</strong> ), ';
							echo 'Installs ( <strong>' . wppr_get_formatted_number($competitor['active_installs']) . '</strong> ), ';
							echo 'Rating ( <strong>' . $competitor['rating'] . '%</strong> )';
							echo '</li>';
						}
						echo '</ul>';
					}
					echo '</td>';
					// Top tags
					echo '<td class="wppr-top-competitor-tags">';
					if (!empty($data['top_competitor_tags'])) {
						$i = 0;
						$top_tags = "";
						foreach ($data['top_competitor_tags'] as $tag) {
							if ($i > 10) {
								break;
							}
							$top_tags .= $tag['label'] . ', ';
							$i++;
						}
						echo trim($top_tags, ',');
					}
					echo '</td>';

					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
			}

			echo '<div class="clearfix"></div>';
			if (!isset($_GET['fresh'])) {
				printf('<p><strong>Note:</strong> The data is stored for 24 hours to reduce API calls on each visit, if you want to see fresh data, use this <a href="%s">link</a></p>', add_query_arg('fresh', true, menu_page_url('wp_plugin_rank', false)));
			}
			echo '<p><strong>Made by:</strong> <a href="https://alikhallad.com">Ali Khallad</a></p>';
			echo '</div>';
		}
	}
}
