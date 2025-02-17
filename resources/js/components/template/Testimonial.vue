<template>
  <!-- TESTIMONIAL AREA START -->
  <section class="section" id="testimonial-area">
    <div class="sec-heading">
      <span v-if="appInfo && appInfo.testimonial_section_tagline">
        {{ appInfo && appInfo.testimonial_section_tagline }}
      </span>
      <h2 v-if="appInfo && appInfo.testimonial_section_title">
        {{ appInfo && appInfo.testimonial_section_title }}
      </h2>
    </div>
    <div class="container testimonial-wrap">
      <div class="row">
        <carousel-3d v-if="appInfo" :disable3d="true" :space="365" :clickable="false" :controls-visible="true">
          <slide v-for="(testimonial, i) in appInfo && appInfo.testimonials" :index="i" :key="i"
            class="testimonial-single">
            <div>
              <h4 class="title">{{ testimonial.name }}</h4>
              <h5 class="title">{{ testimonial.title }}</h5>
              <div class="d-flex">
                <i class="fa fa-quote-left text-gray"></i>
                <p>{{ testimonial.description }}</p>
              </div>
            </div>
            <img :data-index="i" :src="testimonial.image" />
          </slide>
        </carousel-3d>
      </div>
    </div>
    <div v-if="appInfo.is_show_brand_section" class="container client-logo">
      <div class="row">
        <carousel-3d v-if="appInfo" :disable3d="true" :space="200" :height="50" :display="7" :clickable="false"
          :controls-visible="true">
          <slide v-for="(brand, i) in appInfo && appInfo.brands" :index="i" :key="i" class="client-logo-single">
            <img :data-index="i" :src="brand.image" />
          </slide>
        </carousel-3d>
      </div>
    </div>
  </section>
  <!-- TESTIMONIAL AREA END -->
</template>


<script>
import { mapGetters } from 'vuex'
import { Carousel3d, Slide } from 'vue-carousel-3d'

export default {

  components: {
    Carousel3d,
    Slide
  },

  computed: {
    ...mapGetters('operations', ['appInfo']),
  },
}
</script>


<style>
.testimonial-wrap .carousel-3d-controls {
  top: 77% !important;
  margin-top: 0 !important;
  left: 45% !important;
}

.testimonial-wrap a.prev,
.testimonial-wrap a.next {
  position: initial !important;
}

.client-logo a.prev span,
.client-logo a.next span,
.testimonial-wrap a.prev span,
.testimonial-wrap a.next span {
  font-family: none;
  background: #3865f22e;
  width: 40px;
  height: 40px;
  display: inline-block;
  justify-content: center;
  font-size: 30px;
  line-height: 35px;
  text-align: center;
  border-radius: 100%;
  position: relative;
  bottom: 42%;
  color: #3865f2;
  border: 1px solid #3865f2;
}

.testimonial-wrap a.prev {
  margin-right: 15px !important;
}

.client-logo .carousel-3d-slide {
  width: 170px !important;
}

.client-logo .prev {
  left: 0px !important;
  margin-top: 5px;
}

.client-logo .next {
  right: 0px !important;
  margin-top: 5px;
}

.client-logo a.prev span,
.client-logo a.next span {
  background: #3865f2;
  color: #ffffff;
  border: 1px solid #3865f2;
}
</style>
