## Introduction
This small tool was initially made to track WP repository ranking for [Mega Forms](https://wordpress.org/plugins/mega-forms/) plugin. I noticed the plugin isn't getting any traction compared to other solution that are far lower in terms of quality, so I decided I need a way to track how's the plugin doing in WP repository search results and try to improve the ranking based on the given results.

I did a small research and found that WordPress has a public API that I can use to pull data about any plugin. All I needed after that was 2 hours to create this small plugin to pull ranking data for the said plugin and present it in the admin dashboard. Later I thought it would be nice to share, especially that there are services charing for similar data.

## Installation

### Step 1
- Download the plugin zip file
- Open the main plugin file `wp-plugin-rank.php`
- Change the constant `WPPR_Name` value to your plugin's name
- Change the constant `WPPR_Slug` value to your plugin's slug
- Save the file

### Step 2
- Log into your WordPress site.
- Go to: Plugins > Add New.
- Upload the compressed version (zip) of the updated plugin
- Activate

Once installed and activated you should see a new sub menu in dashboard under the "tools" tab called `Plugin Rank`

____________________________________________________________

That's it, hope this tool can help you improve you plugin's SEO and get better results.