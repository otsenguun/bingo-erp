<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->

    <div class="container-fluid">
      <!-- Main row -->
      <div v-if="isDemoMode" class="alert alert-danger">
        <strong class="text-capitalize"><i class="icon fas fa-ban"></i> Delete buttons are hidden in demo
          version.</strong><br />
        <strong class="text-capitalize"><i class="icon fas fa-ban"></i> Demo database will be cleared every
          two hours.</strong><br />
        <strong class="text-capitalize"><i class="icon fas fa-ban"></i> Email & SMS notifications are
          disabled in demo version.</strong>
      </div>

      <div v-if="$can('account-summery') && dashboardSummery" class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title mt-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                </svg>
                {{ $t( form.summeryType) }}
                {{ $t("Summary") }}
              </h3>
              <div class="card-tools">
                <select v-model="form.summeryType" @change="getSummery($event)" class="form-control" id="summeryType"
                  name="summeryType">
                  <option value="today" selected>
                    {{ $t("Today") }}
                  </option>
                  <option value="last_7_days">
                    {{ $t("Last 7 Days") }}
                  </option>
                  <option value="this_month">
                    {{ $t("This Month") }}
                  </option>
                  <option value="this_year">
                    {{ $t("This Year") }}
                  </option>
                </select>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-lg-3 col-6">
                  <div class="small-box bg-primary">
                    <div class="inner">
                      <h3>
                        {{ dashboardSummery.purchaseAmount | withCurrency }}
                      </h3>
                      <p>{{ $t("Purchase") }}</p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-truck-loading"></i>
                    </div>
                    <router-link :to="{ name: 'purchases.index' }" class="small-box-footer">
                      {{ $t("More info") }}
                      <i class="fas fa-arrow-circle-right"></i>
                    </router-link>
                  </div>
                </div>
                <div class="col-lg-3 col-6">
                  <div class="small-box bg-info">
                    <div class="inner">
                      <h3>
                        {{
                          dashboardSummery.purchaseReturnAmount | withCurrency
                        }}
                      </h3>
                      <p>{{ $t("Purchase Return") }}</p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-forward"></i>
                    </div>
                    <router-link :to="{ name: 'purchaseReturns.index' }" class="small-box-footer">
                      {{ $t("More info") }}
                      <i class="fas fa-arrow-circle-right"></i>
                    </router-link>
                  </div>
                </div>
                <div class="col-lg-3 col-6">
                  <div class="small-box bg-success">
                    <div class="inner">
                      <h3>{{ dashboardSummery.salesAmount | withCurrency }}</h3>
                      <p>{{ $t("Sales") }}</p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-shopping-bag"></i>
                    </div>
                    <router-link :to="{ name: 'invoices.index' }" class="small-box-footer">
                      {{ $t("More info") }}
                      <i class="fas fa-arrow-circle-right"></i>
                    </router-link>
                  </div>
                </div>
                <div class="col-lg-3 col-6">
                  <div class="small-box bg-gray">
                    <div class="inner">
                      <h3>
                        {{ dashboardSummery.salesReturnAmount | withCurrency }}
                      </h3>
                      <p>{{ $t("Sales Return") }}</p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-backward"></i>
                    </div>
                    <router-link :to="{ name: 'invoiceReturns.index' }" class="small-box-footer">
                      {{ $t("More info") }}
                      <i class="fas fa-arrow-circle-right"></i>
                    </router-link>
                  </div>
                </div>
                <div class="col-lg-3 col-6">
                  <div class="small-box bg-olive">
                    <div class="inner">
                      <h3>
                        {{ dashboardSummery.paymentReceived | withCurrency }}
                      </h3>
                      <p>
                        {{ $t("Client Payment") }}
                      </p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <router-link :to="{ name: 'invoicePayments.index' }" class="small-box-footer">
                      {{ $t("More info") }}
                      <i class="fas fa-arrow-circle-right"></i>
                    </router-link>
                  </div>
                </div>
                <div class="col-lg-3 col-6">
                  <div class="small-box bg-indigo">
                    <div class="inner">
                      <h3>{{ dashboardSummery.paymentSent | withCurrency }}</h3>
                      <p>{{ $t("Supplier Payment") }}</p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <router-link :to="{ name: 'purchasePayments.index' }" class="small-box-footer">
                      {{ $t("More info") }}
                      <i class="fas fa-arrow-circle-right"></i>
                    </router-link>
                  </div>
                </div>
                <div class="col-lg-3 col-6">
                  <div class="small-box bg-danger">
                    <div class="inner">
                      <h3>
                        {{ dashboardSummery.expenseAmount | withCurrency }}
                      </h3>
                      <p>{{ $t("Expense") }}</p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-calculator"></i>
                    </div>
                    <router-link :to="{ name: 'expenses.index' }" class="small-box-footer">
                      {{ $t("More info") }}
                      <i class="fas fa-arrow-circle-right"></i>
                    </router-link>
                  </div>
                </div>
                <div class="col-lg-3 col-6">
                  <div class="small-box bg-navy">
                    <div class="inner">
                      <h3>
                        {{ dashboardSummery.balanceTransfer | withCurrency }}
                      </h3>
                      <p>
                        {{ $t("Balance Transfers") }}
                      </p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-exchange-alt"></i>
                    </div>
                    <router-link :to="{ name: 'transferBalances.index' }" class="small-box-footer">
                      {{ $t("More info") }}
                      <i class="fas fa-arrow-circle-right"></i>
                    </router-link>
                  </div>
                </div>
                <div class="col-lg-3 col-6">
                  <div class="small-box bg-success">
                    <div class="inner">
                      <h3>
                        {{ dashboardSummery.totalStockQuantity }}
                      </h3>
                      <p>{{ $t("Total Stock Quantity") }}
                         <span v-tooltip="$t('Till Now')">
                            <i class="fas fa-info"></i>
                          </span>
                      </p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-backward"></i>
                    </div>
                    <router-link :to="{ name: 'inventory.index' }" class="small-box-footer">
                      {{ $t("More info") }}
                      <i class="fas fa-arrow-circle-right"></i>
                    </router-link>
                  </div>
                </div>
                <div class="col-lg-3 col-6">
                  <div class="small-box bg-info">
                    <div class="inner">
                      <h3>
                        {{ dashboardSummery.totalStockValue }}
                      </h3>
                      <p>{{ $t("Total Stock Value") }}
                        <span v-tooltip="$t('Till Now')">
                          <i class="fas fa-info"></i>
                        </span>
                      </p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-backward"></i>
                    </div>
                    <router-link :to="{ name: 'inventory.index' }" class="small-box-footer">
                      {{ $t("More info") }}
                      <i class="fas fa-arrow-circle-right"></i>
                    </router-link>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="$can('top-selling-products') || $can('recent-activities')" class="row">
        <div v-if="$can('top-selling-products') &&
          pieChartOptions.legend.data &&
          pieChartOptions.legend.data.length > 0
          " class="col-md-12 col-lg-4">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                {{ $t("Top Selling Products") }} ({{ year }})
              </h3>
            </div>
            <div class="card-body">
              <template>
                <v-chart class="chart" :option="pieChartOptions" />
              </template>
            </div>
          </div>
        </div>
        <div v-if="$can('recent-activities')" class="col-md-12" :class="$can('top-selling-products') &&
          pieChartOptions.legend.data &&
          pieChartOptions.legend.data.length > 0
          ? 'col-lg-8'
          : 'col-lg-12'
          ">
          <RecentActivities />
        </div>
      </div>

      <div v-if="$can('payment-sent-vs-payment-received') || $can('top-clients')" class="row">
        <div v-if="$can('payment-sent-vs-payment-received') &&
          lineChartOptions.series[0].data &&
          lineChartOptions.series[0].data.length > 0
          " class="col-md-12 col-lg-8">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                {{ $t("Payment Sent vs Payment Received") }} ({{ year }})
                <a href="#" class="badge badge-info ml-2" v-tooltip="$t('Payment Sent = Supplier Payment + Loan Payment <br/> Payment Received = Client Payment + Loan Recevied')">
                  <i class="fas fa-info"></i>
                </a>
              </h3>
            </div>
            <div class="card-body">
              <template>
                <v-chart class="chart" :option="lineChartOptions" />
              </template>
            </div>
          </div>
        </div>
        <div v-if="$can('top-clients')" class="col-md-12" :class="$can('payment-sent-vs-payment-received') ? 'col-lg-4' : 'col-lg-12'
          ">
          <TopClients />
        </div>
      </div>

      <div v-if="$can('stock-alert') || $can('sales-vs-purchases')" class="row">
        <div v-if="$can('stock-alert')" class="col-md-12 col-lg-6">
          <StockAlert />
        </div>
        <div v-if="$can('sales-vs-purchases') &&
          barChartOptions.series[0].data &&
          barChartOptions.series[0].data.length > 0
          " class="col-md-12 col-lg-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                {{ $t("Sales vs Purchases") }} ({{ year }})
                <a href="#" class="badge badge-info ml-2" v-tooltip="$t('Monthly sales & purchases after deduction of the cost of return products.')">
                  <i class="fas fa-info"></i>
                </a>
              </h3>
            </div>
            <div class="card-body">
              <template>
                <v-chart class="chart" :option="barChartOptions" />
              </template>
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
import { use } from "echarts/core";
import "echarts/lib/component/grid";
import { PieChart } from "echarts/charts";
import { BarChart } from "echarts/charts";
import { LineChart } from "echarts/charts";
import VChart, { THEME_KEY } from "vue-echarts";
import { CanvasRenderer } from "echarts/renderers";

