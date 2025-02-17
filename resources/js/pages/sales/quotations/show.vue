<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->

    <div class="row no-print mb-2">
      <div class="w-100 text-right float-right">
        <div class="d-flex justify-content-between" v-if="allData">
          <div class="btn-group">
            <ul class="nav nav-pills">
              <li class="nav-item">
                <a
                  class="nav-link active"
                  href="#details"
                  data-toggle="tab"
                  @click="getQuotation"
                >
                  <i class="fa fa-info"></i>
                  {{ $t("Details") }}</a
                >
              </li>
              <li class="nav-item">
                <a
                  @click="getActivity"
                  class="nav-link"
                  href="#activity-log"
                  data-toggle="tab"
                >
                  <i class="nav-icon fa fa-bell" aria-hidden="true"></i>
                  {{ $t("Activity log") }}</a
                >
              </li>
            </ul>
          </div>

          <div class="btn-group">
            <a
              @click="notify((form.isSendSMS = true))"
              href="#"
              class="btn btn-secondary"
            >
              <i class="fas fa-sms"></i> {{ $t("SMS") }}
            </a>
            <a
              @click="notify((form.isSendEmail = true))"
              href="#"
              class="btn btn-success"
              ><i class="fas fa-paper-plane"></i> {{ $t("email") }}</a
            >
            <a @click="generatePDF()" href="#" class="btn btn-primary">
              <i class="fas fa-download"></i> {{ $t("download") }}
            </a>
            <a @click="printWindow()" href="#" class="btn btn-secondary">
              <i class="fas fa-print"></i> {{ $t("Print") }}
            </a>
            <router-link
              v-if="$can('quotation-edit')"
              :to="{
                name: 'quotations.edit',
                params: { slug: allData.slug },
              }"
              class="btn btn-info"
            >
              <i class="fas fa-edit" /> {{ $t("Edit") }}
            </router-link>
            <router-link
              :to="{ name: 'quotations.index' }"
              class="btn btn-dark float-right"
            >
              <i class="fas fa-long-arrow-alt-left" /> {{ $t("Back") }}
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <div class="tab-content">
      <div class="tab-pane active" id="details">
        <div class="row">
          <!-- Main content -->
          <div class="invoice p-3 mb-3 w-100" id="content-to-pdf">
            <table-loading v-show="loading" />
            <!-- info row -->
            <div class="row invoice-info">
              <div class="col-sm-4 invoice-col">
                <CompanyInfo />
              </div>
              <!-- /.col -->
              <div
                class="col-sm-6 offset-sm-2 invoice-col float-right text-md-right"
              >
                <h5>{{ $t("Client Details") }}</h5>
                <div v-if="allData.client">
                  <span v-if="allData.client.companyName"
                    ><strong>{{ $t("Client ID") }}:</strong>
                    {{ allData.client.clientID | withPrefix(clientPrefix) }}<br
                  /></span>
                  <strong>{{ $t("Client Name") }}:</strong>
                  {{ allData.client.name }}<br />
                  <span v-if="allData.client.companyName"
                    ><strong>{{ $t("Company Name") }}:</strong>
                    {{ allData.client.companyName }}<br
                  /></span>
                  <span v-if="allData.client.email"
                    ><strong>{{ $t("Email") }}:</strong>
                    {{ allData.client.email }}<br
                  /></span>
                  <span v-if="allData.client.phoneNumber"
                    ><strong>{{ $t("Contact Number") }}:</strong>
                    {{ allData.client.phoneNumber }}<br
                  /></span>
                  <span v-if="allData.client.address"
                    ><strong>{{ $t("Address") }}:</strong>
                    {{ allData.client.address }}<br
                  /></span>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->

            <div class="row mt-3">
              <div class="col-12">
                <div class="table-responsive table-custom">
                  <table class="table">
                    <thead>
                      <tr>
                        <th v-if="allData.quotationNo">
                          {{ $t("Quotation No") }}
                        </th>
                        <th v-if="allData.reference">
                          {{ $t("Reference") }}
                        </th>
                        <th v-if="allData.date">
                          {{ $t("Quotation Date") }}
                        </th>
                        <th v-if="allData.deliveryPlace">
                          {{ $t("Delivery Place") }}
                        </th>
                        <th v-if="allData.note">{{ $t("Note") }}</th>
                        <th>{{ $t("Status") }}</th>
                        <th class="text-right">
                          {{ $t("Created By") }}
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td v-if="allData.quotationNo">
                          {{
                            allData.quotationNo | withPrefix(quotationPrefix)
                          }}
                        </td>
                        <td v-if="allData.reference">
                          {{ allData.reference }}
                        </td>
                        <td v-if="allData.date">
                          {{ allData.date | moment("Do MMM, YYYY") }}
                        </td>
                        <td v-if="allData.deliveryPlace">
                          {{ allData.deliveryPlace }}
                        </td>
                        <td v-if="allData.note">{{ allData.note }}</td>
                        <td>
                          <span
                            v-if="allData.status === 1"
                            class="badge bg-success"
                            >{{ $t("Active") }}</span
                          >
                          <span v-else class="badge bg-danger">{{
                            $t("Inactive")
                          }}</span>
                        </td>
                        <td class="text-right">
                          {{ allData.createdBy }}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Table row -->
            <div class="row position-relative mt-4">
              <div class="col-12">
                <strong class="mb-2 d-block"
                  >{{ $t("Products") }}:</strong
                >
                <div class="table-custom table-responsive">
                  <table class="table table-sm">
                    <thead>
                      <tr>
                        <th>{{ $t("#") }}</th>
                        <th>{{ $t("Code") }}</th>
                        <th>{{ $t("Item Name") }}</th>
                        <th>{{ $t("Quantity") }}</th>
                        <th>{{ $t("Unit Price") }}</th>
                        <th>{{ $t("Unit Tax") }}</th>
                        <th>{{ $t("Unit Cost") }}</th>
                        <th class="text-right">{{ $t("Subtotal") }}</th>
                      </tr>
                    </thead>
                    <tbody v-if="allData.products">
                      <tr v-for="(data, i) in allData.products" :key="i">
                        <td>{{ ++i }}</td>
                        <td>
                          {{ data.productCode | withPrefix(productPrefix) }}
                        </td>
                        <td>{{ data.productName }}</td>
                        <td>{{ data.quantity }} {{ data.productUnit }}</td>
                        <td>{{ data.salePrice | withCurrency }}</td>
                        <td>{{ data.taxAmount | withCurrency }}</td>
                        <td>{{ data.unitCost | withCurrency }}</td>
                        <td class="text-right">
                          {{ data.unitCostTotal | withCurrency }}
                        </td>
                      </tr>
                      <tr>
                        <td class="text-right" colspan="7">
                          <strong>{{ $t("Subtotal") }}</strong>
                        </td>
                        <td class="text-right">
                          <strong>{{ allData.subTotal | withCurrency }}</strong>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <!-- /.row -->
            <div class="row mt-3">
              <div class="offset-xl-8 col-lg-12 col-xl-4 text-xl-right">
                <div class="table-responsive table-custom table-border-y-0">
                  <table class="table">
                    <tbody>
                      <tr class="bg-sub-light text-bold">
                        <th>{{ $t("Subtotal") }}:</th>
                        <td>{{ allData.subTotal | withCurrency }}</td>
                      </tr>
                      <tr>
                        <th>{{ $t("Transport") }}:</th>
                        <td>
                          <span class="plus-sign">+</span>
                          {{ allData.transport | withCurrency }}
                        </td>
                      </tr>
                      <tr>
                        <th>
                          {{ $t("Discount") }}
                          <span v-if="allData.discountPercentage > 0"
                            >({{ allData.discountPercentage }}%)</span
                          >:
                        </th>
                        <td>
                          <span class="minus-sign">-</span>
                          {{ allData.discount | withCurrency }}
                        </td>
                      </tr>
                      <tr>
                        <th>
                          {{ $t("Tax") }}
                          <span v-if="allData.quotationTax"
                            >({{ allData.quotationTax.rate }}%)</span
                          >: <br />
                          <span
                            v-if="
                              allData.quotationTax &&
                              allData.quotationTax.group_tax_details &&
                              allData.quotationTax.group_tax_details.length
                            "
                          >
                            (<span
                              v-for="(tax, index) in allData.quotationTax
                                .group_tax_details"
                              :key="tax.id"
                            >
                              {{ tax.rate }}%<span
                                v-if="
                                  index <
                                  allData.quotationTax.group_tax_details
                                    .length -
                                    1
                                "
                              >
                                +</span
                              > </span
                            >)
                          </span>
                        </th>
                        <td>
                          <span class="plus-sign">+</span>
                          {{ allData.totalTax | withCurrency }}
                        </td>
                      </tr>
                      <tr class="bg-indigo-light">
                        <th>{{ $t("Total") }}:</th>
                        <td>
                          <span class="equal-sign">=</span>
                          {{ allData.total | withCurrency }}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <!-- /.row -->
          </div>
          <!-- /.invoice -->
        </div>
      </div>

      <!--  activity logs -->
      <div class="tab-pane" id="activity-log">
        <div class="card custom-card w-100 mt-5 no-print">
          <div class="card-header setings-header">
            <div class="col-xl-4 col-4">
              <h3 class="card-title">
                {{ $t("Activity log") }}
              </h3>
            </div>
            <div class="col-xl-8 col-8 float-right text-right">
              <div class="btn-group c-w-100">
                <a
                  @click="refreshTable()"
                  href="#"
                  v-tooltip="'Refresh'"
                  class="btn btn-success"
                >
                  <i class="fas fa-sync"></i>
                </a>
                <a
                  @click="print"
                  v-tooltip="$t('Print Table')"
                  class="btn btn-info"
                >
                  <i class="fas fa-print"></i>
                </a>
              </div>
            </div>
          </div>
          <table-loading v-show="loading" />
          <div class="card-body position-relative">
            <div class="row">
              <div class="col-6 col-xl-4 mb-2">
                <search
                  v-model="query"
                  @reset-pagination="resetPagination()"
                  @reload="reload"
                />
              </div>
            </div>
            <div id="printMe" class="table-responsive table-custom mt-3">
              <div
                v-show="items.length > 0"
                v-for="(data, i) in items"
                :key="i"
              >
                <div class="card mb-0 border border-gray">
                  <div class="card-body py-1">
                    <div class="row">
                      <div
                        class="col-1 d-flex justify-content-center align-items-center"
                      >
                        <i
                          v-if="data.event == 'Update'"
                          class="fa fa-magic"
                          aria-hidden="true"
                        ></i>
                        <i
                          v-if="data.event == 'Create'"
                          class="fa fa-plus-circle"
                          aria-hidden="true"
                        ></i>
                        <i
                          v-if="data.event == 'Delete'"
                          class="fa fa-trash"
                          aria-hidden="true"
                        ></i>
                      </div>
                      <div class="col-11">
                        <div class="row">
                          <div class="col-12">
                            <p class="text-bold mb-0">{{ data.causer_name }}</p>
                          </div>
                          <div class="col-12">
                            <p class="mb-0">{{ data.description }}</p>
                          </div>
                          <div class="col-12">
                            <p class="mb-0">{{ data.performedAt }}</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="text-center" v-show="!loading && !items.length">
                <EmptyTable />
              </div>
            </div>
          </div>
          <div class="card-footer">
            <div class="dtable-footer">
              <div class="form-group row display-per-page">
                <label>{{ $t("per_page") }} </label>
                <div>
                  <select
                    @change="updatePerPager"
                    v-model="perPage"
                    class="form-control form-control-sm ml-1"
                  >
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                  </select>
                </div>
              </div>
              <!-- pagination-start -->
              <pagination
                v-if="pagination && pagination.last_page > 1"
                :pagination="pagination"
                :offset="5"
                class="justify-flex-end"
                @paginate="paginate"
              />
              <!-- pagination-end -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Form from "vform";
