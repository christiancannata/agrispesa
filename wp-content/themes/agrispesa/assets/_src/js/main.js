/* global WPURL:readonly */
window.baseurl = WPURL.siteurl


function setCookie(cName, cValue, expDays) {
  let date = new Date();
  date.setTime(date.getTime() + (expDays * 24 * 60 * 60 * 1000));
  const expires = "expires=" + date.toUTCString();
  document.cookie = cName + "=" + cValue + "; " + expires + "; path=/";
}

function getCookie(cName) {
  const name = cName + "=";
  const cDecoded = decodeURIComponent(document.cookie); //to be careful
  const cArr = cDecoded.split('; ');
  let res;
  cArr.forEach(val => {
    if (val.indexOf(name) === 0) res = val.substring(name.length);
  })
  return res;
}

let hasCookie = getCookie('home_popup')
if (!hasCookie) {
  jQuery('#home_popup').fadeIn(300)
}

stickyHeader();
openMenu();
clearSearch();
openSearch();
openSubMenu();
quantityInput();
variationToRadio();
faqs();
reviewsSlider();
footerMenu();
hideBreadcrum();
productsCarousel();
productsCarouselHome();
loginForms();
showCoupon();
closeNotices();
relatedSlider();
formGiftCard();
infoAgr();
closePopup();
pressSlider();
scrollTo();
hideGlossarioAlpha();
magazineSlider();
removeCheckoutButton();
//changeShippingLabel();

function removeCheckoutButton() {
  if(jQuery('.minimum-amount-advice').length) {
    jQuery('.wc-proceed-to-checkout').hide();
  }
}

function hideGlossarioAlpha() {
  var glossarioElements = jQuery('.glossario--anchor');
  glossarioElements.each(function(index) {
    var alphabet = jQuery('.glossario--link');
    var target = jQuery(this).attr('data-alpha');

    jQuery('.glossario--link[data-alpha="' + target + '"]').removeClass('disabled');
  });
}

function changeShippingLabel() {
  if(jQuery('.cart_totals').length) {
    let amount = jQuery('.cart_totals').find('.shipping .amount').html();
    jQuery('.woocommerce-shipping-totals td').html(amount);
  }
}


function scrollTo() {

  jQuery('.sliding-link').on('click', function(event) {
    var target = jQuery(this.getAttribute('href'));
    var scrollto = target.offset().top - 100

    if (target.length) {
      event.preventDefault();
      jQuery('html, body').stop().animate({
        scrollTop: scrollto
      }, 800);
    }
  });
}

function pressSlider() {

  var _carousel = jQuery(".press--slider");

  _carousel.slick({
    dots: true,
    arrows: false,
    infinite: true,
    speed: 300,
    slidesToShow: 6,
    slidesToScroll: 6,
    centerMode: false,
    autoplay: false,
    responsive: [{
        breakpoint: 1240,
        settings: {
          slidesToShow: 5,
          slidesToScroll: 5,
          dots: true,
          arrows: false
        }
      }, {
        breakpoint: 1024,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3,
          dots: true,
          arrows: false
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          dots: true,
          arrows: false
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          dots: true,
          arrows: false
        }
      }
    ]
  });

}

function closePopup() {
  // Close Popup
  jQuery('[popup-close]').on('click', function() {
    var popup_name = jQuery(this).attr('popup-close');
    jQuery('[popup-name="' + popup_name + '"]').fadeOut(300);
    setCookie('home_popup', 1, 14)
  });


  // Close Popup When Click Outside
  jQuery('.popup').on('click', function() {
    var popup_name = jQuery(this).find('[popup-close]').attr('popup-close');
    jQuery('[popup-name="' + popup_name + '"]').fadeOut(300);
    setCookie('home_popup', 1, 14)
  }).children().click(function() {
    return false;
  });
}

function infoAgr() {
  jQuery('.info_agr--button').on('click', function(e) {
    e.preventDefault();
    jQuery(this).toggleClass('active');
    jQuery('.info_agr').toggleClass('hide');
  });
}

