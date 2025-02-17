<template>
  <div class="landing">
    <template-nav />
    <div v-if="appInfo">
      <hero-banner v-if="appInfo.is_show_hero_section" />
      <about v-if="appInfo.is_show_about_us_section"/>
      <why-us v-if="appInfo.is_show_why_us_section"/>
      <business-start v-if="appInfo.is_show_business_start_section"/>
      <features v-if="appInfo.is_show_features_section"/>
      <business-need v-if="appInfo.is_show_business_need_section"/>
      <extra-features v-if="appInfo.is_show_extra_features_section"/>
      <cta v-if="appInfo.is_show_cta_section"/>
      <screenshot v-if="appInfo.is_show_screenshot_section"/>
      <pricing v-if="appInfo.is_show_pricing_section"/>
      <testimonial v-if="appInfo.is_show_testimonial_section"/>
      <newsletter v-if="appInfo.is_show_newsletter_section"/>
    </div>
    <template-footer />
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import TemplateNav from '~/components/template/Navbar'
import HeroBanner from '~/components/template/HeroBanner'
import About from '~/components/template/About'
import WhyUs from '~/components/template/WhyUs'
import BusinessStart from '~/components/template/BusinessStart'
import Features from '~/components/template/Features'
import BusinessNeed from '~/components/template/BusinessNeed'
import ExtraFeatures from '~/components/template/ExtraFeatures'
import Cta from '~/components/template/Cta'
import Screenshot from '~/components/template/Screenshot'
import Pricing from '~/components/template/Pricing'
import Testimonial from '~/components/template/Testimonial'
import Newsletter from '~/components/template/Newsletter'
import TemplateFooter from '~/components/template/Footer'

export default {
  layout: "template",
  metaInfo() {
    return { title: 'Ultimate Sales, Inventory, Accounting Management SaaS Application.' }
  },
  computed: mapGetters({
    appInfo: 'operations/appInfo',
  }),
  components: {
    TemplateNav,
    HeroBanner,
    About,
    WhyUs,
    BusinessStart,
    Features,
    BusinessNeed,
    ExtraFeatures,
    Cta,
    Screenshot,
    Pricing,
    Testimonial,
    Newsletter,
    TemplateFooter
  },


};
</script>
<style scoped>
@import "../../../../sass/template.scss";
</style>
