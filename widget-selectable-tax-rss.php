<?php 

/**
 * RSS widget class
 *
 * @since 2.8.0
 */
class Selectable_RSS extends WP_Widget_RSS {

	function Selectable_RSS() {
		$widget_ops = array( 'description' => __('Entries from any RSS or Atom Xpert feed') );
		$control_ops = array( 'width' => 400, 'height' => 200 );
		$this->WP_Widget( 'selectable-rss', __('Selectable RSS'), $widget_ops, $control_ops );
	}

	function widget($args, $instance) {

		if ( isset($instance['error']) && $instance['error'] )
			return;

		extract($args, EXTR_SKIP);

		$url = $instance['url'];
		while ( stristr($url, 'http') != $url )
			$url = substr($url, 1);

		if ( empty($url) )
			return;

		// If we can't find a term, we might not be in a taxonomy archive...
		// we should bomb out here.
		if ( ! $term_slug = $this->get_current_term_slug() ) {
			?>
				<!-- <p><?php _e( 'I am an invisible Selectable RSS widget.', 'selectable-tax-rss' ); ?></p> -->
			<?php
			return;
		}
		// error_log( "Term: $term_slug" );

		$term_slug = urlencode( $term_slug );
		$url = sprintf( $url, $term_slug );
		// $url = "http://www.nottingham.ac.uk/xpert/wordpress/$term_slug";
		
		// error_log( "URL: $url" );

		$rss = fetch_feed($url);
		$desc = '';
		$link = '';

		if ( ! is_wp_error($rss) ) {
			$title = esc_html(strip_tags($rss->get_title()));
			$desc = esc_attr(strip_tags(@html_entity_decode($rss->get_description(), ENT_QUOTES, get_option('blog_charset'))));
			$link = esc_url(strip_tags($rss->get_permalink()));
			while ( stristr($link, 'http') != $link )
				$link = substr($link, 1);
		}

		if ( empty($title) )
			$title = empty($desc) ? __('Unknown Feed') : $desc;

		$title = apply_filters('widget_title', $title, $instance, $this->id_base);
		$url = esc_url(strip_tags($url));
		$icon = includes_url('images/rss.png');
		if ( $title )
			$title = "<a class='rsswidget' href='$url' title='" . esc_attr__( 'Syndicate this content' ) ."'><img style='border:0' width='14' height='14' src='$icon' alt='RSS' /></a> <a class='rsswidget' href='$link' title='$desc'>$title</a>";

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		wp_widget_rss_output( $rss, $instance );
		echo $after_widget;

		if ( ! is_wp_error($rss) )
			$rss->__destruct();
		unset($rss);
	}

	function form($instance) {

		if ( empty($instance) )
			$instance = array( 'title' => '', 'url' => '', 'items' => 10, 'error' => false, 'show_summary' => 0, 'show_author' => 0, 'show_date' => 0 );
		$instance['number'] = $this->number;

		$default_inputs = array( 'items' => true, 'show_summary' => true, 'show_author' => true, 'show_date' => true );
		$inputs = wp_parse_args( $inputs, $default_inputs );
		extract( $instance	 );
		extract( $inputs, EXTR_SKIP);

		$number = esc_attr( $number );
		$title  = esc_attr( $title );
		$url    = esc_url( $url );
		$items  = (int) $items;
		if ( $items < 1 || 20 < $items )
			$items  = 10;
		$show_summary   = (int) $show_summary;
		$show_author    = (int) $show_author;
		$show_date      = (int) $show_date;

		if ( !empty($error) )
			echo '<p class="widget-error"><strong>' . sprintf( __('RSS Error: %s'), $error) . '</strong></p>';

	?>
		<p><label for="rss-url-<?php echo $number; ?>"><?php _e('Enter the RSS feed URL here, place a <code>%s</code> where you want the tag replaced:'); ?></label>
	<input class="widefat" id="rss-url-<?php echo $number; ?>" name="widget-rss[<?php echo $number; ?>][url]" type="text" value="<?php echo $url; ?>" /></p>
	
		<p><?php _e( 'Some example URLs for you:', 'tax-rss' ); ?></p>
			
		<ul>
			<li><code>http://www.merlot.org/merlot/materials.xml?community=&amp;category=&amp;keywords=%s</code></li>
			<li><code>http://www.nottingham.ac.uk/xpert/wordpress/%s</code></li>
			<li><code>http://www.oercommons.org/search?f.search=%s&amp;feed=yes</code></li>
		</ul>

		<p><?php _e( 'This widget will only display on a taxonomy archive page, whereupon it will display things from the UON Xpert learning objects database.', 'selectable-tax-rss' ); ?></p>
		<p><label for="rss-items-<?php echo $number; ?>"><?php _e('How many items would you like to display?'); ?></label>
		<select id="rss-items-<?php echo $number; ?>" name="widget-rss[<?php echo $number; ?>][items]">
	<?php
			for ( $i = 1; $i <= 20; ++$i )
				echo "<option value='$i' " . ( $items == $i ? "selected='selected'" : '' ) . ">$i</option>";
	?>
		</select></p>

		<p><input id="rss-show-summary-<?php echo $number; ?>" name="widget-rss[<?php echo $number; ?>][show_summary]" type="checkbox" value="1" <?php if ( $show_summary ) echo 'checked="checked"'; ?>/>
		<label for="rss-show-summary-<?php echo $number; ?>"><?php _e('Display item content?'); ?></label></p>

		<p><input id="rss-show-author-<?php echo $number; ?>" name="widget-rss[<?php echo $number; ?>][show_author]" type="checkbox" value="1" <?php if ( $show_author ) echo 'checked="checked"'; ?>/>
		<label for="rss-show-author-<?php echo $number; ?>"><?php _e('Display item author if available?'); ?></label></p>

		<p><input id="rss-show-date-<?php echo $number; ?>" name="widget-rss[<?php echo $number; ?>][show_date]" type="checkbox" value="1" <?php if ( $show_date ) echo 'checked="checked"'; ?>/>
		<label for="rss-show-date-<?php echo $number; ?>"><?php _e('Display item date?'); ?></label></p>
	<?php
		foreach ( array_keys($default_inputs) as $input ) :
			if ( 'hidden' === $inputs[$input] ) :
				$id = str_replace( '_', '-', $input );
	?>
		<input type="hidden" id="rss-<?php echo $id; ?>-<?php echo $number; ?>" name="widget-rss[<?php echo $number; ?>][<?php echo $input; ?>]" value="<?php echo $$input; ?>" />
	<?php
			endif;
		endforeach;
	}
	
	/**
	 * Checks if the current page is either a tag or category archive, and if it is
	 * it will return the URL safe version of the term.
	 * 
	 * PROBLEM: No way to get custom taxonomy terms out without adding them into this
	 * method in cold, hard code.
	 *
	 * @return bool|string The URL safe version of the current archive's term, if it exists, otherwise false
	 * @author Simon Wheatley
	 **/
	protected function get_current_term_slug() {
		if ( ! is_archive() )
			return false;
		if ( $cat = get_query_var( 'category_name' ) )
			return $cat;
		if ( $tag = get_query_var( 'tag' ) )
			return $tag;
		return false;
	}
	
}

 ?>