import {
  TitleComponent,
  TooltipComponent,
  LegendComponent,
} from "echarts/components";

use([
  CanvasRenderer,
  PieChart,
  LineChart,
  BarChart,
  TitleComponent,
  TooltipComponent,
  LegendComponent,
]);

export default {
  middleware: "auth",
  metaInfo() {
    return { title: this.$t("Dashboard") };
  },
  components: {
    VChart,
  },
  provide: {
    [THEME_KEY]: "vintage",
  },

  data: () => ({
    paymentStatus: null,
    paymentMessage: null,
    isDemoMode: window.config.isDemoMode,
    breadcrumbsCurrent: "Dashboard",
    breadcrumbs: [
      {
        name: "Dashboard",
        url: "",
      },
    ],
    form: new Form({
      summeryType: "today",
    }),
    year: new Date().getFullYear(),
    className: "col-lg-4",
    allData: "",
    topClients: "",
    dashboardSummery: "",
    loading: false,

    // options for pie chart(Top selling products)
    pieChartOptions: {
      responsive: true,
      tooltip: {
        trigger: "item",
        formatter: "{a} <br/>{b} : {c} ({d}%)",
      },
      legend: {
        orient: "vertical",
        left: "left",
        data: [],
      },
      series: [
        {
          name: "Top Selling Products",
          type: "pie",
          radius: "55%",
          center: ["50%", "60%"],
          data: [],
          emphasis: {
            itemStyle: {
              shadowBlur: 10,
              shadowOffsetX: 0,
              shadowColor: "rgba(0, 0, 0, 0.5)",
            },
          },
        },
      ],
    },

    // options for line chart(payment sent & receive)
    lineChartOptions: {
      responsive: true,
      tooltip: {
        trigger: "axis",
      },
      legend: {
        data: ["Payment Sent", "Payment Received"],
      },
      xAxis: {
        type: "category",
        boundaryGap: false,
        data: [],
      },
      yAxis: {
        type: "value",
      },
      series: [
        {
          name: "Payment Sent",
          type: "line",
          smooth: true,
          data: [],
        },
        {
          name: "Payment Received",
          type: "line",
          smooth: true,
          data: [],
        },
      ],
      color: ["#dc3545", "#28a745"],
    },

    // options for bar chart(purcahses vs sales)
    barChartOptions: {
      responsive: true,
      tooltip: {
        trigger: "axis",
        axisPointer: {
          type: "shadow",
        },
      },
      legend: {
        data: ["Purchases", "Sales"],
      },
      grid: {
        left: "3%",
        right: "4%",
        bottom: "3%",
        containLabel: true,
      },
      xAxis: {
        type: "category",
        data: [],
      },
      yAxis: {
        type: "value",
        boundaryGap: [0, 0.01],
      },
      series: [
        {
          name: "Purchases",
          type: "bar",
          data: [],
        },
        {
          name: "Sales",
          type: "bar",
          data: [],
        },
      ],
      color: ["#007bff", "#28a745"],
    },
  }),

  mounted() {
    // to show payment status
    const urlParams = new URLSearchParams(window.location.search);
    this.paymentStatus = urlParams.get('payment_status');
    this.paymentMessage = urlParams.get('message');
    if (this.paymentStatus && this.paymentMessage) {
      this.showPaymentNotification(this.paymentStatus, this.paymentMessage);
    }
  },

  created() {
    this.loading = true;
    if (this.$can("account-summery")) {
      this.getSummery();
    }
    if (this.$can("top-selling-products")) {
      this.getTopSellingProducts();
    }
    if (this.$can("payment-sent-vs-payment-received")) {
      this.getMonthlySentAndReceived();
    }
    if (this.$can("top-clients")) {
      this.getTopClients();
    }
    if (this.$can("sales-vs-purchases")) {
      this.getMonthlySalesAndPurchases();
    }
    this.loading = false;
  },
  methods: {
    showPaymentNotification(status, message) {
      if (status === 'success') {
        toast.fire({
            type: 'success',
            title: message,
        })
      } else if (status === 'cancelled') {
        toast.fire({
            type: 'error',
            title: message,
        })
      }
    },

    // get summery
    async getSummery(event) {
      let summerType = "Today";
      if (event) {
        summerType = event.target.value;
      }
      const { data } = await axios.get(
        window.location.origin + "/api/dashboard-summery/" + summerType
      );
      this.dashboardSummery = data;
    },

    // get top-selling products
    async getTopSellingProducts() {
      const { data } = await axios.get(
        window.location.origin + "/api/dashboard/top-selling-products"
      );
      this.pieChartOptions.legend.data = data.names;
      this.pieChartOptions.series[0].data = data.products;
    },

    // get top clients
    async getTopClients() {
      const { data } = await axios.get(
        window.location.origin + "/api/dashboard/top-clients"
      );
      this.topClients = data;
    },

    // get monthly sent & received
    async getMonthlySentAndReceived() {
      const { data } = await axios.get(
        window.location.origin + "/api/dashboard/monthly-payment-sent-received"
      );
      this.lineChartOptions.xAxis.data = data.months;
      this.lineChartOptions.series[0].data = data.sent;
      this.lineChartOptions.series[1].data = data.received;
    },

    // get monthly sales & purchases
    async getMonthlySalesAndPurchases() {
      const { data } = await axios.get(
        window.location.origin + "/api/dashboard/monthly-sales-purchases"
      );
      this.barChartOptions.xAxis.data = data.months;
      this.barChartOptions.series[0].data = data.purchase;
      this.barChartOptions.series[1].data = data.sales;
    },
  },
};
</script>
<style scoped>
.chart {
  height: 400px;
}
</style>
