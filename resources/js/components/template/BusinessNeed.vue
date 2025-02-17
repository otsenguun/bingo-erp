<template>
  <!-- BUSINESS NEED AREA START -->
  <section class="section" id="business-need">
    <div class="container">
      <div v-for="(explore, i) in appInfo && appInfo.explorers" :key="i" class="about-inner"
        :class="{ 'about-btm': i !== appInfo.explorers.length - 1 }">
        <div v-if="explore.image_align_left == 1" class="row align-items-center">
          <div class="col-md-5">
            <img :src="explore.image" alt="" />
          </div>
          <div class="col-md-6 offset-md-1">
            <div class="about-content">
              <h3>{{ explore.title }}</h3>
              <ul>
                <li v-for="(point, i) in parseJson(explore.points)" :key="i">
                  <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="18" height="18" fill="#3865F2" />
                    <path d="M4.5 10.0798L7.07143 12.5998L13.5 6.2998" stroke="white" stroke-linecap="round"
                      stroke-linejoin="round" />
                  </svg>
                  <span>{{ point }}</span>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div v-else class="row align-items-center">
          <div class="col-md-6">
            <div class="about-content">
              <h3>{{ explore.title }}</h3>
              <ul>
                <li v-for="(point, i) in parseJson(explore.points)" :key="i">
                  <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="18" height="18" fill="#3865F2" />
                    <path d="M4.5 10.0798L7.07143 12.5998L13.5 6.2998" stroke="white" stroke-linecap="round"
                      stroke-linejoin="round" />
                  </svg>
                  <span>{{ point }}</span>
                </li>
              </ul>
            </div>
          </div>
          <div class="col-md-5 offset-md-1">
            <img :src="explore.image" alt="" />
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
  name: 'BusinessNeed',
  methods: {
    parseJson(data) {
      return JSON.parse(data)
    }
  },
  // Map Getters
  computed: {
    ...mapGetters('operations', ['appInfo']),
  },
}
</script>
