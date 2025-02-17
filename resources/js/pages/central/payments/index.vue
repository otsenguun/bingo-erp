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
                {{ $t("Payments") }}
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
                    <th>{{ $t('ID') }}</th>
                    <th>{{ $t('Plan') }}</th>
                    <th>{{ $t('Month') }}</th>
                    <th>{{ $t('Transaction Type') }}</th>
                    <th>{{ $t('Trx ID') }}</th>
                    <th>{{ $t('Amount') }}
                      <span class="badge badge-info"
                        v-tooltip="'Base Currency  <br/><small>(Converted USD)</small>'">
                        <i class="fas fa-info"></i>
                      </span>
                    </th>
                    <th>{{ $t('Payment Status') }}</th>
                    <th>{{ $t('Created At') }}</th>
                    <th class="text-right">{{ $t('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-show="items.length" v-for="(payment, i) in items" :key="i">
                    <td>{{ payment.id }}</td>
                    <td>{{ payment.plan.name }}</td>
                    <td>{{ payment.quantity }}</td>
                    <td>{{ payment.method }}</td>
                    <td>{{ payment.system_trx_id }}</td>
                    <td>
                      {{ payment.default_amount_rate * payment.quantity | withCurrency}}<br>
                      (${{ payment.amount * payment.quantity }})
                    </td>
                    <td>{{ payment.status }}</td>
                    <td>{{ payment.created_at | moment("Do MMM, YYYY HH:mm:A") }}</td>
                    <td class="text-center no-print">
                      <div v-if="payment.status == 'success'" class="btn-group">
                        <button type="button" v-tooltip="$t('Download')" class="btn btn-info btn-sm"
                          @click="download(payment.id)">
                          <i class="fas fa-file-download" />
                        </button>
                      </div>
                      <div v-else class="text-center">
                        <p>N/A</p>
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
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  layout: 'central',
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('Payments') }
  },
  data: () => ({
    breadcrumbsCurrent: 'Payments',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Payments',
        url: '',
      },
    ],
    query: '',
    perPage: 10,
    billingHistory: [],
  }),
  // Map Getters
  computed: {
    ...mapGetters('operations', ['items', 'loading', 'pagination', 'appInfo', 'tenant']),
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
        path: '/api/payments?page=',
        currentPage: currentPage + '&perPage=' + this.perPage,
      })
    },

    // update per page count
    updatePerPager() {
      this.pagination.current_page = 1
      this.query === '' ? this.getData() : this.searchData()
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

    // download invoice
    async download(id) {
      await this.$axios.post(
        window.location.origin + "/api/payments/download", {
        payment_id: id,
      }, {
        responseType: "blob",
      }
      )
        .then((response) => {
          toast.fire({
            type: "success",
            title: this.$t("Successfully created"),
          });
          window.open(URL.createObjectURL(response.data));
        })
        .catch(() => {
          toast.fire({
            type: "error",
            title: this.$t("Opps...something went wrong"),
          });
        });
    },
  },
}
</script>
