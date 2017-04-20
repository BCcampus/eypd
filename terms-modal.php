<?php
// Get the Terms and Conditions post by it's ID 
$post_id      = 1515;
$queried_post = get_post( $post_id );
$title        = $queried_post->post_title;
?>

<!-- Modal for Registration page "Terms and Conditions" Field -->
<div id="terms" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" type="button" data-dismiss="modal">×</button>
                <h4 class="modal-title"><?php echo $title; ?></h4>
            </div>
            <div class="modal-body"><?php echo $queried_post->post_content; ?></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
