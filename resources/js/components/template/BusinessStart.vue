<template>

  <!-- BUSINESS-START AREA START -->
  <section class="section" id="business-start">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6 bs-right">
          <img src="/images/template/start.png" alt="image" />
        </div>
        <div class="col-md-6 bs-left">
          <div class="section-heading">
            <p v-if="appInfo && appInfo.business_start_section_tagline">
              {{ appInfo && appInfo.business_start_section_tagline }}</p>
          </div>
          <h3 v-if="appInfo && appInfo.business_start_section_title">{{
              appInfo && appInfo.business_start_section_title
          }}</h3>
          <p v-if="appInfo && appInfo.business_start_section_description">
            {{ appInfo && appInfo.business_start_section_description }}</p>
          <ul class="support-list">
            <li v-for="(business_start_support_item, i) in parseJson(appInfo && appInfo.business_start_support_list)"
              :key="i">
              <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="13" cy="13" r="13" fill="#3865F2" />
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M19.7069 8.29303C19.8944 8.48056 19.9997 8.73487 19.9997 9.00003C19.9997 9.26519 19.8944 9.5195 19.7069 9.70703L11.7069 17.707C11.5194 17.8945 11.2651 17.9998 10.9999 17.9998C10.7348 17.9998 10.4804 17.8945 10.2929 17.707L6.29292 13.707C6.11076 13.5184 6.00997 13.2658 6.01224 13.0036C6.01452 12.7414 6.11969 12.4906 6.3051 12.3052C6.49051 12.1198 6.74132 12.0146 7.00352 12.0124C7.26571 12.0101 7.51832 12.1109 7.70692 12.293L10.9999 15.586L18.2929 8.29303C18.4804 8.10556 18.7348 8.00024 18.9999 8.00024C19.2651 8.00024 19.5194 8.10556 19.7069 8.29303Z"
                  fill="white" />
              </svg>
              <span>{{ business_start_support_item }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>
  <!-- BUSINESS-START AREA END -->
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'BusinessStart',
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