function formGiftCard() {

  if (jQuery('.gift-card-page').length) {
    jQuery('.woocommerce-breadcrumb').hide();
  }

  jQuery('.gift-card-content-editor input,.gift-card-content-editor textarea').on('focus', function() {
    jQuery(this).parent().find('label').addClass('move');
  })

  jQuery('label[for = ywgc-delivery-date]').text('Data di consegna');
  jQuery('label[for = ywgc-recipient-name]').text('Il suo nome');
  jQuery('label[for = ywgc-recipient-email]').text('Il suo indirizzo email');
  jQuery('label[for = ywgc-sender-name]').text('Il tuo nome');
  jQuery('label[for = ywgc-edit-message]').text('Scrivi un messaggio');


  if (jQuery("#ywgc-edit-message").length) {
    jQuery("#ywgc-edit-message").attr('maxlength', '350')
  }

}

function closeNotices() {
  jQuery('.woocommerce-notices-wrapper .close-notice').on('click', function(e) {
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
  let showLogin = jQuery('.show-login-form');
  let showRegister = jQuery('.show-register-form');
  let loginForm = jQuery('.check-login-form');
  let registerForm = jQuery('.check-register-form');

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
  let boxPage = jQuery('.the-box-page');
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
    slidesToScroll: 1,
    arrows: true,
    dots: false,
    nextArrow: '<span class="slick-next-agr icon-arrow-right"></span>',
    prevArrow: '<span class="slick-prev-agr icon-arrow-left"></span>',
    responsive: [{
      breakpoint: 900,
      settings: {
        arrows: false,
        dots: true
      }
    }]
  });
}


function productsCarouselHome() {

  let _carousel = jQuery(".products-carousel-home--slider");

  _carousel.slick({
    infinite: true,
    speed: 300,
    slidesToShow: 3,
    slidesToScroll: 1,
    arrows: true,
    dots: false,
    centerMode: false,
    nextArrow: '<span class="slick-next-agr icon-arrow-right"></span>',
    prevArrow: '<span class="slick-prev-agr icon-arrow-left"></span>',
    responsive: [{
      breakpoint: 1400,
      settings: {
        slidesToShow: 3,
      }
    }, {
      breakpoint: 1100,
      settings: {
        slidesToShow: 3,
      }
    }, {
      breakpoint: 800,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 2,
        arrows: false,
        dots: true
      }
    }, {
      breakpoint: 600,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        dots: true
      }
    }]
  });
}

function productsCarousel() {

  let _carousel = jQuery(".products-carousel");

  _carousel.slick({
    infinite: true,
    speed: 300,
    slidesToShow: 5,
    slidesToScroll: 1,
    arrows: true,
    dots: false,
    centerMode: false,
    nextArrow: '<span class="slick-next-agr icon-arrow-right"></span>',
    prevArrow: '<span class="slick-prev-agr icon-arrow-left"></span>',
    responsive: [{
      breakpoint: 1400,
      settings: {
        slidesToShow: 4,
      }
    }, {
      breakpoint: 1100,
      settings: {
        slidesToShow: 3,
      }
    }, {
      breakpoint: 800,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 2,
        arrows: false,
        dots: true
      }
    }, {
      breakpoint: 600,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        dots: true
      }
    }]
  });
}

function magazineSlider() {

  let _carousel = jQuery(".magazine--slider");

  _carousel.slick({
    infinite: true,
    speed: 300,
    slidesToShow: 3,
    slidesToScroll: 1,
    arrows: true,
    dots: false,
    centerMode: false,
    nextArrow: '<span class="slick-next-agr icon-arrow-right"></span>',
    prevArrow: '<span class="slick-prev-agr icon-arrow-left"></span>',
    responsive: [{
      breakpoint: 1240,
      settings: {
        slidesToShow: 2,
        arrows: false,
        dots: true
      }
    }, {
      breakpoint: 860,
      settings: {
        slidesToShow: 1,
        arrows: false,
        dots: true
      }
    }]
  });
}

