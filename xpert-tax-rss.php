<?php

/*
Plugin Name: OER Taxonomy RSS
Plugin URI: http://simonwheatley.co.uk/wordpress/xtr
Description: Extends and mangles the WP RSS widget to always query OER repositories, including Pat's bag of tricks.
Version: 0.2
Author: Simon Wheatley
Author URI: http://simonwheatley.co.uk/wordpress/
*/
 
/*  Copyright 2010 Simon Wheatley

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

/**
 * Hook the WP widgets_init action to kick this widget off.
 * 
 */
function xtr_widgets_init() {
	require_once( 'widget-xpert-tax-rss.php' );
	require_once( 'widget-selectable-tax-rss.php' );
	register_widget( 'Xpert_RSS' );
	register_widget( 'Selectable_RSS' );
}
add_action( 'widgets_init', 'xtr_widgets_init', 1 );


?>