<template>
  <aside class="control-sidebar control-sidebar-dark px-3 py-4">
    <!-- Control sidebar content goes here -->
    <h5 class="mb-4">{{ $t('Customize') }}</h5>

    <div class="mb-4">
      <div class="custom-control custom-checkbox">
        <input class="custom-control-input" type="checkbox" id="dark-modee" value="1" :checked="isDark ? true : false"
          @click="addBodyClass('isDark', 'dark-mode')" />
        <label for="dark-modee" class="custom-control-label">{{
            $t('Dark Mode')
        }}</label>
      </div>
    </div>
    <!-- Dark Mode -->

    <div>
      <h6>{{ $t('Header Options') }}</h6>
      <div class="custom-control custom-checkbox mb-1">
        <input class="custom-control-input" type="checkbox" id="layout-navbar-fixed" value="1"
          :checked="isNavFixed ? true : false" @click="addBodyClass('isNavFixed', 'layout-navbar-fixed')" />
        <label for="layout-navbar-fixed" class="custom-control-label">{{
            $t('Fixed')
        }}</label>
      </div>
      <div class="custom-control custom-checkbox mb-4">
        <input class="custom-control-input" type="checkbox" id="border-bottom-0" value="1"
          :checked="isBorderBtm ? true : false" @click="addBodyClass('isBorderBtm', 'border-bottom-0')" />
        <label for="border-bottom-0" class="custom-control-label">{{
            $t('No border')
        }}</label>
      </div>
    </div>
    <!-- Header-options -->

    <div>
      <h6>{{ $t('Sidebar Options') }}</h6>
      <div class="custom-control custom-checkbox mb-1">
        <input class="custom-control-input" type="checkbox" id="sidebar-dark" value="1"
          :checked="isSidebarDark ? true : false" @click="addSidebarClass('isSidebarDark', 'dark-sidebar')" />
        <label for="sidebar-dark" class="custom-control-label">{{
            $t('Sidebar Dark')
        }}</label>
      </div>
      <div class="custom-control custom-checkbox mb-1">
        <input class="custom-control-input" type="checkbox" id="collapsed" value="1"
          :checked="isSidebarCollasped ? true : false"
          @click="addBodyClass('isSidebarCollasped', 'sidebar-collapse')" />
        <label for="collapsed" class="custom-control-label">{{
            $t('Sidebar Collapsed')
        }}</label>
      </div>
      <div class="custom-control custom-checkbox mb-1">
        <input class="custom-control-input" type="checkbox" id="layout-fixed" value="1"
          :checked="isLayoutFixed ? true : false" @click="addBodyClass('isLayoutFixed', 'layout-fixed')" />
        <label for="layout-fixed" class="custom-control-label">{{
            $t('Sidebar Fixed')
        }}</label>
      </div>
      <div class="custom-control custom-checkbox mb-1">
        <input class="custom-control-input" type="checkbox" id="sidebar-mini" value="1"
          :checked="isSidebarMini ? true : false" @click="addBodyClass('isSidebarMini', 'sidebar-mini')" />
        <label for="sidebar-mini" class="custom-control-label">{{
            $t('Sidebar Mini')
        }}</label>
      </div>
      <div class="custom-control custom-checkbox mb-1">
        <input class="custom-control-input" type="checkbox" id="nav-flat" value="1" :checked="isNavFlat ? true : false"
          @click="addNavSidebarClass('isNavFlat', 'nav-flat')" />
        <label for="nav-flat" class="custom-control-label">{{
            $t('Nav Flat Style')
        }}</label>
      </div>
      <div class="custom-control custom-checkbox mb-1">
        <input class="custom-control-input" type="checkbox" id="nav-legacy" value="1"
          :checked="isNavLegacy ? true : false" @click="addNavSidebarClass('isNavLegacy', 'nav-legacy')" />
        <label for="nav-legacy" class="custom-control-label">{{
            $t('Nav Legacy Style')
        }}</label>
      </div>
      <div class="custom-control custom-checkbox mb-1">
        <input class="custom-control-input" type="checkbox" id="nav-child-indent" value="1"
          :checked="isNavChildIndent ? true : false"
          @click="addNavSidebarClass('isNavChildIndent', 'nav-child-indent')" />
        <label for="nav-child-indent" class="custom-control-label">{{
            $t('Nav Child Indent')
        }}</label>
      </div>
      <div class="custom-control custom-checkbox mb-4">
        <input class="custom-control-input" type="checkbox" id="disableHoverExpand" value="1"
          :checked="isDisableHoverExpand ? true : false"
          @click="addSidebarClass('isDisableHoverExpand', 'sidebar-no-expand')" />
        <label for="disableHoverExpand" class="custom-control-label">{{
            $t('Disable Hover/Focus Auto-Expand')
        }}</label>
      </div>
    </div>
    <!-- Header-options -->
  </aside>
