<template>
  <!-- EXTRA-FEATURES START -->
  <section class="section" id="core-modules">
    <div class="container">
      <div class="sec-heading">
        <span v-if="appInfo && appInfo.all_features_section_tagline">
          {{ appInfo && appInfo.all_features_section_tagline }}
        </span>
        <h2 v-if="appInfo && appInfo.all_features_section_title">
          {{ appInfo && appInfo.all_features_section_title }}
        </h2>
      </div>
      <div class="row">
        <div v-for="(allFeature, i) in appInfo && appInfo.all_features" :key="i" class="col-md-3 col-sm-6 col-12">
          <div class="single-extra-feature d-flex flex-column align-items-center justify-content-center text-center">
            <img :src="allFeature.image" alt="img" />
            <h4>{{ allFeature.title }}</h4>
            <p>{{ allFeature.description }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- EXTRA-FEATURES END -->
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'ExtraFeatures',
  // Map Getters
  computed: {
    ...mapGetters('operations', ['appInfo']),
  },
}
</script>
