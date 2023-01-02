stickyHeader();
scrollTo();
hideGlossarioAlpha();
openMenu();
clearSearch();
openSearch();
openSubMenu();
quantityInput();
variablesToRadio();
faqs();
reviewsSlider();
footerMenu();
hideBreadcrum();
productsHome();
loginForms();
showCoupon();
closeNotices();


function closeNotices() {
  jQuery('.woocommerce-message .close-notice').on('click', function(e) {
    e.preventDefault();
    jQuery(this).closest('.woocommerce-notices-wrapper').remove();
  });
}

function showCoupon() {
  jQuery('.show-coupon').on('click', function(e) {
    e.preventDefault();
    jQuery('.my-coupon').slideToggle();
  });
}

function loginForms() {
  var showLogin = jQuery('.show-login-form');
  var showRegister = jQuery('.show-register-form');
  var loginForm = jQuery('.check-login-form');
  var registerForm = jQuery('.check-register-form');

  showLogin.on('click', function(e) {
    e.preventDefault();
    loginForm.slideToggle();
    registerForm.slideToggle();
  });
  showRegister.on('click', function(e) {
    e.preventDefault();
    loginForm.slideToggle();
    registerForm.slideToggle();
  });

}

function hideBreadcrum() {
  var boxPage = jQuery('.the-box-page');
  if (boxPage.length) {
    jQuery('.woocommerce-breadcrumb').hide();
    jQuery('.summary .price').hide();
  }
}


function footerMenu() {
  if (window.screen.width < 641) {
    jQuery('.footer--menu--title').on('click', function() {
      jQuery(this).next('.footer--menu--list').slideToggle();
      jQuery(this).find('.footer--menu--title__icon').toggleClass('rotate');
      jQuery(this).closest('.footer-menu').siblings().find('.footer--menu--list').slideUp();
      jQuery(this).closest('.footer-menu').siblings().find('.footer--menu--title__icon').removeClass('rotate');
    });
  }
}

function reviewsSlider() {

  let _carousel = jQuery(".reviews--slider");

  _carousel.slick({
    infinite: true,
    speed: 300,
    slidesToShow: 1,
    arrows: true,
    dots: false,
    nextArrow: '<span class="slick-next-agr icon-arrow-right"></span>',
    prevArrow: '<span class="slick-prev-agr icon-arrow-left"></span>',
    responsive: [{
      breakpoint: 600,
      settings: {
        arrows: false,
        dots: true
      }
    }]
  });
}

function productsHome() {

  let _carousel = jQuery(".products-home");

  _carousel.slick({
    infinite: true,
    speed: 300,
    slidesToShow: 5,
    slidesToScroll: 1,
    arrows: false,
    dots: false,
    nextArrow: '<span class="slick-next-agr icon-arrow-right"></span>',
    prevArrow: '<span class="slick-prev-agr icon-arrow-left"></span>',
    responsive: [{
      breakpoint: 1300,
      settings: {
        slidesToShow: 4,
        arrows: false,
        dots: true
      }
    }, {
      breakpoint: 1140,
      settings: {
        slidesToShow: 3,
        arrows: false,
        dots: true
      }
    },{
      breakpoint: 860,
      settings: {
        slidesToShow: 2,
        arrows: false,
        dots: true
      }
    }, {
      breakpoint: 600,
      settings: {
        slidesToShow: 1,
        arrows: false,
        dots: true
      }
    }]
  });
}


function faqs() {

  var faqTitle = jQuery('.faq__title');

  faqTitle.on('click', function(e) {
    e.preventDefault();
    var description = jQuery(this).next('.faq__description');
    var others = jQuery(this).closest('.faq__item').siblings();

    jQuery(this).find('.faq__icon').toggleClass('show-faq');
    others.find('.faq__icon').removeClass('show-faq');
    others.find('.faq__description').slideUp();
    description.slideToggle();
  });
  if (window.screen.width > 640) {
    jQuery('.faq-1 .faq__title').trigger("click");
  }
}

