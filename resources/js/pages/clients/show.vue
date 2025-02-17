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
                  @click="loadInitialData"
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
        </div>
      </div>
    </div>

    <div class="tab-content">
      <div class="tab-pane active" id="details">
        <div class="row">
          <div class="col-md-12 col-lg-3">
            <div class="card">
              <div class="card-body box-profile">
                <div class="text-center mb-2">
                  <a
                    v-if="allData.image"
                    href="#"
                    id="show-modal"
                    @click="previewModal(allData.image)"
                  >
                    <img
                      :src="allData.image"
                      class="profile-user-img img-fluid img-circle"
                      loading="lazy"
                    />
                  </a>
                  <div v-else class="bg-secondary no-preview-lg">
                    <small>{{ $t("No Preview") }}</small>
                  </div>
                </div>
                <h3 class="profile-username text-center">{{ allData.name }}</h3>
                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <strong>{{ $t("Client ID") }}</strong>
                    <span class="float-right">{{
                      allData.clientID | withPrefix(clientPrefix)
                    }}</span>
                  </li>
                  <li class="list-group-item">
                    <strong>{{ $t("Name") }}</strong>
                    <span class="float-right">{{ allData.name }}</span>
                  </li>
                  <li class="list-group-item">
                    <strong>{{ $t("Email") }}</strong>
                    <span class="float-right">{{ allData.email }}</span>
                  </li>
                  <li class="list-group-item">
                    <strong>{{ $t("Contact Number") }}</strong>
                    <span class="float-right">{{ allData.phoneNumber }}</span>
                  </li>
                  <li class="list-group-item">
                    <strong>{{ $t("Company Name") }}</strong>
                    <span class="float-right">{{ allData.companyName }}</span>
                  </li>
                  <li
                    v-if="allData.taxRegistrationNumber"
                    class="list-group-item"
                  >
                    <strong>{{ $t("Tax Registration Number") }}</strong>
                    <span class="float-right">{{
                      allData.taxRegistrationNumber
                    }}</span>
                  </li>
                  <li class="list-group-item">
                    <strong>{{ $t("Address") }}</strong>
                    <span class="float-right">{{ allData.address }}</span>
                  </li>
                </ul>
                <span
                  v-if="allData.status === 1"
                  class="btn-block btn bg-success"
                  >{{ $t("Active") }}</span
                >
                <span v-else class="btn-block btn bg-danger">{{
                  $t("Inactive")
                }}</span>
              </div>
              <!-- /.card-body -->
            </div>
          </div>
          <!-- /.col -->
          <div class="col-md-12 col-lg-9">
            <div class="row">
              <div class="col-lg-6 col-md-4 col-12">
                <div class="card bg-info">
                  <div class="card-content">
                    <div class="card-body pb-1">
                      <div class="row">
                        <div class="col-6">
                          <h6 class="text-white">
                            {{ $t("Invoice Total") }}
                          </h6>
                          <h6 class="text-white">
                            {{ $t("Non Invoice Total") }}
                          </h6>
                          <hr />
                          <h4 class="text-white mb-1">
                            {{ $t("Total") }}
                          </h4>
                        </div>
                        <div class="col-6 text-right">
                          <h6 class="text-white">
                            {{ allData.clientInvoiceTotal | withCurrency }}
                          </h6>
                          <h6 class="text-white">
                            {{ allData.nonInvoiceDue | withCurrency }}
                          </h6>
                          <hr />
                          <h4 class="text-white mb-1">
                            {{
                              (allData.clientInvoiceTotal +
                                allData.nonInvoiceDue)
                                | withCurrency
                            }}
                          </h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 col-md-4 col-12">
                <div class="card bg-danger">
                  <div class="card-content">
                    <div class="card-body pb-1">
                      <div class="row">
                        <div class="col-6">
                          <h6 class="text-white">
                            {{ $t("Invoice Due") }}
                          </h6>
                          <h6 class="text-white">
                            {{ $t("Non Invoice Due") }}
                          </h6>
                          <hr />
                          <h4 class="text-white mb-1">
                            {{ $t("Total Due") }}
                          </h4>
                        </div>
                        <div class="col-6 text-right">
                          <h6 class="text-white">
                            {{ allData.clientDue | withCurrency }}
                          </h6>
                          <h6 class="text-white">
                            {{ allData.nonInvoiceCurrentDue | withCurrency }}
                          </h6>
                          <hr />
                          <h4 class="text-white mb-1">
                            {{
                              (allData.clientDue + allData.nonInvoiceCurrentDue)
                                | withCurrency
                            }}
                          </h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div
              v-if="
                $can('invoice-list') ||
                $can('invoice-return-list') ||
                $can('invoice-payment-list') ||
                $can('non-invoice-payment-list')
              "
              class="card"
            >
              <div class="card-header p-2">
                <div class="row">
                  <div class="col-md-10">
                    <ul class="nav nav-pills">
                      <li v-if="$can('invoice-list')" class="nav-item">
                        <a
                          class="nav-link active"
                          href="#invoices"
                          data-toggle="tab"
                          @click="activeTab = 'invoices'"
                        >
                          <i class="fas fa-file-invoice"></i>
                          {{ $t("Invoices") }}
                          <span v-if="pagination" class="badge badge-dark">{{
                            pagination.total
                          }}</span>
                        </a>
                      </li>
                      <li v-if="$can('invoice-return-list')" class="nav-item">
                        <a
                          class="nav-link"
                          href="#invoice-returns"
                          data-toggle="tab"
                          @click="getInvoiceReturns"
                        >
                          <i class="fas fa-undo-alt"></i>
                          {{ $t("Invoice Returns") }}
                          <span
                            v-if="invoiceReturnPagination"
                            class="badge badge-dark"
                            >{{ invoiceReturnPagination.total }}</span
                          >
                        </a>
                      </li>
                      <li v-if="$can('invoice-payment-list')" class="nav-item">
                        <a
                          class="nav-link"
                          href="#invoice-payments"
                          data-toggle="tab"
                          @click="getInvoicePayments"
                        >
                          <i class="fas fa-receipt"></i>
                          {{ $t("Invoice Payments") }}
                          <span
                            v-if="paymentPagination"
                            class="badge badge-dark"
                            >{{ paymentPagination.total }}</span
                          >
                        </a>
                      </li>
                      <li
                        v-if="$can('non-invoice-payment-list')"
                        class="nav-item"
                      >
                        <a
                          class="nav-link"
                          href="#non-invoice-transactions"
                          data-toggle="tab"
                          @click="nonInvoiceTransactions"
                        >
                          <i class="fas fa-money-bill"></i>
                          {{ $t("Non Invoice Transactions") }}
                          <span
                            v-if="nonInvoicePagination"
                            class="badge badge-dark"
                            >{{ nonInvoicePagination.total }}</span
                          >
                        </a>
                      </li>
                      <li v-if="$can('invoice-list')" class="nav-item">
                        <a
                          class="nav-link"
                          href="#ledger"
                          data-toggle="tab"
                          @click="getLedger"
                        >
                          <i class="fas fa-list-ul"></i>
                          {{ $t("Ledger") }}
                        </a>
                      </li>
                    </ul>
                  </div>
                  <div class="col-md-2 text-right">
                    <div class="btn-group">
                      <a
                        @click="generatePDF()"
                        href="#"
                        class="btn btn-primary"
                        v-tooltip="$t('download')"
                      >
                        <i class="fas fa-download"></i>
                      </a>
                      <a
                        @click="print()"
                        href="#"
                        class="btn btn-secondary"
                        v-tooltip="$t('print')"
                      >
                        <i class="fas fa-print"></i>
                      </a>
                      <a
                        @click="refreshTable(activeTab)"
                        href="#"
                        v-tooltip="'Refresh'"
                        class="btn btn-success"
                      >
                        <i class="fas fa-sync"></i>
                      </a>
                      <router-link
                        :to="{ name: 'clients.index' }"
                        class="btn btn-dark float-right"
                        title="Back"
                        v-tooltip="$t('Back')"
                      >
                        <i class="fas fa-long-arrow-alt-left" />
                      </router-link>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="tab-content" id="content-to-pdf">
                  <div class="col-md-12" v-if="headerShow">
                    <h4 class="text-capitalize">
                      {{ activeTab.replace(/-/g, " ") }}
                    </h4>
                    <strong> {{ $t("Date") }}</strong> :
                    {{ date | moment("Do MMM, YYYY") }}<br />
                    <strong>{{ $t("Name") }}</strong> : {{ allData.name
                    }}<br />
                    <strong>{{ $t("Contact Number") }}</strong> :
                    {{ allData.phoneNumber }}<br />
                    <strong>{{ $t("Email") }}</strong> :
                    {{ allData.email }}<br />
                    <hr />
                  </div>
                  <!-- Invoices -->
                  <div class="tab-pane active" id="invoices">
                    <div
                      class="row no-print"
                      id="element-to-hide"
                      data-html2canvas-ignore="true"
                    >
                      <div class="col-12 col-md-9 mb-2">
                        <search
                          v-model="query"
                          @reset-pagination="resetPagination"
                          @reload="reload"
                        />
                      </div>
                      <div class="col-12 col-md-3 text-right pull-right">
                        <date-range-picker
                          ref="picker"
                          opens="left"
                          :locale-data="locale"
                          :minDate="minDate"
                          :maxDate="maxDate"
                          :singleDatePicker="false"
                          :showWeekNumbers="false"
                          :showDropdowns="true"
                          :autoApply="true"
                          v-model="dateRange"
                          @update="updateValues('invoice')"
                          :linkedCalendars="true"
                          class="c-w-100"
                        >
                          <template
                            v-slot:input="picker"
                            style="min-width: 350px"
                          >
                            {{ picker.startDate | startDate }} -
                            {{ picker.endDate | endDate }}
                          </template>
                        </date-range-picker>
                      </div>
                    </div>
                    <table-loading v-show="loading" />
                    <div class="table-responsive table-custom mt-3">
                      <table class="table">
                        <thead>
                          <tr>
                            <th>{{ $t("#") }}</th>
                            <th>{{ $t("Invoice No") }}</th>
                            <th>{{ $t("Invoice Date") }}</th>
                            <th>{{ $t("Net Total") }}</th>
                            <th>{{ $t("Total Paid") }}</th>
                            <th>{{ $t("Total Due") }}</th>
                            <th>{{ $t("Status") }}</th>
                            <th
                              v-if="
                                $can('invoice-view') ||
                                $can('invoice-edit') ||
                                $can('invoice-delete')
                              "
                              class="text-right no-print"
                              id="element-to-hide"
                              data-html2canvas-ignore="true"
                            >
                              {{ $t("Action") }}
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr
                            v-show="items && items.length"
                            v-for="(data, i) in items"
                            :key="i"
                          >
                            <td>
                              <span
                                v-if="pagination && pagination.current_page > 1"
                              >
                                {{
                                  pagination.per_page *
                                    (pagination.current_page - 1) +
                                  (i + 1)
                                }}
                              </span>
                              <span v-else>{{ i + 1 }}</span>
                            </td>
                            <td>
                              <router-link
                                v-if="$can('invoice-view')"
                                :to="{
                                  name: 'invoices.show',
                                  params: { slug: data.slug },
                                }"
                              >
                                {{ data.invoiceNo | withPrefix(invoicePrefix) }}
                              </router-link>
                              <span v-else>{{
                                data.invoiceNo | withPrefix(invoicePrefix)
                              }}</span>
                            </td>
                            <td>
                              <span v-if="data.invoiceDate">{{
                                data.invoiceDate | moment("Do MMM, YYYY")
                              }}</span>
                            </td>

                            <td>{{ data.invoiceTotal | withCurrency }}</td>
                            <td>{{ data.totalPaid | withCurrency }}</td>
                            <td>{{ data.due | withCurrency }}</td>
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
                            <td
                              v-if="
                                $can('invoice-view') ||
                                $can('invoice-edit') ||
                                $can('invoice-delete')
                              "
                              class="text-right no-print"
                              id="element-to-hide"
                              data-html2canvas-ignore="true"
                            >
                              <div class="btn-group">
                                <router-link
                                  v-if="$can('invoice-view')"
                                  v-tooltip="$t('View')"
                                  :to="{
                                    name: 'invoices.show',
                                    params: { slug: data.slug },
                                  }"
                                  class="btn btn-primary btn-sm"
                                >
                                  <i class="fas fa-eye" />
                                </router-link>
                                <router-link
                                  v-if="$can('invoice-edit')"
                                  v-tooltip="$t('Edit')"
                                  :to="{
                                    name: 'invoices.edit',
                                    params: { slug: data.slug },
                                  }"
                                  class="btn btn-info btn-sm"
                                >
                                  <i class="fas fa-edit" />
                                </router-link>
                                <a
                                  v-if="$can('invoice-delete')"
                                  v-tooltip="$t('Delete')"
                                  href="#"
                                  class="btn btn-danger btn-sm"
                                  @click="deleteInvoiceData(data.slug)"
                                >
                                  <i class="fas fa-trash" />
                                </a>
                              </div>
                            </td>
                          </tr>
                          <tr v-show="!loading && items && !items.length">
                            <td colspan="8">
                              <EmptyTable />
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <!-- NEW PAGINATION -->
                    <div
                      v-if="pagination && pagination.total > 0"
                      class="dtable-footer no-print"
                      id="element-to-hide"
                      data-html2canvas-ignore="true"
                    >
                      <div class="form-group row display-per-page">
                        <label>{{ $t("per_page") }} </label>
                        <div>
                          <select
                            @change="updatePerPager('invoice')"
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
                    <!-- NEW PAGINATION END -->
                  </div>
                  <!-- Invoices Returns -->
                  <div class="tab-pane" id="invoice-returns">
                    <div
                      class="row no-print"
                      id="element-to-hide"
                      data-html2canvas-ignore="true"
                    >
                      <div class="col-12 col-md-9 mb-2">
                        <search
                          v-model="invoiceReturnQuery"
                          @reset-pagination="resetReturnPagination"
                          @reload="returnReload"
                        />
                      </div>
                      <div class="col-12 col-md-3 text-right pull-right">
                        <date-range-picker
                          ref="picker"
                          opens="left"
                          :locale-data="locale"
                          :minDate="minDate"
                          :maxDate="maxDate"
                          :singleDatePicker="false"
                          :showWeekNumbers="false"
                          :showDropdowns="true"
                          :autoApply="true"
                          v-model="dateRange"
                          @update="updateValues('invoice-returns')"
                          :linkedCalendars="true"
                          class="c-w-100"
                        >
                          <template
                            v-slot:input="picker"
                            style="min-width: 350px"
                          >
                            {{ picker.startDate | startDate }} -
                            {{ picker.endDate | endDate }}
                          </template>
                        </date-range-picker>
                      </div>
                    </div>
                    <table-loading v-show="invoiceReturnLoading" />
                    <div class="table-responsive table-custom mt-3">
                      <table class="table">
                        <thead>
                          <tr>
                            <th>{{ $t("#") }}</th>
                            <th>{{ $t("Return No") }}</th>
                            <th>{{ $t("Invoice No") }}</th>
                            <th>{{ $t("Return Reason") }}</th>
                            <th>{{ $t("Cost of Return Products") }}</th>
                            <th>{{ $t("Date") }}</th>
                            <th>{{ $t("Status") }}</th>
                            <th
                              v-if="
                                $can('invoice-return-edit') ||
                                $can('invoice-return-view') ||
                                $can('invoice-return-delete')
                              "
                              class="text-right no-print"
                              id="element-to-hide"
                              data-html2canvas-ignore="true"
                            >
                              {{ $t("Action") }}
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr
                            v-show="allReturns.length"
                            v-for="(data, i) in allReturns"
                            :key="i"
                          >
                            <td>
                              <span
                                v-if="pagination && pagination.current_page > 1"
                              >
                                {{
                                  pagination.per_page *
                                    (pagination.current_page - 1) +
                                  (i + 1)
                                }}
                              </span>
                              <span v-else>{{ i + 1 }}</span>
                            </td>
                            <td>
                              <router-link
                                v-if="$can('invoice-return-view')"
                                :to="{
                                  name: 'invoiceReturns.show',
                                  params: { slug: data.invoiceSlug },
                                }"
                              >
                                {{
                                  data.returnNo
                                    | withPrefix(invoiceReturnPrefix)
                                }}
                              </router-link>
                              <span v-else>{{
                                data.returnNo | withPrefix(invoiceReturnPrefix)
                              }}</span>
                            </td>
                            <td>
                              {{ data.invoiceNo | withPrefix(invoicePrefix) }}
                            </td>
                            <td>{{ data.reason }}</td>
                            <td>{{ data.totalReturn | withCurrency }}</td>
                            <td>
                              <span v-if="data.returnDate">{{
                                data.returnDate | moment("Do MMM, YYYY")
                              }}</span>
                            </td>
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
                            <td
                              v-if="
                                $can('invoice-return-edit') ||
                                $can('invoice-return-view') ||
                                $can('invoice-return-delete')
                              "
                              class="text-right no-print"
                              id="element-to-hide"
                              data-html2canvas-ignore="true"
                            >
                              <div class="btn-group">
                                <router-link
                                  v-if="$can('invoice-return-view')"
                                  v-tooltip="$t('View')"
                                  :to="{
                                    name: 'invoiceReturns.show',
                                    params: { slug: data.slug },
                                  }"
                                  class="btn btn-primary btn-sm"
                                >
                                  <i class="fas fa-eye" />
                                </router-link>
                                <router-link
                                  v-if="$can('invoice-return-edit')"
                                  v-tooltip="$t('Edit')"
                                  :to="{
                                    name: 'invoiceReturns.edit',
                                    params: { slug: data.slug },
                                  }"
                                  class="btn btn-info btn-sm"
                                >
                                  <i class="fas fa-edit" />
                                </router-link>
                                <a
                                  v-if="$can('invoice-return-delete')"
                                  v-tooltip="$t('Delete')"
                                  href="#"
                                  class="btn btn-danger btn-sm"
                                  @click="deleteInvoiceReturnData(data.slug)"
                                >
                                  <i class="fas fa-trash" />
                                </a>
                              </div>
                            </td>
                          </tr>
                          <tr v-show="!loading && !allReturns.length">
                            <td colspan="8">
                              <EmptyTable />
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <!-- NEW PAGINATION -->
                    <div
                      v-if="
                        invoiceReturnPagination &&
                        invoiceReturnPagination.total > 0
                      "
                      class="dtable-footer no-print"
                      id="element-to-hide"
                      data-html2canvas-ignore="true"
                    >
                      <div class="form-group row display-per-page">
                        <label>{{ $t("per_page") }} </label>
                        <div>
                          <select
                            @change="updatePerPager('invoice-returns')"
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
                        v-if="
                          invoiceReturnPagination &&
                          invoiceReturnPagination.last_page > 1
                        "
                        :pagination="
                          allReturns
                            ? invoiceReturnPagination
                            : { current_page: 1 }
                        "
                        :offset="5"
                        class="justify-flex-end"
                        @paginate="invoiceReturnPaginate"
                      />
                      <!-- pagination-end -->
                    </div>
                    <!-- NEW PAGINATION END -->
                  </div>
                  <!-- Invoices Payments -->
                  <div class="tab-pane" id="invoice-payments">
                    <div
                      class="row no-print"
                      id="element-to-hide"
                      data-html2canvas-ignore="true"
                    >
                      <div class="col-12 col-md-9 mb-2">
                        <search
                          v-model="paymentsQuery"
                          @reset-pagination="resetPaymentsPagination"
                          @reload="paymentsReload"
                        />
                      </div>
                      <div class="col-12 col-md-3 text-right pull-right">
                        <date-range-picker
                          ref="picker"
                          opens="left"
                          :locale-data="locale"
                          :minDate="minDate"
                          :maxDate="maxDate"
                          :singleDatePicker="false"
                          :showWeekNumbers="false"
                          :showDropdowns="true"
                          :autoApply="true"
                          v-model="dateRange"
                          @update="updateValues('invoice-payments')"
                          :linkedCalendars="true"
                          class="c-w-100"
                        >
                          <template
                            v-slot:input="picker"
                            style="min-width: 350px"
                          >
                            {{ picker.startDate | startDate }} -
                            {{ picker.endDate | endDate }}
                          </template>
                        </date-range-picker>
                      </div>
                    </div>
                    <table-loading v-show="paymentsLoading" />
                    <div class="table-responsive table-custom mt-3">
                      <table class="table">
                        <thead>
                          <tr>
                            <th>{{ $t("#") }}</th>
                            <th>{{ $t("Invoice No") }}</th>
                            <th>{{ $t("Total") }}</th>
                            <th>{{ $t("Paid Amount") }}</th>
                            <th>{{ $t("Account") }}</th>
                            <th>{{ $t("Payment Date") }}</th>
                            <th>{{ $t("Status") }}</th>
                            <th
                              v-if="
                                $can('invoice-payment-edit') ||
                                $can('invoice-payment-view') ||
                                $can('invoice-payment-delete')
                              "
                              class="text-right no-print"
                              id="element-to-hide"
                              data-html2canvas-ignore="true"
                            >
                              {{ $t("Action") }}
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr
                            v-show="allPayments.length"
                            v-for="(data, i) in allPayments"
                            :key="i"
                          >
                            <td>
                              <span
                                v-if="pagination && pagination.current_page > 1"
                              >
                                {{
                                  pagination.per_page *
                                    (pagination.current_page - 1) +
                                  (i + 1)
                                }}
                              </span>
                              <span v-else>{{ i + 1 }}</span>
                            </td>
                            <td v-if="data.invoice && invoicePrefix">
                              <router-link
                                v-if="$can('invoice-view')"
                                :to="{
                                  name: 'invoices.show',
                                  params: { slug: data.invoice.slug },
                                }"
                              >
                                {{
                                  data.invoice.invoiceNo
                                    | withPrefix(invoicePrefix)
                                }}
                              </router-link>
                              <span v-else>{{
                                data.invoice.invoiceNo
                                  | withPrefix(invoicePrefix)
                              }}</span>
                            </td>
                            <td v-if="data.invoice">
                              {{ data.invoice.invoiceTotal | withCurrency }}
                            </td>
                            <td>{{ data.amount | withCurrency }}</td>
                            <td>
                              <span v-if="data.account">{{
                                data.account.label
                              }}</span>
                            </td>
                            <td>
                              <span v-if="data.date">{{
                                data.date | moment("Do MMM, YYYY")
                              }}</span>
                            </td>
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
                            <td
                              v-if="
                                $can('invoice-payment-edit') ||
                                $can('invoice-payment-view') ||
                                $can('invoice-payment-delete')
                              "
                              class="text-right no-print"
                              id="element-to-hide"
                              data-html2canvas-ignore="true"
                            >
                              <div class="btn-group">
                                <router-link
                                  v-if="$can('invoice-payment-view')"
                                  v-tooltip="$t('View')"
                                  :to="{
                                    name: 'invoicePayments.show',
                                    params: { slug: data.slug },
                                  }"
                                  class="btn btn-primary btn-sm"
                                >
                                  <i class="fas fa-eye" />
                                </router-link>
                                <router-link
                                  v-if="$can('invoice-payment-edit')"
                                  v-tooltip="$t('Edit')"
                                  :to="{
                                    name: 'invoicePayments.edit',
                                    params: { slug: data.slug },
                                  }"
                                  class="btn btn-info btn-sm"
                                >
                                  <i class="fas fa-edit" />
                                </router-link>
                                <a
                                  v-if="$can('invoice-payment-delete')"
                                  v-tooltip="$t('Delete')"
                                  href="#"
                                  class="btn btn-danger btn-sm"
                                  @click="deletePaymentData(data.slug)"
                                >
                                  <i class="fas fa-trash" />
                                </a>
                              </div>
                            </td>
                          </tr>
                          <tr v-show="!loading && !allPayments.length">
                            <td colspan="9">
                              <EmptyTable />
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <!-- NEW PAGINATION -->
                    <div
                      v-if="paymentPagination && paymentPagination.total > 0"
                      class="dtable-footer no-print"
                      id="element-to-hide"
                      data-html2canvas-ignore="true"
                    >
                      <div class="form-group row display-per-page">
                        <label>{{ $t("per_page") }} </label>
                        <div>
                          <select
                            @change="updatePerPager('invoice-payments')"
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
                        v-if="
                          paymentPagination && paymentPagination.last_page > 1
                        "
                        :pagination="paymentPagination"
                        :offset="5"
                        class="justify-flex-end"
                        @paginate="paymentsPaginate"
                      />
                      <!-- pagination-end -->
                    </div>
                    <!-- NEW PAGINATION END -->
                  </div>
                  <!-- Invoices Transactions -->
                  <div class="tab-pane" id="non-invoice-transactions">
                    <div
                      class="row no-print"
                      id="element-to-hide"
                      data-html2canvas-ignore="true"
                    >
                      <div class="col-12 col-md-9 mb-2">
                        <search
                          v-model="nonInvoiceQuery"
                          @reset-pagination="resetNonInvoiceTransPagination"
                          @reload="nonInvoiceTransReload"
                        />
                      </div>
                      <div class="col-12 col-md-3 text-right pull-right">
                        <date-range-picker
                          ref="picker"
                          opens="left"
                          :locale-data="locale"
                          :minDate="minDate"
                          :maxDate="maxDate"
                          :singleDatePicker="false"
                          :showWeekNumbers="false"
                          :showDropdowns="true"
                          :autoApply="true"
                          v-model="dateRange"
                          @update="updateValues('non-invoice-transactions')"
                          :linkedCalendars="true"
                          class="c-w-100"
                        >
                          <template
                            v-slot:input="picker"
                            style="min-width: 350px"
                          >
                            {{ picker.startDate | startDate }} -
                            {{ picker.endDate | endDate }}
                          </template>
                        </date-range-picker>
                      </div>
                    </div>
                    <table-loading v-show="nonInvoiceTransLoading" />
                    <div class="table-responsive table-custom mt-3">
                      <table class="table">
                        <thead>
                          <tr>
                            <th>{{ $t("#") }}</th>
                            <th>{{ $t("Payment Type") }}</th>
                            <th>{{ $t("Paid Amount") }}</th>
                            <th>{{ $t("Account") }}</th>
                            <th>{{ $t("Payment Date") }}</th>
                            <th>{{ $t("Status") }}</th>
                            <th
                              v-if="
                                $can('non-invoice-payment-edit') ||
                                $can('non-invoice-payment-view') ||
                                $can('non-invoice-payment-delete')
                              "
                              class="text-right no-print"
                              id="element-to-hide"
                              data-html2canvas-ignore="true"
                            >
                              {{ $t("Action") }}
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr
                            v-show="allNonInvoiceTrans.length"
                            v-for="(data, i) in allNonInvoiceTrans"
                            :key="i"
                          >
                            <td>
                              <span
                                v-if="pagination && pagination.current_page > 1"
                              >
                                {{
                                  pagination.per_page *
                                    (pagination.current_page - 1) +
                                  (i + 1)
                                }}
                              </span>
                              <span v-else>{{ i + 1 }}</span>
                            </td>
                            <td>
                              <span
                                v-if="data.type === 1"
                                class="badge bg-primary"
                                >{{ $t("Due Paid") }}</span
                              >
                              <span v-else class="badge bg-danger">{{
                                $t("Due Added")
                              }}</span>
                            </td>
                            <td>{{ data.amount | withCurrency }}</td>
                            <td>
                              <span v-if="data.account">{{
                                data.account.label
                              }}</span>
                            </td>
                            <td>
                              <span v-if="data.date">{{
                                data.date | moment("Do MMM, YYYY")
                              }}</span>
                            </td>
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
                            <td
                              v-if="
                                $can('non-invoice-payment-edit') ||
                                $can('non-invoice-payment-view') ||
                                $can('non-invoice-payment-delete')
                              "
                              class="text-right no-print"
                              id="element-to-hide"
                              data-html2canvas-ignore="true"
                            >
                              <div class="btn-group">
                                <router-link
                                  v-if="$can('non-invoice-payment-edit')"
                                  v-tooltip="$t('Edit')"
                                  :to="{
                                    name: 'nonInvoicePayments.edit',
                                    params: { slug: data.slug },
                                  }"
                                  class="btn btn-info btn-sm"
                                >
                                  <i class="fas fa-edit" />
                                </router-link>
                                <a
                                  v-if="$can('non-invoice-payment-delete')"
                                  v-tooltip="$t('Delete')"
                                  href="#"
                                  class="btn btn-danger btn-sm"
                                  @click="deleteNonInvoicePayment(data.slug)"
                                >
                                  <i class="fas fa-trash" />
                                </a>
                              </div>
                            </td>
                          </tr>
                          <tr v-show="!loading && !allNonInvoiceTrans.length">
                            <td colspan="7">
                              <EmptyTable />
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <!-- NEW PAGINATION -->
                    <div
                      v-if="
                        nonInvoicePagination && nonInvoicePagination.total > 0
                      "
                      class="dtable-footer"
                      id="element-to-hide"
                      data-html2canvas-ignore="true"
                    >
                      <div class="form-group row display-per-page">
                        <label>{{ $t("per_page") }} </label>
                        <div>
                          <select
                            @change="updatePerPager('non-invoice-transactions')"
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
                        v-if="
                          nonInvoicePagination &&
                          nonInvoicePagination.last_page > 1
                        "
                        :pagination="nonInvoicePagination"
                        :offset="5"
                        class="justify-flex-end"
                        @paginate="nonInvoiceTransPaginate"
                      />
                      <!-- pagination-end -->
                    </div>
                    <!-- NEW PAGINATION END -->
                  </div>
                  <!--ledger-->
                  <div class="tab-pane print-area" id="ledger">
                    <table-loading v-show="loading" />
                    <div class="table-responsive table-custom mt-3">
                      <table class="table">
                        <thead>
                          <tr>
                            <th>{{ $t("#") }}</th>
                            <th>{{ $t("Date") }}</th>
                            <th>{{ $t("Info") }}</th>
                            <th>{{ $t("Credit") }}</th>
                            <th>{{ $t("Debit") }}</th>
                            <th>{{ $t("Discount") }}</th>
                            <th>{{ $t("Balance") }}</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(data, i) in ledgerItems" :key="i">
                            <td>{{ i + 1 }}</td>
                            <td>
                              {{ data.original_date | moment("Do MMM, YYYY") }}
                            </td>
                            <td>
                              <router-link
                                v-if="
                                  $can('invoice-view') &&
                                  data.action_type == 'invoice'
                                "
                                :to="{
                                  name: 'invoices.show',
                                  params: { slug: data.slug },
                                }"
                              >
                                {{ data.particulars }}
                              </router-link>
                              <router-link
                                v-if="data.action_type == 'invoice-payment'"
                                :to="{
                                  name: 'invoicePayments.show',
                                  params: { slug: data.slug },
                                }"
                              >
                                {{ data.particulars }}
                              </router-link>
                              <router-link
                                v-if="
                                  $can('invoice-return-view') &&
                                  data.action_type == 'invoice-return'
                                "
                                :to="{
                                  name: 'invoiceReturns.show',
                                  params: { slug: data.slug },
                                }"
                              >
                                {{ data.particulars }}
                              </router-link>
                            </td>
                            <td>{{ data.credit | withCurrency }}</td>
                            <td>{{ data.debit | withCurrency }}</td>
                            <td>{{ data.discount | withCurrency }}</td>
                            <td>{{ data.balance | withCurrency }}</td>
                          </tr>
                          <tr v-if="ledgerItems[ledgerItems.length - 1]">
                            <td>{{ ledgerItems.length + 1 }}</td>
                            <td>{{ date | moment("Do MMM, YYYY") }}</td>
                            <td>{{ $t("Non Invoice Due") }}</td>
                            <td>{{ 0 | withCurrency }}</td>
                            <td>
                              {{ allData.nonInvoiceCurrentDue | withCurrency }}
                            </td>
                            <td>{{ 0 | withCurrency }}</td>
                            <td>
                              {{
                                (ledgerItems[ledgerItems.length - 1].balance +
                                  allData.nonInvoiceCurrentDue)
                                  | withCurrency
                              }}
                            </td>
                          </tr>
                        </tbody>
                        <tfoot>
                          <tr v-if="ledgerItems[ledgerItems.length - 1]">
                            <td colspan="3">{{ $t("Summery") }}</td>
                            <td>{{ ledgerTotalCredit | withCurrency }}</td>
                            <td>
                              {{
                                (ledgerTotalDebit +
                                  allData.nonInvoiceCurrentDue)
                                  | withCurrency
                              }}
                            </td>
                            <td>{{ ledgerTotalDiscount | withCurrency }}</td>
                            <td>
                              {{
                                (ledgerItems[ledgerItems.length - 1].balance +
                                  allData.nonInvoiceCurrentDue)
                                  | withCurrency
                              }}
                              [{{ $t("Total Due") }}]
                            </td>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
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
                  @click="refreshActivityTable()"
                  href="#"
                  v-tooltip="'Refresh'"
                  class="btn btn-success"
                >
                  <i class="fas fa-sync"></i>
                </a>
                <a
                  @click="printActivityLog"
                  v-tooltip="$t('Print Table')"
                  class="btn btn-info"
                >
                  <i class="fas fa-print"></i>
                </a>
              </div>
            </div>
          </div>
          <table-loading v-show="activityLoading" />
          <div class="card-body position-relative">
            <div class="row">
              <div class="col-6 col-xl-4 mb-2">
                <search
                  v-model="activitySearchQuery"
                  @reset-pagination="resetActivityPagination()"
                  @reload="activityReload"
                />
              </div>
            </div>
            <div id="printMe" class="table-responsive table-custom mt-3">
              <div
                v-show="allActivityLog.length > 0"
                v-for="(data, i) in allActivityLog"
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
              <div
                class="text-center"
                v-show="!activityLoading && !allActivityLog.length"
              >
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
                    @change="updateActivityPerPager"
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
                v-if="
                  allActivityLogPagination &&
                  allActivityLogPagination.last_page > 1
                "
                :pagination="
                  allActivityLog
                    ? allActivityLogPagination
                    : { current_page: 1 }
                "
                :offset="5"
                class="justify-flex-end"
                @paginate="activityPaginate"
              />
              <!-- pagination-end -->
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- use the modal component, pass in the prop -->
    <Modal v-if="showModal" @close="previewModal()">
      <h5 slot="header">{{ $t("Attached Image Preview") }}</h5>
      <div class="w-100" slot="body">
        <img :src="allData.image" class="img-fluid" loading="lazy" />
      </div>
    </Modal>
  </div>
