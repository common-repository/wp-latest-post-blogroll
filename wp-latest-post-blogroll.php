<?php
/*
Plugin Name: WP Latest Post Blogroll
Version: 1.0
Description: WP Latest Post Blogroll shows the most recent post title for each blog listed in the blogroll.
Author: LizzyFin
Author URI: http://computeraxe.com/
Plugin URI: http://computeraxe.com/wordpress-plugins/wp-latest-post-blogroll/
*/
/*  
Copyright (c) 2011 LizzyFin

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the <a href="http://wordpress.org/about/gpl/">GNU General Public License</a>
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

Contact the author via http://computeraxe.com/about-axe/ 
*/
/* Version check */
global $wp_version;
$exit_msg = __('WP Latest Post Blogroll requires WordPress 3.0 or newer. This plugin has been deactivated. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update!</a>');
if (version_compare($wp_version,"3.0","<"))
{
exit ($exit_msg);
}
//define plugin path
$wp_latest_blogroll_plugin_url = trailingslashit(WP_PLUGIN_DIR.'/'.dirname(plugin_basename(__FILE__)));
//include file that contains functions for parsing RSS feeds
require_once(ABSPATH . WPINC . '/feed.php');
//filter hook for changing display of bookmarks or blogroll
add_filter('get_bookmarks', 'WPLatestRoll_GetBookmarksFilter');
//filter function to get bookmarks
function WPLatestRoll_GetBookmarksFilter($items)
{
//do nothing if in the admin menu
    if (is_admin())
    {
   return $items;
    }
    
    //parse all blogroll items
    foreach($items as $item)
    {
   //check if the link is public
        if ($item->link_visible=='Y')
        {
        $link_url=trailingslashit($item->link_url);
        
       //simple feed guessing
       if (strstr($link_url,"blogspot"))
       {
           //blogspot blog
           $feed_url=$link_url."feeds/posts/default/";      
       }
            elseif (strstr($link_url,"typepad"))
            {
           //typepad blog
                $feed_url=$link_url."atom.xml";
            }
            else
            {
           //own domain or wordpress blog
                $feed_url=$link_url."feed/";
            }
            
            // use WordPress to fetch the RSS feed, $feedfile is SimplePie object
            $feedfile = fetch_feed($feed_url);
         if (!is_wp_error($feedfile)) { // Checks that the object is created ok 
              // Figure out how many total items there are, but limit it to 5
              $maxitems = $feedfile->get_item_quantity(5); 
              // Build an array of all the items
              $feed_items = $feedfile->get_items(0, $maxitems); 
              $last_item = $feedfile->get_item(0);
              $link_link = $last_item->get_permalink();   
              $link_name = $last_item->get_title();

          $item->link_url=$link_link;
          $item->link_name=$link_name;
}
if (!isset($feedfile)) {
             $item->link_url=$item->link_url;
          $item->link_name=$item->link_name;
}
        }    
        
    }
    //return the items
        return $items;
}
?>