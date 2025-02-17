<template>
  <div>
    <div class="wrapper">
      <navbar />
      <sidebar />
      <!-- Main content -->
      <section class="content-wrapper">
        <div v-if="demo_message" class="demo-message alert alert-danger rounded-0">
          {{ demo_message }}
        </div>
        <div class="container-fluid page-padding">
          <child />
        </div>
        <!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <footer class="main-footer">
        <strong v-if="appInfo">{{ appInfo.copyright }}</strong>
        <!-- Developed by
        <a href="https://codeshaper.net/" target="__blank">Codeshaper</a> -->
        <div v-if="appInfo" class="float-right d-none d-sm-inline-block"><b>Version</b> {{ appInfo.version }}</div>
      </footer>

      <!-- Control Sidebar -->
      <sidebar-control />
      <!-- /.control-sidebar -->
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Navbar from '~/components/central/Navbar'
import Sidebar from '~/components/central/Sidebar'
import SidebarControl from '~/components/central/SidebarControl'

export default {
  name: 'CentralLayout',
  data: () => ({
    year: new Date().getFullYear(),
  }),
  components: {
    Sidebar,
    Navbar,
    SidebarControl
  },
  // Map Getters
  computed: {
    ...mapGetters('operations', ['appInfo', 'demo_message']),
  },
  methods: {},
}
</script>

<style lang="scss">
.settings-card {
  .nav-item {
    width: 100%;
    border-bottom: none !important;

    svg {
      width: 15px;
      height: 15px;
      display: inline-block;
      margin-right: 8px;
    }
  }
}
</style>
