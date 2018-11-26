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
	<?php /* while ( bp_profile_groups() ) : bp_the_profile_group(); ?>

		<?php if ( bp_profile_group_has_fields() ) : ?>

			<?php do_action( 'bp_before_profile_field_content' ); ?>

			<div class="bp-widget <?php bp_the_profile_group_slug(); ?>">

				<h4><?php bp_the_profile_group_name(); ?></h4>

				<table class="profile-fields">

					<?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>

						<?php if ( bp_field_has_data() ) : ?>

							<tr<?php bp_field_css_class(); ?>>

								<td class="label"><?php bp_the_profile_field_name(); ?></td>

								<td class="data"><?php bp_the_profile_field_value(); ?></td>

							</tr>

						<?php endif; ?>

						<?php do_action( 'bp_profile_field_item' ); ?>

					<?php endwhile; ?>

				</table>
			</div>

			<?php do_action( 'bp_after_profile_field_content' ); ?>

		<?php endif; ?>

	<?php endwhile; */?>

	<?php do_action( 'bp_profile_field_buttons' ); ?>

<?php endif; ?>

<?php do_action( 'bp_after_profile_loop_content' ); ?>
