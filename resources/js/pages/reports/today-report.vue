<template>
    <div class="mb-50">
        <!-- breadcrumbs Start -->
        <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
        <!-- breadcrumbs end -->
        <div class="row">
            <div class="col-lg-12">
                <table-loading v-show="loading" />
                <div class="card custom-card w-100">
                    <div class="card-header setings-header">
                        <div class="col-xl-4 col-4">
                            <h3 class="card-title">
                                {{ $t("Todays Report") }}
                            </h3>
                        </div>
                        <div class="col-xl-8 col-8 float-right text-right">
                            <div class="btn-group c-w-100">
                                <a @click="refreshTable()" href="#" v-tooltip="'Refresh'" class="btn btn-success">
                                    <i class="fas fa-sync"></i>
                                </a>
                                <a @click="printWindow()" href="#" v-tooltip="'Print'" class="btn btn-secondary">
                                    <i class="fas fa-print"></i> {{ $t("Print") }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body position-relative">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="box box-solid">
                                    <div class="box-body">

                                        <table class="table table-striped">
                                            <tbody>
                                                <tr>
                                                    <th>{{ $t("Opening Stock") }} <br><small
                                                            class="text-muted">{{ $t("By purchase price")
                                                            }}</small>:</th>
                                                    <td>{{ reportInfo.openingStockByPurchasePrice | withCurrency }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ $t("Opening Stock") }} <br><small
                                                            class="text-muted">{{ $t("By sale price")
                                                            }}</small>:
                                                    </th>
                                                    <td>{{ reportInfo.openingStockBySalePrice | withCurrency }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ $t("Total Purchase") }}:</th>
                                                    <td>{{ reportInfo.totalPurchase | withCurrency }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ $t("Total Expense") }}:</th>
                                                    <td>{{ reportInfo.expenses | withCurrency }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ $t("Total Payroll") }}:</th>
                                                    <td>{{ reportInfo.payrolls | withCurrency }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ $t("Total Loan Interest") }}:</th>
                                                    <td>{{ reportInfo.loanInterest | withCurrency }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ $t("Asset Depreciation") }}:</th>
                                                    <td>{{ reportInfo.assetDepriciation | withCurrency }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ $t("Total Sell Discount") }}:</th>
                                                    <td>{{ reportInfo.invoiceDiscount | withCurrency }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ $t("Total Sell Return") }}:</th>
                                                    <td>{{ reportInfo.invoiceReturn | withCurrency }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="box box-solid">

                                    <div class="box-body">
                                        <table class="table table-striped">
                                            <tbody>
                                                <tr>
                                                    <th>{{ $t("Closing Stock") }} <br><small
                                                            class="text-muted">{{ $t("By purchase price")
                                                            }}</small>:</th>
                                                    <td>{{ reportInfo.closingStockByPurchasePrice | withCurrency }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ $t("Closing Stock") }}<br><small
                                                            class="text-muted">{{ $t("By sale price")
                                                            }}</small>:
                                                    </th>
                                                    <td>{{ reportInfo.closingStockBySalePrice | withCurrency }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ $t("Total Sales") }}:
                                                    </th>
                                                    <td>{{ reportInfo.invoiceSales | withCurrency }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ $t("Total Purchase Return") }}:</th>
                                                    <td>{{ reportInfo.purchaseReturn | withCurrency }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ $t("Total Purchase Discount") }}:</th>
                                                    <td>{{ reportInfo.todayPurchaseDiscount | withCurrency }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        &nbsp;
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                            </div>
                            <div class="col-md-12 ml-2 mt-3">
                                <div class="box box-solid">

                                    <div class="box-body">
                                        <h3 v-if="reportInfo.grossProfit >= 0" class="text-success">
                                            {{ $t("Gross Profit") }}: {{ reportInfo.grossProfit |
            withAbsoluteCurrency }}
                                        </h3>
                                        <h3 v-else-if="reportInfo.grossProfit < 0" class="text-danger">
                                            {{ $t("Gross Loss") }}: {{ reportInfo.grossProfit |
            withAbsoluteCurrency }}
                                        </h3>
                                        <h3 v-if="reportInfo.netProfit >= 0" class="text-success">
                                            {{ $t("Net Profit") }}: {{ reportInfo.netProfit |
            withAbsoluteCurrency }}
                                        </h3>
                                        <h3 v-else-if="reportInfo.netProfit < 0" class="text-danger">
                                            {{ $t("Net Loss") }}: {{ reportInfo.netProfit | withAbsoluteCurrency
                                            }}
                                        </h3>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
import axios from 'axios';

export default {
    middleware: ["auth", "check-permissions"],
    metaInfo() {
        return { title: this.$t("Today Report") };
    },
    data: () => ({

        breadcrumbsCurrent: "Today Report",
        breadcrumbs: [
            {
                name: "Dashboard",
                url: "home",
            },
            {
                name: "Today Report",
                url: "",
            },
        ],
        reportInfo: '',
        loading: false,
    }),
    // Map Getters
    computed: {
        ...mapGetters("operations", ["appInfo"]),
    },
    created() {
        this.getTodayReportData();
    },
    methods: {

        // refresh table
        refreshTable() {
            this.getTodayReportData();
        },

        // print
        printWindow() {
            window.print();
        },

        // get data
        async getTodayReportData() {
            this.loading = true;
            await axios.get(window.location.origin + '/api/reports/todayReport')
                .then((response) => {
                    this.reportInfo = response.data
                    this.loading = false;
                }).catch(() => {
                    toast.fire({ type: 'error', title: 'Opps...something went wrong 😔' })
                    this.loading = false;
                })
        },

    },
};
</script>