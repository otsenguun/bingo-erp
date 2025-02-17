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
                {{ $t("Pages") }}
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
                <router-link :to="{ name: 'pages.create' }" class="btn btn-primary">
                  {{ $t('Create') }}
                  <i class="fas fa-plus-circle d-none d-sm-inline-block" />
                </router-link>
              </div>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body position-relative">
            <div class="row">
              <div class="col-6 col-xl-4 mb-2">
                <search v-model="query" @reset-pagination="resetPagination()" @reload="reload" />
              </div>
            </div>
            <table-loading v-show="loading" />
            <div id="printMe" class="table-responsive table-custom mt-3">
              <table class="table">
                <thead>
                  <tr>
                    <th>{{ $t('#') }}</th>
                    <th>{{ $t('Pages') }}</th>
                    <th>{{ $t('Slug') }}</th>
                    <th>{{ $t('Type') }}</th>
                    <th>{{ $t('Status') }}</th>
                    <th class="text-right no-print">{{ $t('Action') }}</th>
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
                    <td>{{ data.name }}</td>
                    <td>{{ data.slug }}</td>
                    <td>
                      <span v-if="data.type === 0">
                        {{ $t('Information') }}
                      </span>
                      <span v-if="data.type === 1">
                        {{ $t('Need Help') }}
                      </span>
                    </td>
                    <td>
                      <span v-if="data.status === 1" class="badge bg-success">{{
                        $t('Active')
                      }}</span>
                      <span v-else class="badge bg-danger">{{
                        $t('Inactive')
                      }}</span>
                    </td>
                    <td class="text-right no-print">
                      <div class="btn-group">
                        <router-link v-tooltip="$t('Edit')" :to="{
                              name: 'pages.edit',
                              params: { id: data.id },
                            }" class="btn btn-info btn-sm">
                          <i class="fas fa-edit" />
                        </router-link>
                        <a v-tooltip="$t('Delete')" href="#" class="btn btn-danger btn-sm"
                          @click="deleteData(data.id)">
                          <i class="fas fa-trash" />
                        </a>
                      </div>
                    </td>
                  </tr>
                  <tr v-show="!loading && !items.length">
                    <td colspan="6">
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
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  layout: 'central',
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('Pages') }
  },

  data: () => ({
    breadcrumbsCurrent: 'Pages',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Pages',
        url: '',
      },
    ],

    query: '',
    perPage: 10,
  }),
  // Map Getters
  computed: {
    ...mapGetters('operations', ['items', 'loading', 'pagination']),
  },
  watch: {
    // watch search data
    query: function (newQ) {
      if (newQ === '') {
        this.getData()
      } else {
        this.searchData()
      }
    },
  },
  created() {
    this.getData()
  },
  methods: {
    // update per page count
    updatePerPager() {
      this.pagination.current_page = 1
      this.query === '' ? this.getData() : this.searchData()
    },
    // get data
    async getData() {
      this.$store.state.operations.loading = true
      let currentPage = this.pagination ? this.pagination.current_page : 1
      await this.$store.dispatch('operations/fetchData', {
        path: '/api/pages?page=',
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

    // search data
    async searchData() {
      this.$store.state.operations.loading = true
      let currentPage = this.pagination ? this.pagination.current_page : 1
      await this.$store.dispatch('operations/searchData', {
        term: this.query,
        path: '/api/pages/search/',
        currentPage: currentPage + '&perPage=' + this.perPage,
      })
    },

    // Reload after search
    async reload() {
      this.query = ''
    },

    // refresh table
    refreshTable() {
      this.query = "";
      this.query === "" ? this.getData() : this.searchData();
    },

    // print table
    async print() {
      await this.$htmlToPaper('printMe')
    },

    // delete data
    async deleteData(id) {
      Swal.fire({
        title: this.$t('Are you sure?'),
        text: this.$t('You will not be able to return to this!'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: this.$t('Delete'),
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          this.$store
            .dispatch('operations/deleteData', {
              path: '/api/pages/',
              slug: id,
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
                  this.$t('There was something wrong.'),
                  'warning'
                )
              }
            })
        }
      })
    },
  },
}
</script>
