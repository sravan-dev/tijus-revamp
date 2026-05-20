<?php
/**
 * The template for displaying course reviews (comments)
 */

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area mt-5 pt-4">

	<?php if ( have_comments() ) : ?>
		<h3 class="tab-title">Student Reviews:</h3>
		
		<ul class="comment-list review-list" style="list-style: none; padding-left: 0;">
			<?php
			wp_list_comments( [
				'style'       => 'ul',
				'short_ping'  => true,
				'callback'    => 'tijus_course_comment_callback',
			] );
			?>
		</ul>
	<?php else : ?>
        <h3 class="tab-title">Student Reviews:</h3>
		<p>No reviews yet. Be the first to review this course!</p>
	<?php endif; ?>

	<div class="review-form-wrapper mt-5">
            <h4 class="mb-3">Leave a Review</h4>
			<?php
			$comment_args = [
				'title_reply'          => '',
				'comment_notes_before' => '',
				'comment_field'        => '
                    <div class="rating-selection mb-3">
                        <label class="d-block mb-1" style="font-weight: 600;">Your Rating <span class="text-danger">*</span></label>
                        <div class="star-rating-input" id="star-rating-input">
                            <i class="icofont-star" data-val="1"></i>
                            <i class="icofont-star" data-val="2"></i>
                            <i class="icofont-star" data-val="3"></i>
                            <i class="icofont-star" data-val="4"></i>
                            <i class="icofont-star" data-val="5"></i>
                            <input type="hidden" name="course_rating" id="course_rating_val" value="0" required />
                        </div>
                    </div>
                    <div class="comment-form-comment mb-3">
                        <label for="comment" style="font-weight: 600;">Your Review <span class="text-danger">*</span></label>
                        <textarea id="comment" name="comment" cols="45" rows="5" class="form-control" required="required" placeholder="Write your experience here..."></textarea>
                    </div>',
				'class_submit'         => 'btn btn-primary btn-hover-dark mt-3',
				'label_submit'         => 'Submit Review',
			];
			comment_form( $comment_args );
			?>
		</div>
        <style>
        .star-rating-input i {
            font-size: 24px;
            color: #ddd;
            cursor: pointer;
            transition: color 0.1s;
        }
        .star-rating-input i.active {
            color: #ffb800; /* Theme yellow */
        }
        .comment-list .comment {
            border-bottom: 1px solid #eee;
            padding: 20px 0;
            display: flex;
            gap: 20px;
        }
        .comment-list .comment:last-child {
            border-bottom: none;
        }
        .comment-list .comment-avatar img {
            border-radius: 50%;
            width: 60px;
            height: 60px;
            object-fit: cover;
        }
        .comment-list .comment-author {
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 2px;
        }
        .comment-list .comment-date {
            font-size: 13px;
            color: #888;
            margin-bottom: 8px;
            display: block;
        }
        .comment-list .comment-rating i {
            color: #ffb800;
            font-size: 14px;
        }
        .comment-list .comment-rating i.empty {
            color: #ddd;
        }
        .comment-list .comment-content {
            margin-top: 10px;
            color: #555;
            line-height: 1.6;
        }
        </style>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var stars = document.querySelectorAll('.star-rating-input i');
            var input = document.getElementById('course_rating_val');
            
            stars.forEach(function(star) {
                star.addEventListener('click', function() {
                    var val = parseInt(this.getAttribute('data-val'));
                    input.value = val;
                    stars.forEach(function(s, index) {
                        if (index < val) {
                            s.classList.add('active');
                            s.style.color = '#ffb800';
                        } else {
                            s.classList.remove('active');
                            s.style.color = '#ddd';
                        }
                    });
                });
                
                star.addEventListener('mouseover', function() {
                    var val = parseInt(this.getAttribute('data-val'));
                    stars.forEach(function(s, index) {
                        if (index < val) {
                            s.style.color = '#ffb800';
                        } else {
                            s.style.color = '#ddd';
                        }
                    });
                });
                
                star.addEventListener('mouseout', function() {
                    var val = parseInt(input.value);
                    stars.forEach(function(s, index) {
                        if (index < val) {
                            s.style.color = '#ffb800';
                        } else {
                            s.style.color = '#ddd';
                        }
                    });
                });
            });
            
            var form = document.getElementById('commentform');
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (input.value == '0') {
                        e.preventDefault();
                        alert('Please select a star rating by clicking the stars.');
                    }
                });
            }
        });
        </script>
    

</div><!-- #comments -->
