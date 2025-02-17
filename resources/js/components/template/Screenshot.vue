<template>
  <!-- SCREENSHOT AREA START -->
  <section class="section" id="ss-area">
    <div class="sec-heading">
      <span v-if="appInfo && appInfo.software_overview_section_tagline">{{
          appInfo && appInfo.software_overview_section_tagline
      }}</span>
      <h2 v-if="appInfo && appInfo.software_overview_section_title">
        {{ appInfo && appInfo.software_overview_section_title }}</h2>
    </div>
    <div class="container">
      <div class="row">
        <carousel-3d v-if="appInfo" :controls-visible="true" :height="320">
          <slide v-for="(software_overview_image, i) in appInfo.software_overview_images" :index="i" :key="i">
            <template slot-scope="{ index, isCurrent, leftIndex, rightIndex }">
              <img :data-index="index"
                :class="{ current: isCurrent, onLeft: (leftIndex >= 0), onRight: (rightIndex >= 0) }"
                :src="software_overview_image.image">
            </template>
          </slide>
        </carousel-3d>
      </div>
    </div>
  </section>
  <!-- SCREENSHOT AREA END -->
</template>


<script>
import { mapGetters } from 'vuex'
import { Carousel3d, Slide } from 'vue-carousel-3d'

export default {
  name: 'Screenshot',
  components: {
    Carousel3d,
    Slide
  },

  computed: {
    ...mapGetters('operations', ['appInfo']),
  },
}
</script>

