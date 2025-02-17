<template>
    <div>
        <!-- breadcrumbs Start -->
        <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
        <!-- breadcrumbs end -->
        <div class="row">

            <div class="col-12 col-xl-3">
                <SettingsSidebar />
            </div>
            <div class="col-12 col-xl-9">
                <form role="form" @submit.prevent="saveCurrency" @keydown="form.onKeydown($event)">
                    <div class="card">
                        <div class="card-header setings-header">
                            <div class="col-xl-4 col-4">
                                <h3 class="card-title">
                                    {{ $t("Create Currency") }}
                                </h3>
                            </div>
                            <div class="col-xl-8 col-8 float-right text-right">
                                <router-link :to="{ name: 'setup.centralCurrency.index' }"
                                    class="btn btn-dark float-right">
                                    <i class="fas fa-long-arrow-alt-left" />
                                    {{ $t("Back") }}
                                </router-link>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">{{ $t("Name") }}<span class="required">*</span></label>
                                <input id="name" v-model="form.name" type="text" class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('name') }" name="name"
                                    :placeholder="$t('Enter a name')" />
                                <has-error :form="form" field="name" />
                            </div>
                            <div class="form-group">
                                <label for="code">{{ $t("Code") }}<span class="required">*</span></label>
                                <input id="code" v-model="form.code" type="text" class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('code') }" name="code"
                                    :placeholder="$t('Enter a code')" />
                                <has-error :form="form" field="code" />
                            </div>

                            <div class="form-group">
                                <label for="rate">{{ $t("Rate")
                                    }}<span class="required">*</span></label>
                                <input id="rate" v-model="form.rate" type="text" class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('rate') }" name="rate" :placeholder="$t('Exchange rate for your currency (USD comparison)')
            " />
                                <has-error :form="form" field="rate" />
                            </div>

                            <div class="form-group">
                                <label for="symbol">{{ $t("Symbol")
                                    }}<span class="required">*</span></label>
                                <input id="symbol" v-model="form.symbol" type="text" class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('symbol') }" name="symbol" :placeholder="$t('Enter a symbol')
            " />
                                <has-error :form="form" field="symbol" />
                            </div>
                            <div class="form-group">
                                <label for="position">{{
            $t("Currency Position")
        }}</label>
                                <select v-model="form.position" class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('position') }" name="position">
                                    <option value="left">
                                        {{
            $t("Left align")
        }}
                                    </option>
                                    <option value="right">
                                        {{
                $t("Right align")
            }}
                                    </option>
                                </select>
                                <has-error :form="form" field="position" />
                            </div>
                            <div class="form-group">
                                <label for="status">{{ $t("Status") }}</label>
                                <select id="status" v-model="form.status" class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('status') }">
                                    <option value="1">{{ $t("Active") }}</option>
                                    <option value="0">{{ $t("Inactive") }}</option>
                                </select>
                                <has-error :form="form" field="status" />
                            </div>

                            <div class="form-group">
                                <label for="note">{{ $t("Note") }}</label>
                                <textarea id="note" v-model="form.note" class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('note') }"
                                    :placeholder="$t('Write your note here!')" />
                                <has-error :form="form" field="note" />
                            </div>
                        </div>

                        <div class="card-footer">
                            <v-button :loading="form.busy" class="btn btn-primary">
                                <i class="fas fa-save" /> {{ $t("Save") }}
                            </v-button>
                            <button type="reset" class="btn btn-secondary float-right" @click="form.reset()">
                                <i class="fas fa-power-off" /> {{ $t("Reset") }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import Form from 'vform'
import axios from 'axios'
import SettingsSidebar from '~/components/central/SettingsSidebar'

export default {
    layout: 'central',
    middleware: ['auth', 'check-permissions'],

    metaInfo() {
        return { title: this.$t('Create Currency') }
    },

    components: {
        SettingsSidebar,
    },

    data: () => ({
        breadcrumbsCurrent: 'Create Currency',
        breadcrumbs: [
            {
                name: 'Dashboard',
                url: 'home',
            },
            {
                name: 'Currency Settings',
                url: 'setup.centralCurrency.index',
            },
            {
                name: 'Create',
                url: '',
            },
        ],
        form: new Form({
            name: "",
            note: "",
            status: 1,
            code: "",
            rate: "",
            symbol: "",
            position: "left",
        }),
        loading: true,
    }),

    created() {
    },

    methods: {
        // save currency
        async saveCurrency() {
            await this.form
                .post(window.location.origin + "/api/currencies")
                .then(() => {
                    toast.fire({
                        type: "success",
                        title: this.$t("Currency added successfully"),
                    });
                    this.$router.push({ name: "setup.centralCurrency.index" });
                })
                .catch(() => {
                    toast.fire({
                        type: "error",
                        title: this.$t("Opps...something went wrong"),
                    });
                });
        },
    },
}
</script>

<style lang="scss" scoped></style>