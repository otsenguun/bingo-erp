<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->

    <div class="row" v-if="data">
      <div class="col-md-12 col-lg-4 tenant-details">
        <div class="card card-primary card-outline">
          <div class="card-body">
            <div class="text-center">
              <img class="profile-user-img img-fluid img-circle" :src="data.photo_url" :alt="$t('Attached Image')" />
            </div>
            <h3 class="profile-username text-center text-capitalize">
              {{ data.name }}
            </h3>
            <a href="#" class="profile-username text-center d-block">{{
      data.email
    }}</a>
            <table>
              <tr>
                <td width="100">{{ $t("Tenant ID") }}</td>
                <td>{{ data.id }}</td>
              </tr>
              <tr>
                <td width="100">{{ $t("name") }}</td>
                <td>{{ data.name }}</td>
              </tr>
              <tr>
                <td width="100">{{ $t("email") }}</td>
                <td>{{ data.email }}</td>
              </tr>
              <tr>
                <td width="100">{{ $t("Company") }}</td>
                <td>{{ data.company }}</td>
              </tr>
              <tr>
                <td width="100">{{ $t("domain") }}</td>
                <td>{{ data.domain }}</td>
              </tr>
              <tr>
                <td width="100">{{ $t("Current Plan") }}</td>
                <td>{{ data.plan && data.plan.name }}</td>
              </tr>
              <tr>
                <td width="100">{{ $t("Trial Started At") }}</td>
                <td>{{ data.created_at | moment("Do MMM, YYYY") }}</td>
              </tr>
              <tr v-if="data.trial_ends_at">
                <td width="100">{{ $t("Trial Ends At") }}</td>
                <td>{{ data.trial_ends_at | moment("Do MMM, YYYY") }}</td>
              </tr>
              <tr v-if="data.created_at">
                <td width="100">{{ $t("Created At") }}</td>
                <td>{{ data.created_at | moment("Do MMM, YYYY") }}</td>
              </tr>
              <tr>
                <td width="100">{{ $t("Banned") }}</td>
                <td>{{ data.is_banned ? "True" : "False" }}</td>
              </tr>
            </table>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- CARD DETAILS TABLE -->

        <div class="domain-twrapper card mt-0">
          <div class="card-header">
            <h3>{{ $t("domain") }}</h3>
          </div>
          <div class="card-body">
            <table class="mt-0">
              <tr>
                <td width="100">{{ $t("primary_domain") }}</td>
                <td>
                  <a :href="data.domain_url" target="_blank">{{
      data.domain_url
    }}</a>
                </td>
              </tr>
              <tr v-for="(domain, i) in data.domains" :key="domain.id">
                <td width="100">{{ $t("domain") }} {{ i + 1 }}</td>
                <td>
                  <a :href="domain" target="_blank">{{ data.domain }}</a>
                </td>
              </tr>
            </table>
          </div>
        </div>
        <!-- DOMAIN TABLE-->
      </div>
      <!-- /.col -->

      <div class="col-md-12 col-lg-8">
        <div class="row">
          <div v-for="(limitation, i) in data.limitations && data.limitations" :key="i"
            class="col-lg-4 col-md-2 col-sm-6 col-12">
            <div class="small-box" :class="limitation.bgColor">
              <div class="inner">
                <h3 class="text-wrap">
                  {{ $t(limitation.name) }}: {{ limitation.limit }}
                </h3>
                <p class="mb-0">
                  {{ $t("Used") }}: {{ limitation.current }}
                </p>
                <p>{{ $t("Remaining") }}: {{ limitation.remaining }}</p>
              </div>
              <div class="icon">
                <i :class="limitation.icon"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header setings-header">
            <h3 class="card-title">{{ $t("Invoice") }}</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="row">
              <div class="col-lg-12">
                <div class="card-body p-0 position-relative">
                  <table-loading v-show="loading" />
                  <div class="table-responsive table-custom mt-3" id="printMe">
                    <table class="table">
                      <thead>
                        <tr>
                          <th>{{ $t('ID') }}</th>
                          <th>{{ $t('Plan') }}</th>
                          <th>{{ $t('Qty') }}</th>
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
                        <tr v-show="data && data.tenant_invoices" v-for="(tenant_invoice, i) in data &&
      data.tenant_invoices" :key="i">
                          <td>{{ tenant_invoice.id }}</td>
                          <td>{{ tenant_invoice.plan.name }}</td>
                          <td>{{ tenant_invoice.quantity }}</td>
                          <td>{{ tenant_invoice.method }}</td>
                          <td>{{ tenant_invoice.system_trx_id }}</td>
                          <td>
                            {{ tenant_invoice.default_amount_rate *
      tenant_invoice.quantity | withCurrency }}
                            <br>
                            (${{ tenant_invoice.amount * tenant_invoice.quantity }})
                          </td>
                          <td>{{ tenant_invoice.status }}</td>
                          <td>{{ tenant_invoice.created_at | moment("Do MMM, YYYY HH:mm:A") }}</td>
                          <td class="text-center no-print">
                            <div v-if="tenant_invoice.status == 'success'" class="btn-group">
                              <button type="button" v-tooltip="$t('Download')" class="btn btn-info btn-sm"
                                @click="download(tenant_invoice.id)">
                                <i class="fas fa-file-download" />
                              </button>
                            </div>
                            <div v-else class="text-center">
                              <p>N/A</p>
                            </div>
                          </td>
                        </tr>
                        <tr v-show="data.tenant_invoices &&
      data.tenant_invoices.length < 1
      ">
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
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";
import { mapGetters } from "vuex";

export default {
  layout: "central",
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("Tenant Details") };
  },
  data: () => ({
    breadcrumbsCurrent: "Tenant Details",
    breadcrumbs: [
      {
        name: "Dashboard",
        url: "home",
      },
      {
        name: "Tenants",
        url: "tenants.index",
      },
      {
        name: "Tenant Details",
        url: "",
      },
    ],
    url: null,
    showModal: false,
    data: {},
    loading: true,
  }),

  computed: {
    ...mapGetters("operations", ["appInfo"]),
  },

  created() {
    this.getTenant();
  },
  methods: {
    // get the tenant
    async getTenant() {
      const { data } = await axios.get(
        window.location.origin + "/api/tenants/" + this.$route.params.id
      );
      this.data = data;
      this.loading = false;
    },

    // print
    printWindow() {
      window.print();
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

<style lang="scss" scoped>
.tenant-details h3 {
  margin: 15px 0 0 0;
}

.card-body.tenant-details a {
  text-align: center;
  display: block;
  font-size: 16px;
  margin-top: 0;
}

.tenant-details table {
  width: 100%;
  margin-top: 20px;
}

.tenant-details table tr td {
  border: 1px solid #ddd;
  padding: 8px 15px;
}

.tenant-details table tr td:first-child {
  width: 30%;
}

.domain-twrapper .card-header h3 {
  padding: 0;
  margin: 0;
  font-size: 16px;
}

.box-profile .list-group-item {
  padding-left: 20px;
  padding-right: 20px;
}

.box-profile .list-group {
  border-left: 1px solid #ddd;
  border-right: 1px solid #ddd;
  border-radius: 0;
}
</style>
