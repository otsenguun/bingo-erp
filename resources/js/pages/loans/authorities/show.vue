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
                  @click="getLoanAuthority"
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
            <a @click="generatePDF()" href="#" class="btn btn-primary">
              <i class="fas fa-download"></i> {{ $t("download") }}
            </a>
            <a @click="printWindow()" href="#" class="btn btn-secondary">
              <i class="fas fa-print"></i> {{ $t("Print") }}
            </a>
            <router-link
              v-if="$can('loan-authority-edit')"
              :to="{
                name: 'authorities.edit',
                params: { slug: allData.slug },
              }"
              class="btn btn-info"
            >
              <i class="fas fa-edit" /> {{ $t("Edit") }}
            </router-link>
            <router-link
              :to="{ name: 'authorities.index' }"
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
            <!-- info row -->
            <div class="row invoice-info">
              <div class="col-sm-4 invoice-col">
                <CompanyInfo />
              </div>
              <!-- /.col -->
              <div
                class="col-sm-6 offset-sm-2 invoice-col float-right text-md-right"
              >
                <h5>{{ $t("Authority Details") }}</h5>
                <strong>{{ $t("Name") }}:</strong> {{ allData.name
                }}<br />
                <strong>{{ $t("Email") }}:</strong> {{ allData.email
                }}<br />
                <strong>{{ $t("Contact Number") }}:</strong>
                {{ allData.contactNumber }}<br />
                <strong>{{ $t("Address") }}:</strong> {{ allData.address
                }}<br />
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- Table row -->
            <div class="row">
              <div
                v-if="allData.loans && allData.loans.length > 0"
                class="col-12"
              >
                <strong class="mt-4 mb-2 d-block"
                  >{{ $t("All Loans") }}:</strong
                >
                <div class="table-responsive table-custom">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>{{ $t("#") }}</th>
                        <th>{{ $t("Ref. No") }}</th>
                        <th>{{ $t("Account") }}</th>
                        <th>{{ $t("Amount") }}</th>
                        <th>{{ $t("Payable") }}</th>
                        <th>{{ $t("Interest") }}</th>
                        <th>{{ $t("Due") }}</th>
                        <th>{{ $t("Installment") }}</th>
                        <th>{{ $t("Status") }}</th>
                        <th class="text-right">{{ $t("Date") }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(data, i) in allData.loans" :key="i">
                        <td>{{ ++i }}</td>
                        <td>{{ data.reference }}</td>
                        <td>
                          {{ data.transaction.cashbook_account.account_number }}
                        </td>
                        <td>{{ data.transaction.amount | withCurrency }}</td>
                        <td>{{ data.payable | withCurrency }}</td>
                        <td>
                          <span v-if="data.loanType == 1">
                            {{ data.interestAmount | withCurrency }} ({{
                              data.interestRate
                            }}%)
                          </span>
                          <span v-else>{{ 0 | withCurrency }}</span>
                        </td>
                        <td>{{ data.due | withCurrency }}</td>
                        <td>{{ data.installment }}</td>
                        <td>
                          <span
                            v-if="data.status === 1"
                            class="badge bg-success"
                            >{{ $t("Active") }}</span
                          >
                          <span v-else class="badge bg-danger">{{
                            $t("Inactive")
                          }}</span>
                        </td>
                        <td class="text-right">
                          {{ data.date | moment("Do MMM, YYYY") }}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="callout callout-danger mt-3 w-100" v-else>
                <h5>{{ $t("No loans are available yet!") }}</h5>
                <p>{{ $t("You haven't added any loan for this authority. After adding loans you will see the list here.") }}</p>
              </div>
            </div>

            <!-- /.row -->
            <div class="row mt-4">
              <div class="col-lg-12 col-xl-8">
                <div class="table-responsive table-custom">
                  <table class="table">
                    <thead>
                      <tr>
                        <th v-if="allData.createdAt">
                          {{ $t("Created At") }}
                        </th>
                        <th v-if="allData.note">{{ $t("Note") }}</th>
                        <th class="text-right">{{ $t("Status") }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td v-if="allData.createdAt">
                          {{ allData.createdAt | moment("Do MMM, YYYY") }}
                        </td>
                        <td v-if="allData.note">{{ allData.note }}</td>
                        <td class="text-right">
                          <span
                            v-if="allData.status === 1"
                            class="badge bg-success"
                            >{{ $t("Active") }}</span
                          >
                          <span v-else class="badge bg-danger">{{
                            $t("Inactive")
                          }}</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <!-- /.col -->
              <div class="col-lg-12 col-xl-4 text-lg-right">
                <div class="table-responsive table-custom table-border-y-0">
                  <table class="table">
                    <tbody>
                      <tr class="bg-sub-light text-bold">
                        <th>{{ $t("CC Limit") }}:</th>
                        <td>{{ allData.ccLimit | withCurrency }}</td>
                      </tr>
                      <tr>
                        <th>{{ $t("CC Loan Taken") }}:</th>
                        <td>
                          <span class="minus-sign">-</span
                          >{{ allData.ccLoanTaken | withCurrency }}
                        </td>
                      </tr>
                      <tr class="bg-indigo-light">
                        <th>{{ $t("Available CC Loan") }}:</th>
                        <td>
                          <span class="equal-sign">=</span>
                          {{ allData.availableAmount | withCurrency }}
                        </td>
                      </tr>
                      <tr>
                        <th>
                          {{ $t("Total Loan") }}:<br />
                          <small>({{ $t("CC + Term") }})</small>
                        </th>
                        <td>{{ allData.totalLoan | withCurrency }}</td>
                      </tr>
                      <tr>
                        <th>{{ $t("Total Payable") }}:</th>
                        <td>{{ allData.totalPayable | withCurrency }}</td>
                      </tr>
                      <tr>
                        <th>{{ $t("Interest Paid") }}:</th>
                        <td>{{ allData.interestPaid | withCurrency }}</td>
                      </tr>
                      <tr>
                        <th>{{ $t("Total Paid") }}:</th>
                        <td>
                          <span class="minus-sign">-</span>
                          {{ allData.totalPaid | withCurrency }}
                        </td>
                      </tr>
                      <tr class="bg-red-light">
                        <th>{{ $t("Total Due") }}:</th>
                        <td>{{ allData.totalDue | withCurrency }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
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
import { mapGetters } from "vuex";
import axios from "axios";
import html2pdf from "html2pdf.js";

export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("View Loan Authority") };
  },
  data: () => ({
    breadcrumbsCurrent: "Authority Details",
    breadcrumbs: [
      {
        name: "Dashboard",
        url: "home",
      },
      {
        name: "Loan Authorities",
        url: "authorities.index",
      },
      {
        name: "Details",
        url: "",
      },
    ],
    allData: "",
    query: "",
    perPage: 10,
  }),
  computed: {
    ...mapGetters("operations", ["appInfo", "items", "loading", "pagination"]),
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

  created() {
    this.getLoanAuthority();
  },
  methods: {
    // get the Loan Authority
    async getLoanAuthority() {
      const { data } = await axios.get(
        window.location.origin +
          "/api/loan-authorities/" +
          this.$route.params.slug
      );
      this.allData = data.data;
    },
    // print
    printWindow() {
      window.print();
    },

    // display modal
    previewModal(image) {
      this.imagePath = image;
      if (this.showModal) {
        return (this.showModal = false);
      }
      return (this.showModal = true);
    },

    // download pdf
    generatePDF() {
      // Get the HTML content to be converted
      const element = document.getElementById("content-to-pdf");
      // Options for PDF generation
      const options = {
        margin: 5,
        filename: "Loan Authority-" + this.$route.params.slug + ".pdf",
        image: { type: "jpeg", quality: 0.98 },
        pagebreak: { mode: "avoid-all", before: "#page-break" },
        html2canvas: { scale: 2 },
        jsPDF: { unit: "mm", format: "a4", orientation: "landscape" },
      };
      // Generate PDF from HTML content
      html2pdf().from(element).set(options).save();
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
      let modelName = "LoanAuthority";
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
      let modelName = "LoanAuthority";
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
