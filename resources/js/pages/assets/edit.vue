<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">{{ $t('Edit Asset') }}</h3>
            <router-link :to="{ name: 'assets.index' }" class="btn btn-dark float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('Back') }}
            </router-link>
          </div>
          <!-- /.card-header -->

          <!-- form start -->
          <form role="form" enctype="multipart/form-data" @submit.prevent="updateAsset" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="name">{{ $t('Asset Name') }}
                    <span class="required">*</span></label>
                  <input id="name" v-model="form.name" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('name') }" name="name"
                    :placeholder="$t('Enter asset name')" />
                  <has-error :form="form" field="name" />
                </div>
                <div v-if="items" class="form-group col-md-6">
                  <label for="assetType">{{ $t('Asset Type') }}
                    <span class="required">*</span></label>
                  <v-select v-model="form.assetType" :options="items" label="name"
                    :class="{ 'is-invalid': form.errors.has('assetType') }" name="assetType"
                    :placeholder="$t('Select an asset type')" />
                  <has-error :form="form" field="assetType" />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="assetCost">{{ $t('Asset Cost') }}
                    <span class="required">*</span></label>
                  <input id="assetCost" v-model="form.assetCost" type="number" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('assetCost') }" name="assetCost"
                    :placeholder="$t('Enter asset cost')" @change="calculateDepreciationExpense"
                    @keyup="calculateDepreciationExpense" />
                  <has-error :form="form" field="assetCost" />
                </div>
                <div class="form-group col-md-6">
                  <label for="depreciation">{{ $t('Depreciation') }}
                    <span class="required">*</span></label>
                  <select id="depreciation" v-model="form.depreciation" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('depreciation') }">
                    <option value="1">{{ $t('Yes') }}</option>
                    <option value="0">{{ $t('No') }}</option>
                  </select>
                  <has-error :form="form" field="depreciation" />
                </div>
              </div>
              <div v-if="form.depreciation == 1" class="row">
                <div class="form-group col-md-3">
                  <label for="depreciationType">{{
                    $t('Depreciation Type')
                  }}</label>
                  <select id="depreciationType" v-model="form.depreciationType" class="form-control" :class="{
                    'is-invalid': form.errors.has('depreciationType'),
                  }" @change="calculateDepreciationExpense">
                    <option value="Month">{{ $t('Monthly') }}</option>
                    <option value="Year">{{ $t('Yearly') }}</option>
                  </select>
                  <has-error :form="form" field="depreciationType" />
                </div>
                <div class="form-group col-md-3">
                  <label for="salvageValue">{{
                    $t('Salvage Value')
                  }}</label>
                  <input id="salvageValue" v-model="form.salvageValue" type="number" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('salvageValue') }" name="salvageValue"
                    :placeholder="$t('Salvage Value')" @change="calculateDepreciationExpense"
                    @keyup="calculateDepreciationExpense" />
                  <has-error :form="form" field="salvageValue" />
                </div>
                <div class="form-group col-md-3">
                  <label for="usefulLife">{{ $t('Useful Life') }}
                    <span class="required">*</span></label>
                  <input id="usefulLife" v-model="form.usefulLife" type="number" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('usefulLife') }" name="usefulLife"
                    :placeholder="$t('Enter useful life')" @change="calculateDepreciationExpense"
                    @keyup="calculateDepreciationExpense" />
                  <has-error :form="form" field="usefulLife" />
                </div>
                <div class="form-group col-md-3">
                  <label for="depreciationExpense">{{
                    $t('Depreciation')
                  }}</label>
                  <input id="depreciationExpense" v-model="form.depreciationExpense" type="text" class="form-control"
                    name="depreciationExpense" readonly />
                </div>
              </div>

              <div class="form-group">
                <label for="note">{{ $t('Note') }}</label>
                <textarea id="note" v-model="form.note" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('note') }" :placeholder="$t('Write your note here!')" />
                <has-error :form="form" field="note" />
              </div>
              <div class="row">
                <div class="form-group col-md-3">
                  <label for="date">{{ $t('Date') }}
                    <span class="required">*</span></label>
                  <input id="date" v-model="form.date" type="date" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('date') }" name="date" />
                  <has-error :form="form" field="date" />
                </div>
                <div class="form-group col-md-3">
                  <label for="status">{{ $t('Status') }}</label>
                  <select id="status" v-model="form.status" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('status') }">
                    <option value="1">{{ $t('Active') }}</option>
                    <option value="0">{{ $t('Inactive') }}</option>
                  </select>
                  <has-error :form="form" field="status" />
                </div>
                <div class="form-group col-md-6">
                  <label for="image">{{ $t('Image') }}</label>
                  <div class="custom-file">
                    <input id="image" type="file" class="custom-file-input" name="image"
                      :class="{ 'is-invalid': form.errors.has('image') }" @change="onFileChange" />
                    <label class="custom-file-label" for="image">{{
                      $t('Choose file')
                    }}</label>
                  </div>
                  <has-error :form="form" field="image" />
                  <div class="bg-light mt-4 w-25">
                    <img v-if="url" :src="url" class="img-fluid" :alt="$t('Attached Image')" />
                  </div>
                </div>
              </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <v-button :loading="form.busy" class="btn btn-primary">
                <i class="fas fa-edit" /> {{ $t('Save changes') }}
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
import axios from 'axios'
import { mapGetters } from 'vuex'


