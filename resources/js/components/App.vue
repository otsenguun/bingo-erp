<template>
  <div id="app">
    <loading ref="loading" />
    <transition name="page" mode="out-in">
      <component :is="layout" v-if="layout" />
    </transition>
  </div>
</template>

<script>
import Loading from './Loading'
import store from '../store'

// Load layout components dynamically.
const requireContext = require.context('~/layouts', false, /.*\.vue$/)

const layouts = requireContext
  .keys()
  .map((file) => [file.replace(/(^.\/)|(\.vue$)/g, ''), requireContext(file)])
  .reduce((components, [name, component]) => {
    components[name] = component.default || component
    return components
  }, {})

export default {
  el: '#app',

  components: {
    Loading,
  },

  data: () => ({
    layout: null,
    defaultLayout: 'default',
  }),

  metaInfo() {
    const { appName } = window.config

    return {
      title: appName,
      titleTemplate: `%s · ${appName}`,
    }
  },

  mounted() {
    this.$loading = this.$refs.loading
    this.getSettings()

    if (!store.getters['auth/check'] && store.getters['auth/token']) {
      try {
        this.getTenant()
      } catch (e) {
        console.log('unauthenticated')
      }
    }
  },

  methods: {
    // get settings
    async getSettings() {
      await this.$store.dispatch('operations/fetchSettingData')
    },

    // get settings
    async getTenant() {
      await this.$store.dispatch('operations/fetchTenant')
    },

    /**
     * Set the application layout.
     *
     * @param {String} layout
     */
    setLayout(layout) {
      if (!layout || !layouts[layout]) {
        layout = this.defaultLayout
      }

      this.layout = layouts[layout]
    },
  },
}
</script>
