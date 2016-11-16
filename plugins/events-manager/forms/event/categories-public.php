<?php
/*
* Modified from original events manager plugin version: 5.6.6.1 file 'templates/forms/categories-public.php'
* Copyright (c) 2016, Marcus Sykes
* http://wp-events-plugin.com
* Licence: GPL 2 or later
*
* Modifications by Brad Payne
* Licence GPL 3+
*/

global $EM_Event;
/* @var $EM_Event EM_Event */
$categories = EM_Categories::get( array( 'orderby' => 'name', 'hide_empty' => 0 ) );
?>
<?php if ( count( $categories ) > 0 ): ?>
	<div class="event-categories">
		<!-- START Categories -->
		<label for="event_categories[]"><?php _e( 'Category:', 'events-manager' ); ?></label>
		<p class="margin-up"><i>(To select multiple items [mac]=command,click [pc]=ctrl,click)</i></p>
		<select name="event_categories[]" multiple size="10">
			<?php
			$selected = $EM_Event->get_categories()->get_ids();
			$walker   = new EM_Walker_CategoryMultiselect();
			$args_em  = array( 'hide_empty'   => 0,
			                   'name'         => 'event_categories[]',
			                   'hierarchical' => true,
			                   'id'           => EM_TAXONOMY_CATEGORY,
			                   'taxonomy'     => EM_TAXONOMY_CATEGORY,
			                   'selected'     => $selected,
			                   'walker'       => $walker
			);
			echo walk_category_dropdown_tree( $categories, 0, $args_em );
			?></select>
		<!-- END Categories -->
	</div>
<?php endif; ?>