<template>
  <footer id="footer">
    <div class="container">
      <div class="row">

        <div class="col-md-5">
          <div class="footer-single" v-if="appInfo">
            <router-link :to="{ name: 'welcome' }">
              <img :src="appInfo.blackLogo" alt="logo" />
              <p>
                {{ appInfo.companyTagline }}
              </p>
            </router-link>
            <div class="footer-social">
              <a v-if="appInfo && appInfo.facebook_link" :href="appInfo && appInfo.facebook_link" target="_blank">
                <svg width="13" height="20" viewBox="0 0 13 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M0.583496 7.83333V12.1667H3.8335V19.75H8.16683V12.1667H11.4168L12.5002 7.83333H8.16683V5.66667C8.16683 5.37935 8.28097 5.1038 8.48413 4.90063C8.68729 4.69747 8.96284 4.58333 9.25016 4.58333H12.5002V0.25H9.25016C7.81357 0.25 6.43582 0.820683 5.42 1.8365C4.40418 2.85233 3.8335 4.23008 3.8335 5.66667V7.83333H0.583496Z"
                    fill="white" />
                </svg>
              </a>
              <a v-if="appInfo && appInfo.instagram_link" :href="appInfo && appInfo.instagram_link" target="_blank">
                <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M17.3335 4.33398H8.66683C6.2736 4.33398 4.3335 6.27408 4.3335 8.66732V17.334C4.3335 19.7272 6.2736 21.6673 8.66683 21.6673H17.3335C19.7267 21.6673 21.6668 19.7272 21.6668 17.334V8.66732C21.6668 6.27408 19.7267 4.33398 17.3335 4.33398Z"
                    stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  <path
                    d="M13 16.25C14.7949 16.25 16.25 14.7949 16.25 13C16.25 11.2051 14.7949 9.75 13 9.75C11.2051 9.75 9.75 11.2051 9.75 13C9.75 14.7949 11.2051 16.25 13 16.25Z"
                    stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  <path d="M17.875 8.125V8.12613" stroke="white" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
                </svg>
              </a>
              <a v-if="appInfo && appInfo.twitter_link" :href="appInfo && appInfo.twitter_link" target="_blank">
                <svg width="22" height="18" viewBox="0 0 22 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M21.8332 0.344425C20.7498 0.875259 19.6882 1.09084 18.5832 1.41693C17.3688 0.0465086 15.5683 -0.0293247 13.8382 0.618509C12.1081 1.26634 10.9749 2.85018 10.9998 4.66692V5.75026C7.48442 5.84017 4.35359 4.23901 2.33317 1.41693C2.33317 1.41693 -2.19733 9.46934 6.6665 13.3336C4.6385 14.6845 2.61592 15.5956 0.166504 15.5003C3.75017 17.4535 7.65559 18.1252 11.0367 17.1437C14.915 16.017 18.1022 13.1104 19.3253 8.75651C19.6901 7.43232 19.8713 6.06427 19.8637 4.69076C19.8615 4.42101 21.4995 1.68776 21.8332 0.343342V0.344425Z"
                    fill="white" />
                </svg>
              </a>
              <a v-if="appInfo && appInfo.linkedin_link" :href="appInfo && appInfo.linkedin_link" target="_blank">
                <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M19.5002 4.33398H6.50016C5.30355 4.33398 4.3335 5.30403 4.3335 6.50065V19.5007C4.3335 20.6973 5.30355 21.6673 6.50016 21.6673H19.5002C20.6968 21.6673 21.6668 20.6973 21.6668 19.5007V6.50065C21.6668 5.30403 20.6968 4.33398 19.5002 4.33398Z"
                    stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  <path d="M8.6665 11.916V17.3327" stroke="white" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
                  <path d="M8.6665 8.66602V8.67685" stroke="white" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
                  <path d="M13 17.3327V11.916" stroke="white" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
                  <path
                    d="M17.3333 17.3327V14.0827C17.3333 13.508 17.1051 12.9569 16.6987 12.5506C16.2924 12.1443 15.7413 11.916 15.1667 11.916C14.592 11.916 14.0409 12.1443 13.6346 12.5506C13.2283 12.9569 13 13.508 13 14.0827"
                    stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </a>
            </div>
          </div>
        </div>
        <!--- FOOTER SINGLE -->

        <div class="col-md-7">
          <div class="row">
            <div class="col-md-4 col-6">
              <div class="footer-single">
                <h4>Information</h4>
                <ul>
                  <li v-for="(information_plan, i) in appInfo && appInfo.information_plans" :key="i">
                    <router-link :to="{ name: 'pages.show', params: { slug: information_plan.slug } }">
                      {{ information_plan.name }}
                    </router-link>
                  </li>
                </ul>
              </div>
            </div>
            <!--- FOOTER SINGLE -->
            <div class="col-md-4 col-6">
              <div class="footer-single">
                <h4>Need Help?</h4>
                <ul>
                  <li v-for="(need_help_plan, i) in appInfo && appInfo.need_help_plans" :key="i">
                    <router-link :to="{ name: 'pages.show', params: { slug: need_help_plan.slug } }">
                      {{ need_help_plan.name }}
                    </router-link>
                  </li>
                </ul>

              </div>
            </div>
            <!--- FOOTER SINGLE -->
            <div class="col-md-4 col-12">
              <div class="footer-single">
                <h4>Contact Us</h4>
                <ul>
                  <li class="d-flex">
                    <a href="#!">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.7"
                          d="M12 13C13.6569 13 15 11.6569 15 10C15 8.34315 13.6569 7 12 7C10.3431 7 9 8.34315 9 10C9 11.6569 10.3431 13 12 13Z"
                          stroke="#1B1B1C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path opacity="0.7"
                          d="M17.6572 15.6567L13.4142 19.8997C13.0392 20.2743 12.5308 20.4848 12.0007 20.4848C11.4707 20.4848 10.9623 20.2743 10.5872 19.8997L6.34325 15.6567C5.22447 14.5379 4.46258 13.1124 4.15394 11.5606C3.84529 10.0087 4.00374 8.40022 4.60925 6.93844C5.21476 5.47665 6.24014 4.22725 7.55573 3.34821C8.87131 2.46918 10.418 2 12.0002 2C13.5825 2 15.1292 2.46918 16.4448 3.34821C17.7604 4.22725 18.7857 5.47665 19.3912 6.93844C19.9968 8.40022 20.1552 10.0087 19.8466 11.5606C19.5379 13.1124 18.776 14.5379 17.6572 15.6567V15.6567Z"
                          stroke="#1B1B1C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                    </a>
                    <span>{{ appInfo && appInfo.address }}</span>
                  </li>
                  <li>
                    <a v-if="appInfo && appInfo.email" :href="`mailto:${appInfo && appInfo.email}`">
                      <img src="/images/template/envelope.png" alt="envelope">
                      <span>{{ appInfo && appInfo.email }}</span>
                    </a>
                  </li>
                  <li>
                    <a v-if="appInfo && appInfo.phone" :href="`tel:${appInfo && appInfo.phone}`">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g opacity="0.7">
                          <path
                            d="M5 4H9L11 9L8.5 10.5C9.57096 12.6715 11.3285 14.429 13.5 15.5L15 13L20 15V19C20 19.5304 19.7893 20.0391 19.4142 20.4142C19.0391 20.7893 18.5304 21 18 21C14.0993 20.763 10.4202 19.1065 7.65683 16.3432C4.8935 13.5798 3.23705 9.90074 3 6C3 5.46957 3.21071 4.96086 3.58579 4.58579C3.96086 4.21071 4.46957 4 5 4"
                            stroke="#1B1B1C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </g>
                      </svg>
                      <span>{{ appInfo && appInfo.phone }}</span>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
            <!--- FOOTER SINGLE -->
          </div>
        </div>
      </div>
    </div>
    <div class="footer-btm">
      <p v-if="appInfo && appInfo.copyright">{{ appInfo && appInfo.copyright }}</p>
    </div>
  </footer>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'Footer',
  // Map Getters
  computed: {
    ...mapGetters('operations', ['appInfo']),
  },
}
</script>
