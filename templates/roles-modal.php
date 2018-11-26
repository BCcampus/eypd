<?php
// Get the Terms and Conditions post by page name
$page = get_page_by_title( 'What’s the difference between Learner and Organizer?' );
// $page is the post array. Get the title and content
$title = $page->post_title;
$content = $page->post_content;
?>

<!-- Modal for Registration page "Terms and Conditions" Field -->
<div id="role" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?php echo $title; ?></h4>
				<button class="close" type="button" data-dismiss="modal">×</button>
			</div>
			<div class="modal-body"><?php echo $content; ?></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
