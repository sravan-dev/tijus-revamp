<?php
/**
 * The template for displaying all single posts
 *
 * @package tijus-theme
 */

get_header();
?>

<!-- Page Banner Start -->
<div class="section page-banner">
    <img class="shape-1 animation-round" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-8.png" alt="Shape">
    <img class="shape-2" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-23.png" alt="Shape">

    <div class="container">
        <!-- Page Banner Start -->
        <div class="page-banner-content">
            <ul class="breadcrumb">
                <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
                <li class="active">Blog</li>
            </ul>
            <h2 class="title"><?php the_title(); ?></h2>
        </div>
        <!-- Page Banner End -->
    </div>

    <!-- Shape Icon Box Start -->
    <div class="shape-icon-box">
        <img class="icon-shape-1 animation-left" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-5.png" alt="Shape">
        <div class="box-content">
            <div class="box-wrapper">
                <i class="flaticon-badge"></i>
            </div>
        </div>
        <img class="icon-shape-2" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-6.png" alt="Shape">
    </div>
    <!-- Shape Icon Box End -->

    <img class="shape-3" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-24.png" alt="Shape">
    <img class="shape-author" src="<?php echo get_template_directory_uri(); ?>/assets/images/author/author-11.jpg" alt="Shape">
</div>
<!-- Page Banner End -->

<!-- Blog Details Start -->
<div class="section section-padding mt-n10">
    <div class="container">

        <div class="row flex-row-reverse gx-10">
            <div class="col-lg-8">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<!-- Blog Details Wrapper Start -->
					<div class="blog-details-wrapper">
						<div class="blog-details-admin-meta">
							<div class="author">
								<div class="author-thumb">
									<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
										<?php echo get_avatar( get_the_author_meta( 'ID' ), 60 ); ?>
									</a>
								</div>
								<div class="author-name">
									<a class="name" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a>
								</div>
							</div>
							<div class="blog-meta">
								<span> <i class="icofont-calendar"></i> <?php echo get_the_date( 'd F, Y' ); ?></span>
								<span> <i class="icofont-heart"></i> <?php echo get_comments_number(); ?> </span>
								<span class="tag">
									<?php
									$categories = get_the_category();
									if ( ! empty( $categories ) ) {
										echo '<a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . esc_html( $categories[0]->name ) . '</a>';
									}
									?>
								</span>
							</div>
						</div>

						<h2 class="title"><?php the_title(); ?></h2>

						<div class="blog-details-description">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'full', array( 'style' => 'margin-bottom:30px;' ) ); ?>
							<?php endif; ?>
							
                            <div class="read-aloud-widget" style="margin-bottom: 20px; display:flex; justify-content: flex-start; align-items: center; gap: 10px;">
                                <select id="voice-select" style="display:none; height: 40px; padding: 0 10px; font-size: 14px; border-radius: 5px; background: #fff; color: #309255; border: 1px solid #309255; outline: none; cursor: pointer;"></select>
                                <button id="read-aloud-btn" class="btn btn-primary btn-hover-dark" style="height: 40px; display:inline-flex; align-items:center; gap:8px; padding: 0 20px; font-size: 14px; border-radius: 5px; background: rgba(48, 146, 85, 0.1); color: #309255; border: 1px solid #309255; box-shadow: none; line-height: 1;">
                                    <i class="icofont-play-alt-2" id="read-aloud-icon"></i> <span id="read-aloud-text">Listen to this article</span>
                                </button>
                            </div>

							<div id="blog-readable-content">
							    <?php the_content(); ?>
                            </div>
						</div>

						<div class="blog-details-label">
							<h4 class="label">Tags:</h4>
							<ul class="tag-list">
								<?php
								$post_tags = get_the_tags();
								if ( $post_tags ) {
									foreach ( $post_tags as $tag ) {
										echo '<li><a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '">' . esc_html( $tag->name ) . '</a></li>';
									}
								} else {
									echo '<li><a>None</a></li>';
								}
								?>
							</ul>
						</div>

						<div class="blog-details-label">
							<?php
							$share_url = urlencode( get_permalink() );
							$share_title = urlencode( get_the_title() );
							?>
							<h4 class="label">Share:</h4>
							<ul class="social small-social">
								<li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>" target="_blank" rel="noopener noreferrer"><i class="flaticon-facebook"></i></a></li>
								<li><a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $share_url; ?>&title=<?php echo $share_title; ?>" target="_blank" rel="noopener noreferrer"><i class="flaticon-linkedin"></i></a></li>
								<li><a href="https://twitter.com/intent/tweet?url=<?php echo $share_url; ?>&text=<?php echo $share_title; ?>" target="_blank" rel="noopener noreferrer"><i class="flaticon-twitter"></i></a></li>
								<li><a href="https://api.whatsapp.com/send?text=<?php echo $share_title . ' ' . $share_url; ?>" target="_blank" rel="noopener noreferrer"><i class="flaticon-skype"></i></a></li>
								<li><a href="mailto:?subject=<?php echo $share_title; ?>&body=<?php echo $share_url; ?>" target="_blank" rel="noopener noreferrer"><i class="flaticon-email"></i></a></li>
							</ul>
						</div>

					</div>
					<!-- Blog Details Wrapper End -->

					<!-- Blog Details Comment End -->
					<?php
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;
					?>
					<!-- Blog Details Comment End -->

					<?php
				endwhile; // End of the loop.
				?>
            </div>
            
            <div class="col-lg-4">
                <?php get_sidebar(); ?>
            </div>
        </div>

    </div>
