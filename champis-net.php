<?php
/**
 * Champis.net
 *
 * @package           PluginPackage
 * @author            Félicien Corbat
 * @copyright         2021 Champis.net
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Champis.net
 * Plugin URI:        https://champis.net/viewtopic.php?f=12&t=25454
 * Description:       Afficher les événements mycologiques provenant du forum événements de champis.net
 * Version:           1.0.3
 * Author:            Félicien Corbat
 * Author URI:        https://champis.net
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

/*
Champis.net is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Champis.net is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
*/

add_filter( 'the_content', 'champis_net_add_events');
 
function champis_net_add_events( $content ) {
    
    if (strpos($content, '[champis-events]') !== false) {
        $now=time();
        $response = wp_remote_get( 'https://api.champis.net/v1/events');
        
        if ( is_wp_error( $response ) ) {
             return $content;
        } else {
            $events = json_decode(wp_remote_retrieve_body( $response ), true);
            $events = $events['hydra:member'];
        
            $addContent = "<ul>";
            foreach($events as $event) {
                $beginDate = new DateTime($event['beginDate']);
                $endDate = new DateTime($event['endDate']);
                $addContent .= "<li><div>".$beginDate->format('d.m.Y')." - ".$endDate->format('d.m.Y')."</div>";
                $addContent .= "<div><strong><a href=https://champis.net/viewtopic.php?t=".$event['topic']['id'].">".$event['topic']['title']."</a></strong></div>";
                $addContent .= "<div>".$event['place']."</div>";
                $addContent .= "<div><a href=".$event['url'].">".$event['url']."</a></div><br/>";
             }
        
            $addContent .= "</ul>";
            $number = 1;
            
            $content = str_replace('[champis-events]', $addContent, $content, $number);
            return $content;
        }
    }
    
    return $content;
        
}

