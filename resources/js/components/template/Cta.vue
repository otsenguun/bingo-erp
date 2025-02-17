<template>
  <!-- CTA AREA START -->
  <section class="cta-area">
    <div class="container">
      <div class="col-md-10 m-auto">
        <div class="cta-band">
          <div>
            <h2 v-if="appInfo && appInfo.get_started_box_title">
              {{ appInfo && appInfo.get_started_box_title }}
            </h2>
            <p v-if="appInfo && appInfo.get_started_box_description">
              {{ appInfo && appInfo.get_started_box_description }}
            </p>
          </div>
          <a v-if="appInfo && appInfo.get_started_box_button_text && appInfo && appInfo.get_started_box_button_link"
            :href="appInfo && appInfo.get_started_box_button_link" class="btn btn-primary">
            {{ appInfo && appInfo.get_started_box_button_text }}
          </a>
        </div>
      </div>
    </div>
  </section>
  <!-- CTA AREA  END -->
</template>
<script>
import { mapGetters } from 'vuex'

export default {
  name: 'Cta',
  // Map Getters
  computed: {
    ...mapGetters('operations', ['appInfo']),
  },
}
</script>