</div>
<!-- Blog Details End -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('read-aloud-btn');
    if (!btn) return;

    const icon = document.getElementById('read-aloud-icon');
    const textSpan = document.getElementById('read-aloud-text');
    const voiceSelect = document.getElementById('voice-select');
    
    let isPlaying = false;
    let synth = window.speechSynthesis;
    let utterance = null;
    let availableVoices = [];

    function populateVoices() {
        availableVoices = synth.getVoices().filter(v => v.lang.startsWith('en'));
        if (availableVoices.length === 0) return;
        
        voiceSelect.innerHTML = '';
        
        // Find best candidates for male and female voices based on standard OS/browser naming conventions
        let female = availableVoices.find(v => /female|samantha|zira|victoria|karen|veena/i.test(v.name));
        let male = availableVoices.find(v => /male|alex|david|daniel|mark|rishi/i.test(v.name));
        
        if (!female && availableVoices.length > 0) female = availableVoices[0];
        if (!male && availableVoices.length > 1) male = availableVoices.find(v => v !== female);
        
        if (female) {
            let opt = document.createElement('option');
            opt.textContent = 'Female Voice';
            opt.value = female.name;
            voiceSelect.appendChild(opt);
        }
        
        if (male && male !== female) {
            let opt = document.createElement('option');
            opt.textContent = 'Male Voice';
            opt.value = male.name;
            voiceSelect.appendChild(opt);
        }
        
        if (voiceSelect.options.length > 0) {
            voiceSelect.style.display = 'block';
            voiceSelect.style.height = '40px';
            voiceSelect.style.lineHeight = '40px';
            voiceSelect.style.margin = '0';
            
            // Destroy any nice-select duplicate
            const ns = voiceSelect.nextElementSibling;
            if (ns && ns.classList.contains('nice-select')) {
                ns.style.display = 'none';
            }
        }
    }

    populateVoices();
    if (speechSynthesis.onvoiceschanged !== undefined) {
        speechSynthesis.onvoiceschanged = populateVoices;
    }

    btn.addEventListener('click', function(e) {
        e.preventDefault();
        
        if (isPlaying || synth.speaking) {
            synth.cancel();
            isPlaying = false;
            icon.className = 'icofont-play-alt-2';
            textSpan.textContent = 'Listen to this article';
            return;
        }

        const titleObj = document.querySelector('.blog-details-wrapper h2.title');
        const contentDiv = document.getElementById('blog-readable-content');
        if (!contentDiv) return;

        // Strictly target only readable paragraphs, lists, and headers 
        // to avoid reading injected plugin artifacts like social share buttons
        let textChunks = [];
        if (titleObj) textChunks.push(titleObj.innerText || titleObj.textContent);

        const elements = contentDiv.querySelectorAll('h1, h2, h3, h4, h5, h6, p, li, blockquote');
        if (elements.length > 0) {
            elements.forEach(el => {
                let txt = el.innerText || el.textContent;
                if (txt.trim()) textChunks.push(txt.trim());
            });
        } else {
            textChunks.push(contentDiv.innerText || contentDiv.textContent);
        }

        let textToRead = textChunks.join('. ').trim();

        if (textToRead === '') return;

        utterance = new SpeechSynthesisUtterance(textToRead);
        
        // Attach the selected Voice Profile
        if (voiceSelect.value) {
            const selectedVoice = availableVoices.find(v => v.name === voiceSelect.value);
            if (selectedVoice) {
                utterance.voice = selectedVoice;
            }
        }
        
        utterance.onend = function() {
            isPlaying = false;
            icon.className = 'icofont-play-alt-2';
            textSpan.textContent = 'Listen to this article';
        };
        
        utterance.onerror = function() {
            isPlaying = false;
            icon.className = 'icofont-play-alt-2';
            textSpan.textContent = 'Listen to this article';
        };

        synth.speak(utterance);
        isPlaying = true;
        icon.className = 'icofont-ui-pause';
        textSpan.textContent = 'Stop reading';
    });
    
    // Stop reading immediately when switching tabs or navigating away
    window.addEventListener('beforeunload', function() {
        if (synth && isPlaying) {
            synth.cancel();
        }
    });
});
</script>

<?php
get_footer();
