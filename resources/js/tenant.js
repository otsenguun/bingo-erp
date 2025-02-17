import Vue from 'vue'
import '~/plugins'
import '~/seperate-plugins/axios-tenant'
import '~/components'
import store from '~/store'
import router from '~/router/tenant'
import i18n from '~/plugins/i18n'
import can from '~/helpers/can'
import App from '~/components/App'

// vue page transition
import '@toast-ui/editor/dist/toastui-editor.css'

// vue page transition
import VuePageTransition from 'vue-page-transition'
// vue clipboard
import Clipboard from 'v-clipboard'
// vue-masonry-css for role permissions
import VueMasonry from 'vue-masonry-css'
// vue print tables
import VueHtmlToPaper from 'vue-html-to-paper'
// vue date range picker
import DateRangePicker from 'vue-mj-daterangepicker'
// vue v-select
import vSelect from 'vue-select'
// vue tooltip
import VTooltip from 'v-tooltip'
import axios from 'axios'

Vue.use(VuePageTransition)

window.Vue = import('vue').default
Vue.use(Clipboard)

Vue.use(VueMasonry)

const options = {
  name: '_blank',
  styles: [
    'https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap',
    window.location.origin + '/css/app.css'
  ],
  timeout: 1000, // default timeout before the print window appears
}
Vue.use(VueHtmlToPaper, options)

Vue.use(DateRangePicker)

Vue.component('VSelect', vSelect)

import VueMoment from 'vue-moment'
// vue moment js
Vue.use(VueMoment)


import { vfmPlugin } from "vue-final-modal";

Vue.use(vfmPlugin);

Vue.use(VTooltip)
VTooltip.options.defaultTemplate = '<div class="tooltip-vue" role="tooltip"><div class="tooltip-vue-arrow"></div><div class="tooltip-vue-inner"></div></div>'
VTooltip.options.defaultArrowSelector = '.tooltip-vue-arrow, .tooltip-vue__arrow'
VTooltip.options.defaultInnerSelector = '.tooltip-vue-inner, .tooltip-vue__inner'

Vue.config.productionTip = false
Vue.prototype.$can = can
Vue.prototype.$tenant = window.tenant
Vue.prototype.$stripe_key = window.stripe_key
Vue.prototype.$axios = axios

/* eslint-disable no-new */
new Vue({
  i18n,
  store,
  router,
  ...App
})
