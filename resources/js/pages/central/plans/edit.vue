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
                            {{ $t('Edit plan') }}
                        </h3>
                        <router-link
                            :to="{ name: 'plans.index' }"
                            class="btn btn-dark float-right"
                        >
                            <i class="fas fa-long-arrow-alt-left" />
                            {{ $t('Back') }}
                        </router-link>
                    </div>
                    <!-- /.card-header -->

                    <!-- form start -->
                    <form
                        role="form"
                        enctype="multipart/form-data"
                        @submit.prevent="updatePlan"
                        @keydown="form.onKeydown($event)"
                    >
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="name"
                                        >{{ $t('Name') }}
                                        <span class="required">*</span></label
                                    >
                                    <input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid': form.errors.has('name'),
                                        }"
                                        name="name"
                                        :placeholder="
                                            $t('Enter plan name')
                                        "
                                    />
                                    <has-error :form="form" field="name" />
                                </div>

                                <div class="form-group col-md-6">
                                        <label for="amount">
                                            {{ $t('Amount') }}
                                            <span class="required">*</span>
                                        </label>
                                        <input
                                            id="amount"
                                            v-model="form.amount"
                                            type="text"
                                            class="form-control"
                                            :class="{
                                                'is-invalid':
                                                    form.errors.has('amount'),
                                            }"
                                            name="amount"
                                            :placeholder="
                                                $t(
                                                    'Amount'
                                                )
                                            "
                                        />
                                        <has-error :form="form" field="amount" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="note">{{
                                    $t('Description')
                                }}</label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    class="form-control"
                                    :class="{
                                        'is-invalid':
                                            form.errors.has('description'),
                                    }"
                                    :placeholder="
                                        $t(
                                            'Enter plan description'
                                        )
                                    "
                                />
                                <has-error :form="form" field="description" />
                            </div>

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="limit_clients"
                                        >{{ $t('Limit Clients') }}
                                        <span class="required">*</span>
                                        <span class="text-sm text-muted"
                                            >({{
                                                $t(
                                                    '0 means unlimited'
                                                )
                                            }})</span
                                        >
                                    </label>
                                    <input
                                        id="limit_clients"
                                        v-model="form.limit_clients"
                                        type="number"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                form.errors.has(
                                                    'limit_clients'
                                                ),
                                        }"
                                        name="limit_clients"
                                        :placeholder="
                                            $t(
                                                'Limit clients'
                                            )
                                        "
                                    />
                                    <has-error
                                        :form="form"
                                        field="limit_clients"
                                    />
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="limit_suppliers"
                                        >{{
                                            $t('Limit Suppliers')
                                        }}
                                        <span class="required">*</span>
                                        <span class="text-sm text-muted"
                                            >({{
                                                $t(
                                                    '0 means unlimited'
                                                )
                                            }})</span
                                        >
                                    </label>
                                    <input
                                        id="limit_suppliers"
                                        v-model="form.limit_suppliers"
                                        type="number"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                form.errors.has(
                                                    'limit_suppliers'
                                                ),
                                        }"
                                        name="limit_suppliers"
                                        :placeholder="
                                            $t(
                                                'Limit suppliers'
                                            )
                                        "
                                    />
                                    <has-error
                                        :form="form"
                                        field="limit_suppliers"
                                    />
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="limit_employees"
                                        >{{
                                            $t('Limit Employees')
                                        }}
                                        <span class="required">*</span>
                                        <span class="text-sm text-muted"
                                            >({{
                                                $t(
                                                    '0 means unlimited'
                                                )
                                            }})</span
                                        >
                                    </label>
                                    <input
                                        id="limit_employees"
                                        v-model="form.limit_employees"
                                        type="number"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                form.errors.has(
                                                    'limit_employees'
                                                ),
                                        }"
                                        name="limit_employees"
                                        :placeholder="
                                            $t(
                                                'Limit employees'
                                            )
                                        "
                                    />
                                    <has-error
                                        :form="form"
                                        field="limit_employees"
                                    />
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="limit_domains">
                                        {{ $t('Limit Domains') }}
                                        <span class="required">*</span>
                                        <span class="text-sm text-muted">
                                            ({{
                                                $t(
                                                    '0 means unlimited'
                                                )
                                            }})
                                        </span>
                                    </label>
                                    <input
                                        id="limit_domains"
                                        v-model="form.limit_domains"
                                        type="number"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                form.errors.has(
                                                    'limit_domains'
                                                ),
                                        }"
                                        name="limit_domains"
                                        :placeholder="
                                            $t(
                                                'Limit domains'
                                            )
                                        "
                                    />
                                    <has-error
                                        :form="form"
                                        field="limit_domains"
                                    />
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="limit_purchases">
                                        {{
                                            $t('Limit Purchases')
                                        }}
                                        <span class="required">*</span>
                                        <span class="text-sm text-muted">
                                            ({{
                                                $t(
                                                    '0 means unlimited'
                                                )
                                            }})
                                        </span>
                                    </label>
                                    <input
                                        id="limit_purchases"
                                        v-model="form.limit_purchases"
                                        type="number"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                form.errors.has(
                                                    'limit_purchases'
                                                ),
                                        }"
                                        name="limit_purchases"
                                        :placeholder="
                                            $t(
                                                'Limit purchases'
                                            )
                                        "
                                    />
                                    <has-error
                                        :form="form"
                                        field="limit_purchases"
                                    />
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="limit_invoices">
                                        {{ $t('Limit Invoices') }}
                                        <span class="required">*</span>
                                        <span class="text-sm text-muted">
                                            ({{
                                                $t(
                                                    '0 means unlimited'
                                                )
                                            }})
                                        </span>
                                    </label>
                                    <input
                                        id="limit_invoices"
                                        v-model="form.limit_invoices"
                                        type="number"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                form.errors.has(
                                                    'limit_invoices'
                                                ),
                                        }"
                                        name="limit_invoices"
                                        :placeholder="
                                            $t(
                                                'Limit invoices'
                                            )
                                        "
                                    />
                                    <has-error
                                        :form="form"
                                        field="limit_invoices"
                                    />
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="features">
                                        {{ $t('Pricing features') }}
                                        <span class="required">*</span>
                                    </label>
                                    <v-select
                                        v-model="form.features"
                                        :options="features"
                                        label="name"
                                        :placeholder="
                                            $t('Select pricing features')
                                        "
                                        multiple
                                        :class="{
                                            'is-invalid':
                                                form.errors.has('features'),
                                        }"
                                    />
                                    <has-error :form="form" field="features" />
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="image">{{
                                        $t('Image')
                                    }}</label>
                                    <div class="custom-file">
                                        <input
                                            id="image"
                                            type="file"
                                            class="custom-file-input"
                                            name="image"
                                            :class="{
                                                'is-invalid':
                                                    form.errors.has('image'),
                                            }"
                                            @change="onFileChange"
                                        />
                                        <label
                                            class="custom-file-label"
                                            for="image"
                                            >{{
                                                $t('Choose file')
                                            }}</label
                                        >
                                    </div>
                                    <has-error :form="form" field="image" />
                                    <div v-if="url" class="bg-light mt-4 w-25">
                                        <img
                                            :src="url"
                                            class="img-fluid"
                                            :alt="$t('Attached Image')"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <v-button
                                :loading="form.busy"
                                class="btn btn-primary"
                            >
                                <i class="fas fa-edit" />
                                {{ $t('Save changes') }}
                            </v-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Form from 'vform';
