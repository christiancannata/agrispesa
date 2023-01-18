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
productsHome();
loginForms();
showCoupon();
closeNotices();
relatedSlider();
formGiftCard();


function formGiftCard() {

  if (jQuery('.gift-card-page').length) {
    jQuery('.woocommerce-breadcrumb').hide();
  }

  jQuery('.gift-card-content-editor input,.gift-card-content-editor textarea').on('focus', function () {
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
  jQuery('.woocommerce-notices-wrapper .close-notice').on('click', function (e) {
    e.preventDefault();
    jQuery(this).closest('.woocommerce-notices-wrapper').remove();
  });
}

function showCoupon() {
  jQuery('.show-coupon').on('click', function (e) {
    e.preventDefault();
    jQuery('.my-coupon').slideToggle();
  });
}

function loginForms() {
  let showLogin = jQuery('.show-login-form');
  let showRegister = jQuery('.show-register-form');
  let loginForm = jQuery('.check-login-form');
  let registerForm = jQuery('.check-register-form');

  showLogin.on('click', function (e) {
    e.preventDefault();
    loginForm.slideToggle();
    registerForm.slideToggle();
  });
  showRegister.on('click', function (e) {
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
    jQuery('.footer--menu--title').on('click', function () {
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

  let _carousel = jQuery(".products-carousel");

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

  faqTitle.on('click', function (e) {
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
  jQuery(document).on('change', '.variation-radios input', function () {
    jQuery('.variation-radios input:checked').each(function (index, element) {
      let $el = jQuery(element);
      let thisName = $el.attr('name');
      let thisVal = $el.attr('value');
      jQuery('select[name="' + thisName + '"]').val(thisVal).trigger('change');
    });
  });
  jQuery(document).on('woocommerce_update_variation_values', function () {
    jQuery('.variation-radios input').each(function (index, element) {
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
  jQuery('.product-quantity--plus').click(function (e) {

    console.log('clic più');
    let numberInput = jQuery(this).closest('.product-quantity--change').find('.quantity input[type=number]');
    // Stop acting like a button
    e.preventDefault();
    // Get its current value
    let currentVal = parseInt(numberInput.val());
    // If is not undefined
    if (!isNaN(currentVal)) {
      // Increment
      numberInput.val(currentVal + 1);
      jQuery(this).closest('.product-quantity--change').find('.product-quantity--minus').removeClass('disabled');
    } else {
      // Otherwise put a 0 there
      numberInput.val(1);
      jQuery(this).closest('.product-quantity--change').find('.product-quantity--minus').removeClass('disabled');
    }

    numberInput.trigger('change');
    //jQuery('.btn-cart-update').click();

  });

  jQuery(".product-quantity--minus").click(function (e) {
    let numberInput = jQuery(this).closest('.product-quantity--change').find('.quantity input[type=number]');
    // Stop acting like a button
    console.log('clic meno');
    e.preventDefault();
    // Get its current value
    let currentVal = numberInput.val();
    // If it isn't undefined or its greater than 0

    if (!isNaN(currentVal) && currentVal > 2) {
      // Decrement one
      numberInput.val(currentVal - 1);
      jQuery(this).removeClass('disabled');
    } else if (!isNaN(currentVal) && currentVal > 1) {
      // Decrement one
      numberInput.val(currentVal - 1);
      jQuery(this).addClass('disabled');

    } else {
      // Otherwise put a 0 there
      numberInput.val(1);
      jQuery(this).addClass('disabled');
    }

    numberInput.trigger('change');

    //jQuery('.btn-cart-update').click();

  });
}

function openSubMenu() {
  let link = jQuery('.get-user-menu');
  let menu = jQuery('.top-user__menu');

  if (window.screen.width > 640) {
    link.on('click', function (e) {
      e.preventDefault();
      jQuery(this).toggleClass('active');
      menu.toggleClass('active');
    });
  }
}

function openSearch() {
  jQuery('.openSearch').on('click', function (e) {
    e.preventDefault();
    jQuery('.header--search').addClass('showme');
    jQuery(this).closest('.menu--search').addClass('hideme');
  });
}

function clearSearch() {
  jQuery('.delete-search').on('click', function () {
    jQuery('.search-input-field').val('');
  });
}

function stickyHeader() {
  let header = jQuery('.header');
  let headerH = header.outerHeight();
  let lastScrollTop = 0;

  jQuery(window).on('scroll', function () {
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
  jQuery('.get-menu, .close-menu').on('click', function (e) {
    e.preventDefault();

    let menu = jQuery('.agr-menu');
    let body = jQuery('body');

    menu.toggleClass('show-menu');
    body.toggleClass('fixed');
  });
}
