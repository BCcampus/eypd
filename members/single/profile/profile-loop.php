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