import axios from 'axios';
import { mapGetters } from 'vuex';

export default {
    layout: 'central',
    middleware: ['auth', 'check-permissions'],
    metaInfo() {
        return { title: this.$t('Edit Plan') };
    },
    data: () => ({
        breadcrumbsCurrent: 'Edit Plan',
        breadcrumbs: [
            {
                name: 'Dashboard',
                url: 'home',
            },
            {
                name: 'Plans',
                url: 'plans.index',
            },
            {
                name: 'Edit Plan',
                url: '',
            },
        ],
        url: null,
        form: new Form({
            image: '',
            name: '',
            amount: 0,
            description: '',
            limit_clients: 0,
            limit_suppliers: 0,
            limit_employees: 0,
            limit_domains: 0,
            limit_purchases: 0,
            limit_invoices: 0,
            features: [],
        }),
        features: [],
        loading: true,
        isDemoMode: window.config.isDemoMode,
    }),
    computed: {
        ...mapGetters('operations', ['items']),
    },
    created() {
        this.getPlan();
        this.getFeatures();
    },
    methods: {
        // get features
        async getFeatures() {
            const { data } = await axios.get(
                window.location.origin + '/api/features'
            );
            this.features = data.data;
        },

        // get plan
        async getPlan() {
            const { data } = await axios.get(
                window.location.origin + '/api/plans/' + this.$route.params.id
            );
            this.form.fill(data.data);
            this.url = data.data.image;
        },

        // vue file upload
        onFileChange(e) {
            const file = e.target.files[0];
            const reader = new FileReader();
            if (
                file.size < 2111775 &&
                (file.type === 'image/jpeg' ||
                    file.type === 'image/png' ||
                    file.type === 'image/gif')
            ) {
                reader.onloadend = () => {
                    this.form.image = reader.result;
                };
                reader.readAsDataURL(file);
                this.url = URL.createObjectURL(file);
            } else {
                Swal.fire(
                    this.$t('Error!'),
                    this.$t('Please select a valid thumbnail with size less than 2 MB'),
                    'error'
                );
            }
        },
        // update plan
        async updatePlan() {
            // disable for demo
            if (this.isDemoMode) {
                return toast.fire({
                    type: 'warning',
                    title: this.$t(
                        'You are not allowed to do this in demo version.'
                    ),
                });
            }
            await this.form
                .patch(
                    window.location.origin +
                        '/api/plans/' +
                        this.$route.params.id
                )
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('breadcrumbs_first'),
                    });
                    this.$router.push({ name: 'plans.index' });
                })
                .catch(() => {
                    toast.fire({
                        type: 'error',
                        title: this.$t('Error!'),
                    });
                });
        },
    },
};
</script>

<style lang="scss" scoped></style>
