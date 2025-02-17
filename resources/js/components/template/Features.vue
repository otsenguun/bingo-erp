<template>
  <!-- FEATURES AREA START -->
  <section class="section" id="features">
    <div class="container">
      <div class="about-inner">
        <div class="row align-items-center">
          <div class="col-md-5">
            <div class="about-content">
              <div class="section-heading">
                <p v-if="appInfo && appInfo.features_section_tagline">
                  {{ appInfo && appInfo.features_section_tagline }}
                </p>
              </div>
              <h3 v-if="appInfo && appInfo.features_section_title">
                {{ appInfo && appInfo.features_section_title }}
              </h3>
              <p v-if="appInfo && appInfo.features_section_description">
                {{ appInfo && appInfo.features_section_description }}.</p>
            </div>
          </div>
          <div class="col-md-7">
            <div class="main-service">
              <div v-for="(feature, i) in appInfo && appInfo.features" :key="i" class="single-ms">
                <img :src="feature.image" alt="ws1 image" />
                <h4>{{ feature.title }}</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- FEATURES AREA END -->
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'Features',
  // Map Getters
  computed: {
    ...mapGetters('operations', ['appInfo']),
  },
}
</script>
