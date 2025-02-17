<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row no-print">
      <div class="col-lg-12">
        <div class="card">
          <!-- form start -->
          <form role="form" @submit.prevent="saveType" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row">
                <div class="form-group col-md-12">
                  <label for="reportType">{{
                    $t("Report Type")
                  }}</label>
                  <select id="reportType" v-model="form.reportType" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('reportType') }">
                    <option value="1">
                      {{ $t("Gross Profit/Loss") }}
                    </option>
                    <option value="0">
                      {{ $t("Net Profit/Loss") }}
                    </option>
                  </select>
                  <has-error :form="form" field="reportType" />
                </div>
              </div>
              <div class="col-12">
                <template :class="w - 100">
                  <date-range-picker :from="form.fromDate" :to="form.toDate" :panel="$route.query.panel"
                    @update="update" />
                </template>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div v-if="allData && allData.length > 0" class="row">
      <div class="col-lg-12">
        <div class="invoice p-3 mb-3">
          <div class="m-auto invoice-col">
            <CompanyInfo class="text-center" />
          </div>
          <hr />

          <div v-if="reportType === 1" class="row mt-5 position-relative">
            <table-loading v-show="loading" />
            <div class="table-responsive table-custom" v-if="loading == false">
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th>{{ $t("#") }}</th>
                    <th>{{ $t("Code") }}</th>
                    <th>{{ $t("Name") }}</th>
                    <th>{{ $t("Avg. Purchase Price") }}</th>
                    <th>{{ $t("Avg. Selling Price") }}</th>
                    <th>{{ $t("Sold Qty") }}</th>
                    <th class="text-right">
                      <strong>
                        <span class="green">{{ $t("Profit") }}</span> /
                        <span class="red">{{ $t("Loss") }}</span>
                      </strong>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(data, i) in grossItems" :key="i">
                    <td>{{ ++i }}</td>
                    <td>{{ data.itemCode | withPrefix(productPrefix) }}</td>
                    <td>{{ data.itemName }}</td>
                    <td>{{ data.avgPurchasePrice | withAbsoluteCurrency }}</td>
                    <td>{{ data.avgSalePrice | withAbsoluteCurrency }}</td>
                    <td>{{ data.currentQty }}</td>
                    <td class="text-right">
                      <strong>
                        <span v-if="data.profitOrLoss >= 0" class="green">{{
                          data.profitOrLoss
                        }}</span>
                        <span v-else class="red">{{ data.profitOrLoss }}</span>
                      </strong>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="5" class="text-right">
                      <strong>{{ $t("Total") }}</strong>
                    </td>
                    <td>
                      <strong>{{ totalQty }}</strong>
                    </td>
                    <td class="text-right">
                      <strong>{{
                        totalProfitOrLoss | withAbsoluteCurrency
                      }}</strong>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="table-responsive table-custom" v-if="loading == false">
              <table class="table">
                <tbody>
                  <tr>
                    <th>{{ $t("Total Sales (Average)") }}</th>
                    <td></td>
                    <td class="text-right">
                      <strong>{{ totalSold | withAbsoluteCurrency }}</strong>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="3"></td>
                  </tr>
                  <tr>
                    <th>{{ $t("Total Purchase (Average)") }}</th>
                    <td></td>
                    <td class="text-right">
                      <u><strong>({{ totalPurchased | withAbsoluteCurrency }})</strong></u>
                    </td>
                  </tr>
                  <tr :class="totalProfitOrLoss >= 0 ? 'green' : 'red'">
                    <th>
                      <span v-if="totalProfitOrLoss >= 0">{{
                        $t("Profit")
                      }}</span>
                      <span v-else>{{ $t("Loss") }}</span>
                    </th>
                    <td></td>
                    <td class="text-right">
                      <strong>{{
                        totalProfitOrLoss | withAbsoluteCurrency
                      }}</strong>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div v-else class="row mt-5 position-relative">
            <table-loading v-show="loading" />
            <div class="table-responsive table-custom">
              <table class="table text-left">
                <thead>
                  <tr class="success text-center">
                    <th colspan="3">
                      <h5>{{ $t("Income Statement") }} <br /></h5>
                    </th>
                  </tr>
                  <tr class="text-center">
                    <td colspan="3">
                      <strong>{{ $t("From") }}
                        {{ form.fromDate | moment("Do MMM, YYYY") }}
                        {{ $t("To") }}
                        {{ form.toDate | moment("Do MMM, YYYY") }}</strong>
                    </td>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th>{{ $t("Total Sales") }}</th>
                    <td></td>
                    <td class="text-right">
                      <strong>{{
                        allData[0].totalSales | withAbsoluteCurrency
                      }}</strong>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="3"></td>
                  </tr>
                  <tr>
                    <th>{{ $t("Cost of Goods Sold") }}</th>
                    <td></td>
                    <td class="text-right">
                      <u>
                        <strong>({{
                          allData[0].costOfGoodsSold | withAbsoluteCurrency
                        }})</strong>
                      </u>
                    </td>
                  </tr>
                  <tr>
                    <th colspan="3">
                      {{ $t("Inventory Adjustment") }}
                    </th>
                  </tr>
                  <tr class="text-success">
                    <td>{{ $t("Positive Adjusted") }}</td>
                    <td class="text-right">
                      <u><strong>{{
                        allData[0].posAdjustment | withAbsoluteCurrency
                      }}</strong></u>
                    </td>
                    <td></td>
                  </tr>
                  <tr class="text-danger">
                    <td>{{ $t("Negative Adjusted") }}</td>
                    <td class="text-right">
                      <u><strong>({{
                        allData[0].negAdjustment | withAbsoluteCurrency
                      }})</strong>
                      </u>
                    </td>
                    <td></td>
                  </tr>
                  <tr>
                    <th>{{ $t("Total Adjusted") }}</th>
                    <td></td>
                    <td class="text-right" :class="allData[0].totalAdjustment >= 0
                      ? 'text-success'
                      : 'text-danger'
                      ">
                      <strong v-if="allData[0].totalAdjustment >= 0">{{
                        allData[0].totalAdjustment | withAbsoluteCurrency
                      }}</strong>
                      <strong v-else>({{
                        allData[0].totalAdjustment | withAbsoluteCurrency
                      }})</strong>
                    </td>
                  </tr>
                  <tr>
                  <th>
                    {{ $t("Total Sell Return") }}
                  </th>
                  <td></td>
                  <td class="text-right">
                    <strong>{{
                      allData[0].totalSalesReturn | withAbsoluteCurrency
                    }}</strong>
                  </td>
                </tr>
                  <tr>
                    <th>
                      <span v-if="allData[0].grossProfitOrLoss > 0">{{
                        $t("Gross Profit")
                      }}</span>
                      <span v-else>{{ $t("Gross Loss") }}</span>
                    </th>
                    <td></td>
                    <td class="text-right">
                      <strong>{{
                        allData[0].grossProfitOrLoss | withAbsoluteCurrency
                      }}</strong>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="3"></td>
                  </tr>
                  <tr>
                    <th colspan="3">{{ $t("Operating Expenses") }}</th>
                  </tr>
                  <tr>
                    <td>{{ $t("Salaries") }}</td>
                    <td class="text-right">
                      <strong>{{
                        allData[0].payrollAmount | withAbsoluteCurrency
                      }}</strong>
                    </td>
                    <td></td>
                  </tr>
                  <tr>
                    <td>{{ $t("General Expenses") }}</td>
                    <td class="text-right">
                      <strong>{{
                        allData[0].expenseAmount | withAbsoluteCurrency
                      }}</strong>
                    </td>
                    <td></td>
                  </tr>
                  <tr>
                    <td>{{ $t("Loan Interest") }}</td>
                    <td class="text-right">
                      <strong>{{
                        allData[0].loanInterest | withAbsoluteCurrency
                      }}</strong>
                    </td>
                    <td></td>
                  </tr>
                  <tr>
                    <td>{{ $t("Asset Depreciation") }}</td>
                    <td class="text-right">
                      <strong>{{
                        allData[0].assetDepriciation | withAbsoluteCurrency
                      }}</strong>
                    </td>
                    <td></td>
                  </tr>
                  <tr>
                    <th>{{ $t("Total Expense") }}</th>
                    <td></td>
                    <td class="text-right">
                      <strong>({{
                        allData[0].totalExpense | withAbsoluteCurrency
                      }})</strong>
                    </td>
                  </tr>
                  <tr :class="allData[0].netProfitOrLoss >= 0
                        ? 'text-success'
                        : 'text-danger'
                      ">
                    <th>
                      <span v-if="allData[0].netProfitOrLoss >= 0">{{
                        $t("Net Profit")
                      }}</span>
                      <span v-else>{{ $t("Net Loss") }}</span>
                    </th>
                    <td></td>
                    <td class="text-right">
                      <strong>
                        {{ allData[0].netProfitOrLoss | withAbsoluteCurrency }}
                      </strong>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="row no-print mt-5">
            <div class="col-12">
              <router-link :to="{ name: 'inventory.index' }" class="btn btn-dark float-right">
                <i class="fas fa-long-arrow-alt-left" /> {{ $t("Back") }}
              </router-link>
              <a href="#" @click="printWindow" class="btn btn-default"><i class="fas fa-print"></i> {{ $t("Print")
              }}</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else-if="allData && allData.length <= 0" class="row">
      <div class="col-lg-12 col-xl-10 offset-xl-1">
        <div class="alert alert-secondary">
          <h5>
            <i class="icon fas fa-info"></i>
            {{ $t("Empty profit/loss!") }}
          </h5>
          {{ $t("Sorry, no profit/loss were found for your selected category and date range.") }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Form from "vform";
import { mapGetters } from "vuex";
import "vue-mj-daterangepicker/dist/vue-mj-daterangepicker.css";

export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("Profit/Loss Report") };
  },
  data: () => ({
    breadcrumbsCurrent: "Profit/Loss Report",
    breadcrumbs: [
      {
        name: "Dashboard",
        url: "home",
      },
      {
        name: "Reports",
        url: "",
      },
      {
        name: "Profit/Loss Report",
        url: "",
      },
    ],
    form: new Form({
      fromDate: String(new Date(Date.now() - 7 * 24 * 60 * 60 * 1000)),
      toDate: String(new Date()),
      reportType: 1,
    }),
    loading: false,
    allData: "",
    reportType: "",
    totalQty: 0,
    grossItems: [],
    totalProfitOrLoss: 0,
    totalPurchased: 0,
    totalSold: 0,
    date: new Date(),
    productPrefix: "",
  }),

  // Map Getters
  computed: {
    ...mapGetters("operations", ["appInfo"]),
  },

  methods: {
    // get filtered data
    async update(values) {
      this.form.fromDate = values.from;
      this.form.toDate = values.to;
      this.productPrefix = this.appInfo.productPrefix;
      this.loading = true;
      await this.form
        .post(window.location.origin + "/api/reports/profit-loss")
        .then((response) => {
          this.allData = response.data.reportData;
          this.reportType = response.data.type;
          if (this.reportType == 1) {
            this.calculateTotal(this.allData);
            this.grossItems = this.allData;
            this.grossItems.sort(this.sortProducts);
          }
          this.loading = false;
        })
        .catch(() => {
          toast.fire({ type: "error", title: this.$t("There was something wrong.") });
        });
    },

    sortProducts(a, b) {
      if (a.code < b.code) {
        return -1;
      }
      if (a.code > b.code) {
        return 1;
      }
      return 0;
    },

    // calculate total
    calculateTotal(items) {
      [
        this.totalQty,
        this.totalProfitOrLoss,
        this.totalPurchased,
        this.totalSold,
      ] = [0, 0, 0, 0];
      items.forEach((item) => {
        this.totalQty += item.currentQty;
        this.totalProfitOrLoss += item.profitOrLoss;
        this.totalPurchased += item.avgPurchasePrice * item.currentQty;
        this.totalSold += item.avgSalePrice * item.currentQty;
      });
      return;
    },

    // print
    printWindow() {
      window.print();
    },
  },
};
</script>
