<?php
function wppr_get_plugin_data()
{

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
                'screenshots' => false,
                'versions' => false,
                'homepage' => false,
                'added' => false,
                'last_updated' => false,
                'compatibility' => false,
                'tested' => false,
                'requires' => false,
                'downloadlink' => false,
                'banners' => false,
            )
        );
        set_transient('wppr_data', $call_api, DAY_IN_SECONDS);
        error_log(print_r($call_api, true),0);
    }

    return $call_api;
}

function wppr_display_plugin_data($plugin_data)
{

    /** Check for Errors & Display the results */
    if (is_wp_error($plugin_data)) {
        echo '<pre>' . print_r($plugin_data->get_error_message(), true) . '</pre>';
    } elseif (isset($plugin_data->tags)) {

        wppr_get_template('plugin-details', $plugin_data);
    }
}
function wppr_display_plugin_ranking($plugin_data)
{
    if (isset($plugin_data->tags)) {

        $ranking_data = get_transient('wppr_ranking_data');
        if ($ranking_data === false || isset($_GET['fresh'])) {
            $ranking_data = array();
            foreach ($plugin_data->tags as $tag_slug => $tag) {
                $ranking_data[$tag_slug] = wppr_get_tag_results($tag);
            }
            // Add custom keywords if available
            if (!empty(WPPR_Keywords) && is_array(WPPR_Keywords)) {
                foreach (WPPR_Keywords as $keyword) {
                    $keyword_slug = sanitize_title($keyword);
                    $ranking_data[$keyword_slug] = wppr_get_tag_results($keyword);
                }
            }
            set_transient('wppr_ranking_data', $ranking_data, DAY_IN_SECONDS);
        }

        wppr_get_template('plugin-rankings', $ranking_data);
    }
}

function wppr_get_tag_results($tag, $page = 1, $saved_data = array())
{

    $response = plugins_api(
        'query_plugins',
        array(
            'page' => $page,
            'per_page' => 20,
            'search' => $tag,
            'fields' => array(
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
                'requires_php' => false,
                'downloadlink' => false,
                'ratings' => false,
                'icons' => false,
            )
        )
    );

    $results = isset($response->plugins) ? $response->plugins : array();

    // Check if our plugin is in the results
    $key = wppr_get_plugin_position($results);
    $position = (int)$key + 1;
    // prepare returned data
    $data = array(
        'tag' => $tag,
        'page' => $page,
        'position' => $page === 1 ? $position : (($page - 1) * 20) + $position,
        'top_competitors' => isset($saved_data['top_competitors']) ? $saved_data['top_competitors'] : wppr_get_plugin_competitors($results),
        'top_competitor_tags' => isset($saved_data['top_competitor_tags']) ? $saved_data['top_competitor_tags'] : wppr_get_plugin_competitor_tags($results),
    );

    if ($key === false && $page < 21 && isset($response->info) && $page < (int)$response->info['pages']) {
        // If the plugin is not in the current page results, check next page
        return wppr_get_tag_results($tag, $page + 1, $data);
    } elseif ($key === false) {
        // If the plugin is not in the results, and the page number is greater than 20, update data and return it
        $data['page'] = "+" . $data['page'];
        $data['position'] = "+" . $data['position'];
    }

    return $data;
}

function wppr_get_plugin_position($results)
{
    return array_search(WPPR_Slug, array_column((array)$results, 'slug'));
}

function wppr_get_plugin_competitors($results)
{
    $competitors = array();
    $i = 0;
    foreach ($results as $plugin) {
        // Only get the first 5 competitors
        if ($i > 5) {
            break;
        }
        if (isset($plugin['author_profile'])) unset($plugin['author_profile']);
        if (isset($plugin['download_link'])) unset($plugin['download_link']);
        if (isset($plugin['tags'])) unset($plugin['tags']);
        $competitors[] = $plugin;
        $i++;
    }

    return $competitors;
}

function wppr_get_plugin_competitor_tags($results)
{
    $tags = array();
    foreach ($results as $plugin) {

        if (!isset($plugin['tags'])) {
            continue;
        }

        foreach ($plugin['tags'] as $tag_slug => $tag) {
            if (isset($tags[$tag_slug])) {
                $tags[$tag_slug]['count'] = $tags[$tag_slug]['count'] + 1;
            } else {
                $tags[$tag_slug] = array(
                    'label' => $tag,
                    'count' => 1,
                );
            }
        }
    }

    usort($tags, function ($a, $b) {
        return $b['count'] > $a['count'];
    });

    return $tags;
}


function wppr_get_formatted_number(float $num, int $precision = 2)
{
    $absNum = abs($num);

    if ($absNum < 1000) {
        return (string)round($num, $precision);
    }

    $groups = ['k', 'M', 'B', 'T', 'Q'];

    foreach ($groups as $i => $group) {
        $div = 1000 ** ($i + 1);

        if ($absNum < $div * 1000) {
            return round($num / $div, $precision) . $group;
        }
    }

    return '999Q+';
}
function wppr_get_template($name, $args = array())
{

    if ($overridden_template = locate_template('wppr/' . $name . '.php')) {
        /*
         * locate_template() returns path to file.
         * if either the child theme or the parent theme have overridden the template.
         */
        load_template($overridden_template, true, $args);
    } else {
        /*
         * If neither the child nor parent theme have overridden the template,
         * we load the template from the 'templates' sub-directory of the directory this file is in.
         */
        load_template(dirname(__DIR__) . '/templates/' . $name . '.php', true, $args);
    }
}
