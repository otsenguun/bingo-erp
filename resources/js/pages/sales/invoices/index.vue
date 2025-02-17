<template>
  <div class="mb-50">
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card custom-card w-100">
          <div class="card-header setings-header">
            <div class="col-xl-4 col-4">
              <h3 class="card-title">
                {{ $t("Invoices") }}
              </h3>
            </div>
            <div class="col-xl-8 col-8 float-right text-right">
              <div class="btn-group c-w-100">
                <a @click="refreshTable()" href="#" v-tooltip="'Refresh'" class="btn btn-success">
                  <i class="fas fa-sync"></i>
                </a>
                <a :href="exportUrl" v-tooltip="$t('Export to Excel')" class="btn btn-info">
                  <i class="fa fa-arrow-circle-down"></i>
                </a>
                <a href="/invoices/pdf" v-tooltip="$t('Export to PDF')" class="btn btn-secondary">
                  <i class="fas fa-file-export"></i>
                </a>
                <a @click="print" v-tooltip="$t('Print Table')" class="btn btn-info">
                  <i class="fas fa-print"></i>
                </a>
                <router-link v-if="$can('invoice-create')" :to="{ name: 'invoices.create' }" class="btn btn-primary">
                  {{ $t("Create") }}
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
              <div class="col-6 col-xl-8 mb-2 text-right">
                <date-range-picker ref="picker" opens="left" :locale-data="locale" :minDate="minDate" :maxDate="maxDate"
                  :singleDatePicker="false" :showWeekNumbers="false" :showDropdowns="true" :autoApply="true"
                  v-model="dateRange" @update="updateValues" :linkedCalendars="true" class="c-w-100">
                  <template v-slot:input="picker" style="min-width: 350px">
                    {{ picker.startDate | startDate }} -
                    {{ picker.endDate | endDate }}
                  </template>
                </date-range-picker>
              </div>
            </div>
            <table-loading v-show="loading" />
            <div class="table-responsive table-custom mt-3" id="printMe">
              <table class="table">
                <thead>
                  <tr>
                    <th>{{ $t("#") }}</th>
                    <th>{{ $t("Invoice No") }}</th>
                    <th>{{ $t("Invoice Date") }}</th>
                    <th>{{ $t("Client") }}</th>

                    <th>{{ $t("Net Total") }}</th>
                    <th>{{ $t("Total Paid") }}</th>
                    <th>{{ $t("Total Due") }}</th>
                    <th>{{ $t("Status") }}</th>
                    <th v-if="$can('invoice-view') ||
                      $can('invoice-edit') ||
                      $can('invoice-delete')
                      " class="text-right no-print">
                      {{ $t("Action") }}
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
                    <td>
                      <router-link v-if="$can('invoice-view')" :to="{
                        name: 'invoices.show',
                        params: { slug: data.slug },
                      }">
                        {{ data.invoiceNo | withPrefix(prefix) }}
                      </router-link>
                      <span v-else>{{
                        data.invoiceNo | withPrefix(prefix)
                      }}</span>
                    </td>
                    <td>
                      <span v-if="data.invoiceDate">{{
                        data.invoiceDate | moment("Do MMM, YYYY")
                      }}</span>
                    </td>
                    <td>{{ data.client }}</td>
                    <td>{{ data.invoiceTotal | withCurrency }}</td>
                    <td>{{ data.totalPaid | withCurrency }}</td>
                    <td>{{ data.due | withCurrency }}</td>
                    <td>
                      <span v-if="data.status === 1" class="badge bg-success">{{
                        $t("Active")
                      }}</span>
                      <span v-else class="badge bg-danger">{{
                        $t("Inactive")
                      }}</span>
                    </td>
                    <td v-if="$can('invoice-view') ||
                        $can('invoice-edit') ||
                        $can('invoice-delete')
                        " class="text-right no-print">
                      <div class="btn-group">
                        <a v-if="$can('invoice-view') && data.due > 0" v-tooltip="$t('Add Payment?')"
                          class="btn btn-secondary btn-sm" @click="handleModal(data)">
                          <i class="fas fa-money-check-alt" />
                        </a>
                        <router-link v-if="$can('invoice-view')" v-tooltip="$t('View')" :to="{
                          name: 'invoices.show',
                          params: { slug: data.slug },
                        }" class="btn btn-primary btn-sm">
                          <i class="fas fa-eye" />
                        </router-link>
                        <router-link v-if="$can('invoice-edit')" v-tooltip="$t('Edit')" :to="{
                          name: 'invoices.edit',
                          params: { slug: data.slug },
                        }" class="btn btn-info btn-sm">
                          <i class="fas fa-edit" />
                        </router-link>
                        <a v-if="$can('invoice-delete')" v-tooltip="$t('Delete')" href="#"
                          class="btn btn-danger btn-sm" @click="deleteData(data.slug)">
                          <i class="fas fa-trash" />
                        </a>
                      </div>
                    </td>
                  </tr>
                  <tr v-show="!loading && !items.length">
                    <td colspan="13">
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
      </div>
    </div>
    <Modal v-if="showModal" @close="showModal = false">
      <h5 slot="header">
        {{ $t("Create invoice payment") }} :
        {{ form.selectedInvoice.invoiceNo | withPrefix(prefix) }}
      </h5>
      <div slot="body" class="row">
        <form role="form" @submit.prevent="savePayment" @keydown="form.onKeydown($event)" class="w-100">
          <div class="row">
            <div class="form-group col-md-6">
              <label for="clientInvoiceTotal">{{
                $t("Invoice Total")
              }}</label>
              <input type="text" class="form-control" readonly v-model="form.selectedInvoice.invoiceTotal" />
            </div>
            <div class="form-group col-md-6">
              <label for="clientInvoiceDue">{{
                $t("Invoice Due")
              }}</label>
              <input type="text" class="form-control" readonly v-model="form.selectedInvoice.due" />
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-4">
              <label for="paidAmount">{{ $t("Paid Amount") }}</label>
              <input type="number" step="any" class="form-control" :placeholder="$t('Enter an amount')"
                required min="1" v-model="form.paidAmount" :max="form.selectedInvoice.due" />
            </div>
            <div class="form-group col-md-8">
              <label for="account">{{ $t("Account") }}
                <span class="required">*</span></label>
              <v-select v-model="form.account" :options="accounts" label="label"
                :class="{ 'is-invalid': form.errors.has('account') }" name="account"
                :placeholder="$t('Select an account')">
                 <template slot="option" slot-scope="option">
                    <img :src="option.image" style="width: 30px; height: 30px;" />
                    {{ option.label }}
                </template>
              </v-select>
              <has-error :form="form" field="account" />
            </div>
          </div>

          <div class="row">
            <div class="form-group col-md-6">
              <label for="chequeNo">{{ $t("Cheque No") }}</label>
              <input type="text" v-model="form.chequeNo" class="form-control"
                :class="{ 'is-invalid': form.errors.has('chequeNo') }" id="chequeNo"
                :placeholder="$t('Enter a cheque number')" />
              <has-error :form="form" field="chequeNo" />
            </div>
            <div class="form-group col-md-6">
              <label for="receiptNo">{{ $t("Receipt No") }}</label>
              <input type="text" v-model="form.receiptNo" class="form-control"
                :class="{ 'is-invalid': form.errors.has('receiptNo') }" id="receiptNo"
                :placeholder="$t('Enter a receipt no')" />
              <has-error :form="form" field="receiptNo" />
            </div>
            <div class="form-group col-md-6">
              <label for="paymentDate">{{ $t("Payment Date") }}</label>
              <input id="paymentDate" v-model="form.paymentDate" type="date" class="form-control"
                :class="{ 'is-invalid': form.errors.has('paymentDate') }" name="paymentDate" />
              <has-error :form="form" field="paymentDate" />
            </div>
            <div class="form-group col-md-6">
              <label for="status">{{ $t("Status") }}</label>
              <select id="status" v-model="form.status" class="form-control"
                :class="{ 'is-invalid': form.errors.has('status') }">
                <option value="1">{{ $t("Active") }}</option>
                <option value="0">{{ $t("Inactive") }}</option>
              </select>
              <has-error :form="form" field="status" />
            </div>
          </div>
          <div class="form-group">
            <label for="note">{{ $t("Note") }}</label>
            <textarea id="note" v-model="form.note" class="form-control"
              :class="{ 'is-invalid': form.errors.has('note') }" :placeholder="$t('Write your note here!')" />
            <has-error :form="form" field="note" />
          </div>
          <div class="form-group col-12 d-flex flex-wrap">
            <div class="pr-5">
              <toggle-button v-model="form.isSendEmail" :disabled="isDemoMode"/>
              {{ $t("Send Email Notification") }}
            </div>
          </div>
          <div class="form-group col-12 d-flex flex-wrap">
            <div class="pr-5">
              <toggle-button v-model="form.isSendSMS" :disabled="isDemoMode"/>
              {{ $t("Send SMS Notification") }}
            </div>
          </div>
          <v-button :loading="form.busy" class="btn btn-primary">
            <i class="fas fa-save" /> {{ $t("Save") }}
          </v-button>
        </form>
      </div>
    </Modal>
  </div>
