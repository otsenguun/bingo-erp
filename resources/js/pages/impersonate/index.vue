<template>
  <div class="buildingSite">
    <h2>{{ $t('We are building your site') }}</h2>
    <p>{{ $t('Please wait while our 🤖 robots build your website.<br>It shouldn't take more than a minute.') }}</p>
    <button type="button" class="btn btn-primary" :class="{ 'btn-loading': isActive }" @click="tenantRegister()">
      <span class="text-uppercase">{{ $t('retry') }}</span>
    </button>
  </div>
</template>


<script>
import axios from 'axios'

export default {
  layout: 'basic',
  middleware: 'guest',

  metaInfo() {
    return { title: this.$t('register') }
  },

  data: () => ({
    isActive: false
  }),

  methods: {
    async tenantRegister() {
      this.isActive = true;
      // Register the user.
      axios.get(window.location.origin + '/api/impersonate/' + this.$route.params.token)
        .then(({ data }) => {
          this.$store.dispatch('auth/saveToken', {
            token: data.token,
            remember: true,
          })
          this.$router.push({ name: 'setup.general' }).catch(() => { })
        })
        .catch(() => this.$router.push({ name: 'login' }).catch(() => { }))
    }
  },

  created() {
    this.tenantRegister();

    setTimeout(() => {
      this.tenantRegister();
    }, 10000);
  }
}
</script>

<style scoped>
.buildingSite {
  width: 100%;
  height: 100vh;
  display: flex;
  justify-content: center;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.buildingSite h2 {
  font-size: 40px;
  margin-bottom: 5px;
}

.buildingSite p {
  font-size: 18px;
  line-height: 32px;
}

.btn-loading {
  position: relative;
  pointer-events: none;
  color: transparent !important;
}

.btn-loading:after {
  animation: spinAround 500ms infinite linear;
  border: 2px solid #dbdbdb;
  border-radius: 50%;
  border-right-color: transparent;
  border-top-color: transparent;
  content: "";
  display: block;
  height: 1em;
  width: 1em;
  position: absolute;
  left: calc(50% - (1em / 2));
  top: calc(50% - (1em / 2));
}

@keyframes spinAround {
  from {
    transform: rotate(0deg);
  }

  to {
    transform: rotate(359deg);
  }
}
</style>