</template>

<script>
import axios from "axios";
import moment from "moment";
import i18n from "~/plugins/i18n";
import { mapGetters } from "vuex";
import html2pdf from "html2pdf.js";
import DateRangePicker from "vue2-daterange-picker";

export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("Client Details") };
  },
  components: {
    DateRangePicker,
  },
  data: () => ({
    breadcrumbsCurrent: "Client Details",
    breadcrumbs: [
      {
        name: "Dashboard",
        url: "home",
      },
      {
        name: "Clients",
        url: "clients.index",
      },
      {
        name: "Details",
        url: "",
      },
    ],
    paymentsLoading: false,
    invoiceReturnLoading: false,
    creditsLoading: false,
    debitsLoading: false,
    nonInvoiceTransLoading: false,
    url: null,
    showModal: false,
    allData: "",
    allPayments: "",
    allReturns: "",
    allNonInvoiceTrans: "",
    paymentPagination: "",
    invoiceReturnPagination: "",
    nonInvoicePagination: "",
    query: "",
    invoiceReturnQuery: "",
    paymentsQuery: "",
    nonInvoiceQuery: "",
    clientPrefix: "",
    invoicePrefix: "",
    invoiceReturnPrefix: "",
    activitySearchQuery: "",
    allActivityLog: "",
    allActivityLogPagination: "",
    activityLoading: false,
    perPage: 10,
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
    ledgerItems: [],
    ledgerTotalDiscount: 0,
    ledgerTotalDebit: 0,
    ledgerTotalCredit: 0,
    finalBalance: 0,
    headerShow: false,
    date: new Date().toISOString().slice(0, 10),
    activeTab: "invoices",
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
  },
  watch: {
    // watch invoice search data
    query: function (newQ, oldQ) {
      if (newQ === "") {
        if (this.dateRange.startDate && this.dateRange.endDate) {
          this.searchInvoicesData();
        } else {
          this.getInvoices();
        }
      } else {
        this.searchInvoicesData();
      }
    },
    // watch invoice return search data
    invoiceReturnQuery: function (newQ, oldQ) {
      if (newQ === "") {
        if (this.dateRange.startDate && this.dateRange.endDate) {
          this.searchReturnData();
        } else {
          this.getInvoiceReturns();
        }
      } else {
        this.searchReturnData();
      }
    },
    // watch payment search data
    paymentsQuery: function (newQ, oldQ) {
      if (newQ === "") {
        if (this.dateRange.startDate && this.dateRange.endDate) {
          this.searchPaymentData();
        } else {
          this.getInvoicePayments();
        }
      } else {
        this.searchPaymentData();
      }
    },

    // watch non invoice transaction search data
    nonInvoiceQuery: function (newQ, oldQ) {
      if (newQ === "") {
        if (this.dateRange.startDate && this.dateRange.endDate) {
          this.searchNonInvoiceTransactions();
        } else {
          this.nonInvoiceTransactions();
        }
      } else {
        this.searchNonInvoiceTransactions();
      }
    },

    // watch activitySearchQuery data
    activitySearchQuery: function (newQ, oldQ) {
      if (newQ === "") {
        this.getActivity();
      } else {
        this.searchActivity();
      }
    },
  },
  created() {
    this.getClient();
    this.getInvoices();
    this.clientPrefix = this.appInfo.clientPrefix;
    this.invoicePrefix = this.appInfo.invoicePrefix;
    this.invoiceReturnPrefix = this.appInfo.invoiceReturnPrefix;

    Fire.$on("AfterDelete", () => {
      this.getInvoices();
      this.getInvoiceReturns();
      this.getInvoicePayments();
      this.nonInvoiceTransactions();
    });
  },
  methods: {
    switchTab(tabName) {
      switch (tabName) {
        case "invoice":
          this.searchInvoicesData();
          break;
        case "invoice-returns":
          this.searchReturnData();
          break;
        case "invoice-payments":
          this.searchPaymentData();
          break;
        case "non-invoice-transactions":
          this.searchNonInvoiceTransactions();
          break;
      }
    },

    async loadInitialData() {
      this.getClient();
      this.getInvoices();
    },

    // filter data for selected date range
    async updateValues(tabName) {
      this.dateRange.startDate = moment(this.dateRange.startDate).format(
        "YYYY-MM-DD"
      );
      this.dateRange.endDate = moment(this.dateRange.endDate).format(
        "YYYY-MM-DD"
      );
      await this.switchTab(tabName);
    },
    // refresh table
    refreshTable(tabName) {
      this.query = "";
      this.dateRange.startDate = null;
      this.dateRange.endDate = null;
      setTimeout(
        function () {
          this.dateRange.startDate = "";
          this.dateRange.endDate = "";
          this.switchTab(tabName);
        }.bind(this),
        1000
      );
    },

    // get the client
    async getClient() {
      const { data } = await axios.get(
        window.location.origin + "/api/clients/" + this.$route.params.slug
      );
      this.allData = data.data;
    },

    // update per page count
    updatePerPager(tabName) {
      this.pagination.current_page = 1;
      this.invoiceReturnPagination.hasOwnProperty("current_page")
        ? (this.invoiceReturnPagination.current_page = 1)
        : "";
      this.paymentPagination.hasOwnProperty("current_page")
        ? (this.paymentPagination.current_page = 1)
        : "";
      this.nonInvoicePagination.hasOwnProperty("current_page")
        ? (this.nonInvoicePagination.current_page = 1)
        : "";

      this.switchTab(tabName);
    },

    // get the client invoices
    async getInvoices() {
      this.activeTab = "invoices";
      this.$store.state.operations.loading = true;
      let currentPage = this.pagination ? this.pagination.current_page : 1;
      await this.$store.dispatch("operations/fetchData", {
        path: "/api/client/" + this.$route.params.slug + "/all-invoices/?page=",
        currentPage: currentPage + "&perPage=" + this.perPage,
      });
    },

    // search invoices
    async searchInvoicesData() {
      this.$store.state.operations.loading = true;
      let currentPage = this.pagination ? this.pagination.current_page : 1;
      await this.$store.dispatch("operations/searchData", {
        path: "/api/client/" + this.$route.params.slug + "/all-invoices/search",
        term: this.query,
        currentPage: currentPage + "&perPage=" + this.perPage,
        startDate: this.dateRange.startDate,
        endDate: this.dateRange.endDate,
      });
    },

    // pagination
    async paginate() {
      this.query === "" ? this.getInvoices() : this.searchInvoicesData();
    },

    // reset purchase pagination
    async resetPagination() {
      this.pagination.current_page = 1;
    },

    // reload purchases after search
    async reload() {
      this.query = "";
      await this.searchInvoicesData();
    },

    // get client invoice returns
    async getInvoiceReturns() {
      this.activeTab = "invoice-returns";
      this.invoiceReturnLoading = true;
      let currentPage = this.allReturns
        ? this.invoiceReturnPagination.current_page
        : 1;
      const { data } = await axios.get(
        window.location.origin +
          "/api/client/" +
          this.$route.params.slug +
          "/invoice-returns?page=" +
          currentPage +
          "&perPage=" +
          this.perPage
      );
      this.allReturns = data.data;
      this.invoiceReturnPagination = data.meta;
      this.invoiceReturnLoading = false;
    },

    // search invoice returns
    async searchReturnData() {
      this.invoiceReturnLoading = true;
      let currentPage = this.allReturns
        ? this.invoiceReturnPagination.current_page
        : 1;
      const { data } = await axios.get(
        window.location.origin +
          "/api/client/" +
          this.$route.params.slug +
          "/invoice-returns/search" +
          "?term=" +
          this.invoiceReturnQuery +
          "&page=" +
          currentPage +
          "&perPage=" +
          this.perPage +
          "&startDate=" +
          this.dateRange.startDate +
          "&endDate=" +
          this.dateRange.endDate
      );
      this.allReturns = data.data;
      this.invoiceReturnPagination = data.meta;
      this.invoiceReturnLoading = false;
    },

    // invoice return pagination
    async invoiceReturnPaginate() {
      this.query === "" ? this.getInvoiceReturns() : this.searchReturnData();
    },

    // reset invoice return pagination
    async resetReturnPagination() {
      this.invoiceReturnPagination.current_page = 1;
    },

    // Reload purchases after search
    async returnReload() {
      this.invoiceReturnQuery = "";
      await this.searchReturnData();
    },

    // Get the invoice payments
    async getInvoicePayments() {
      this.activeTab = "invoice-payments";
      this.paymentsLoading = true;
      let currentPage = this.allPayments
        ? this.paymentPagination.current_page
        : 1;
      const { data } = await axios.get(
        window.location.origin +
          "/api/client/" +
          this.$route.params.slug +
          "/invoice-payments?page=" +
          currentPage +
          "&perPage=" +
          this.perPage
      );
      this.allPayments = data.data;
      this.paymentPagination = data.meta;
      this.paymentsLoading = false;
    },

    // search payments
    async searchPaymentData() {
      this.paymentsLoading = true;
      let currentPage = this.allPayments
        ? this.paymentPagination.current_page
        : 1;
      const { data } = await axios.get(
        window.location.origin +
          "/api/client/" +
          this.$route.params.slug +
          "/invoice-payments/search" +
          "?term=" +
          this.paymentsQuery +
          "&page=" +
          currentPage +
          "&perPage=" +
          this.perPage +
          "&startDate=" +
          this.dateRange.startDate +
          "&endDate=" +
          this.dateRange.endDate
      );
      this.allPayments = data.data;
      this.paymentPagination = data.meta;
      this.paymentsLoading = false;
    },

    // Payments pagination
    async paymentsPaginate() {
      this.query === this.getInvoicePayments() ? this.searchPaymentData() : "";
    },

    // Reset payments pagination
    async resetPaymentsPagination() {
      this.paymentPagination.current_page = 1;
    },

    // Reload payments after search
    async paymentsReload() {
      this.paymentsQuery = "";
      await this.searchPaymentData();
    },

    // Get the non invoice transactions
    async nonInvoiceTransactions() {
      this.activeTab = "non-invoice-transactions";
      this.nonInvoiceTransLoading = true;
      let currentPage = this.allNonInvoiceTrans
        ? this.nonInvoicePagination.current_page
        : 1;
      const { data } = await axios.get(
        window.location.origin +
          "/api/client/" +
          this.$route.params.slug +
          "/non-invoice-payments?page=" +
          currentPage +
          "&perPage=" +
          this.perPage
      );
      this.allNonInvoiceTrans = data.data;
      this.nonInvoicePagination = data.meta;
      this.nonInvoiceTransLoading = false;
    },

    // search non invoice transactions
    async searchNonInvoiceTransactions() {
      this.nonInvoiceTransLoading = true;
      let currentPage = this.allNonInvoiceTrans
        ? this.nonInvoicePagination.current_page
        : 1;
      const { data } = await axios.get(
        window.location.origin +
          "/api/client/" +
          this.$route.params.slug +
          "/non-invoice-payments/search" +
          "?term=" +
          this.nonInvoiceQuery +
          "&page=" +
          currentPage +
          "&perPage=" +
          this.perPage +
          "&startDate=" +
          this.dateRange.startDate +
          "&endDate=" +
          this.dateRange.endDate
      );
      this.allNonInvoiceTrans = data.data;
      this.nonInvoicePagination = data.meta;
      this.nonInvoiceTransLoading = false;
    },

    // non invoice transactions pagination
    async nonInvoiceTransPaginate() {
      this.query === this.nonInvoiceTransactions()
        ? this.searchNonInvoiceTransactions()
        : "";
    },

    // Reset non invoice transactions pagination
    async resetNonInvoiceTransPagination() {
      this.nonInvoicePagination.current_page = 1;
    },

    // Reload non invoice transactions after search
    async nonInvoiceTransReload() {
      this.nonInvoiceQuery = "";
      await this.searchNonInvoiceTransactions();
    },

    // display modal
    previewModal(image) {
      this.imagePath = image;
      if (this.showModal) {
        return (this.showModal = false);
      }
      return (this.showModal = true);
    },

    // get ledger
    async getLedger() {
      this.ledgerLoading = true;
      this.activeTab = "ledger";
      const { data } = await axios.get(
        window.location.origin +
          "/api/client/" +
          this.$route.params.slug +
          "/ledger"
      );
      this.ledgerItems = data.items;
      this.ledgerTotalDiscount = data.totalDiscount;
      this.ledgerTotalDebit = data.totalDebit;
      this.ledgerTotalCredit = data.totalCredit;
      this.finalBalance = data.finalBalance;
      this.ledgerLoading = false;
    },

    // generate pdf
    async generatePDF() {
      // Get the HTML content to be converted
      this.headerShow = true;
      const element = document.getElementById("content-to-pdf");
      setTimeout(async () => {
        // Options for PDF generation
        const options = {
          margin: 5,
          filename: this.activeTab + ".pdf",
          image: { type: "jpeg", quality: 0.98 },
          html2canvas: { scale: 2 },
          jsPDF: { unit: "mm", format: "a4", orientation: "landscape" },
        };

        // Generate PDF from HTML content
        html2pdf().from(element).set(options).save();
        this.headerShow = false;
      }, 2000);
    },

    // print table
    async print() {
      this.headerShow = true;
      await this.$htmlToPaper(this.activeTab);
      setTimeout(async () => {
        this.headerShow = false;
      }, 2000);
    },

    // print table
    async printActivityLog() {
      await this.$htmlToPaper("printMe");
    },

    // get activity logs
    async getActivity() {
      this.activityLoading = true;
      let currentPage = this.allActivityLog
        ? this.allActivityLogPagination.current_page
        : 1;
      let slug = this.$route.params.slug;
      let modelName = "Client";

      const baseUrl = `${window.location.origin}/api/activity-log-specific`;
      const queryParams = `?page=${currentPage}&perPage=${this.perPage}&slug=${slug}&modelName=${modelName}`;
      const url = baseUrl + queryParams;

      const { data } = await axios.get(url);

      this.allActivityLog = data.data;
      this.allActivityLogPagination = data.meta;
      this.activityLoading = false;
    },

    // search data
    async searchActivity() {
      this.activityLoading = true;
      let currentPage = this.allActivityLog
        ? this.allActivityLogPagination.current_page
        : 1;
      let term = this.activitySearchQuery;
      let slug = this.$route.params.slug;
      let modelName = "Client";

      const baseUrl = `${window.location.origin}/api/activity-log-specific`;
      const queryParams = `?page=${currentPage}&perPage=${this.perPage}&term=${term}&slug=${slug}&modelName=${modelName}`;
      const url = baseUrl + queryParams;

      const { data } = await axios.get(url);

      this.allActivityLog = data.data;
      this.allActivityLogPagination = data.meta;
      this.activityLoading = false;
    },

    // activity pagination
    async activityPaginate() {
      this.getActivity();
    },

    // update activity per page count
    updateActivityPerPager() {
      this.allActivityLogPagination.current_page = 1;
      this.activitySearchQuery === ""
        ? this.getActivity()
        : this.searchActivity();
    },

    // reload after search
    async activityReload() {
      this.activitySearchQuery = "";
    },

    // refresh activity table
    refreshActivityTable() {
      this.activitySearchQuery = "";
      this.activitySearchQuery === ""
        ? this.getActivity()
        : this.searchActivity();
    },

    // reset activity pagination
    async resetActivityPagination() {
      this.allActivityLogPagination.current_page = 1;
    },

    // delete invoice data
    async deleteInvoiceData(slug) {
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
                Fire.$emit("AfterDelete");
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

    // delete invoice return data
    async deleteInvoiceReturnData(slug) {
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
              path: "/api/invoice-returns/",
              slug: slug,
            })
            .then((response) => {
              if (response === true) {
                Swal.fire(
                  this.$t("Deleted!"),
                  this.$t("Deleted successfully."),
                  "success"
                );
                Fire.$emit("AfterDelete");
              } else {
                Swal.fire(
                  this.$t("Failed!"),
                  this.$t("There was something wrong."),
                  "warning"
                );
              }
            });
        }
      });
    },

    // delete invoice payment data
    async deletePaymentData(slug) {
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
              path: "/api/payments/invoice/",
              slug: slug,
            })
            .then((response) => {
              if (response === true) {
                Swal.fire(
                  this.$t("Deleted!"),
                  this.$t("Deleted successfully."),
                  "success"
                );
                Fire.$emit("AfterDelete");
              } else {
                Swal.fire(
                  this.$t("Failed!"),
                  this.$t("Sorry you can't delete this payment!"),
                  "warning"
                );
              }
            });
        }
      });
    },

    // delete non invoice payment data
    async deleteNonInvoicePayment(slug) {
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
              path: "/api/payments/non-invoice/",
              slug: slug,
            })
            .then((response) => {
              if (response === true) {
                Swal.fire(
                  this.$t("Deleted!"),
                  this.$t("Deleted successfully."),
                  "success"
                );
                Fire.$emit("AfterDelete");
              } else {
                Swal.fire(
                  this.$t("Failed!"),
                  this.$t("Sorry you can't delete this payment!"),
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

<style scoped>
tfoot {
  font-weight: 700;
}
.nav-pills .nav-item {
  background: #ddd;
  margin: 2px;
  border-radius: 0.25rem;
}
</style>