</template>

<script>
import Form from "vform";
import axios from "axios";
import moment from "moment";
import i18n from "~/plugins/i18n";
import { mapGetters } from "vuex";
import DateRangePicker from "vue2-daterange-picker";
import { ToggleButton } from "vue-js-toggle-button";

export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("Invoices") };
  },
  components: {
    DateRangePicker,
    ToggleButton,
  },
  data: () => ({
    isDemoMode: window.config.isDemoMode,
    breadcrumbsCurrent: "Invoices",
    breadcrumbs: [
      {
        name: "Dashboard",
        url: "home",
      },
      {
        name: "Sales",
        url: "",
      },
      {
        name: "Invoices",
        url: "",
      },
    ],
    showModal: false,
    perPage: 10,
    query: "",
    prefix: "",
    minDate: moment(new Date("01-01-2021")).format("YYYY-MM-DD"),
    maxDate: moment().add(1, "days").format("YYYY-MM-DD"),
    dateRange: {
      startDate: "",
      endDate: "",
    },
    locale: {
      direction: "ltr",
      format: "YYYY-MM-DD",
      separator: " - ",
      applyLabel: "Apply",
      cancelLabel: "Cancel",
      weekLabel: "W",
      customRangeLabel: "Custom Range",
      daysOfWeek: moment.weekdaysMin(),
      monthNames: moment.monthsShort(),
      firstDay: 1,
    },
    accounts: [],
    form: new Form({
      selectedInvoice: "",
      paidAmount: 1,
      invoice_id: "",
      paymentDate: new Date().toISOString().slice(0, 10),
      date: new Date().toISOString().slice(0, 10),
      account: "",
      chequeNo: "",
      receiptNo: "",
      note: "",
      netTotal: 0,
      status: 1,
      isSendEmail: false,
      isSendSMS: false,
    }),
  }),
  filters: {
    startDate(val) {
      return val ? moment(val).format("YYYY-MM-DD") : i18n.t("From");
    },
    endDate(val) {
      return val ? moment(val).format("YYYY-MM-DD") : i18n.t("To");
    },
  },
  // Map Getters
  computed: {
    ...mapGetters("operations", ["items", "loading", "pagination", "appInfo"]),
    exportUrl() {
      // Create a dynamic export URL with query parameters
      return `/invoices/export/export?start_date=${this.dateRange.startDate}&end_date=${this.dateRange.endDate}&term=${this.query}`;
    },
  },
  watch: {
    // watch search data
    query: function (newQ) {
      if (newQ === "") {
        if (this.dateRange.startDate && this.dateRange.endDate) {
          this.searchData();
        } else {
          this.getData();
        }
      } else {
        this.searchData();
      }
    },
  },
  created() {
    this.getData();
    this.getAccounts();
    this.prefix = this.appInfo.invoicePrefix;
  },
  methods: {
    // get accounts
    async getAccounts() {
      const { data } = await axios.get(
        window.location.origin + "/api/all-accounts"
      );
      this.accounts = data.data;

      // assign default account
      if (this.accounts && this.accounts.length > 0) {
        let defaultAccountSlug = this.appInfo.defaultAccountSlug;
        this.form.account = this.accounts.find(
          (account) => account.slug == defaultAccountSlug
        );
      }
    },

    // filter data for selected date range
    async updateValues() {
      this.dateRange.startDate = moment(this.dateRange.startDate).format(
        "YYYY-MM-DD"
      );
      this.dateRange.endDate = moment(this.dateRange.endDate).format(
        "YYYY-MM-DD"
      );
      this.searchData();
    },

    // refresh table
    refreshTable() {
      this.query = "";
      this.dateRange.startDate = null;
      this.dateRange.endDate = null;
      this.query === "" ? this.getData() : this.searchData();
      setTimeout(
        function () {
          this.dateRange.startDate = "";
          this.dateRange.endDate = "";
        }.bind(this),
        500
      );
    },
    // update per page count
    updatePerPager() {
      this.pagination.current_page = 1;
      this.query === "" ? this.getData() : this.searchData();
    },
    // get data
    async getData() {
      this.$store.state.operations.loading = true;
      let currentPage = this.pagination ? this.pagination.current_page : 1;
      await this.$store.dispatch("operations/fetchData", {
        path: "/api/invoices?page=",
        currentPage: currentPage + "&perPage=" + this.perPage,
      });
    },

    // Pagination
    async paginate() {
      this.query === "" ? this.getData() : this.searchData();
    },

    // Reset pagination
    async resetPagination() {
      this.pagination.current_page = 1;
    },

    // search data
    async searchData() {
      this.$store.state.operations.loading = true;
      let currentPage = this.pagination ? this.pagination.current_page : 1;
      await this.$store.dispatch("operations/searchData", {
        path: "/api/invoices/search",
        term: this.query,
        currentPage: currentPage + "&perPage=" + this.perPage,
        startDate: this.dateRange.startDate,
        endDate: this.dateRange.endDate,
      });
    },

    // Reload after search
    async reload() {
      this.query = "";
      await this.searchData();
    },

    // print table
    async print() {
      await this.$htmlToPaper("printMe");
    },

    handleModal(item) {
      this.form.selectedInvoice = item;
      this.form.invoice_id = item.id;
      this.form.netTotal = item.due;
      this.showModal = true;
    },

    // save  payment
    async savePayment() {
      await this.form
        .post(window.location.origin + "/api/invoices-pay")
        .then(() => {
          toast.fire({
            type: "success",
            title: this.$t("Invoice payment added successfully"),
          });
          this.$router.push({
            name: "invoices.show",
            params: { slug: this.form.selectedInvoice.slug },
          });
        })
        .catch(() => {
          toast.fire({ type: "error", title: this.$t("Opps...something went wrong") });
        });
    },

    // delete data
    async deleteData(slug) {
      Swal.fire({
        title: this.$t("Are you sure?"),
        text: this.$t("You will not be able to return to this!"),
        type: "warning",
        showCancelButton: true,
        confirmButtonText: this.$t("Confirm"),
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          this.$store
            .dispatch("operations/deleteData", {
              path: "/api/invoices/",
              slug: slug,
            })
            .then((response) => {
              if (response === true) {
                Swal.fire(
                  this.$t("Deleted!"),
                  this.$t("Deleted successfully."),
                  "success"
                );
              } else {
                Swal.fire(
                  this.$t("Failed!"),
                  this.$t("Sorry you can't delete this invoice!"),
                  "warning"
                );
              }
            });
        }
      });
    },
  },
};
</script>