</template>

<script>
export default {
  name: 'sidebar-controll',
  data: () => ({
    isDark: false,
    isBorderBtm: false,
    isNavFixed: false,
    isSidebarCollasped: false,
    isLayoutFixed: false,
    isSidebarMini: false,
    isNavFlat: false,
    isSidebarDark: false,
    isNavChildIndent: false,
    isNavLegacy: false,
    isDisableHoverExpand: false,
  }),

  mounted() {
    this.getLocalStorageData()
  },

  methods: {
    // ADD OR REMOVE VALUE TO LOCALSTORAGE AND SET OR REMOVE IN BODY CLASS
    addBodyClass(varVal, className) {
      this[varVal] = !this[varVal]
      if (this[varVal]) {
        localStorage[varVal] = true
        document.body.classList.add(className)
      } else {
        localStorage.removeItem(varVal)
        document.body.classList.remove(className)
      }
    },

    // ADD OR REMOVE CLASS TO MAIN-SIDEBAR
    addSidebarClass(varVal, className) {
      this[varVal] = !this[varVal]

      if (this[varVal]) {
        localStorage[varVal] = true
        var data = document.querySelector('.main-sidebar')
        data.classList.add(className)
      } else {
        localStorage.removeItem(varVal)
        data = document.querySelector('.main-sidebar')
        data.classList.remove(className)
      }
    },

    // ADD CLASS TO SIDEBAR NAV
    addNavSidebarClass(varVal, className) {
      this[varVal] = !this[varVal]

      if (this[varVal]) {
        localStorage[varVal] = true
        var data = document.querySelector('.nav-sidebar')
        data.classList.add(className)
      } else {
        localStorage.removeItem(varVal)
        data = document.querySelector('.nav-sidebar')
        data.classList.remove(className)
      }
    },

    // Dark mode check from localstorage
    getLocalStorageData() {
      // Check if isDark true in localstorage
      if (localStorage.isDark) {
        document.body.classList.add('dark-mode')
        this.isDark = true
      }

      // Check if isDark true in localstorage
      if (localStorage.isBorderBtm) {
        document.body.classList.add('border-bottom-0')
        this.isBorderBtm = true
      }

      // Check if headerFixed true in localstorage
      if (localStorage.isNavFixed) {
        document.body.classList.add('layout-navbar-fixed')
        this.isNavFixed = true
      }

      // Check if headerFixed true in localstorage
      if (localStorage.isSidebarCollasped) {
        document.body.classList.add('sidebar-collapse')
        this.isSidebarCollasped = true
      }

      // Check if headerFixed true in localstorage
      if (localStorage.isLayoutFixed) {
        document.body.classList.add('layout-fixed')
        this.isLayoutFixed = true
      }

      // Check if headerFixed true in localstorage
      if (localStorage.isSidebarMini) {
        document.body.classList.add('sidebar-mini')
        this.isSidebarMini = true
      }

      // Check if headerFixed true in localstorage
      if (localStorage.isNavFlat) {
        this.isNavFlat = true
        var navSidebar = document.querySelector('.nav-sidebar')
        navSidebar.classList.add('nav-flat')
      }

      // Check if headerFixed true in localstorage
      if (localStorage.isSidebarDark) {
        this.isSidebarDark = true
        var sideBarDark = document.querySelector('.main-sidebar')
        sideBarDark.classList.add('dark-sidebar')
      }

      // Check if headerFixed true in localstorage
      if (localStorage.isNavChildIndent) {
        this.isNavChildIndent = true
        var navChildIndent = document.querySelector('.nav-sidebar')
        navChildIndent.classList.add('nav-child-indent')
      }

      // Check if headerFixed true in localstorage
      if (localStorage.isNavLegacy) {
        this.isNavLegacy = true
        var navLegacy = document.querySelector('.nav-sidebar')
        navLegacy.classList.add('nav-legacy')
      }

      // Check if headerFixed true in localstorage
      if (localStorage.addSidebarClass) {
        this.addSidebarClass = true
        var sidebarClass = document.querySelector('.main-sidebar')
        sidebarClass.classList.add('sidebar-no-expand')
      }
    },
  },
}
</script>
