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
      if (this.isBlacklisted(product, subscription)) {
        this.deletePreference(product, subscription);
      } else {
        this.addPreference(subscription, product);
      }
    },
    isBlacklisted: function (product, subscription) {
      return subscription.box_preferences.some(function (field) {
        return field.id === product.ID
      })
    },
    getSubscriptions: function () {
      const $vm = this
      axios.get('/wp-json/agrispesa/v1/user-subscriptions')
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
      axios.post(window.baseurl + '/wp-json/agrispesa/v1/subscription-preference', {
        product_id: product.ID,
        subscription_id: subscription.id
      })
        .then((response) => {
          $vm.getSubscriptions()
        });
    },
    deletePreference: function (subscription, product) {
      const $vm = this
      axios.patch(window.baseurl + '/wp-json/agrispesa/v1/subscription-preference', {
        product_id: product.id,
        subscription_id: subscription.id
      })
        .then((response) => {
          $vm.getSubscriptions()
        });
    }
  }
}).mount('#box-app')
