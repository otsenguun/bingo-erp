<template>
  <!-- WHY-US AREA START -->
  <section class="section" id="why-us">
    <div class="container">
      <div class="about-inner">
        <div class="row align-items-center">
          <div class="col-md-5">
            <div class="about-content">
              <div class="section-heading">
                <p v-if="appInfo && appInfo.why_us_tagline">{{ appInfo && appInfo.why_us_tagline }}</p>
              </div>
              <h3 v-if="appInfo && appInfo.why_us_title">
                {{ appInfo && appInfo.why_us_title }}
              </h3>
              <p v-if="appInfo && appInfo.why_us_description">
                {{ appInfo && appInfo.why_us_description }}
              </p>
            </div>
          </div>
          <div class="col-md-7">
            <div class="why-content">
              <div v-for="(why_us_card, i) in appInfo && appInfo.why_us_cards" :key="i" class="single-ws">
                <img :src="why_us_card.image" alt="why us card image" />
                <h4>{{ why_us_card.title }}</h4>
                <p>{{ why_us_card.description }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- WHY-US AREA END -->

</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'WhyUs',
  // Map Getters
  computed: {
    ...mapGetters('operations', ['appInfo']),
  },
}
</script>
