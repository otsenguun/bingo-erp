<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card custom-card w-100">
          <div class="card-header setings-header">
            <div class="col-xl-4 col-4">
              <h3 class="card-title">
                {{ $t("Domain Management") }}
              </h3>
            </div>
            <div class="col-xl-8 col-8 float-right text-right">
              <div class="btn-group c-w-100">
                <a @click="print" v-tooltip="$t('Print Table')" class="btn btn-info">
                  <i class="fas fa-print"></i>
                </a>
              </div>
            </div>
          </div>
          <div class="card-body position-relative">
            <table-loading v-show="loading" />
            <div class="table-responsive table-custom mt-3" id="printMe">
              <table class="table">
                <thead>
                  <tr>
                    <th>{{ $t('#') }}</th>
                    <th>{{ $t('Domain') }}</th>
                    <th>{{ $t('Type') }}</th>
                    <th>{{ $t('Fallback') }}</th>
                    <th>{{ $t('Primary') }}</th>
                    <th class="text-right no-print">
                      {{ $t('Action') }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-show="items.length" v-for="(data, i) in items" :key="i">
                    <td>
                      <span v-if="pagination && pagination.current_page > 1">
                        {{
                          pagination.per_page * (pagination.current_page - 1) +
                          (i + 1)
                        }}
                      </span>
                      <span v-else>{{ i + 1 }}</span>
                    </td>
                    <td>{{ data.domain }}</td>
                    <td>
                      <span v-if="data.is_domain"> Domain </span>
                      <span v-else> Sub Domain </span>
                    </td>
                    <td>
                      <span class="badge badge-success" v-if="data.is_fallback">
                        True
                      </span>
                      <span class="badge badge-danger" v-else> False </span>
                    </td>
                    <td>
                      <span class="badge badge-success" v-if="data.is_primary">
                        True
                      </span>
                      <span class="badge badge-danger" v-else> False </span>
                    </td>
                    <td class="text-right no-print">
                      <div v-if="data.id" class="btn-group">
                        <span v-if="data.is_fallback && data.is_primary">
                          {{ $t('No action available') }}
                        </span>
                        <a v-if="!data.is_primary" v-tooltip="$t('Make Primary')" href="#"
                          class="btn btn-success btn-sm" @click="makePrimary(data.id)">
                          <i class="fas fa-check-circle"></i>
                        </a>
                        <a v-if="!data.is_fallback && !data.is_primary" v-tooltip="$t('Delete')" href="#"
                          class="btn btn-danger btn-sm" @click="deleteData(data.id)">
                          <i class="fas fa-trash" />
                        </a>
                      </div>
                    </td>
                  </tr>
                  <tr v-show="!loading && !items.length">
                    <td colspan="12">
                      <EmptyTable />
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('Domain Management') }
  },
  data: () => ({
    breadcrumbsCurrent: 'Domain Management',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Domain Management',
        url: '',
      },
    ],
    query: '',
    perPage: 10,

  }),
  // Map Getters
  computed: {
    ...mapGetters('operations', ['items', 'loading', 'pagination', 'appInfo', 'tenant']),
  },
  created() {
    this.getData()
    this.employeePrefix = this.appInfo.employeePrefix
  },
  methods: {
    // update per page count
    updatePerPager() {
      this.pagination.current_page = 1
      this.query === '' ? this.getData() : this.searchData()
    },
    async paginate() {
      this.query === '' ? this.getData() : this.searchData()
    },
    // get data
    async getData() {
      this.$store.state.operations.loading = true
      let currentPage = this.pagination ? this.pagination.current_page : 1
      await this.$store.dispatch('operations/fetchData', {
        path: '/api/domains?page=',
        currentPage: currentPage + '&perPage=' + this.perPage,
      })
    },

    // print table
    async print() {
      await this.$htmlToPaper('printMe')
    },

    // delete data
    async deleteData(slug) {
      Swal.fire({
        title: this.$t('Are you sure?'),
        text: this.$t('Are you sure you want to delete this domain?'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: this.$t('Confirm'),
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          this.$store
            .dispatch('operations/deleteData', {
              path: '/api/domains/',
              slug: slug,
            })
            .then((response) => {
              if (response === true) {
                Swal.fire(
                  this.$t('Deleted!'),
                  this.$t('Deleted successfully.'),
                  'success'
                )
                this.getData()
              } else {
                Swal.fire(
                  this.$t('Failed!'),
                  this.$t('Delete failed'),
                  'warning'
                )
              }
            })
        }
      })
    },

    // delete data
    async makePrimary(id) {
      await this.$axios
        .post('/api/domains/' + id)
        .then(() => {
          toast.fire({
            type: 'success',
            title: this.$t('Successfully made primary'),
          })
          this.getData()
        })
        .catch(() => {
          toast.fire({
            type: 'error',
            title: this.$t('Error!'),
          })
        })
    },
  },
}
</script>
