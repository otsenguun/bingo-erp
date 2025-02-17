<template>
  <!-- ABOUT-US AREA START -->
  <section class="section" id="about-us">
    <div class="container">
      <div class="about-inner">
        <div class="row align-items-center">
          <div class="col-md-5 about-left">
            <img src="/images/template/about.png" alt="" />
          </div>
          <div class="col-md-6 offset-md-1 about-right">
            <div class="about-content">
              <div class="section-heading">
                <p v-if="appInfo && appInfo.about_us_tagline">
                  {{ appInfo && appInfo.about_us_tagline }}
                </p>
              </div>
              <h3 v-if="appInfo && appInfo.about_us_title">{{ appInfo && appInfo.about_us_title }}</h3>
              <p v-if="appInfo && appInfo.about_us_description">{{ appInfo && appInfo.about_us_description }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- ABOUT-US AREA END -->

</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'About',
  // Map Getters
  computed: {
    ...mapGetters('operations', ['appInfo']),
  },
}
</script>
