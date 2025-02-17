<template>
  <div v-if="appInfo" class="company-logo">
    <img v-if="appInfo" :src="appInfo.blackLogo" :alt="appInfo.companyName" class="lg-logo" />
    <address class="mt-2">
      <strong v-html="appInfo.companyTagline" /><br />
      <strong v-if="appInfo.phone">{{ $t('Phone') }}: </strong>
      {{ appInfo.phone }}<br />
      <strong v-if="appInfo.email">{{ $t('Email') }}: </strong>
      {{ appInfo.email }}<br />
      <strong v-if="appInfo.address">{{ $t('Address') }}: </strong>
      {{ appInfo.address }}
    </address>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
export default {
  name: 'CompanyInfo',
  // Map Getters
  computed: {
    ...mapGetters('operations', ['appInfo']),
  },
}
</script>
