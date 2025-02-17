<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="card custom-card w-100">
        <div class="card-header setings-header">
          <div class="col-xl-4 col-4">
            <h3 class="card-title">
              {{ $t("All Domains") }}
            </h3>
          </div>
          <div class="col-xl-8 col-8 float-right text-right">
            <div class="btn-group c-w-100">
              <a @click="refreshTable()" href="#" v-tooltip="'Refresh'" class="btn btn-success">
                <i class="fas fa-sync"></i>
              </a>
              <a @click="print" v-tooltip="$t('Print Table')" class="btn btn-info">
                <i class="fas fa-print"></i>
              </a>
            </div>
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body position-relative">
          <table-loading v-show="loading" />
          <div id="printMe" class="table-responsive table-custom mt-3">
            <table class="table">
              <thead>
                <tr>
                  <th>{{ $t('#') }}</th>
                  <th>{{ $t('Domain') }}</th>
                  <th>{{ $t('Tenant') }}</th>
                  <th class="text-right no-print">
                    {{ $t('Action') }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-show="items.length" v-for="(data, i) in items" :key="i">
                  <td>
                    <span v-if="pagination &&
                      pagination.current_page > 1
                      ">
                      {{
                        pagination.per_page *
                        (pagination.current_page -
                          1) +
                        (i + 1)
                      }}
                    </span>
                    <span v-else>{{ i + 1 }}</span>
                  </td>
                  <td>{{ data.domain }}</td>

                  <td v-if="data.tenant">
                    <router-link :to="{
                      name: 'tenants.show',
                      params: { id: data.tenant_id },
                    }">
                      {{ data.tenant.name }}
                    </router-link>
                  </td>
                  <td class="text-right no-print">
                    <div v-if="data.id" class="btn-group">
                      <a v-tooltip="$t('Delete')" href="#" class="btn btn-danger btn-sm"
                        @click="deleteData(data.id)">
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
        <div class="card-footer">
          <div class="dtable-footer">
            <div class="form-group row display-per-page">
              <label>{{ $t('per_page') }} </label>
              <div>
                <select @change="updatePerPager" v-model="perPage" class="form-control form-control-sm ml-1">
                  <option value="10">10</option>
                  <option value="25">25</option>
                  <option value="50">50</option>
                  <option value="100">100</option>
                </select>
              </div>
            </div>
            <!-- pagination-start -->
            <pagination v-if="pagination && pagination.last_page > 1" :pagination="pagination" :offset="5"
              class="justify-flex-end" @paginate="paginate" />
            <!-- pagination-end -->
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  layout: 'central',
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('All Domains') }
  },
  data: () => ({
    breadcrumbsCurrent: 'All Domains',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'All Domains',
        url: '',
      },
    ],
    query: '',
    perPage: 10,
  }),
  // Map Getters
  computed: {
    ...mapGetters('operations', [
      'items',
      'loading',
      'pagination',
      'appInfo',
      'tenant',
    ]),
  },
  created() {
    this.getData()
  },
  methods: {
    // get data
    async getData() {
      this.$store.state.operations.loading = true
      let currentPage = this.pagination ? this.pagination.current_page : 1
      await this.$store.dispatch('operations/fetchData', {
        path: '/api/domains?page=',
        currentPage: currentPage + '&perPage=' + this.perPage,
      })
    },

    // Pagination
    async paginate() {
      this.query === '' ? await this.getData() : await this.searchData()
    },

    // Reset pagination
    async resetPagination() {
      this.pagination.current_page = 1
    },

    // Reload after search
    async reload() {
      this.query = ''
    },

    // print table
    async print() {
      await this.$htmlToPaper('printMe')
    },

    // refresh table
    refreshTable() {
      this.query = "";
      this.query === "" ? this.getData() : this.searchData();
    },

    // update per page count
    updatePerPager() {
      this.pagination.current_page = 1;
      this.query === "" ? this.getData() : this.searchData();
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
                  'success',
                )
                this.getData()
              } else {
                Swal.fire(
                  this.$t('Failed!'),
                  this.$t('Delete failed'),
                  'warning',
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
