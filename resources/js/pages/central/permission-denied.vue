<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent"/>
    <!-- breadcrumbs end -->
    <div class="col-lg-12 col-xl-8 offset-xl-2 h-100">
      <div class="row align-items-center h-100">
        <div class="text-center w-100 mt-5 box-wrapper">
          <h1>
            <code>{{ $t('Permission Denied') }}</code>
          </h1>
          <hr/>
          <h3>{{ $t('You do not have permission to view this page.') }}</h3>
          <h3>🚫🚫🚫🚫</h3>
          <h6>{{ $t('error code:403 forbidden.') }}</h6>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  layout: 'central',
  middleware: 'auth',
  metaInfo() {
    return { title: this.$t('Permission Denied') }
  },

  data: () => ({
    breadcrumbsCurrent: 'Permission Denied',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Permission Denied',
        url: '',
      },
    ],
  }),
}
</script>

<style scoped>
h1 {
  color: red;
  font-size: 5em;
}

h6 {
  color: red;
  text-decoration: underline;
}

hr {
  margin: auto;
  width: 50%;
}
</style>
