<?php
infinity_get_header();

global $EM_Event;
$lat = $EM_Event->location->location_latitude;
$lng = $EM_Event->location->location_longitude;

?>

    <div id="content" role="main" class="<?php do_action('content_class'); ?>">
        <?php
        do_action('open_content');
        do_action('open_single');
        ?>


        <h2><?php echo $EM_Event->event_name ?></h2>

        <?php echo $EM_Event->output_single(); ?>
        <?php
        foreach ($EM_Event->event_attributes as $key => $att) {
            echo "<p><b>" . $key . "</b><br>" . $att . "</p>";
        };
        ?>

        <?php
        do_action('close_single');
        do_action('close_content');
        ?>

    </div>
    <?php
    infinity_get_sidebar();
    infinity_get_footer();
