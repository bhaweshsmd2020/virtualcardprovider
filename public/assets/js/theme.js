// js Document


(function($) {
    "use strict";

      //-------------- Click event to scroll to top
      $(window).on('scroll', function (){
        if ($(this).scrollTop() > 200) {
          $('.scroll-top').fadeIn();
        } else {
          $('.scroll-top').fadeOut();
        }
      });
      $('.scroll-top').on('click', function() {
        $('html, body').animate({scrollTop : 0});
        return false;
      });

      // --------------------- Add .active class to current navigation based on URL
      var pgurl = window.location.href.substr(window.location.href.lastIndexOf("/")+1);
      $(".navbar-nav > li  a").each(function(){
      if($(this).attr("href") == pgurl || $(this).attr("href") == '' )
      $(this).addClass("active");
      })

      
      // ----------------------------- Lazy Load
        if($(".lazy-img").length) {
          $('.lazy-img').Lazy({
            effect: 'fadeIn',
            threshold: 300
          });
        }


        // ----------------------------- Counter Function
        var timer = $('.counter');
        if(timer.length) {
          $('.counter').counterUp({
            delay: 10,
            time: 1200,
          });
        }

        // ------------------------ Navigation Scroll
        $(window).on('scroll', function (){   
          var sticky = $('.sticky-menu'),
          scroll = $(window).scrollTop();
          if (scroll >= 180) sticky.addClass('fixed');
          else sticky.removeClass('fixed');

        });


      // -------------------- Remove Placeholder When Focus Or Click
        $("input,textarea").each( function(){
            $(this).data('holder',$(this).attr('placeholder'));
            $(this).on('focusin', function() {
                $(this).attr('placeholder','');
            });
            $(this).on('focusout', function() {
                $(this).attr('placeholder',$(this).data('holder'));
            });     
        });

        // ------------------------ Product Quantity Selector
        if ($(".product-value").length) {
            $('.value-increase').on('click',function(){
              var $qty=$(this).closest('ul').find('.product-value');
              var currentVal = parseInt($qty.val());
              if (!isNaN(currentVal)) {
                  $qty.val(currentVal + 1);
              }
          });
          $('.value-decrease').on('click',function(){
              var $qty=$(this).closest('ul').find('.product-value');
              var currentVal = parseInt($qty.val());
              if (!isNaN(currentVal) && currentVal > 1) {
                  $qty.val(currentVal - 1);
              }
          });
        }

        // ------------------------ Prepaid Card Option 
        if($("#credit-card").length) {
          $(".payment-radio-button").on('click',function(){
             if ($("#credit-card").is(":checked")) {
               $(".credit-card-form").show(300);
             } else {
               $(".credit-card-form").hide(300);
             }
           });
        }



        // ------------------------ Hero Slider One
        if($(".hero-slider-one").length) {
          $('.hero-slider-one').slick({
              dots: false,
              arrows: false,
              lazyLoad: 'ondemand',
              centerPadding: '0px',
              slidesToShow: 1,
              slidesToScroll: 1,
              autoplay: true,
              fade: true,
              autoplaySpeed: 7000,
            });
        }


        // ------------------------ Feedback Slider One
        if($(".feedback-slider-one").length) {
          $('.feedback-slider-one').slick({
              dots: false,
              arrows: false,
              lazyLoad: 'ondemand',
              centerPadding: '0px',
              slidesToShow: 1,
              slidesToScroll: 1,
              fade: true,
              autoplay: true,
              autoplaySpeed: 3000
            });
        }

        // ------------------------ Feedback Slider Two
        if($(".feedback-slider-two").length) {
          $('.feedback-slider-two').slick({
              dots: false,
              arrows: false,
              lazyLoad: 'ondemand',
              centerPadding: '0px',
              slidesToShow: 3,
              slidesToScroll: 1,
              centerMode: true,
              autoplay: true,
              autoplaySpeed: 3000,
              responsive: [
                {
                  breakpoint: 768,
                  settings: {
                    slidesToShow: 1
                  }
                }
              ]
            });
        }

        // ------------------------ Feedback Slider Three
        if($(".feedback-slider-three").length) {
          $('.feedback-slider-three').slick({
              dots: true,
              arrows: false,
              lazyLoad: 'ondemand',
              centerPadding: '0px',
              slidesToShow: 1,
              slidesToScroll: 1,
              fade:true,
              autoplay: true,
              autoplaySpeed: 6000
            });
        }

        // ------------------------ Feedback Slider Four
        if($(".feedback-slider-four").length) {
          $('.feedback-slider-four').slick({
              dots: false,
              arrows: true,
              prevArrow: $('.prev_a'),
              nextArrow: $('.next_a'),
              lazyLoad: 'ondemand',
              centerPadding: '0px',
              slidesToShow: 2,
              slidesToScroll: 1,
              centerMode: true,
              autoplay: false,
              autoplaySpeed: 3000,
              responsive: [
                {
                  breakpoint: 768,
                  settings: {
                    slidesToShow: 1
                  }
                }
              ]
            });
        }

        // ------------------------ Feedback Slider Five
        if($(".feedback-slider-five").length) {
          $('.feedback-slider-five').slick({
              dots: true,
              arrows: false,
              fade: true,
              lazyLoad: 'ondemand',
              centerPadding: '0px',
              slidesToShow: 1,
              slidesToScroll: 1,
              centerMode: true,
              autoplay: false,
              autoplaySpeed: 3000,
            });
        }

        // ------------------------ Feedback Slider Six
        if($(".feedback-slider-six").length) {
          $('.feedback-slider-six').slick({
              dots: false,
              arrows: true,
              prevArrow: $('.prev_S'),
              nextArrow: $('.next_S'),
              lazyLoad: 'ondemand',
              centerPadding: '0px',
              slidesToShow: 1,
              slidesToScroll: 1,
              centerMode: true,
              autoplay: false,
              autoplaySpeed: 3000,
            });
        }


        // ------------------------ Service Slider One
        if($(".service-slider-one").length) {
          $('.service-slider-one').slick({
              dots: true,
              arrows: false,
              lazyLoad: 'ondemand',
              centerPadding: '0px',
              slidesToShow: 3,
              slidesToScroll: 3,
              centerMode: true,
              autoplay: false,
              autoplaySpeed: 3000,
              responsive: [
                {
                  breakpoint: 1200,
                  settings: {
                    slidesToShow: 2
                  }
                },
                {
                  breakpoint: 768,
                  settings: {
                    slidesToShow: 1
                  }
                }
              ]
            });
        }

        // ------------------------ Project Slider One
        if($(".project-slider-one").length) {
          $('.project-slider-one').slick({
              dots: false,
              arrows: true,
              prevArrow: $('.prev_b'),
              nextArrow: $('.next_b'),
              lazyLoad: 'ondemand',
              centerPadding: '0px',
              slidesToShow: 3,
              slidesToScroll: 1,
              centerMode: true,
              autoplay: false,
              autoplaySpeed: 3000,
              responsive: [
                {
                  breakpoint: 768,
                  settings: {
                    slidesToShow: 1
                  }
                }
              ]
            });
        }


        // ------------------------ Partner Slider One
        if($(".partner-slider-one").length) {
          $('.partner-slider-one').slick({
              dots: false,
              arrows: false,
              lazyLoad: 'ondemand',
              centerPadding: '0px',
              slidesToShow: 5,
              slidesToScroll: 1,
              autoplay: true,
              autoplaySpeed: 3500,
              responsive: [
                {
                  breakpoint: 992,
                  settings: {
                    slidesToShow: 4
                  }
                },
                {
                  breakpoint: 768,
                  settings: {
                    slidesToShow: 3
                  }
                },
                {
                  breakpoint: 576,
                  settings: {
                    slidesToShow: 2
                  }
                }
              ]
            });
        }





          // ------------------------ Password Toggler
          if($(".user-data-form").length) {
            $(".passVicon").on('click', function() {
              $(".passVicon").toggleClass("eye-slash");
              var input = $(".pass_log_id");
              if (input.attr("type") === "password") {
                input.attr("type", "text");
              } else {
                input.attr("type", "password");
              }

            });
          }
      

    
$(window).on ('load', function (){ // makes sure the whole site is loaded

// -------------------- Site Preloader
        $('#ctn-preloader').fadeOut(); // will first fade out the loading animation
        $('#preloader').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website.
        $('body').delay(350).css({'overflow':'visible'});



// ------------------------------- Scroll Animation
          var wow = new WOW(
            {
              boxClass:     'wow',      // animated element css class (default is wow)
              animateClass: 'animated', // animation css class (default is animated)
              offset:       0,          // distance to the element when triggering the animation (default is 0)
              mobile:       true,       // trigger animations on mobile devices (default is true)
              live:         true,       // act on asynchronously loaded content (default is true)
            }
          );
          wow.init();
        
// ------------------------------------- Fancybox
        var fancy = $ (".fancybox");
        if(fancy.length) {
          fancy.fancybox({
            arrows: true,
            buttons: [
              "zoom",
              "slideShow",            
              "thumbs",
              "close"
            ],
            animationEffect: "zoom-in-out",
            transitionEffect: "zoom-in-out",
          });
        }



// ----------------------------- isotop gallery
        if ($("#isotop-gallery-wrapper").length) {
            var $grid = $('#isotop-gallery-wrapper').isotope({
              // options
              itemSelector: '.isotop-item',
              percentPosition: true,
              masonry: {
                // use element for option
                columnWidth: '.grid-sizer'
              }

            });

            // filter items on button click
            $('.isotop-menu-wrapper').on( 'click', 'li', function() {
              var filterValue = $(this).attr('data-filter');
              $grid.isotope({ filter: filterValue });
            });

            // change is-checked class on buttons
            $('.isotop-menu-wrapper').each( function( i, buttonGroup ) {
                var $buttonGroup = $( buttonGroup );
                $buttonGroup.on( 'click', 'li', function() {
                  $buttonGroup.find('.is-checked').removeClass('is-checked');
                  $( this ).addClass('is-checked');
                });
            });
        }


    });  //End On Load Function

})(jQuery);