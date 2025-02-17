<template>
  <!-- NEWSLETTER AREA START -->
  <section id="newsletter">
    <div class="container">
      <div class="newsletter-inner">
        <div class="row align-items-center">
          <div class="col-md-6">
            <div class="newsletter-left">
              <h2 v-if="appInfo && appInfo.newsletter_section_title">
                {{ appInfo && appInfo.newsletter_section_title }}
              </h2>
              <p v-if="appInfo && appInfo.newsletter_section_description">
                {{ appInfo && appInfo.newsletter_section_description }}
              </p>
            </div>
          </div>
          <div class="col-md-6">
            <form @submit.prevent="subscribeNewsletter" class="newsletter-right">
              <input v-model="form.email" type="email" name="email" placeholder="Enter your email address" required>
              <v-button :loading="form.busy">
                <img src="/images/template/paper-plane.png" />
              </v-button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- NEWSLETTER AREA END -->
</template>

<script>
import { mapGetters } from 'vuex'
import Form from 'vform'

export default {
  name: 'Newsletter',
  // Map Getters
  computed: {
    ...mapGetters('operations', ['appInfo']),
  },

  data() {
    return {
      form: new Form({
        email: '',
      }),
    }
  },

  methods: {
    async subscribeNewsletter() {
      await this.form
        .post(window.location.origin + '/api/newsletter-send-confirmation')
        .then(({ data }) => {
          toast.fire({
            type: 'success',
            title: data.message,
          })
          this.form.reset()
        })
        .catch((e) => {
          toast.fire({ type: 'error', title: e.response.data.message })
        })
    },
  },
}
</script>

<style scoped lang="scss">
.newsletter-right input {
  outline: none;
}

.btn-loading img {
  display: none;
}
</style>
