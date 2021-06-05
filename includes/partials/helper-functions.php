<?php
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
        'position' => ($page * 20) + $position,
        'top_competitors' => isset($saved_data['top_competitors']) ? $saved_data['top_competitors'] : wppr_get_plugin_competitors($results),
        'top_competitor_tags' => isset($saved_data['top_competitor_tags']) ? $saved_data['top_competitor_tags'] : wppr_get_plugin_competitor_tags($results),
    );

    if ($key === false && $page < 21 && $page < (int)$response->info['pages']) {
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