function relatedSlider() {

  let _carousel = jQuery(".related--list");

  _carousel.slick({
    infinite: true,
    speed: 300,
    slidesToShow: 3,
    slidesToScroll: 1,
    arrows: true,
    dots: false,
    nextArrow: '<span class="slick-next-agr icon-arrow-right"></span>',
    prevArrow: '<span class="slick-prev-agr icon-arrow-left"></span>',
    responsive: [{
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

  let faqTitle = jQuery('.faq__title');

  faqTitle.on('click', function(e) {
    e.preventDefault();
    let description = jQuery(this).next('.faq__description');
    let others = jQuery(this).closest('.faq__item').siblings();

    jQuery(this).find('.faq__icon').toggleClass('show-faq');
    others.find('.faq__icon').removeClass('show-faq');
    others.find('.faq__description').slideUp();
    description.slideToggle();
  });
  if (window.screen.width > 640) {
    jQuery('.faq-1 .faq__title').trigger("click");
  }
}

function variationToRadio() {
  jQuery(document).on('change', '.variation-radios input', function() {
    jQuery('.variation-radios input:checked').each(function(index, element) {
      let $el = jQuery(element);
      let thisName = $el.attr('name');
      let thisVal = $el.attr('value');
      jQuery('select[name="' + thisName + '"]').val(thisVal).trigger('change');
    });
  });
  jQuery(document).on('woocommerce_update_variation_values', function() {
    jQuery('.variation-radios input').each(function(index, element) {
      let $el = jQuery(element);
      let thisName = $el.attr('name');
      let thisVal = $el.attr('value');
      $el.removeAttr('disabled');
      if (jQuery('select[name="' + thisName + '"] option[value="' + thisVal + '"]').is(':disabled')) {
        $el.prop('disabled', true);
      }
    });
  });
}

function quantityInput() {

  jQuery(function($) {
    if (!String.prototype.getDecimals) {
      String.prototype.getDecimals = function() {
        var num = jQuery(this),
          match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
        if (!match) {
          return 0;
        }
        return Math.max(0, (match[1] ? match[1].length : 0) - (match[2] ? +match[2] : 0));
      }
    }
    // Quantity "plus" and "minus" buttons
    jQuery(document.body).on('click', '.product-quantity--plus, .product-quantity--minus', function() {
      var $qty = jQuery(this).closest('.product-quantity--change').find('.quantity .qty'),
        currentVal = parseFloat($qty.val()),
        max = parseFloat($qty.attr('max')),
        min = parseFloat($qty.attr('min')),
        step = $qty.attr('step');

      // Format values
      if (!currentVal || currentVal === '' || currentVal === 'NaN') currentVal = 0;
      if (max === '' || max === 'NaN') max = '';
      if (min === '' || min === 'NaN') min = 0;
      if (step === 'any' || step === '' || step === undefined || parseFloat(step) === 'NaN') step = 1;

      // Change the value
      if (jQuery(this).is('.product-quantity--plus')) {

        if (max && (currentVal >= max)) {
          $qty.val(max);
        } else {
          $qty.val((currentVal + parseFloat(step)).toFixed(step.getDecimals()));
        }
      } else {
        if (min && (currentVal <= min)) {
          $qty.val(min);
        } else if (currentVal > 0) {
          $qty.val((currentVal - parseFloat(step)).toFixed(step.getDecimals()));
        }
      }


      setTimeout(function() {
        jQuery("[name='update_cart']").removeAttr('disabled');
        jQuery("[name='update_cart']").trigger("click");
      }, 500);



    });
  });

}


function openSubMenu() {
  let link = jQuery('.get-user-menu');
  let menu = jQuery('.top-user__menu');

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
  let header = jQuery('.header');
  let headerH = header.outerHeight();
  let lastScrollTop = 0;

  jQuery(window).on('scroll', function() {
    let st = jQuery(this).scrollTop();

    if (jQuery(document).scrollTop() >= headerH) {
      if (st > lastScrollTop) {
        //scroll down
        jQuery('.header').addClass("sticky-hide");
        jQuery('.header').removeClass("sticky");
      } else {
        //scroll up
        jQuery('.header').addClass("sticky");
        jQuery('.header').removeClass("sticky-hide");
      }
      lastScrollTop = st;
    } else {
      jQuery('.header').removeClass("sticky-hide");
      jQuery('.header').removeClass("sticky");

    }

  });
}

function openMenu() {
  jQuery('.get-menu, .close-menu').on('click', function(e) {
    e.preventDefault();

    let menu = jQuery('.agr-menu');
    let body = jQuery('body');

    menu.toggleClass('show-menu');
    body.toggleClass('fixed');
  });
}
