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
      product_to_remove: null,
      product_to_add: null
    }
  },
  mounted() {
    this.getSubscriptions()
    this.getCategories()
  },
  methods: {
    getSubscriptions: function () {
      const $vm = this
      axios.get('/wp-json/agrispesa/v1/user-subscriptions')
        .then((response) => {
          $vm.subscriptions = response.data
        });
    },
    getCategories: function () {
      const $vm = this
      axios.get('/wp-json/agrispesa/v1/shop-categories')
        .then((response) => {
          $vm.categories = response.data
        });
    },
    addPreference: function (subscription_id) {
      const $vm = this
      axios.post('/wp-json/agrispesa/v1/subscription-preference', {
        product_to_remove: $vm.product_to_remove,
        product_to_add: $vm.product_to_add,
        subscription_id: subscription_id
      })
        .then((response) => {
          $vm.getSubscriptions()
        });
    },
    deletePreference: function (subscription_id, index) {
      const $vm = this
      axios.patch('/wp-json/agrispesa/v1/subscription-preference', {
        index: index,
        subscription_id: subscription_id
      })
        .then((response) => {
          $vm.getSubscriptions()
        });
    }
  }
}).mount('#box-app')
