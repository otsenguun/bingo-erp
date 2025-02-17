<template>
  <!-- HERO AREA START -->
  <section class="hero-area" id="home">
    <div class="container">
      <div class="row">
        <div class="col-md-7">
          <h5 v-if="appInfo && appInfo.hero_tagline">
            {{ appInfo && appInfo.hero_tagline }}
          </h5>
          <h2 v-if="appInfo && appInfo.hero_title">
            {{ appInfo && appInfo.hero_title }}
          </h2>
          <p v-if="appInfo && appInfo.hero_description">
            {{ appInfo && appInfo.hero_description }}
          </p>
          <div class="hero-btn">
            <a v-if="appInfo && appInfo.hero_demo_button_link && appInfo && appInfo.hero_demo_button_link"
              :href="appInfo && appInfo.hero_demo_button_link">
              {{ appInfo && appInfo.hero_demo_button_text }}
            </a>
            <a v-if="appInfo && appInfo.hero_get_started_button_link && appInfo && appInfo.hero_get_started_button_text"
              :href="appInfo && appInfo.hero_get_started_button_link">
              {{ appInfo && appInfo.hero_get_started_button_text }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- HERO AREA END -->
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'HeroBanner',
  // Map Getters
  computed: {
    ...mapGetters('operations', ['appInfo']),
  },
}
</script>
