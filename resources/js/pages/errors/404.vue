<template>
  <div class="container-fluid">
    <div class="row no-gutter">
      <!-- The image half -->
      <div class="col-md-6 d-none d-md-flex bg-image"></div>
      <!-- The content half -->
      <div class="col-md-6 bg-light">
        <div class="auth-wrapper d-flex align-items-center py-5">
          <!-- Demo content-->
          <div class="container">
            <div class="row">
              <div class="col-lg-10 col-xl-7 mx-auto text-center">
                <h2 class="display-3">😞</h2>
                <h3 class="display-4">{{ $t('404') }}</h3>
                <h4 class="display-5">
                  {{ $t('Page Not Found') }}
                </h4>
                <p class="text-muted mb-4">
                  {{ $t('Sorry, the requested URL was not found on this server. That is all we know!') }}
                </p>
                <router-link v-if="user" :to="{ name: 'home' }" class="ml-auto my-auto btn btn-primary">{{
                  $t('Back to Dashboard')
                }}</router-link>
                <router-link v-else :to="{ name: 'login' }" class="ml-auto my-auto btn btn-primary">{{
                  $t('Back to login')
                }}</router-link>
              </div>
            </div>
          </div>
          <!-- End -->
        </div>
      </div>
      <!-- End -->
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
export default {
  layout: 'basic',

  computed: mapGetters({
    user: 'auth/user',
  }),
}
</script>
