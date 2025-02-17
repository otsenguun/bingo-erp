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
                <form
                    role="form"
                    @submit.prevent="updateSettings"
                    @keydown="form.onKeydown($event)"
                >
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                {{
                                    $t(
                                        'General Settings'
                                    )
                                }}
                            </h3>
                            <router-link
                                :to="{ name: 'setup.index' }"
                                class="btn btn-dark float-right"
                            >
                                <i class="fas fa-long-arrow-alt-left" />
                                {{ $t('Back') }}
                            </router-link>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="card inner-card">
                                <div class="card-header">
                                    {{
                                        $t(
                                            'Add Your Company Information'
                                        )
                                    }}
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="companyName"
                                                >{{
                                                    $t('Company Name')
                                                }}
                                                <span class="required"
                                                    >*</span
                                                ></label
                                            >
                                            <input
                                                id="companyName"
                                                v-model="form.companyName"
                                                type="text"
                                                class="form-control"
                                                :class="{
                                                    'is-invalid':
                                                        form.errors.has('name'),
                                                }"
                                                name="companyName"
                                                :placeholder="
                                                    $t(
                                                        'Enter a company name'
                                                    )
                                                "
                                            />
                                            <has-error
                                                :form="form"
                                                field="companyName"
                                            />
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="companyTagline"
                                                >{{
                                                    $t(
                                                        'Company Tagline'
                                                    )
                                                }}
                                                <span class="required"
                                                    >*</span
                                                ></label
                                            >
                                            <input
                                                id="companyTagline"
                                                v-model="form.companyTagline"
                                                type="text"
                                                class="form-control"
                                                :class="{
                                                    'is-invalid':
                                                        form.errors.has(
                                                            'companyTagline'
                                                        ),
                                                }"
                                                name="companyTagline"
                                                :placeholder="
                                                    $t(
                                                        'Company Tagline'
                                                    )
                                                "
                                            />
                                            <has-error
                                                :form="form"
                                                field="companyTagline"
                                            />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="emailAddress"
                                                >{{
                                                    $t(
                                                        'Email Address'
                                                    )
                                                }}
                                                <span class="required"
                                                    >*</span
                                                ></label
                                            >
                                            <input
                                                id="emailAddress"
                                                v-model="form.emailAddress"
                                                type="email"
                                                class="form-control"
                                                :class="{
                                                    'is-invalid':
                                                        form.errors.has(
                                                            'emailAddress'
                                                        ),
                                                }"
                                                name="emailAddress"
                                                :placeholder="
                                                    $t(
                                                        'Enter an email address'
                                                    )
                                                "
                                            />
                                            <has-error
                                                :form="form"
                                                field="emailAddress"
                                            />
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="phoneNumber"
                                                >{{
                                                    $t(
                                                        'Phone Number'
                                                    )
                                                }}
                                                <span class="required"
                                                    >*</span
                                                ></label
                                            >
                                            <input
                                                id="phoneNumber"
                                                v-model="form.phoneNumber"
                                                type="text"
                                                class="form-control"
                                                :class="{
                                                    'is-invalid':
                                                        form.errors.has(
                                                            'phoneNumber'
                                                        ),
                                                }"
                                                name="phoneNumber"
                                                :placeholder="
                                                    $t(
                                                        'Phone Number'
                                                    )
                                                "
                                            />
                                            <has-error
                                                :form="form"
                                                field="name"
                                            />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="address">{{
                                            $t('Address')
                                        }}</label>
                                        <textarea
                                            id="address"
                                            v-model="form.address"
                                            class="form-control"
                                            :class="{
                                                'is-invalid':
                                                    form.errors.has('address'),
                                            }"
                                            :placeholder="
                                                $t('Enter an address')
                                            "
                                        />
                                        <has-error
                                            :form="form"
                                            field="address"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="card inner-card">
                                <div class="card-header">
                                    {{ $t('Pricing Plan') }}
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="plan_discount">
                                                {{
                                                    $t(
                                                        'Yearly Plan Discount'
                                                    )
                                                }}</label
                                            >
                                            <input
                                                id="plan_discount"
                                                v-model="form.plan_discount"
                                                type="number"
                                                class="form-control"
                                                :class="{
                                                    'is-invalid':
                                                        form.errors.has(
                                                            'plan_discount'
                                                        ),
                                                }"
                                                name="plan_discount"
                                                :placeholder="
                                                    $t(
                                                        'Enter Yearly Plan Discount'
                                                    )
                                                "
                                            />
                                            <has-error
                                                :form="form"
                                                field="plan_discount"
                                            />
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="trial_day_count">
                                                {{
                                                    $t(
                                                        'Trial Day Count'
                                                    )
                                                }}
                                                <span class="required"
                                                    >*</span
                                                ></label
                                            >
                                            <input
                                                id="trial_day_count"
                                                v-model="form.trial_day_count"
                                                type="number"
                                                min="0"
                                                class="form-control"
                                                :class="{
                                                    'is-invalid':
                                                        form.errors.has(
                                                            'trial_day_count'
                                                        ),
                                                }"
                                                name="trial_day_count"
                                                :placeholder="
                                                    $t(
                                                        'Trial Day Count'
                                                    )
                                                "
                                            />
                                            <has-error
                                                :form="form"
                                                field="trial_day_count"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card inner-card">
                                <div class="card-header">
                                    {{
                                        $t(
                                            'Default Element'
                                        )
                                    }}
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="language"
                                                >{{
                                                    $t(
                                                        'Default Language'
                                                    )
                                                }}
                                                <span class="required"
                                                    >*</span
                                                ></label
                                            >
                                            <select
                                                v-model="form.language"
                                                name=""
                                                class="form-control"
                                                :class="{
                                                    'is-invalid':
                                                        form.errors.has(
                                                            'language'
                                                        ),
                                                }"
                                            >
                                            <option
                                                v-for="(value, key) in locales"
                                                :key="key"
                                                :value="key"
                                            >
                                                {{ value[1] }}
                                            </option>
                                            </select>
                                            <has-error
                                                :form="form"
                                                field="language"
                                            />
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="currency">{{
                                                $t(
                                                "Default Currency"
                                                )
                                            }}
                                                <span class="required">*</span></label>
                                            <v-select v-model="form.currency" :options="items" label="label" :disabled="appInfo.hasDataInCentralPaymentTable"
                                                :class="{ 'is-invalid': form.errors.has('currency'), 'text-uppercase': true }" name="currency" :placeholder="$t(
                                                'Select a currency'
                                                )
                                                " />
                                            <has-error :form="form" field="currency" />
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="copyrightText"
                                                >{{
                                                    $t(
                                                        'Copyright Text'
                                                    )
                                                }}
                                                <span class="required"
                                                    >*</span
                                                ></label
                                            >
                                            <input
                                                id="copyrightText"
                                                v-model="form.copyrightText"
                                                type="text"
                                                class="form-control"
                                                :class="{
                                                    'is-invalid':
                                                        form.errors.has(
                                                            'copyrightText'
                                                        ),
                                                }"
                                                name="copyrightText"
                                                placeholder="$t('Copyright Text')"
                                            />
                                            <has-error
                                                :form="form"
                                                field="copyrightText"
                                            />
                                        </div>

                                        <!-- social media links -->
                                        <div class="form-group col-md-6">
                                            <label for="facebook_link">{{
                                                $t(
                                                    'Facebook Link'
                                                )
                                            }}</label>
                                            <input
                                                id="facebook_link"
                                                v-model="form.facebook_link"
                                                type="url"
                                                class="form-control"
                                                :class="{
                                                    'is-invalid':
                                                        form.errors.has(
                                                            'facebook_link'
                                                        ),
                                                }"
                                                name="facebook_link"
                                                placeholder="$t('Facebook Link')"
                                            />
                                            <has-error
                                                :form="form"
                                                field="facebook_link"
                                            />
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="instagram_link">{{
                                                $t(
                                                    'Instagram Link'
                                                )
                                            }}</label>
                                            <input
                                                id="instagram_link"
                                                v-model="form.instagram_link"
                                                type="url"
                                                class="form-control"
                                                :class="{
                                                    'is-invalid':
                                                        form.errors.has(
                                                            'instagram_link'
                                                        ),
                                                }"
                                                name="instagram_link"
                                                placeholder="$t('Instagram Link')"
                                            />
                                            <has-error
                                                :form="form"
                                                field="instagram_link"
                                            />
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="twitter_link">{{
                                                $t(
                                                    'Twitter Link'
                                                )
                                            }}</label>
                                            <input
                                                id="twitter_link"
                                                v-model="form.twitter_link"
                                                type="url"
                                                class="form-control"
                                                :class="{
                                                    'is-invalid':
                                                        form.errors.has(
                                                            'twitter_link'
                                                        ),
                                                }"
                                                name="twitter_link"
                                                placeholder="$t('Twitter Link')"
                                            />
                                            <has-error
                                                :form="form"
                                                field="twitter_link"
                                            />
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="linkedin_link">{{
                                                $t(
                                                    'LinkedIn Link'
                                                )
                                            }}</label>
                                            <input
                                                id="linkedin_link"
                                                v-model="form.linkedin_link"
                                                type="url"
                                                class="form-control"
                                                :class="{
                                                    'is-invalid':
                                                        form.errors.has(
                                                            'linkedin_link'
                                                        ),
                                                }"
                                                name="linkedin_link"
                                                placeholder="$t('LinkedIn Link')"
                                            />
                                            <has-error
                                                :form="form"
                                                field="linkedin_link"
                                            />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="logo">{{
                                                $t(
                                                    'White Logo'
                                                )
                                            }}</label>
                                            <div class="custom-file">
                                                <input
                                                    id="logo"
                                                    type="file"
                                                    class="custom-file-input"
                                                    name="logo"
                                                    :class="{
                                                        'is-invalid':
                                                            form.errors.has(
                                                                'logo'
                                                            ),
                                                    }"
                                                    @change="onLogoChange"
                                                />
                                                <label
                                                    class="custom-file-label"
                                                    for="logo"
                                                    >{{
                                                        $t('Choose file')
                                                    }}</label
                                                >
                                            </div>
                                            <has-error
                                                :form="form"
                                                field="logo"
                                            />
                                            <div class="bg-light mt-4 w-25">
                                                <img
                                                    v-if="logo"
                                                    :src="logo"
                                                    class="img-fluid"
                                                    alt="Logo"
                                                />
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="blackLogo">{{
                                                $t(
                                                    'Black Logo'
                                                )
                                            }}</label>
                                            <div class="custom-file">
                                                <input
                                                    id="blackLogo"
                                                    type="file"
                                                    class="custom-file-input"
                                                    name="blackLogo"
                                                    :class="{
                                                        'is-invalid':
                                                            form.errors.has(
                                                                'blackLogo'
                                                            ),
                                                    }"
                                                    @change="onBlackLogoChange"
                                                />
                                                <label
                                                    class="custom-file-label"
                                                    for="blackLogo"
                                                    >{{
                                                        $t('Choose file')
                                                    }}</label
                                                >
                                            </div>
                                            <has-error
                                                :form="form"
                                                field="blackLogo"
                                            />
                                            <div class="bg-light mt-4 w-25">
                                                <img
                                                    v-if="blackLogo"
                                                    :src="blackLogo"
                                                    class="img-fluid"
                                                    alt="Black Logo"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="smallLogo">{{
                                                $t(
                                                    'Small Logo'
                                                )
                                            }}</label>
                                            <div class="custom-file">
                                                <input
                                                    id="smallLogo"
                                                    type="file"
                                                    class="custom-file-input"
                                                    name="smallLogo"
                                                    :class="{
                                                        'is-invalid':
                                                            form.errors.has(
                                                                'smallLogo'
                                                            ),
                                                    }"
                                                    @change="onSmallLogoChange"
                                                />
                                                <label
                                                    class="custom-file-label"
                                                    for="smallLogo"
                                                    >{{
                                                        $t('Choose file')
                                                    }}</label
                                                >
                                            </div>
                                            <has-error
                                                :form="form"
                                                field="smallLogo"
                                            />
                                            <div class="bg-light mt-4 w-25">
                                                <img
                                                    v-if="smallLogo"
                                                    :src="smallLogo"
                                                    class="img-fluid"
                                                    alt="Small Logo"
                                                />
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="favicon">{{
                                                $t(
                                                    'Favicon'
                                                )
                                            }}</label>
                                            <div class="custom-file">
                                                <input
                                                    id="favicon"
                                                    type="file"
                                                    class="custom-file-input"
                                                    name="favicon"
                                                    :class="{
                                                        'is-invalid':
                                                            form.errors.has(
                                                                'favicon'
                                                            ),
                                                    }"
                                                    @change="onFaviconChange"
                                                />
                                                <label
                                                    class="custom-file-label"
                                                    for="favicon"
                                                    >{{
                                                        $t('Choose file')
                                                    }}</label
                                                >
                                            </div>
                                            <has-error
                                                :form="form"
                                                field="favicon"
                                            />
                                            <div class="bg-light mt-4 w-25">
                                                <img
                                                    v-if="favicon"
                                                    :src="favicon"
                                                    class="img-fluid"
                                                    alt="Favicon"
                                                />
                                            </div>
                                        </div>
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
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import Form from 'vform';
import { mapGetters } from 'vuex';
import { loadMessages } from '~/plugins/i18n';
import SettingsSidebar from '~/components/central/SettingsSidebar';

