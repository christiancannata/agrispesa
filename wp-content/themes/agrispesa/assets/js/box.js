jQuery(document).ready(function ($) {
  $(".select2").select2()
})

const {createApp} = Vue
createApp({
  data() {
    return {
      message: '',
      categories: [],
      subscriptions: [],
      isAllWishlistToggled: false,
      currentCategory: null,
      product_to_remove: null,
      product_to_add: null,
      products_preferences_id: []
    }
  },
  mounted() {
    this.getSubscriptions()
    this.getCategories()
  },
  methods: {
    toggleAllBlacklist: function (category, subscription) {

      let products = category.products.map(function (product) {
        return product.code
      })

      if (!category.is_all_blacklist_selected) {
        this.addBlacklist(subscription, products);
        category.is_all_blacklist_selected = true
      } else {
        this.deleteBlacklist(subscription, products);
        category.is_all_blacklist_selected = false

      }

    },
    toggleAllWishlist: function (category, subscription) {

      let products = category.products.map(function (product) {
        return product.code
      })

      if (!category.is_all_wishlist_selected) {
        this.addPreference(subscription, products);
        category.is_all_wishlist_selected = true
      } else {
        this.deletePreference(subscription, products);
        category.is_all_wishlist_selected = false
      }

    },
    isAllBlacklistToggled: function (category_id) {
      return false
    },
    togglePreference: function (product, subscription) {
      if (this.isPreference(product, subscription)) {
        this.deletePreference(subscription, [product]);
      } else {
        this.addPreference(subscription, [product]);
      }
    },
    toggleBlacklist: function (product, subscription) {
      if (this.isBlacklisted(product, subscription)) {
        this.deleteBlacklist(subscription, [product]);
      } else {
        this.addBlacklist(subscription, [product]);
      }
    },
    isBlacklisted: function (product, subscription) {
      return subscription.box_blacklist.some(function (field) {
        return field.code == product
      })
    },
    isPreference: function (product, subscription) {
      return subscription.box_preferences.some(function (field) {
        return field.code == product
      })
    },
    getSubscriptions: function () {
      const $vm = this
      axios.get(window.baseurl + '/wp-json/agrispesa/v1/user-subscriptions?userId=' + window.userId)
        .then((response) => {
          $vm.subscriptions = response.data
        });
    },
    getCategories: function () {
      const $vm = this
      axios.get(window.baseurl + '/wp-json/agrispesa/v1/shop-categories')
        .then((response) => {
          $vm.categories = response.data.map(function (category) {
            category.is_all_blacklist_selected = false
            category.is_all_wishlist_selected = false
            return category
          })
          $vm.currentCategory = $vm.categories[0]
        });
    },
    addPreference: function (subscription, products) {
      const $vm = this
      axios.post(window.baseurl + '/wp-json/agrispesa/v1/subscription-preference', {
        product_ids: products,
        subscription_id: subscription.id
      })
        .then((response) => {
          $vm.getSubscriptions()
        });
    },
    addBlacklist: function (subscription, products) {
      const $vm = this
      axios.post(window.baseurl + '/wp-json/agrispesa/v1/subscription-blacklist', {
        product_ids: products,
        subscription_id: subscription.id
      })
        .then((response) => {
          $vm.getSubscriptions()
        });
    },
    deletePreference: function (subscription, products) {
      const $vm = this
      axios.delete(window.baseurl + '/wp-json/agrispesa/v1/subscription-preference', {
        data: {
          product_ids: products,
          subscription_id: subscription.id
        }
      })
        .then((response) => {
          $vm.getSubscriptions()
        });
    },
    deleteBlacklist: function (subscription, products) {
      const $vm = this
      axios.delete(window.baseurl + '/wp-json/agrispesa/v1/subscription-blacklist', {
        data: {
          product_ids: products,
          subscription_id: subscription.id
        }
      })
        .then((response) => {
          $vm.getSubscriptions()
        });
    }
  }
}).mount('#box-app')