function variablesToRadio() {
  jQuery(document).on('change', '.variation-radios input', function() {
    jQuery('.variation-radios input:checked').each(function(index, element) {
      var $el = jQuery(element);
      var thisName = $el.attr('name');
      var thisVal = $el.attr('value');
      jQuery('select[name="' + thisName + '"]').val(thisVal).trigger('change');
    });
  });
  jQuery(document).on('woocommerce_update_variation_values', function() {
    jQuery('.variation-radios input').each(function(index, element) {
      var $el = jQuery(element);
      var thisName = $el.attr('name');
      var thisVal = $el.attr('value');
      $el.removeAttr('disabled');
      if (jQuery('select[name="' + thisName + '"] option[value="' + thisVal + '"]').is(':disabled')) {
        $el.prop('disabled', true);
      }
    });
  });
}

function quantityInput() {
  jQuery('.product-quantity--plus').click(function(e) {
    // Stop acting like a button
    e.preventDefault();
    // Get the field name
    var fieldName = jQuery(this).attr('field');
    // Get its current value
    var currentVal = parseInt(jQuery('input[name=' + fieldName + ']').val());
    // If is not undefined
    if (!isNaN(currentVal)) {
      // Increment
      jQuery('input[name=' + fieldName + ']').val(currentVal + 1);
      jQuery('.product-quantity--minus').removeClass('disabled');
    } else {
      // Otherwise put a 0 there
      jQuery('input[name=' + fieldName + ']').val(1);
      jQuery('.product-quantity--minus').removeClass('disabled');
    }
  });

  jQuery(".product-quantity--minus").click(function(e) {
    // Stop acting like a button
    e.preventDefault();
    // Get the field name
    var fieldName = jQuery(this).attr('field');
    // Get its current value
    var currentVal = parseInt(jQuery('input[name=' + fieldName + ']').val());
    // If it isn't undefined or its greater than 0

    console.log(currentVal);
    if (!isNaN(currentVal) && currentVal > 2) {
      // Decrement one
      jQuery('input[name=' + fieldName + ']').val(currentVal - 1);
      jQuery(this).removeClass('disabled');
    } else if (!isNaN(currentVal) && currentVal > 1) {
      // Decrement one
      jQuery('input[name=' + fieldName + ']').val(currentVal - 1);
      jQuery(this).addClass('disabled');

    } else {
      // Otherwise put a 0 there
      jQuery('input[name=' + fieldName + ']').val(1);
      jQuery(this).addClass('disabled');
    }
  });
}

function openSubMenu() {
  var link = jQuery('.get-user-menu');
  var menu = jQuery('.top-user__menu');

  if (window.screen.width > 640) {
    link.on('click', function(e) {
      e.preventDefault();
      jQuery(this).toggleClass('active');
      menu.toggleClass('active');
    });
  }
}

function openSearch() {
  jQuery('.openSearch').on('click', function(e) {
    e.preventDefault();
    jQuery('.header--search').addClass('showme');
    jQuery(this).closest('.menu--search').addClass('hideme');
  });
}

function clearSearch() {
  jQuery('.delete-search').on('click', function() {
    jQuery('.search-input-field').val('');
  });
}

function stickyHeader() {
  var scrollTop = jQuery(this).scrollTop();
  var header = jQuery('.header');
  var headerH = header.outerHeight();
  var lastScrollTop = 0;

  jQuery(window).on('scroll', function() {
    var st = jQuery(this).scrollTop();

    if (jQuery(document).scrollTop() >= headerH) {
      if (st > lastScrollTop) {
        //scroll down
        jQuery('.header').addClass("sticky-hide");
      } else {
        //scroll up
        jQuery('.header').removeClass("sticky-hide");
      }
      lastScrollTop = st;
    } else {
      jQuery('.header').removeClass("sticky-hide");

    }

  });
}

function openMenu() {
  jQuery('.get-menu, .close-menu').on('click', function(e) {
    e.preventDefault();

    var menu = jQuery('.agr-menu');
    var body = jQuery('body');

    menu.toggleClass('show-menu');
    body.toggleClass('fixed');
  });
}


function hideGlossarioAlpha() {
  var glossarioElements = jQuery('.glossario--anchor');
  glossarioElements.each(function(index) {
    var alphabet = jQuery('.glossario--link');
    var target = jQuery(this).attr('data-alpha');

    jQuery('.glossario--link[data-alpha="' + target + '"]').removeClass('disabled');
  });
}


function scrollTo() {

  jQuery('.sliding-link').on('click', function(event) {
    var target = jQuery(this.getAttribute('href'));
    var scrollto = target.offset().top - 35

    if (target.length) {
      event.preventDefault();
      jQuery('html, body').stop().animate({
        scrollTop: scrollto
      }, 800);
    }
  });
}