import axios from "axios";
import { mapGetters } from "vuex";
import html2pdf from "html2pdf.js";

export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("Quotation Details") };
  },
  data: () => ({
    allData: "",
    breadcrumbsCurrent: "Quotation Details",
    breadcrumbs: [
      {
        name: "Dashboard",
        url: "home",
      },
      {
        name: "Quotations",
        url: "quotations.index",
      },
      {
        name: "Details",
        url: "",
      },
    ],
    quotationProducts: [],
    quotationPrefix: "",
    clientPrefix: "",
    productPrefix: "",
    loading: false,
    form: new Form({
      isSendEmail: false,
      isSendSMS: false,
    }),
    isDemoMode: window.config.isDemoMode,
    query: "",
    perPage: 10,
  }),
  // Map Getters
  computed: {
    ...mapGetters("operations", ["appInfo", "items", "loading", "pagination"]),
  },
  created() {
    this.getQuotation();
    this.quotationPrefix = this.appInfo.quotationPrefix;
    this.clientPrefix = this.appInfo.clientPrefix;
    this.productPrefix = this.appInfo.productPrefix;
  },

  watch: {
    // watch search data
    query: function (newQ, oldQ) {
      if (newQ === "") {
        this.getActivity();
      } else {
        this.searchData();
      }
    },
  },

  methods: {
    // get the quotation
    async getQuotation() {
      this.loading = true;
      const { data } = await axios.get(
        window.location.origin + "/api/quotations/" + this.$route.params.slug
      );
      this.allData = data.data;
      this.quotationProducts = this.allData.products;
      this.quotationProducts.sort(this.sortProducts);
      this.loading = false;
    },
    sortProducts(a, b) {
      if (a.productCode < b.productCode) {
        return -1;
      }
      if (a.productCode > b.productCode) {
        return 1;
      }
      return 0;
    },
    // download pdf
    generatePDF() {
      // Get the HTML content to be converted
      const element = document.getElementById("content-to-pdf");

      // Options for PDF generation
      const options = {
        margin: 5,
        filename: "Quotation-" + this.$route.params.slug + ".pdf",
        image: { type: "jpeg", quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: "mm", format: "a4", orientation: "portrait" },
      };
      // Generate PDF from HTML content
      html2pdf().from(element).set(options).save();
      toast.fire({
        type: "success",
        title: this.$t("Download started."),
      });
    },
    // notify
    async notify() {
      if (!this.isDemoMode) {
        this.loading = true;
        await this.form
          .post(
            window.location.origin +
              "/api/quotation/notify/" +
              this.$route.params.slug
          )
          .then(() => {
            toast.fire({
              type: "success",
              title: this.$t("Notification sent successfully."),
            });
          })
          .catch(() => {
            toast.fire({ type: "error", title: this.$t("Opps...something went wrong") });
          });
        this.loading = false;
      } else {
        toast.fire({
          type: "warning",
          title: this.$t("You are not allowed to do this in demo version."),
        });
      }
    },
    // print
    printWindow() {
      window.print();
    },

    // print table
    async print() {
      await this.$htmlToPaper("printMe");
    },

    // get activity logs
    async getActivity() {
      let currentPage = this.pagination ? this.pagination.current_page : 1;
      this.$store.state.operations.loading = true;
      let slug = this.$route.params.slug;
      let modelName = "Quotation";
      await this.$store.dispatch("operations/fetchSpecificLogs", {
        path: "/api/activity-log-specific?page=",
        currentPage: currentPage + "&perPage=" + this.perPage,
        slug: slug,
        modelName: modelName,
      });
    },

    // search data
    async searchData() {
      this.$store.state.operations.loading = true;
      let slug = this.$route.params.slug;
      let modelName = "Quotation";
      await this.$store.dispatch("operations/fetchSpecificLogs", {
        path: "/api/activity-log-specific?page=",
        currentPage: this.pagination.current_page + "&perPage=" + this.perPage,
        term: this.query,
        slug: slug,
        modelName: modelName,
      });
    },

    // pagination
    async paginate() {
      this.getActivity();
    },

    updatePerPager() {
      this.pagination.current_page = 1;
      this.query === "" ? this.getActivity() : this.searchData();
    },

    // reload after search
    async reload() {
      this.query = "";
    },

    // refresh table
    refreshTable() {
      this.query = "";
      this.query === "" ? this.getActivity() : this.searchData();
    },

    // reset pagination
    async resetPagination() {
      this.pagination.current_page = 1;
    },
  },
};
</script>
<style scoped>
.nav-pills .nav-item {
  background: #ddd;
  margin: 2px;
  border-radius: 0.25rem;
}
</style>
