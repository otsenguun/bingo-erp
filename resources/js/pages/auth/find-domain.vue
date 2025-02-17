<template>
  <div class="container-fluid">
    <div class="row no-gutter">
      <!-- The image half -->
      <div class="col-md-6 d-none d-md-flex bg-image"></div>
      <!-- The content half -->
      <div class="col-md-6 bg-light">
        <div class="auth-wrapper d-flex align-items-center py-5">
          <div class="container">
            <div class="row">
              <div class="col-md-12 col-lg-10 col-xl-8 mx-auto">
                <div class="text-center mb-4">
                  <router-link to="/">
                    <img v-if="appInfo" :src="appInfo.blackLogo" :alt="appInfo.companyName"
                      class="lg-logo img-fluid logo-width" />
                  </router-link>
                </div>

                <form @submit.prevent="findDomain" @keydown="form.onKeydown($event)">
                  <!-- domain -->
                  <div class="form-group mb-3 ">
                    <div class="d-flex url">
                      <input v-model="form.domain" id="domain" name="domain"
                        :class="{ 'is-invalid': form.errors.has('domain') }" class="
                          form-control
                          rounded-pill
                          border-0
                          shadow-sm
                          px-4
                          text-primary
                        " type="text" :placeholder="$t('domain')" />
                      <span>{{ host }}</span>
                    </div>
                    <has-error :form="form" :style="[form.errors.has('domain') ? 'block' : 'none']" field="domain" />
                  </div>
                  <!-- Submit Button -->
                  <v-button :loading="form.busy"
                    class="btn btn-primary btn-block text-uppercase mb-2 rounded-pill shadow-sm">
                    <i class="fas fa-sign-in-alt" />
                    <strong>{{ $t('find') }}</strong>
                  </v-button>
                </form>
                <div class="row text-center">
                  <router-link :to="{ name: 'register' }" class="ml-auto my-auto">
                    {{ $t('register_invite') }}
                  </router-link>
                </div>
              </div>
            </div>
          </div>
          <!-- End -->
        </div>
      </div>
      <!-- End -->
    </div>
  </div>
</template>
<script>
import Form from 'vform'
import { mapGetters } from 'vuex'

export default {
  layout: 'basic',
  middleware: 'guest',
  metaInfo() {
    return { title: this.$t('find_domain') }
  },
  data: () => ({
    form: new Form({
      domain: '',
    }),
    appName: window.config.appName,
    host: location.host
  }),
  // Map Getters
  computed: {
    ...mapGetters('operations', ['appInfo']),
  },
  methods: {
    async findDomain() {
      // find the user.
      const data = await this.form.post('/api/find-domain')
      if (data) {
        window.location.href = location.protocol + '//' + data.data.data.domain
      }
    },
  }
}
</script>

