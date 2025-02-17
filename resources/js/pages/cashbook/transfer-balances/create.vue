<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              {{ $t('Create a balance transfer') }}
            </h3>
            <router-link :to="{ name: 'transferBalances.index' }" class="btn btn-dark float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('Back') }}
            </router-link>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" @submit.prevent="saveTransfer" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row">
                <div class="form-group col-md-12">
                  <label for="transferReason">{{ $t('Transfer Reason') }}
                    <span class="required">*</span></label>
                  <input type="text" id="transferReason" v-model="form.transferReason" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('transferReason') }"
                    :placeholder="$t('Enter a reason')" name="transferReason" />
                  <has-error :form="form" field="transferReason" />
                </div>
              </div>
              <div class="row" v-if="items">
                <div class="form-group col-md-6">
                  <label for="fromAccount">{{ $t('From Account') }}
                    <span class="required">*</span></label>
                  <v-select v-model="form.fromAccount" :options="items" label="label"
                    :class="{ 'is-invalid': form.errors.has('fromAccount') }" name="fromAccount"
                    :placeholder="$t('Select an account')" @input="updateBalance">
                     <template slot="option" slot-scope="option">
                        <img :src="option.image" style="width: 30px; height: 30px;" />
                        {{ option.label }}
                    </template>
                  </v-select>
                  <has-error :form="form" field="fromAccount" />
                </div>
                <div class="form-group col-md-6">
                  <label for="toAccount">{{ $t('To Account') }}
                    <span class="required">*</span></label>
                  <v-select v-model="form.toAccount" :options="items" label="label"
                    :class="{ 'is-invalid': form.errors.has('toAccount') }" name="toAccount"
                    :placeholder="$t('Select an account')">
                     <template slot="option" slot-scope="option">
                        <img :src="option.image" style="width: 30px; height: 30px;" />
                        {{ option.label }}
                    </template>
                  </v-select>
                  <has-error :form="form" field="toAccount" />
                </div>
              </div>
              <div class="row" v-if="form.fromAccount">
                <div class="form-group col-md-6">
                  <label for="availableBalance">{{
                    $t('Available Balance')
                  }}</label>
                  <input id="availableBalance" v-model="form.availableBalance" type="number" step="any"
                    class="form-control" :class="{
                      'is-invalid': form.errors.has('availableBalance'),
                    }" name="availableBalance" readonly />
                  <has-error :form="form" field="availableBalance" />
                </div>
                <div class="form-group col-md-6">
                  <label for="amount">{{ $t('Amount') }}
                    <span class="required">*</span></label>
                  <input id="amount" v-model="form.amount" type="number" step="any" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('amount') }" name="amount" placeholder="Enter an amount" />
                  <has-error :form="form" field="amount" />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="date">{{ $t('Date') }}</label>
                  <input id="date" v-model="form.date" type="date" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('date') }" name="date" />
                  <has-error :form="form" field="date" />
                </div>
                <div class="form-group col-md-6">
                  <label for="status">{{ $t('Status') }}</label>
                  <select id="status" v-model="form.status" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('status') }">
                    <option value="1">
                      {{ $t('Active') }}
                    </option>
                    <option value="0">
                      {{ $t('Inactive') }}
                    </option>
                  </select>
                  <has-error :form="form" field="status" />
                </div>
              </div>
              <div class="form-group">
                <label for="note">{{ $t('Note') }}</label>
                <textarea id="note" v-model="form.note" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('note') }" :placeholder="$t('Write your note here!')" />
                <has-error :form="form" field="note" />
              </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <v-button :loading="form.busy" class="btn btn-primary">
                <i class="fas fa-save" /> {{ $t('Save') }}
              </v-button>
              <button type="reset" class="btn btn-secondary float-right" @click="form.reset()">
                <i class="fas fa-power-off" /> {{ $t('Reset') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Form from 'vform'
import { mapGetters } from 'vuex'

export default {
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('Create Transfer') }
  },
  data: () => ({
    breadcrumbsCurrent: 'Create Transfer',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Cashbook',
        url: '',
      },
      {
        name: 'Balance Transfers',
        url: 'transferBalances.index',
      },
      {
        name: 'Create',
        url: '',
      },
    ],
    form: new Form({
      fromAccount: null,
      toAccount: null,
      transferReason: '',
      availableBalance: 0,
      amount: '',
      date: new Date().toISOString().slice(0, 10),
      note: '',
      status: 1,
    }),
    loading: true,
  }),
  computed: {
    ...mapGetters('operations', ['items', 'appInfo']),
  },
  created() {
    this.getAccoutns()
  },
  methods: {
    // get all accounts
    async getAccoutns() {
      await this.$store.dispatch('operations/allData', {
        path: '/api/all-accounts',
      })
      // assign default account
      if (this.items && this.items.length > 0) {
        let defaultAccountSlug = this.appInfo.defaultAccountSlug;
        this.form.fromAccount = this.items.find(account => account.slug == defaultAccountSlug);
        this.updateBalance()
      }
    },

    // update available balance
    updateBalance() {
      return (this.form.availableBalance =
        this.form.fromAccount.availableBalance)
    },

    // save transfer
    async saveTransfer() {
      await this.form
        .post(window.location.origin + '/api/balance-transfers')
        .then(() => {
          toast.fire({
            type: 'success',
            title: this.$t('Balance transfer added successfully'),
          })
          this.$router.push({ name: 'transferBalances.index' })
        })
        .catch(() => {
          toast.fire({ type: 'error', title: this.$t('Opps...something went wrong') })
        })
    },
  },
}
</script>
