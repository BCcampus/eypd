<?php

/**
 * BuddyPress - Users Activity
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

?>
<?php
if ( is_user_logged_in() && is_admin() || bp_is_my_profile() ) : ?>
<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
	<ul>

		<?php bp_get_options_nav(); ?>

		<li id="activity-filter-select" class="last">
			<label for="activity-filter-by"><?php _e( 'Show:', 'buddypress' ); ?></label>
			<select id="activity-filter-by">
				<option value="-1"><?php _e( '&mdash; Everything &mdash;', 'buddypress' ); ?></option>
				<option value="activity_update"><?php _e( 'Updates', 'buddypress' ); ?></option>

				<?php
				if ( ! bp_is_current_action( 'groups' ) ) :
					if ( bp_is_active( 'blogs' ) ) : ?>

						<option value="new_blog_post"><?php _e( 'Posts', 'buddypress' ); ?></option>
						<option value="new_blog_comment"><?php _e( 'Comments', 'buddypress' ); ?></option>

					<?php
					endif;

					if ( bp_is_active( 'friends' ) ) : ?>

						<option value="friendship_accepted,friendship_created"><?php _e( 'Friendships', 'buddypress' ); ?></option>

					<?php endif;

				endif;

				if ( bp_is_active( 'forums' ) ) : ?>

					<option value="new_forum_topic"><?php _e( 'Forum Topics', 'buddypress' ); ?></option>
					<option value="new_forum_post"><?php _e( 'Forum Replies', 'buddypress' ); ?></option>

				<?php endif;

				if ( bp_is_active( 'groups' ) ) : ?>

					<option value="created_group"><?php _e( 'New Groups', 'buddypress' ); ?></option>
					<option value="joined_group"><?php _e( 'Group Memberships', 'buddypress' ); ?></option>

				<?php endif;

				do_action( 'bp_member_activity_filter_options' ); ?>

			</select>
		</li>
	</ul>
</div><!-- .item-list-tabs -->

<?php do_action( 'bp_before_member_activity_post_form' ); ?>

<?php
if ( is_user_logged_in() && bp_is_my_profile() && ( ! bp_current_action() || bp_is_current_action( 'just-me' ) ) ) {
	locate_template( array( 'activity/post-form.php' ), true );
}

do_action( 'bp_after_member_activity_post_form' );
do_action( 'bp_before_member_activity_content' ); ?>

<div class="activity" role="main">

	<?php locate_template( array( 'activity/activity-loop.php' ), true ); ?>

</div><!-- .activity -->

<?php do_action( 'bp_after_member_activity_content' ); ?>

<?php
	else :
?>

<?php do_action( 'bp_before_profile_loop_content' ); ?>

<?php $role = bp_get_profile_field_data( 'field=Position/Role' ); ?>

<?php if ( bp_has_profile() ) : ?>
	<h2><?php echo bp_profile_field_data( 'field=First and Last Name' ); ?></h2>
	<p><strong><?php echo $role; ?></strong><br>
		<?php
		if ( $role === 'Organizer' ) {
			echo 'Agency/Institution: <a href="/?members_search.php=' . urlencode( bp_get_profile_field_data( 'field=Agency/Institution (Select if you are an Organizer of training)' ) ) . '">' . bp_get_profile_field_data( 'field=Agency/Institution (Select if you are an Organizer of training)' ) . '</a>';
		}
		?>
	</p>
	<p><?php bp_profile_field_data( 'field=Name of your place of Work' ); ?><br>
		<?php echo bp_get_profile_field_data( 'field=City/Town' ) . ', ' . bp_get_profile_field_data( 'field=Province' ); ?>
	</p>
<?php do_action( 'bp_profile_field_buttons' ); ?>

<?php endif; ?>

<?php do_action( 'bp_after_profile_loop_content' ); ?>

<?php
	endif;
?>
