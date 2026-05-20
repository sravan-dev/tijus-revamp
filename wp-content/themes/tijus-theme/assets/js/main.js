(function ($) {
    "use strict";


    /*--
		Header Sticky
    -----------------------------------*/
    $(window).on('scroll', function(event) {    
        var scroll = $(window).scrollTop();
        if (scroll <= 100) {
            $(".header-main").removeClass("sticky");
        } else{
            $(".header-main").addClass("sticky");
        }
	});
    

    /*--
		Menu Active
    -----------------------------------*/
    $(function () {
    var url = window.location.pathname; 
    var activePage = url.substring(url.lastIndexOf('/') + 1); 
        $('.nav-menu li a').each(function () { 
            var linkPage = this.href.substring(this.href.lastIndexOf('/') + 1); 
    
            if (activePage == linkPage) { 
                $(this).closest("li").addClass("active"); 
            }
        });
    });


    /*--
		Menu Script
	-----------------------------------*/

    function menuScript() {

        $('.menu-toggle').on('click', function(){
            $('.mobile-menu').addClass('open')
            $('.overlay').addClass('open')
        });
        
        $('.menu-close').on('click', function(){
            $('.mobile-menu').removeClass('open')
            $('.overlay').removeClass('open')
        });
        
        $('.overlay').on('click', function(){
            $('.mobile-menu').removeClass('open')
            $('.overlay').removeClass('open')
        });
        
        /*Variables*/
        var $offCanvasNav = $('.mobile-menu-items'),
        $offCanvasNavSubMenu = $offCanvasNav.find('.sub-menu');

        /*Add Toggle Button With Off Canvas Sub Menu*/
        $offCanvasNavSubMenu.parent().prepend('<span class="mobile-menu-expand"></span>');

        /*Close Off Canvas Sub Menu*/
        $offCanvasNavSubMenu.slideUp();

        /*Category Sub Menu Toggle*/
        $offCanvasNav.on('click', 'li a, li .mobile-menu-expand, li .menu-title', function(e) {
            var $this = $(this);
            if (($this.parent().attr('class').match(/\b(menu-item-has-children|has-children|has-sub-menu)\b/)) && ($this.attr('href') === '#' || $this.hasClass('mobile-menu-expand'))) {
                e.preventDefault();
                if ($this.siblings('ul:visible').length) {
                    $this.parent('li').removeClass('active-expand');
                    $this.siblings('ul').slideUp();
                } else {
                    $this.parent('li').addClass('active-expand');
                    $this.closest('li').siblings('li').find('ul:visible').slideUp();
                    $this.closest('li').siblings('li').removeClass('active-expand');
                    $this.siblings('ul').slideDown();
                }
            }
        });

        $( ".sub-menu" ).parent( "li" ).addClass( "menu-item-has-children" );
    }
    menuScript();

    /*--
        Magnific Popup Activation
    -----------------------------------*/
    $('.video-popup').magnificPopup({
        type: 'iframe'
        // other options
    });

    $('.image-popup').magnificPopup({
        type: 'image',
        gallery:{
          enabled:true
        }
    });


    /*--
        Courses Tabs Menu
    -----------------------------------*/
    var edule = new Swiper('.courses-active .swiper-container', {
        speed: 600,
        spaceBetween: 30,        
        navigation: {
            nextEl: '.courses-active .swiper-button-next',
            prevEl: '.courses-active .swiper-button-prev',
        },       
        breakpoints: {
            0: {
                slidesPerView: 1,
            },
            576: {
                slidesPerView: 2,
            },  
            768: {
                slidesPerView: 3,
            },            
            992: {
                slidesPerView: 4,
            },            
            1200: {
                slidesPerView: 5,
            }
        },
    });


    /*--
        Testimonial
    -----------------------------------*/
    var edule = new Swiper('.testimonial-active .swiper-container', {
        speed: 600,
        spaceBetween: 30,        
        pagination: {
            el: '.testimonial-active .swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            0: {
                slidesPerView: 1,
            },  
            768: {
                slidesPerView: 1,
            },            
            992: {
                slidesPerView: 2,
            }
        },
    });


    /*--
        Brand
    -----------------------------------*/
    var edule = new Swiper('.brand-active .swiper-container', {
        speed: 600,
        spaceBetween: 30,
        loop: true,
        breakpoints: {
            0: {
                slidesPerView: 2,
                spaceBetween: 20,
            },  
            576: {
                slidesPerView: 3,
            },  
            768: {
                slidesPerView: 4,
            },            
            992: {
                slidesPerView: 5,
                spaceBetween: 45,
            },            
            1200: {
                slidesPerView: 5,
                spaceBetween: 85,
            }
        },
        autoplay: {
            delay: 8000,
        },
    });


    /*--
        Reviews
    -----------------------------------*/
    var edule = new Swiper('.reviews-active .swiper-container', {
        speed: 600,
        spaceBetween: 30,
        loop: true,  
        pagination: {
            el: '.reviews-active .swiper-pagination',
            clickable: true,
        },      
        autoplay: {
            delay: 8000,
        },
    });


    /*--
        Blog Carousel
    -----------------------------------*/
    var edule = new Swiper('.blog-active.swiper-container', {
        speed: 600,
        spaceBetween: 30,
        loop: true,  
        pagination: {
            el: '.blog-wrapper .swiper-pagination',
            clickable: true,
        },      
        breakpoints: {
            0: {
                slidesPerView: 1,
            },
            768: {
                slidesPerView: 2,
            },
            992: {
                slidesPerView: 3,
            }
        },
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
    });

    /*--
        Student's
    -----------------------------------*/
    var edule = new Swiper('.students-active .swiper-container', {
        speed: 600,
        spaceBetween: 30,        
        navigation: {
            nextEl: '.students-active .swiper-button-next',
            prevEl: '.students-active .swiper-button-prev',
        },       
        breakpoints: {
            0: {
                slidesPerView: 1,
            },  
            768: {
                slidesPerView: 2,
            },
            1600: {
                slidesPerView: 3,
            }
        },
    });


    /*--
		Rating Script
	-----------------------------------*/

	$("#rating li").on('mouseover', function(){
		var onStar = parseInt($(this).data('value'), 10);
		var siblings = $(this).parent().children('li.star');
		Array.from(siblings, function(item){
			var value = item.dataset.value;
			var child = item.firstChild;
			if(value <= onStar){
				child.classList.add('hover')
			} else {
				child.classList.remove('hover')
			}
		})
	})

	$("#rating").on('mouseleave', function(){
		var child = $(this).find('li.star i');
		Array.from(child, function(item){
			item.classList.remove('hover');
		})
	})

	
	$('#rating li').on('click', function(e) {
		var onStar = parseInt($(this).data('value'), 10);
		var siblings = $(this).parent().children('li.star');
		Array.from(siblings, function(item){
			var value = item.dataset.value;
			var child = item.firstChild;
			if(value <= onStar){
				child.classList.remove('hover', 'fa-star-o');
				child.classList.add('star')
			} else {
				child.classList.remove('star');
				child.classList.add('fa-star-o')
			}
		})
	}) 


    /*--
		Video Active
	-----------------------------------*/
    $('.video-playlist .link').on('click', function (event) {
        $(this).siblings('.active').removeClass('active');
        $(this).addClass('active');
        event.preventDefault();
    });


    /*--
        Nice Select
	-----------------------------------*/
    $('select').niceSelect();


    /*--
		Back to top Script
	-----------------------------------*/
    // Show or hide the sticky footer button
    $(window).on('scroll', function (event) {
        if ($(this).scrollTop() > 600) {
            $('.back-to-top').fadeIn(200)
        } else {
            $('.back-to-top').fadeOut(200)
        }
    });

    //Animate the scroll to yop
    $('.back-to-top').on('click', function (event) {
    event.preventDefault();

        $('html, body').animate({
            scrollTop: 0,
        }, 1500);
    });

    
    /*--
        Courses AJAX Filtering
    -----------------------------------*/
    if (typeof tijusAjax !== 'undefined' && $('#tijus-courses-grid').length) {
        
        function fetchCourses(paged) {
            var gridUrl = new URL(window.location.href);
            var params = new URLSearchParams(gridUrl.search);
            
            var category = params.get('course_category') || '';
            var search = params.get('course_search') || '';
            
            var $grid = $('#tijus-courses-grid');
            var $pagination = $('#tijus-courses-pagination-wrapper');
            
            $grid.css('opacity', '0.5');
            $pagination.css('opacity', '0.5');
            
            $.ajax({
                url: tijusAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'tijus_filter_courses',
                    nonce: tijusAjax.nonce,
                    course_category: category,
                    course_search: search,
                    paged: paged || 1
                },
                success: function(res) {
                    if (res.success) {
                        $grid.html(res.data.courses).css('opacity', '1');
                        $pagination.html(res.data.pagination).css('opacity', '1');
                    }
                },
                error: function() {
                    $grid.css('opacity', '1');
                    $pagination.css('opacity', '1');
                }
            });
        }

        // Category Menu Click
        $('#tijus-courses-category-menu a').on('click', function(e) {
            e.preventDefault();
            $('#tijus-courses-category-menu a').removeClass('active');
            $(this).addClass('active');

            var href = new URL(this.href);
            var cat = href.searchParams.get('course_category') || '';
            
            var currentUrl = new URL(window.location.href);
            if (cat) currentUrl.searchParams.set('course_category', cat);
            else currentUrl.searchParams.delete('course_category');
            
            currentUrl.searchParams.delete('paged');
            window.history.pushState({}, '', currentUrl);
            
            var $form = $('#tijus-courses-filter-form');
            if (cat) {
                if ($form.find('input[name="course_category"]').length) {
                    $form.find('input[name="course_category"]').val(cat);
                } else {
                    $form.append('<input type="hidden" name="course_category" value="'+cat+'" />');
                }
            } else {
                $form.find('input[name="course_category"]').remove();
            }

            fetchCourses(1);
        });

        // Search Form Submit
        $('#tijus-courses-filter-form').on('submit', function(e) {
            e.preventDefault();
            var searchVal = $(this).find('input[name="course_search"]').val();
            
            var currentUrl = new URL(window.location.href);
            if (searchVal) currentUrl.searchParams.set('course_search', searchVal);
            else currentUrl.searchParams.delete('course_search');
            
            currentUrl.searchParams.delete('paged');
            window.history.pushState({}, '', currentUrl);
            
            fetchCourses(1);
        });

        // Pagination Click
        $(document).on('click', '#tijus-courses-pagination-wrapper a', function(e) {
            e.preventDefault();
            var href = new URL(this.href);
            var paged = href.searchParams.get('paged') || 1;
            
            if (!paged || paged === "1") {
                var pathParts = href.pathname.split('/');
                var pagedIndex = pathParts.indexOf('page');
                if (pagedIndex !== -1 && pathParts.length > pagedIndex + 1) {
                    paged = pathParts[pagedIndex + 1];
                }
            }
            
            var currentUrl = new URL(window.location.href);
            if (paged > 1) {
                currentUrl.searchParams.set('paged', paged);
            } else {
                currentUrl.searchParams.delete('paged');
            }
            
            window.history.pushState({}, '', currentUrl);
            fetchCourses(paged);
            
            $('html, body').animate({
                scrollTop: $('#tijus-courses-grid').offset().top - 150
            }, 500);
        });
    }

    /*--
        Universal AJAX Live Search Dropdown for Home and Course Pages
    -----------------------------------*/
    if (typeof tijusAjax !== 'undefined') {
        $('.courses-search form').each(function() {
            var $form = $(this);
            var $input = $form.find('input[type="text"]');
            
            // Ensure relative positioning on the parent wrapper
            $form.parent().css('position', 'relative');
            
            // Build the absolute dropdown container
            var $dropdown = $('<ul class="tijus-live-search-results" style="display:none; position:absolute; z-index:9999; background:#fff; width:100%; top:calc(100% + 5px); left:0; box-shadow:0px 10px 30px rgba(48,146,85,0.15); border-radius:8px; max-height:400px; overflow-y:auto; list-style:none; padding:0; margin:0; border: 1px solid #eee;"></ul>');
            
            $form.append($dropdown);
            
            // Prevent browser default autocomplete overlay
            $input.attr('autocomplete', 'off');
            
            var searchTimeout = null;
            
            $input.on('input', function() {
                var query = $(this).val().trim();
                
                clearTimeout(searchTimeout);
                
                if (query.length < 2) {
                    $dropdown.fadeOut(200);
                    return;
                }
                
                // Show loading state
                $dropdown.html('<li><span style="padding:15px; display:block; color:#309255; text-align:center;"><i class="icofont-spinner icofont-spin"></i> Searching...</span></li>').fadeIn(200);
                
                searchTimeout = setTimeout(function() {
                    $.ajax({
                        url: tijusAjax.ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'tijus_live_search',
                            s: query
                        },
                        success: function(res) {
                            if (res.success && res.data) {
                                $dropdown.html(res.data);
                            } else {
                                $dropdown.html('<li><span style="padding:15px; display:block; color:#dc3545;">No courses found.</span></li>');
                            }
                        },
                        error: function() {
                            $dropdown.html('<li><span style="padding:15px; display:block; color:#dc3545;">Search failed.</span></li>');
                        }
                    });
                }, 400); // 400ms debounce
            });
            
            // Hide dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.courses-search').length) {
                    $dropdown.fadeOut(200);
                }
            });
            
            // Reshow if they click back into the input
            $input.on('focus', function() {
                if ($(this).val().trim().length >= 2) {
                    $dropdown.fadeIn(200);
                }
            });
        });
    }

})(jQuery);
