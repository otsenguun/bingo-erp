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
                  @click="getPayroll"
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
              v-if="$can('payroll-edit')"
              :to="{
                name: 'payroll.edit',
                params: { slug: allData.slug },
              }"
              class="btn btn-info"
            >
              <i class="fas fa-edit" /> {{ $t("Edit") }}
            </router-link>
            <router-link
              :to="{ name: 'payroll.index' }"
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
                <div v-if="allData.employee && allData.employee.department">
                  <h5>{{ $t("Employee Details") }}</h5>
                  <strong>{{ $t("Emp ID") }}:</strong>
                  {{ allData.employee.empID | withPrefix(employeePrefix)
                  }}<br />
                  <strong>{{ $t("Emp Name") }}:</strong>
                  {{ allData.employee.name }}<br />
                  <strong>{{ $t("Department") }}:</strong>
                  {{ allData.employee.department.name }}<br />
                  <strong>{{ $t("Designation") }}:</strong>
                  {{ allData.employee.designation }}<br />
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- Table row -->
            <div
              class="row"
              v-if="
                allData.employee &&
                allData.transaction &&
                allData.transaction.cashbook_account
              "
            >
              <div class="col-12">
                <strong class="mt-3 mb-2 d-block"
                  >{{ $t("Payroll Details") }}:</strong
                >
                <div class="table-responsive table-custom">
                  <table class="table">
                    <thead>
                      <tr>
                        <th v-if="allData.image">{{ $t("Image") }}</th>
                        <th v-if="allData.employee">
                          {{ $t("Employee") }}
                        </th>
                        <th v-if="allData.salaryMonth">
                          {{ $t("Month") }}
                        </th>
                        <th v-if="allData.transaction">
                          {{ $t("Paid") }}
                        </th>
                        <th
                          v-if="
                            allData.transaction &&
                            allData.transaction.cashbook_account
                          "
                        >
                          {{ $t("Account") }}
                        </th>
                        <th v-if="allData.transaction.cheque_no">
                          {{ $t("Cheque No") }}
                        </th>
                        <th v-if="allData.deductionReason">
                          {{ $t("Deduction Reason") }}
                        </th>
                        <th v-if="allData.note">{{ $t("Note") }}</th>
                        <th>{{ $t("Status") }}</th>
                        <th v-if="allData.salaryDate">
                          {{ $t("Date") }}
                        </th>
                        <th v-if="allData.createdBy" class="text-right">
                          {{ $t("Created By") }}
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td v-if="allData.image">
                          <a href="#" id="show-modal" @click="showModal = true">
                            <img
                              :src="allData.image"
                              class="rounded preview-sm"
                              loading="lazy"
                            />
                          </a>
                        </td>
                        <td v-if="allData.employee">
                          {{ allData.employee.name }}
                        </td>
                        <td v-if="allData.salaryMonth">
                          {{ allData.salaryMonth }}
                        </td>
                        <td v-if="allData.transaction">
                          {{ allData.transaction.amount | withCurrency }}
                        </td>
                        <td
                          v-if="
                            allData.transaction &&
                            allData.transaction.cashbook_account
                          "
                        >
                          {{
                            allData.transaction.cashbook_account.account_number
                          }}
                        </td>
                        <td v-if="allData.transaction.cheque_no">
                          {{ allData.transaction.cheque_no }}
                        </td>
                        <td v-if="allData.deductionReason">
                          {{ allData.deductionReason }}
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
                        <td v-if="allData.salaryDate">
                          {{ allData.salaryDate | moment("Do MMM, YYYY") }}
                        </td>
                        <td class="text-right">
                          <span v-if="allData.createdBy">{{
                            allData.createdBy
                          }}</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- /.row -->
            <div class="row mb-5 mt-4">
              <div
                class="offset-xl-8 col-lg-12 col-xl-4 text-xl-right"
                v-if="allData.employee && allData.transaction"
              >
                <div class="table-responsive table-custom table-border-y-0">
                  <table class="table">
                    <tbody>
                      <tr class="bg-gray-light">
                        <th>{{ $t("Present Salary") }}:</th>
                        <td>
                          {{ allData.employee.totalSalary | withCurrency }}
                        </td>
                      </tr>
                      <tr>
                        <th>{{ $t("Deduction Amount") }}:</th>
                        <td>
                          <span class="minus-sign">-</span>
                          {{ allData.deductionAmount | withCurrency }}
                        </td>
                      </tr>
                      <tr>
                        <th>{{ $t("Mobile Bill") }}:</th>
                        <td>
                          <span class="plus-sign">+</span>
                          {{ allData.mobileBill | withCurrency }}
                        </td>
                      </tr>
                      <tr>
                        <th>{{ $t("Food Bill") }}:</th>
                        <td>
                          <span class="plus-sign">+</span>
                          {{ allData.foodBill | withCurrency }}
                        </td>
                      </tr>
                      <tr>
                        <th>{{ $t("Bonus") }}:</th>
                        <td>
                          <span class="plus-sign">+</span>
                          {{ allData.bonus | withCurrency }}
                        </td>
                      </tr>
                      <tr>
                        <th>{{ $t("Commission") }}:</th>
                        <td>
                          <span class="plus-sign">+</span>
                          {{ allData.commission | withCurrency }}
                        </td>
                      </tr>
                      <tr>
                        <th>{{ $t("Advance") }}:</th>
                        <td>
                          <span class="plus-sign">+</span>
                          {{ allData.advance | withCurrency }}
                        </td>
                      </tr>
                      <tr>
                        <th>{{ $t("Festival Bonus") }}:</th>
                        <td>
                          <span class="plus-sign">+</span>
                          {{ allData.festivalBonus | withCurrency }}
                        </td>
                      </tr>
                      <tr>
                        <th>{{ $t("Travel Allowance(TA)") }}:</th>
                        <td>
                          <span class="plus-sign">+</span>
                          {{ allData.travelAllowance | withCurrency }}
                        </td>
                      </tr>
                      <tr>
                        <th>{{ $t("Others") }}:</th>
                        <td>
                          <span class="plus-sign">+</span>
                          {{ allData.others | withCurrency }}
                        </td>
                      </tr>
                      <tr v-if="allData.transaction" class="bg-indigo-light">
                        <th>{{ $t("Total") }}:</th>
                        <td>
                          <span class="equal-sign">=</span>
                          {{ allData.transaction.amount | withCurrency }}
                        </td>
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

    <!-- use the modal component, pass in the prop -->
    <Modal v-if="showModal" @close="showModal = false">
      <h5 slot="header">{{ $t("Attached Image Preview") }}</h5>
      <div class="w-100" slot="body">
        <img :src="allData.image" class="rounded img-fluid" loading="lazy" />
      </div>
    </Modal>
  </div>
</template>

<script>
import axios from "axios";
import { mapGetters } from "vuex";
import html2pdf from "html2pdf.js";

export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("Payroll Details") };
  },
  data: () => ({
    breadcrumbsCurrent: "Payroll Details",
    breadcrumbs: [
      {
        name: "Dashboard",
        url: "home",
      },
      {
        name: "Payroll",
        url: "payroll.index",
      },
      {
        name: "Details",
        url: "",
      },
    ],
    showModal: false,
    allData: "",
    employeePrefix: "",
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
    this.getPayroll();
    this.employeePrefix = this.appInfo.employeePrefix;
  },
  methods: {
    // get the payroll
    async getPayroll() {
      const { data } = await axios.get(
        window.location.origin + "/api/payroll/" + this.$route.params.slug
      );
      this.allData = data.data;
    },

    // print
    printWindow() {
      window.print();
    },

    // download pdf
    generatePDF() {
      // Get the HTML content to be converted
      const element = document.getElementById("content-to-pdf");
      // Options for PDF generation
      const options = {
        margin: 5,
        filename: "Payroll-" + this.$route.params.slug + ".pdf",
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
      let modelName = "Payroll";
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
      let modelName = "Payroll";
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
