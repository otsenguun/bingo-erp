<template>
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">{{ $t("Payment Invoices") }}</h3>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-lg-12">
          <!-- /.card-header -->
          <div class="card-body p-0 position-relative">
            <table-loading v-show="loading" />
            <div class="table-responsive table-custom mt-3" id="printMe">
              <table class="table">
                <thead>
                  <tr>
                    <th>{{ $t('ID') }}</th>
                    <th>{{ $t('Plan') }}</th>
                    <th>{{ $t('Months') }}</th>
                    <th>{{ $t('Trx ID') }}</th>
                    <th>{{ $t('Trx Type') }}</th>
                    <th>{{ $t('Payment Status') }}</th>
                    <th>{{ $t('Subscription Till') }}</th>
                    <th>{{ $t('Total') }}
                      <span class="badge badge-info"
                        v-tooltip="'Central Panel Base Currency  <br/><small>(Converted USD)</small>'">
                        <i class="fas fa-info"></i>
                      </span>
                    </th>
                    <th>{{ $t('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-show="items.length" v-for="(payment, i) in items" :key="i">
                    <td><span v-if="pagination && pagination.current_page > 1">
                        {{
                          pagination.per_page * (pagination.current_page - 1) +
                          (i + 1)
                        }}
                      </span>
                      <span v-else>{{ i + 1 }}</span>
                    </td>
                    <td>{{ payment.plan.name }}</td>
                    <td>{{ payment.quantity }}</td>
                    <td>{{ payment.system_trx_id }}</td>
                    <td>{{ payment.method }}</td>
                    <td>{{ payment.status }}</td>
                    <td>
                      <span v-if="payment.subscription && payment.subscription.ends_at">{{
                        payment.subscription ? payment.subscription.ends_at : moment("Do MMM, YYYY")
                      }}</span>
                    </td>
                    <td>
                      {{ (payment.default_amount_rate * payment.quantity) | withCentralAdminCurrency }}<br>
                      (${{ payment.amount * payment.quantity }})
                    </td>
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
          <!-- /.card-body -->
        </div>
      </div>
    </div>
    <div class="card-footer">
      <div class="dtable-footer">
        <div class="form-group row display-per-page">
          <label>{{ $t("per_page") }} </label>
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
</template>

<script>
import { mapGetters } from "vuex";

export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("Subscription Invoices") };
  },
  data: () => ({
    breadcrumbsCurrent: "subscription_invoices.list.index.breadcrumbs_current",
    breadcrumbs: [
      {
        name: "subscription_invoices.list.index.breadcrumbs_first",
        url: "home",
      },
      {
        name: "subscription_invoices.list.index.breadcrumbs_active",
        url: "",
      },
    ],
    perPage: 10,
  }),
  // Map Getters
  computed: {
    ...mapGetters("operations", [
      "items",
      "loading",
      "pagination",
      "appInfo",
      "tenant",
    ]),
  },
  created() {
    this.getData();
  },
  methods: {
    // get data
    async getData() {
      this.$store.state.operations.loading = true;
      let currentPage = this.pagination ? this.pagination.current_page : 1;
      await this.$store.dispatch("operations/fetchData", {
        path: "/api/payments?page=",
        currentPage: currentPage + "&perPage=" + this.perPage,
      });
    },

    // update per page count
    updatePerPager() {
      this.pagination.current_page = 1;
      this.getData()
    },

    // pagination
    async paginate() {
      this.getData()
    },

    // reset pagination
    async resetPagination() {
      this.pagination.current_page = 1;
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
};
</script>
