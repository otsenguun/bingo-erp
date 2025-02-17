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
                    <strong>{{ $t("Supplier ID") }}</strong>
                    <span class="float-right">{{
                      allData.supplierID | withPrefix(supplierPrefix)
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
                <span v-else class="badge bg-danger">{{
                  $t("Inactive")
                }}</span>
              </div>
            </div>
          </div>
          <!-- /.col -->

          <div class="col-md-12 col-lg-9">
            <div class="row">
              <div class="col-lg-6 col-md-6 col-12">
                <div class="card bg-info">
                  <div class="card-content">
                    <div class="card-body pb-1">
                      <div class="row">
                        <div class="col-6">
                          <h6 class="text-white">
                            {{ $t("Purchase Total") }}
                          </h6>
                          <h6 class="text-white">
                            {{ $t("Non Purchase Total") }}
                          </h6>
                          <hr />
                          <h4 class="text-white mb-1">
                            {{ $t("Total") }}
                          </h4>
                        </div>
                        <div class="col-6 text-right">
                          <h6 class="text-white">
                            {{ allData.purchaseTotal | withCurrency }}
                          </h6>
                          <h6 class="text-white">
                            {{ allData.nonPurchaseTotalDue | withCurrency }}
                          </h6>
                          <hr />
                          <h4 class="text-white mb-1">
                            {{
                              (allData.purchaseTotal +
                                allData.nonPurchaseTotalDue)
                                | withCurrency
                            }}
                          </h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-12">
                <div class="card bg-danger">
                  <div class="card-content">
                    <div class="card-body pb-1">
                      <div class="row">
                        <div class="col-6">
                          <h6 class="text-white">
                            {{ $t("Purchase Due") }}
                          </h6>
                          <h6 class="text-white">
                            {{ $t("Non Purchase Due") }}
                          </h6>
                          <hr />
                          <h4 class="text-white mb-1">
                            {{ $t("Total Due") }}
                          </h4>
                        </div>
                        <div class="col-6 text-right">
                          <h6 class="text-white">
                            {{ allData.purchaseTotalDue | withCurrency }}
                          </h6>
                          <h6 class="text-white">
                            {{ allData.nonPurchaseCurrentDue | withCurrency }}
                          </h6>
                          <hr />
                          <h4 class="text-white mb-1">
                            {{
                              (allData.purchaseTotalDue +
                                allData.nonPurchaseCurrentDue)
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
                $can('purchase-list') ||
                $can('purchase-return-list') ||
                $can('purchase-payment-list') ||
                $can('non-purchase-payment-list')
              "
              class="card"
            >
              <div class="card-header p-2">
                <div class="row">
                  <div class="col-md-10">
                    <ul class="nav nav-pills">
                      <li v-if="$can('purchase-list')" class="nav-item">
                        <a
                          class="nav-link active"
                          href="#purchases"
                          data-toggle="tab"
                          @click="activeTab = 'purchases'"
                        >
                          <i class="fas fa-shopping-basket"></i>
                          {{ $t("Purchases") }}
                          <span v-if="pagination" class="badge badge-dark">{{
                            pagination.total
                          }}</span></a
                        >
                      </li>
                      <li v-if="$can('purchase-return-list')" class="nav-item">
                        <a
                          @click="getSupplierReturns"
                          class="nav-link"
                          href="#purchase-returns"
                          data-toggle="tab"
                        >
                          <i class="fas fa-undo-alt"></i>
                          {{ $t("Purchase Returns") }}
                          <span
                            v-if="returnPagination"
                            class="badge badge-dark"
                            >{{ returnPagination.total }}</span
                          ></a
                        >
                      </li>
                      <li v-if="$can('purchase-payment-list')" class="nav-item">
                        <a
                          @click="getSupplierPayments"
                          class="nav-link"
                          href="#purchase-payments"
                          data-toggle="tab"
                        >
                          <i class="fas fa-receipt"></i>
                          {{
                            $t("Purchase Payments")
                          }}
                          <span
                            v-if="paymentPagination"
                            class="badge badge-dark"
                            >{{ paymentPagination.total }}</span
                          ></a
                        >
                      </li>
                      <li
                        v-if="$can('non-purchase-payment-list')"
                        class="nav-item"
                      >
                        <a
                          @click="getNonPurchaseTransactions"
                          class="nav-link"
                          href="#non-purchase-transactions"
                          data-toggle="tab"
                        >
                          <i class="fas fa-money-bill"></i>
                          {{ $t("Non Purchase Transactions") }}
                          <span
                            v-if="transactionPagination"
                            class="badge badge-dark"
                            >{{ transactionPagination.total }}</span
                          ></a
                        >
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
                        :to="{ name: 'suppliers.index' }"
                        class="btn btn-dark float-right"
                        v-tooltip="$t('Back')"
                      >
                        <i class="fas fa-long-arrow-alt-left" />
                      </router-link>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.card-header -->
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
                  <!-- Purchases -->
                  <div
                    v-if="$can('purchase-list')"
                    class="tab-pane active"
                    id="purchases"
                  >
                    <div>
                      <div class="card-body p-0 position-relative">
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
                          <div class="col-12 col-md-3 pull-right text-right">
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
                              @update="updateValues('purchases')"
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
                                <th>
                                  {{ $t("Purchase No") }}
                                </th>
                                <th>{{ $t("Date") }}</th>
                                <th>{{ $t("Subtotal") }}</th>
                                <th>{{ $t("Transport") }}</th>
                                <th>{{ $t("Discount") }}</th>
                                <th>{{ $t("Net Total") }}</th>
                                <th>{{ $t("Total Paid") }}</th>
                                <th>{{ $t("Total Due") }}</th>
                                <th>{{ $t("Status") }}</th>
                                <th
                                  v-if="
                                    $can('purchase-edit') ||
                                    $can('purchase-view') ||
                                    $can('purchase-delete')
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
                                    v-if="
                                      pagination && pagination.current_page > 1
                                    "
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
                                    v-if="$can('purchase-view')"
                                    :to="{
                                      name: 'purchases.show',
                                      params: { slug: data.slug },
                                    }"
                                  >
                                    {{ data.code | withPrefix(purchasePrefix) }}
                                  </router-link>
                                  <span v-else
                                    >{{
                                      data.code | withPrefix(purchasePrefix)
                                    }}
                                  </span>
                                </td>
                                <td>
                                  <span v-if="data.purchaseDate">{{
                                    data.purchaseDate | moment("Do MMM, YYYY")
                                  }}</span>
                                </td>
                                <td>{{ data.subTotal | withCurrency }}</td>
                                <td>{{ data.transport | withCurrency }}</td>
                                <td>{{ data.totalDiscount | withCurrency }}</td>
                                <td>{{ data.purchaseTotal | withCurrency }}</td>
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
                                    $can('purchase-edit') ||
                                    $can('purchase-view') ||
                                    $can('purchase-delete')
                                  "
                                  class="text-right no-print"
                                  id="element-to-hide"
                                  data-html2canvas-ignore="true"
                                >
                                  <div class="btn-group">
                                    <router-link
                                      v-if="$can('purchase-view')"
                                      v-tooltip="$t('View')"
                                      :to="{
                                        name: 'purchases.show',
                                        params: { slug: data.slug },
                                      }"
                                      class="btn btn-primary btn-sm"
                                    >
                                      <i class="fas fa-eye" />
                                    </router-link>
                                    <router-link
                                      v-if="$can('purchase-edit')"
                                      v-tooltip="$t('Edit')"
                                      :to="{
                                        name: 'purchases.edit',
                                        params: { slug: data.slug },
                                      }"
                                      class="btn btn-info btn-sm"
                                    >
                                      <i class="fas fa-edit" />
                                    </router-link>
                                    <a
                                      v-if="$can('purchase-delete')"
                                      v-tooltip="$t('Delete')"
                                      href="#"
                                      class="btn btn-danger btn-sm"
                                      @click="deletePurchaseData(data.slug)"
                                    >
                                      <i class="fas fa-trash" />
                                    </a>
                                  </div>
                                </td>
                              </tr>
                              <tr v-show="!loading && items && !items.length">
                                <td colspan="11">
                                  <EmptyTable />
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
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
                              @change="updatePerPager('purchases')"
                              v-model="perPage"
                              class="form-control form-control-sm ml-1"
                            >
                              <!-- options component -->
                              <option
                                v-for="(option, i) in options"
                                :value="option.value"
                                :key="i"
                              >
                                {{ option.text }}
                              </option>
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
                  </div>

                  <!-- purchase-returns -->
                  <div
                    v-if="$can('purchase-return-list')"
                    class="tab-pane"
                    id="purchase-returns"
                  >
                    <div>
                      <div class="card-body p-0 position-relative">
                        <div
                          class="row no-print"
                          id="element-to-hide"
                          data-html2canvas-ignore="true"
                        >
                          <div class="col-12 col-md-9 mb-2">
                            <search
                              v-model="returnsQuery"
                              @reset-pagination="resetReturnPagination"
                              @reload="returnsReload"
                            />
                          </div>
                          <div class="col-12 col-md-3 pull-right text-right">
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
                              @update="updateValues('purchase-returns')"
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
                        <table-loading v-show="returnLoading" />
                        <div class="table-responsive table-custom mt-3">
                          <table class="table">
                            <thead>
                              <tr>
                                <th>{{ $t("#") }}</th>
                                <th>
                                  {{ $t("Return No") }}
                                </th>
                                <th>
                                  {{ $t("Purchase No") }}
                                </th>
                                <th>
                                  {{
                                    $t("Return Reason")
                                  }}
                                </th>
                                <th>{{ $t("Cost of Return Products") }}</th>
                                <th>{{ $t("Date") }}</th>
                                <th>{{ $t("Status") }}</th>
                                <th
                                  v-if="
                                    $can('purchase-return-edit') ||
                                    $can('purchase-return-view') ||
                                    $can('purchase-return-delete')
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
                                v-show="allReturns && allReturns.length"
                                v-for="(data, i) in allReturns"
                                :key="i"
                              >
                                <td>
                                  <span
                                    v-if="
                                      returnPagination &&
                                      returnPagination.current_page > 1
                                    "
                                  >
                                    {{
                                      returnPagination.per_page *
                                        (returnPagination.current_page - 1) +
                                      (i + 1)
                                    }}
                                  </span>
                                  <span v-else>{{ i + 1 }}</span>
                                </td>
                                <td>
                                  <router-link
                                    v-if="$can('purchase-return-view')"
                                    :to="{
                                      name: 'purchaseReturns.show',
                                      params: { slug: data.slug },
                                    }"
                                  >
                                    {{
                                      data.purReturnNo
                                        | withPrefix(purchaseReturnPrefix)
                                    }}
                                  </router-link>
                                  <span v-else>{{
                                    data.purReturnNo
                                      | withPrefix(purchaseReturnPrefix)
                                  }}</span>
                                </td>
                                <td>
                                  {{
                                    data.purchaseNo | withPrefix(purchasePrefix)
                                  }}
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
                                    $can('purchase-return-edit') ||
                                    $can('purchase-return-view') ||
                                    $can('purchase-return-delete')
                                  "
                                  class="text-right no-print"
                                  id="element-to-hide"
                                  data-html2canvas-ignore="true"
                                >
                                  <div class="btn-group">
                                    <router-link
                                      v-if="$can('purchase-return-view')"
                                      v-tooltip="$t('View')"
                                      :to="{
                                        name: 'purchaseReturns.show',
                                        params: { slug: data.slug },
                                      }"
                                      class="btn btn-primary btn-sm"
                                    >
                                      <i class="fas fa-eye" />
                                    </router-link>
                                    <router-link
                                      v-if="$can('purchase-return-edit')"
                                      v-tooltip="$t('Edit')"
                                      :to="{
                                        name: 'purchaseReturns.edit',
                                        params: { slug: data.slug },
                                      }"
                                      class="btn btn-info btn-sm"
                                    >
                                      <i class="fas fa-edit" />
                                    </router-link>
                                    <a
                                      v-if="$can('purchase-return-delete')"
                                      v-tooltip="$t('Delete')"
                                      href="#"
                                      class="btn btn-danger btn-sm"
                                      @click="
                                        deletePurchaseReturnData(data.slug)
                                      "
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
                      </div>
                      <!-- NEW PAGINATION -->
                      <div
                        v-if="returnPagination && returnPagination.total > 0"
                        class="dtable-footer no-print"
                        id="element-to-hide"
                        data-html2canvas-ignore="true"
                      >
                        <div class="form-group row display-per-page">
                          <label>{{ $t("per_page") }} </label>
                          <div>
                            <select
                              @change="updatePerPager('purchase-returns')"
                              v-model="perPage"
                              class="form-control form-control-sm ml-1"
                            >
                              <!-- options component -->
                              <option
                                v-for="(option, i) in options"
                                :value="option.value"
                                :key="i"
                              >
                                {{ option.text }}
                              </option>
                            </select>
                          </div>
                        </div>
                        <!-- pagination-start -->
                        <pagination
                          v-if="
                            returnPagination && returnPagination.last_page > 1
                          "
                          :pagination="returnPagination"
                          :offset="5"
                          class="justify-flex-end"
                          @paginate="returnsPaginate"
                        />
                        <!-- pagination-end -->
                      </div>
                      <!-- NEW PAGINATION END -->
                    </div>
                  </div>

                  <!-- purchase-payments -->
                  <div
                    v-if="$can('purchase-payment-list')"
                    class="tab-pane"
                    id="purchase-payments"
                  >
                    <div>
                      <div class="card-body p-0 position-relative">
                        <div
                          class="row no-print"
                          id="element-to-hide"
                          data-html2canvas-ignore="true"
                        >
                          <div class="col-12 col-md-9 mb-2">
                            <search
                              v-model="paymentsQuery"
                              @reset-pagination="resetPaymetnsPagination"
                              @reload="paymentsReload"
                            />
                          </div>
                          <div class="col-12 col-md-3 pull-right text-right">
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
                              @update="updateValues('purchase-payments')"
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
                                <th>
                                  {{ $t("Purchase No") }}
                                </th>
                                <th>{{ $t("Total") }}</th>
                                <th>{{ $t("Paid Amount") }}</th>
                                <th>{{ $t("Account") }}</th>
                                <th>{{ $t("Payment Date") }}</th>
                                <th>{{ $t("Status") }}</th>
                                <th
                                  v-if="
                                    $can('purchase-payment-edit') ||
                                    $can('purchase-payment-view') ||
                                    $can('purchase-payment-delete')
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
                                v-show="allPayments && allPayments.length"
                                v-for="(data, i) in allPayments"
                                :key="i"
                              >
                                <td>
                                  <span
                                    v-if="
                                      paymentPagination &&
                                      paymentPagination.current_page > 1
                                    "
                                  >
                                    {{
                                      paymentPagination.per_page *
                                        (paymentPagination.current_page - 1) +
                                      (i + 1)
                                    }}
                                  </span>
                                  <span v-else>{{ i + 1 }}</span>
                                </td>
                                <td v-if="data.purchase">
                                  <router-link
                                    v-if="$can('purchase-view')"
                                    :to="{
                                      name: 'purchases.show',
                                      params: { slug: data.purchase.slug },
                                    }"
                                  >
                                    {{ data.purchase.purchaseNo }}
                                  </router-link>
                                  <span v-else>{{
                                    data.purchase.purchaseNo
                                  }}</span>
                                </td>

                                <td v-if="data.purchase">
                                  {{
                                    data.purchase.purchaseTotal | withCurrency
                                  }}
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
                                    $can('purchase-payment-edit') ||
                                    $can('purchase-payment-view') ||
                                    $can('purchase-payment-delete')
                                  "
                                  class="text-right no-print"
                                  id="element-to-hide"
                                  data-html2canvas-ignore="true"
                                >
                                  <div class="btn-group">
                                    <router-link
                                      v-if="$can('purchase-payment-view')"
                                      v-tooltip="$t('View')"
                                      :to="{
                                        name: 'purchasePayments.show',
                                        params: { slug: data.slug },
                                      }"
                                      class="btn btn-primary btn-sm"
                                    >
                                      <i class="fas fa-eye" />
                                    </router-link>
                                    <router-link
                                      v-if="$can('purchase-payment-edit')"
                                      v-tooltip="$t('Edit')"
                                      :to="{
                                        name: 'purchasePayments.edit',
                                        params: { slug: data.slug },
                                      }"
                                      class="btn btn-info btn-sm"
                                    >
                                      <i class="fas fa-edit" />
                                    </router-link>
                                    <a
                                      v-if="$can('purchase-payment-delete')"
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
                                <td colspan="8">
                                  <EmptyTable />
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
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
                              @change="updatePerPager('purchases-payments')"
                              v-model="perPage"
                              class="form-control form-control-sm ml-1"
                            >
                              <!-- options component -->
                              <option
                                v-for="(option, i) in options"
                                :value="option.value"
                                :key="i"
                              >
                                {{ option.text }}
                              </option>
                            </select>
                          </div>
                        </div>
                        <!-- pagination-start -->
                        <pagination
                          v-if="
                            paymentPagination && paymentPagination.last_page > 1
                          "
                          :pagination="
                            allPayments
                              ? paymentPagination
                              : { current_page: 1 }
                          "
                          :offset="5"
                          class="justify-flex-end"
                          @paginate="paymentsPaginate"
                        />
                        <!-- pagination-end -->
                      </div>
                      <!-- NEW PAGINATION END -->
                    </div>
                  </div>

                  <!-- non-purchase-transactions -->
                  <div
                    v-if="$can('non-purchase-payment-list')"
                    class="tab-pane"
                    id="non-purchase-transactions"
                  >
                    <div>
                      <div class="card-body p-0 position-relative">
                        <div
                          class="row no-print"
                          id="element-to-hide"
                          data-html2canvas-ignore="true"
                        >
                          <div class="col-12 col-md-9 mb-2">
                            <search
                              v-model="transactionsQuery"
                              @reset-pagination="resetTransactionsPagination"
                              @reload="transactionsReload"
                            />
                          </div>
                          <div class="col-12 col-md-3 pull-right text-right">
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
                              @update="
                                updateValues('non-purchase-transactions')
                              "
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
                        <table-loading v-show="transactionLoading" />
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
                                    $can('non-purchase-payment-view') ||
                                    $can('non-purchase-payment-edit') ||
                                    $can('non-purchase-payment-delete')
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
                                v-show="
                                  allTransactions && allTransactions.length
                                "
                                v-for="(data, i) in allTransactions"
                                :key="i"
                              >
                                <td>
                                  <span
                                    v-if="
                                      transactionPagination &&
                                      transactionPagination.current_page > 1
                                    "
                                  >
                                    {{
                                      transactionPagination.per_page *
                                        (transactionPagination.current_page -
                                          1) +
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
                                    $can('non-purchase-payment-view') ||
                                    $can('non-purchase-payment-edit') ||
                                    $can('non-purchase-payment-delete')
                                  "
                                  class="text-right no-print"
                                  id="element-to-hide"
                                  data-html2canvas-ignore="true"
                                >
                                  <div class="btn-group">
                                    <router-link
                                      v-if="$can('non-purchase-payment-edit')"
                                      v-tooltip="$t('Edit')"
                                      :to="{
                                        name: 'nonPurchasePayments.edit',
                                        params: { slug: data.slug },
                                      }"
                                      class="btn btn-info btn-sm"
                                    >
                                      <i class="fas fa-edit" />
                                    </router-link>
                                    <a
                                      v-if="$can('non-purchase-payment-delete')"
                                      v-tooltip="$t('Delete')"
                                      href="#"
                                      class="btn btn-danger btn-sm"
                                      @click="deleteTransactionData(data.slug)"
                                    >
                                      <i class="fas fa-trash" />
                                    </a>
                                  </div>
                                </td>
                              </tr>
                              <tr v-show="!loading && !allTransactions.length">
                                <td colspan="7">
                                  <EmptyTable />
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <!-- NEW PAGINATION -->
                      <div
                        v-if="
                          transactionPagination &&
                          transactionPagination.total > 0
                        "
                        class="dtable-footer no-print"
                        id="element-to-hide"
                        data-html2canvas-ignore="true"
                      >
                        <div class="form-group row display-per-page">
                          <label>{{ $t("per_page") }} </label>
                          <div>
                            <select
                              @change="
                                updatePerPager('non-purchase-transactions')
                              "
                              v-model="perPage"
                              class="form-control form-control-sm ml-1"
                            >
                              <!-- options component -->
                              <option
                                v-for="(option, i) in options"
                                :value="option.value"
                                :key="i"
                              >
                                {{ option.text }}
                              </option>
                            </select>
                          </div>
                        </div>
                        <!-- pagination-start -->
                        <pagination
                          v-if="
                            transactionPagination &&
                            transactionPagination.last_page > 1
                          "
                          :pagination="transactionPagination"
                          :offset="5"
                          class="justify-flex-end"
                          @paginate="transactionsPaginate"
                        />
                        <!-- pagination-end -->
                      </div>
                      <!-- NEW PAGINATION END -->
                    </div>
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
                                  $can('purchase-view') &&
                                  data.action_type == 'purchase'
                                "
                                :to="{
                                  name: 'purchases.show',
                                  params: { slug: data.slug },
                                }"
                              >
                                {{ data.particulars }}
                              </router-link>
                              <router-link
                                v-if="data.action_type == 'purchase-payment'"
                                :to="{
                                  name: 'purchasePayments.show',
                                  params: { slug: data.slug },
                                }"
                              >
                                {{ data.particulars }}
                              </router-link>
                              <router-link
                                v-if="
                                  $can('purchase-return-view') &&
                                  data.action_type == 'purchase-return'
                                "
                                :to="{
                                  name: 'purchaseReturns.show',
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
                            <td>
                              {{ $t("Non Purchase Due") }}
                            </td>
                            <td>{{ 0 | withCurrency }}</td>
                            <td>
                              {{ allData.nonPurchaseCurrentDue | withCurrency }}
                            </td>
                            <td>{{ 0 | withCurrency }}</td>
                            <td>
                              {{
                                (ledgerItems[ledgerItems.length - 1].balance +
                                  allData.nonPurchaseCurrentDue)
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
                                  allData.nonPurchaseCurrentDue)
                                  | withCurrency
                              }}
                            </td>
                            <td>{{ ledgerTotalDiscount | withCurrency }}</td>
                            <td>
                              {{
                                (ledgerItems[ledgerItems.length - 1].balance +
                                  allData.nonPurchaseCurrentDue)
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
    return { title: this.$t("Supplier Details") };
  },
  components: {
    DateRangePicker,
  },
  data: () => ({
    breadcrumbsCurrent: "Supplier Details",
    breadcrumbs: [
      {
        name: "Dashboard",
        url: "home",
      },
      {
        name: "Suppliers",
        url: "suppliers.index",
      },
      {
        name: "Details",
        url: "",
      },
    ],
    url: null,
    query: "",
    allData: "",
    showModal: false,
    allReturns: "",
    returnsQuery: "",
    returnPagination: "",
    returnLoading: false,
    allPayments: "",
    paymentsQuery: "",
    paymentPagination: "",
    paymentsLoading: false,
    allTransactions: "",
    transactionsQuery: "",
    transactionPagination: "",
    transactionLoading: false,
    supplierPrefix: "",
    purchasePrefix: "",
    purchaseReturnPrefix: "",
    activitySearchQuery: "",
    allActivityLog: "",
    allActivityLogPagination: "",
    activityLoading: false,
    perPage: 10,
    options: [
      { value: "10", text: "10" },
      { value: "25", text: "25" },
      { value: "50", text: "50" },
      { value: "100", text: "100" },
    ],
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
    activeTab: "purchases",
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
    // watch purchase search data
    query: function (newQ, oldQ) {
      if (newQ === "") {
        if (this.dateRange.startDate && this.dateRange.endDate) {
          this.searchPurchaseData();
        } else {
          this.getSupplierPurchases();
        }
      } else {
        this.searchPurchaseData();
      }
    },
    // watch return search data
    returnsQuery: function (newQ, oldQ) {
      if (newQ === "") {
        this.getSupplierReturns();
      } else {
        this.searchReturnData();
      }
    },
    // watch payment search data
    paymentsQuery: function (newQ, oldQ) {
      if (newQ === "") {
        this.getSupplierPayments();
      } else {
        this.searchPaymentData();
      }
    },
    // watch non purchase search data
    transactionsQuery: function (newQ, oldQ) {
      if (newQ === "") {
        this.getNonPurchaseTransactions();
      } else {
        this.searchNonPurchaseTransactionData();
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
    this.getSupplier();
    this.getSupplierPurchases();
    this.supplierPrefix = this.appInfo.supplierPrefix;
    this.purchasePrefix = this.appInfo.purchasePrefix;
    this.purchaseReturnPrefix = this.appInfo.purchaseReturnPrefix;
    Fire.$on("AfterDelete", () => {
      this.getSupplierPurchases();
      this.getSupplierReturns();
      this.getSupplierPayments();
      this.getNonPurchaseTransactions();
    });
  },
  methods: {
    switchTab(tabName) {
      switch (tabName) {
        case "purchases":
          this.searchPurchaseData();
          break;
        case "purchase-returns":
          this.searchReturnData();
          break;
        case "purchase-payments":
          this.searchPaymentData();
          break;
        case "non-purchase-transactions":
          this.searchNonPurchaseTransactionData();
          break;
      }
    },

    async loadInitialData() {
      this.getSupplier();
      this.getSupplierPurchases();
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

    // get the supplier
    async getSupplier() {
      const { data } = await axios.get(
        window.location.origin + "/api/suppliers/" + this.$route.params.slug
      );
      this.allData = data.data;
    },

    // update per page count
    updatePerPager(tabName) {
      this.pagination.current_page = 1;
      this.returnPagination.hasOwnProperty("current_page")
        ? (this.returnPagination.current_page = 1)
        : "";
      this.paymentPagination.hasOwnProperty("current_page")
        ? (this.paymentPagination.current_page = 1)
        : "";
      this.transactionPagination.hasOwnProperty("current_page")
        ? (this.transactionPagination.current_page = 1)
        : "";

      this.switchTab(tabName);
    },

    // get the supplier purchases
    async getSupplierPurchases() {
      this.activeTab = "purchases";
      let currentPage = this.pagination ? this.pagination.current_page : 1;
      this.$store.state.operations.loading = true;
      await this.$store.dispatch("operations/fetchData", {
        path: "/api/purchases/supplier/" + this.$route.params.slug + "?page=",
        currentPage: currentPage + "&perPage=" + this.perPage,
      });
    },

    // search purchases
    async searchPurchaseData() {
      this.$store.state.operations.loading = true;
      let currentPage = this.pagination ? this.pagination.current_page : 1;
      await this.$store.dispatch("operations/searchData", {
        path: "/api/purchases/supplier/" + this.$route.params.slug + "/search",
        term: this.query,
        currentPage: currentPage + "&perPage=" + this.perPage,
        startDate: this.dateRange.startDate,
        endDate: this.dateRange.endDate,
      });
    },

    // Pagination
    async paginate() {
      this.query === ""
        ? this.getSupplierPurchases()
        : this.searchPurchaseData();
    },

    // Reset purchase pagination
    async resetPagination() {
      this.pagination.current_page = 1;
    },

    //Reload purchases after search
    async reload() {
      this.query = "";
    },

    // Get the supplier returns
    async getSupplierReturns() {
      this.activeTab = "purchase-returns";
      this.returnLoading = true;
      let currentPage = this.allReturns
        ? this.returnPagination.current_page
        : 1;
      const { data } = await axios.get(
        window.location.origin +
          "/api/purchase-returns/supplier/" +
          this.$route.params.slug +
          "?page=" +
          currentPage +
          "&perPage=" +
          this.perPage
      );
      this.allReturns = data.data;
      this.returnPagination = data.meta;
      this.returnLoading = false;
    },

    // search returns
    async searchReturnData() {
      this.returnLoading = true;
      let currentPage = this.allReturns
        ? this.returnPagination.current_page
        : 1;
      const { data } = await axios.get(
        window.location.origin +
          "/api/purchase-returns/supplier/" +
          this.$route.params.slug +
          "/search" +
          "?term=" +
          this.returnsQuery +
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
      this.returnPagination = data.meta;
      this.returnLoading = false;
    },

    // Returns pagination
    async returnsPaginate() {
      this.returnsQuery === ""
        ? this.getSupplierReturns()
        : this.searchReturnData();
    },

    // Reset return pagination
    async resetReturnPagination() {
      this.returnPagination.current_page = 1;
    },

    // Reload returns after search
    async returnsReload() {
      this.returnsQuery = "";
      await this.searchReturnData();
    },

    // Get the supplier payments
    async getSupplierPayments() {
      this.activeTab = "purchase-payments";
      this.paymentsLoading = true;
      let currentPage = this.allPayments
        ? this.paymentPagination.current_page
        : 1;
      const { data } = await axios.get(
        window.location.origin +
          "/api/payments/supplier/" +
          this.$route.params.slug +
          "?page=" +
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
          "/api/payments/supplier/" +
          this.$route.params.slug +
          "/search" +
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
      this.paymentsQuery === ""
        ? this.getSupplierPayments()
        : this.searchPaymentData();
    },

    // Reset payments pagination
    async resetPaymetnsPagination() {
      this.paymentPagination.current_page = 1;
    },

    // Reload payments after search
    async paymentsReload() {
      this.paymentsQuery = "";
      await this.searchPaymentData();
    },

    // Get the supplier non purchase transactions
    async getNonPurchaseTransactions() {
      this.activeTab = "non-purchase-transactions";
      this.transactionLoading = true;
      let currentPage = this.allTransactions
        ? this.transactionPagination.current_page
        : 1;
      const { data } = await axios.get(
        window.location.origin +
          "/api/non-purchases/supplier/" +
          this.$route.params.slug +
          "?page=" +
          currentPage +
          "&perPage=" +
          this.perPage
      );
      this.allTransactions = data.data;
      this.transactionPagination = data.meta;
      this.transactionLoading = false;
    },

    // search non purchase transactions
    async searchNonPurchaseTransactionData() {
      this.transactionLoading = true;
      let currentPage = this.allTransactions
        ? this.transactionPagination.current_page
        : 1;
      const { data } = await axios.get(
        window.location.origin +
          "/api/non-purchases/supplier/" +
          this.$route.params.slug +
          "/search" +
          "?term=" +
          this.transactionsQuery +
          "&page=" +
          currentPage +
          "&perPage=" +
          this.perPage +
          "&startDate=" +
          this.dateRange.startDate +
          "&endDate=" +
          this.dateRange.endDate
      );
      this.allTransactions = data.data;
      this.transactionPagination = data.meta;
      this.transactionLoading = false;
    },

    // Non purchase transactions pagination
    async transactionsPaginate() {
      this.query === ""
        ? this.getNonPurchaseTransactions()
        : this.searchNonPurchaseTransactionData();
    },

    // Reset non purchase transactions pagination
    async resetTransactionsPagination() {
      this.transactionPagination.current_page = 1;
    },

    // Reload on purchase transactions after search
    async transactionsReload() {
      this.transactionsQuery = "";
      await this.searchNonPurchaseTransactionData();
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
          "/api/supplier/" +
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
      let modelName = "Supplier";

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
      let modelName = "Supplier";

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

    // delete purchase data
    async deletePurchaseData(slug) {
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
              path: "/api/purchases/",
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

    // delete purchase return data
    async deletePurchaseReturnData(slug) {
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
              path: "/api/purchase-returns/",
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

    // delete purchase payment data
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
              path: "/api/payments/purchase/",
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

    // delete non purchase payment data
    async deleteTransactionData(slug) {
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
              path: "/api/payments/non-purchase/",
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
                  this.$t(
                    "Sorry you can't delete this payment!"
                  ),
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
