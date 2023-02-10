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
    togglePreference: function (product, subscription) {
      if (this.isPreference(product, subscription)) {
        this.deletePreference(subscription, product);
      } else {
        this.addPreference(subscription, product);
      }
    },
    toggleBlacklist: function (product, subscription) {
      if (this.isBlacklisted(product, subscription)) {
        this.deleteBlacklist(subscription, product);
      } else {
        this.addBlacklist(subscription, product);
      }
    },
    isBlacklisted: function (product, subscription) {
      return subscription.box_blacklist.some(function (field) {
        return field.id === product
      })
    },
    isPreference: function (product, subscription) {
      return subscription.box_preferences.some(function (field) {
        return field.id === product
      })
    },
    getSubscriptions: function () {
      const $vm = this
      axios.get(window.baseurl + '/wp-json/agrispesa/v1/user-subscriptions')
        .then((response) => {
          $vm.subscriptions = response.data
        });
    },
    getCategories: function () {
      const $vm = this
      axios.get(window.baseurl + '/wp-json/agrispesa/v1/shop-categories')
        .then((response) => {
          $vm.categories = response.data
          $vm.currentCategory = $vm.categories[0]
        });
    },
    addPreference: function (subscription, product) {
      const $vm = this
      console.log(product)
      axios.post(window.baseurl + '/wp-json/agrispesa/v1/subscription-preference', {
        product_id: product,
        subscription_id: subscription.id
      })
        .then((response) => {
          $vm.getSubscriptions()
        });
    },
    addBlacklist: function (subscription, product) {
      const $vm = this
      axios.post(window.baseurl + '/wp-json/agrispesa/v1/subscription-blacklist', {
        product_id: product,
        subscription_id: subscription.id
      })
        .then((response) => {
          $vm.getSubscriptions()
        });
    },
    deletePreference: function (subscription, product) {
      const $vm = this
      axios.patch(window.baseurl + '/wp-json/agrispesa/v1/subscription-preference', {
        product_id: product,
        subscription_id: subscription.id
      })
        .then((response) => {
          $vm.getSubscriptions()
        });
    },
    deleteBlacklist: function (subscription, product) {
      const $vm = this
      axios.patch(window.baseurl + '/wp-json/agrispesa/v1/subscription-blacklist', {
        product_id: product,
        subscription_id: subscription.id
      })
        .then((response) => {
          $vm.getSubscriptions()
        });
    }
  }
}).mount('#box-app')