export default {
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('Edit Asset') }
  },
  data: () => ({
    breadcrumbsCurrent: 'Edit Asset',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Assets',
        url: 'assets.index',
      },
      {
        name: 'Edit',
        url: '',
      },
    ],
    form: new Form({
      name: '',
      assetCost: '',
      assetType: null,
      depreciation: 0,
      salvageValue: 0,
      usefulLife: 0,
      depreciationExpense: '',
      depreciationType: 'Year',
      image: '',
      date: new Date().toISOString().slice(0, 10),
      note: '',
      status: 1,
    }),
    loading: true,
    url: null,
  }),
  computed: {
    ...mapGetters('operations', ['items']),
  },
  created() {
    this.getAsset()
    this.getTypes()
  },
  methods: {
    // get all types
    async getTypes() {
      await this.$store.dispatch('operations/allData', {
        path: '/api/all-asset-types',
      })
    },
    // get asset
    async getAsset() {
      const { data } = await axios.get(
        window.location.origin + '/api/assets/' + this.$route.params.slug
      )
      this.form.name = data.data.name
      this.form.assetCost = data.data.amount
      this.form.note = data.data.note
      this.form.status = data.data.status
      this.form.assetType = data.data.type
      this.form.date = data.data.date
      this.form.depreciation = data.data.depreciation
      this.form.salvageValue =
        data.data.salvageValue > 0 ? data.data.salvageValue : 0
      this.form.usefulLife = data.data.usefulLife
      this.form.depreciationType =
        data.data.depreciationType == 1 ? 'Year' : 'Month'
      this.form.depreciationExpense = data.data.depreciationExpenseTxt
      this.url = data.data.image
    },

    // calculate depreciation expense
    calculateDepreciationExpense() {
      if (
        this.form.depreciation == 1 &&
        this.form.assetCost &&
        this.form.usefulLife
      ) {
        let depreciationExpense =
          (this.form.assetCost - this.form.salvageValue) / this.form.usefulLife
        return (this.form.depreciationExpense =
          depreciationExpense + ' Per ' + this.form.depreciationType)
      }
      return
    },

    // vue file upload
    onFileChange(e) {
      const file = e.target.files[0]
      const reader = new FileReader()
      if (
        file.size < 2111775 &&
        (file.type === 'image/jpeg' ||
          file.type === 'image/png' ||
          file.type === 'image/gif')
      ) {
        reader.onloadend = () => {
          this.form.image = reader.result
        }
        reader.readAsDataURL(file)
        this.url = URL.createObjectURL(file)
      } else {
        Swal.fire(
          this.$t('Error!'),
          this.$t('Please select a valid thumbnail with size less than 2 MB'),
          'error'
        )
      }
    },
    // update asset
    async updateAsset() {
      await this.form
        .patch(
          window.location.origin + '/api/assets/' + this.$route.params.slug
        )
        .then(() => {
          toast.fire({
            type: 'success',
            title: this.$t('Asset updated successfully'),
          })
          this.$router.push({ name: 'assets.index' })
        })
        .catch(() => {
          toast.fire({
            type: 'error',
            title: this.$t('Opps...something went wrong'),
          })
        })
    },
  },
}
</script>

<style lang="scss" scoped></style>
