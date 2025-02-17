<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->

    <div class="container-fluid">
      <!-- Main row -->
      <div v-if="isDemoMode" class="alert alert-danger">
        <strong><i class="icon fas fa-ban"></i> Delete buttons are hidden in demo
          version.</strong><br />
        <strong><i class="icon fas fa-ban"></i> Demo database will be cleared every
          two hours.</strong><br />
        <strong><i class="icon fas fa-ban"></i> Email & SMS notifications are
          disabled in demo version.</strong>
      </div>


      <div v-if="!hasDataInCentralPaymentTable" class="alert alert-info p-3 rounded">
        <strong>
          <i class="icon fas fa-info"></i> {{ $t('Please make sure to select your') }} <router-link :to="{ name: 'setup.general' }">
            {{ $t('default currency') }}
          </router-link> {{ $t('prior to starting any transactions. After a transaction is finalized, it is not possible to alter the default currency.') }}
        </strong>
      </div>


      <div v-if="dashboardSummary && $can('account-summery')" class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title mt-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                </svg>
                {{ $t(form.summaryType) }} {{ $t('Summary') }}
              </h3>
              <div class="card-tools">
                <select v-model="form.summaryType" @change="getSummery($event)" class="form-control" id="summaryType"
                  name="summaryType">
                  <option value="today" selected>
                    {{ $t('Today') }}
                  </option>
                  <option value="last_7_days">
                    {{ $t('Last 7 Days') }}
                  </option>
                  <option value="this_month">
                    {{ $t('This Month') }}
                  </option>
                  <option value="this_year">
                    {{ $t('This Year') }}
                  </option>
                </select>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-lg-3 col-6"
                  v-for="(summary, i) in dashboardSummary.data && dashboardSummary.data.items && dashboardSummary.data.items"
                  :key="i">
                  <div class="small-box" :class="summary.bgColor">
                    <div class="inner">
                      <h3>
                        {{ summary.value }}
                      </h3>
                      <p>{{ $t(summary.name) }}</p>
                    </div>
                    <div class="icon">
                      <i :class="summary.icon"></i>
                    </div>
                    <router-link v-if="summary.route" :to="{ name: summary.route }" class="small-box-footer">
                      {{ $t('More info') }}
                      <i class="fas fa-arrow-circle-right"></i>
                    </router-link>
                    <div v-else class="small-box-footer">
                      {{ $t(summary.name) }}
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div v-if="pieChartOptions.legend.data &&
      pieChartOptions.legend.data.length > 0 &&
      $can('top-plans')
      " class="col-md-12 col-lg-4">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                {{ $t('Top Plans') }} ({{ year }})
              </h3>
            </div>
            <div class="card-body">
              <template>
                <v-chart class="chart" :option="pieChartOptions" />
              </template>
            </div>
          </div>
        </div>

        <div class="col-md-12 col-lg-8">
          <div v-if="topClients && topClients.length > 0 && $can('top-clients')" class="card">
            <div class="card-header">
              <h3 class="card-title">
                {{ $t('Top Clients') }}
              </h3>
            </div>
            <div class="card-body row">
              <div class="table-responsive">
                <table class="clients-table table mb-0">
                  <tbody>
                    <tr v-for="(topClient, i) in topClients && topClients" :key="i">
                      <td>
                        <div class="d-flex align-items-center">
                          <img v-if="topClient.photo_url" :src="topClient.photo_url"
                            class="circle-img circle-img--small mr-2" loading="lazy" />
                          <img v-else src="https://via.placeholder.com/50x50" class="circle-img circle-img--small mr-2"
                            loading="lazy" />
                          <div class="text-left">
                            <h6 class="mb-0">
                              {{ topClient.name }}
                            </h6>
                            <p class="text-muted mb-0">
                              {{ topClient.company }}
                            </p>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="d-flex align-items-baseline">
                          {{ $t('Subscriptions') }}:
                          {{ topClient.subscriptions_count }}
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Form from 'vform'
import axios from 'axios'
import { use } from 'echarts/core'
import 'echarts/lib/component/grid'
import { PieChart } from 'echarts/charts'
import { BarChart } from 'echarts/charts'
import { LineChart } from 'echarts/charts'
import VChart, { THEME_KEY } from 'vue-echarts'
import { CanvasRenderer } from 'echarts/renderers'

import {
  TitleComponent,
  TooltipComponent,
  LegendComponent,
} from 'echarts/components'

use([
  CanvasRenderer,
  PieChart,
  LineChart,
  BarChart,
  TitleComponent,
  TooltipComponent,
  LegendComponent,
])

export default {
  layout: 'central',
  middleware: 'auth',
  metaInfo() {
    return { title: this.$t('Dashboard') }
  },
  components: {
    VChart,
  },
  provide: {
    [THEME_KEY]: 'vintage',
  },

  data: () => ({
    isDemoMode: window.config.isDemoMode,
    breadcrumbsCurrent: 'Dashboard',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: '',
      },
    ],
    form: new Form({
      summaryType: 'today',
    }),
    year: new Date().getFullYear(),
    className: 'col-lg-4',
    allData: '',
    topClients: '',
    hasDataInCentralPaymentTable: '',
    dashboardSummary: {},
    loading: false,

    // options for pie chart(Top plans)
    pieChartOptions: {
      responsive: true,
      tooltip: {
        trigger: 'item',
        formatter: '{a} <br/>{b} : {c} ({d}%)',
      },
      legend: {
        orient: 'vertical',
        left: 'left',
        data: [],
      },
      series: [
        {
          name: 'Top Plans',
          type: 'pie',
          radius: '55%',
          center: ['50%', '60%'],
          data: [],
          emphasis: {
            itemStyle: {
              shadowBlur: 10,
              shadowOffsetX: 0,
              shadowColor: 'rgba(0, 0, 0, 0.5)',
            },
          },
        },
      ],
    },
  }),
  created() {
    this.loading = true
    this.getTransactionStatus()
    this.getSummery()
    this.getTopPlans()
    this.getTopClients()
    this.loading = false
  },
  methods: {
    // working start
    // get summery
    async getSummery(event) {
      let summaryType = 'today'
      if (event) {
        summaryType = event.target.value
      }
      const { data } = await axios.get(
        window.location.origin + '/api/dashboard-summary/' + summaryType
      )
      this.dashboardSummary = data
    },

    // get top plans
    async getTopPlans() {
      const { data } = await axios.get(
        window.location.origin + '/api/dashboard/top-plans'
      )
      this.pieChartOptions.legend.data = data.names
      this.pieChartOptions.series[0].data = data.plans
    },

    // get transaction status
    async getTransactionStatus() {
      const response = await axios.get(window.location.origin + '/api/dashboard/transaction-status');
      this.hasDataInCentralPaymentTable = response.data;
    },


    // get top clients
    async getTopClients() {
      const { data } = await axios.get(
        window.location.origin + '/api/dashboard/top-clients'
      )
      this.topClients = data.data
    },
    // working end
  },
}
</script>
<style scoped>
.chart {
  height: 400px;
}
</style>