export default {
    layout: 'central',
    middleware: ['auth', 'check-permissions'],
    metaInfo() {
        return { title: this.$t('General Settings') };
    },
    components: {
        SettingsSidebar,
    },
    data: () => ({
        isDemoMode: window.config.isDemoMode,
        breadcrumbsCurrent: 'General Settings',
        breadcrumbs: [
            {
                name: 'Dashboard',
                url: 'home',
            },
            {
                name: 'Setup',
                url: 'setup.index',
            },
            {
                name: 'Update general settings',
                url: '',
            },
        ],
        form: new Form({
            companyName: '',
            companyTagline: '',
            emailAddress: '',
            phoneNumber: '',
            address: '',
            clientPrefix: '',
            supplierPrefix: '',
            employeePrefix: '',
            proCatPrefix: '',
            proSubCatPrefix: '',
            productPrefix: '',
            expCatPrefix: '',
            expSubCatPrefix: '',
            purchasePrefix: '',
            purchaseReturnPrefix: '',
            quotationPrefix: '',
            invoicePrefix: '',
            invoiceReturnPrefix: '',
            adjustmentPrefix: '',
            language: 'en',
            copyrightText: '',
            logo: '',
            blackLogo: '',
            currency: "",
            smallLogo: '',
            favicon: '',
            facebook_link: '',
            instagram_link: '',
            twitter_link: '',
            linkedin_link: '',
            trial_day_count: 14,
            plan_discount: '',
        }),
        logo: '',
        blackLogo: '',
        smallLogo: '',
        favicon: '',
    }),
    computed: mapGetters({
        appInfo: 'operations/appInfo',
        items: 'operations/items',
        locales: 'lang/locales',
    }),

    created() {
        this.assignValues();
        this.getCurrencies();
    },

    methods: {
         // get currencies
        async getCurrencies() {
        await this.$store.dispatch("operations/allData", {
            path: "/api/all-currencies",
        });
        },

        // assign values
        assignValues() {
            if (this.appInfo) {
                this.form.companyName = this.appInfo.companyName;
                this.form.companyTagline = this.appInfo.companyTagline;
                this.form.emailAddress = this.appInfo.email;
                this.form.phoneNumber = this.appInfo.phone;
                this.form.address = this.appInfo.address;
                this.form.clientPrefix = this.appInfo.clientPrefix;
                this.form.supplierPrefix = this.appInfo.supplierPrefix;
                this.form.employeePrefix = this.appInfo.employeePrefix;
                this.form.proCatPrefix = this.appInfo.proCatPrefix;
                this.form.proSubCatPrefix = this.appInfo.proSubCatPrefix;
                this.form.productPrefix = this.appInfo.productPrefix;
                this.form.expCatPrefix = this.appInfo.expCatPrefix;
                this.form.expSubCatPrefix = this.appInfo.expSubCatPrefix;
                this.form.purchasePrefix = this.appInfo.purchasePrefix;
                this.form.purchaseReturnPrefix =
                    this.appInfo.purchaseReturnPrefix;
                this.form.quotationPrefix = this.appInfo.quotationPrefix;
                this.form.invoicePrefix = this.appInfo.invoicePrefix;
                this.form.invoiceReturnPrefix =
                    this.appInfo.invoiceReturnPrefix;
                this.form.adjustmentPrefix = this.appInfo.adjustmentPrefix;
                this.form.currency = this.appInfo.currency;
                this.form.language = this.appInfo.language;
                this.logo = this.appInfo.logo;
                this.blackLogo = this.appInfo.blackLogo;
                this.smallLogo = this.appInfo.smallLogo;
                this.favicon = this.appInfo.favicon;
                this.form.copyrightText = this.appInfo.copyright;
                this.form.facebook_link = this.appInfo.facebook_link;
                this.form.instagram_link = this.appInfo.instagram_link;
                this.form.twitter_link = this.appInfo.twitter_link;
                this.form.linkedin_link = this.appInfo.linkedin_link;
                this.form.trial_day_count = this.appInfo.trial_day_count;
                this.form.plan_discount = this.appInfo.plan_discount > 0 ? this.appInfo.plan_discount : '';
            }
        },

        // vue logo upload
        onLogoChange(e) {
            const file = e.target.files[0];
            const reader = new FileReader();
            if (
                file.size < 2111775 &&
                (file.type === 'image/jpeg' ||
                    file.type === 'image/png' ||
                    file.type === 'image/gif' ||
                    file.type === "image/svg" ||
                    file.type === "image/svg+xml")
            ) {
                reader.onloadend = () => {
                    this.form.logo = reader.result;
                };
                reader.readAsDataURL(file);
                this.logo = URL.createObjectURL(file);
            } else {
                Swal.fire(
                    this.$t('Error!'),
                    this.$t('Please select a valid thumbnail with size less than 2 MB'),
                    'error'
                );
            }
        },

        // vue black logo upload
        onBlackLogoChange(e) {
            const file = e.target.files[0];
            const reader = new FileReader();
            if (
                file.size < 2111775 &&
                (file.type === 'image/jpeg' ||
                    file.type === 'image/png' ||
                    file.type === 'image/gif' ||
                    file.type === "image/svg" ||
                    file.type === "image/svg+xml")
            ) {
                reader.onloadend = () => {
                    this.form.blackLogo = reader.result;
                };
                reader.readAsDataURL(file);
                this.blackLogo = URL.createObjectURL(file);
            } else {
                Swal.fire(
                    this.$t('Error!'),
                    this.$t('Please select a valid thumbnail with size less than 2 MB'),
                    'error'
                );
            }
        },

        // vue small logo upload
        onSmallLogoChange(e) {
            const file = e.target.files[0];
            const reader = new FileReader();
            if (
                file.size < 2111775 &&
                (file.type === 'image/jpeg' ||
                    file.type === 'image/png' ||
                    file.type === 'image/gif' ||
                    file.type === "image/svg" ||
                    file.type === "image/svg+xml")
            ) {
                reader.onloadend = () => {
                    this.form.smallLogo = reader.result;
                };
                reader.readAsDataURL(file);
                this.smallLogo = URL.createObjectURL(file);
            } else {
                Swal.fire(
                    this.$t('Error!'),
                    this.$t('Please select a valid thumbnail with size less than 2 MB'),
                    'error'
                );
            }
        },

        // vue favicon upload
        onFaviconChange(e) {
            const file = e.target.files[0];
            const reader = new FileReader();
            if (
                file.size < 2111775 &&
                (file.type === 'image/jpeg' ||
                    file.type === 'image/png' ||
                    file.type === 'image/gif' ||
                    file.type === "image/svg" ||
                    file.type === "image/svg+xml")
            ) {
                reader.onloadend = () => {
                    this.form.favicon = reader.result;
                };
                reader.readAsDataURL(file);
                this.favicon = URL.createObjectURL(file);
            } else {
                Swal.fire(
                    this.$t('Error!'),
                    this.$t('Please select a valid thumbnail with size less than 2 MB'),
                    'error'
                );
            }
        },

        // update settings
        async updateSettings() {
            if (this.isDemoMode) {
                return toast.fire({
                    type: 'warning',
                    title: this.$t(
                        'You are not allowed to do this in demo version.'
                    ),
                });
            }
            // update locale
            let locale = this.form.language;
            if (this.$i18n.locale !== locale) {
                loadMessages(locale);
                this.$store.dispatch('lang/setLocale', { locale });
            }

            await this.form
                .post(window.location.origin + '/api/update-settings')
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t(
                            'Settings updated successfully'
                        ),
                    });
                    window.location.reload();
                })
                .catch(() => {
                    toast.fire({
                        type: 'error',
                        title: this.$t('Opps...something went wrong'),
                    });
                });
        },
    },
};
</script>

<style lang="scss" scoped>
.inner-card {
    box-shadow: 0 0 1px rgba(0, 0, 0, 0.125), 0 1px 3px rgba(0, 0, 0, 0.2);
}

.inner-card .card-header {
    background-color: rgba(0, 0, 0, 0.03);
}
</style>
