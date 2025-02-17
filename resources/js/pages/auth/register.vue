<template>
    <div class="container-fluid">
        <div class="row no-gutter">
            <!-- The image half -->
            <div class="col-md-6 d-none d-md-flex bg-image"></div>
            <!-- The content half -->
            <div class="col-md-6 bg-light">
                <div class="auth-wrapper d-flex align-items-center py-5">
                    <!-- Demo content-->
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12 col-lg-10 col-xl-8 mx-auto">
                                <div class="text-center">
                                    <router-link to="/">
                                        <img
                                            v-if="appInfo"
                                            :src="appInfo.blackLogo"
                                            :alt="appInfo.companyName"
                                            class="lg-logo img-fluid logo-width"
                                        />
                                    </router-link>
                                    <div
                                        v-if="
                                            !verificationForm.email && appInfo
                                        "
                                    >
                                        <p class="text-22 mb-4 mt-2">
                                            {{
                                                $t(
                                                    'Sign Up Instantly Free for'
                                                ) +
                                                ' ' +
                                                appInfo.trial_day_count +
                                                ' ' +
                                                $t(
                                                    'days, no credit card required'
                                                ) +
                                                '.'
                                            }}
                                        </p>
                                    </div>

                                    <div
                                        v-if="isDemoMode"
                                        class="alert alert-danger"
                                    >
                                        To prevent the creation of multiple
                                        sub-domains, we have disabled the
                                        registration feature for the demo. You
                                        can access the Tenant panel
                                        <a
                                            href="https://john.acculance.top/login"
                                            >here</a
                                        >
                                    </div>
                                </div>
                                <form
                                    v-if="!verificationForm.email"
                                    @submit.prevent="tenantRegister"
                                    @keydown="form.onKeydown($event)"
                                >
                                    <!-- Full Name-->
                                    <div class="form-group mb-3">
                                        <input
                                            id="name"
                                            v-model="form.name"
                                            :class="{
                                                'is-invalid':
                                                    form.errors.has('name'),
                                            }"
                                            class="form-control rounded-pill border-0 shadow-sm px-4 text-primary"
                                            type="text"
                                            name="name"
                                            :placeholder="$t('Name')"
                                        />
                                        <has-error
                                            :form="form"
                                            field="name"
                                            class="ml-4"
                                        />
                                    </div>
                                    <!-- Email -->
                                    <div class="form-group mb-3">
                                        <input
                                            v-model="form.email"
                                            id="email"
                                            name="email"
                                            :class="{
                                                'is-invalid':
                                                    form.errors.has('email'),
                                            }"
                                            class="form-control rounded-pill border-0 shadow-sm px-4 text-primary"
                                            type="email"
                                            :placeholder="$t('Email')"
                                        />
                                        <has-error
                                            :form="form"
                                            field="email"
                                            class="ml-4"
                                        />
                                    </div>
                                    <!-- domain -->
                                    <div class="form-group mb-3">
                                        <div class="d-flex url">
                                            <input
                                                v-model="form.domain"
                                                id="domain"
                                                name="domain"
                                                :class="{
                                                    'is-invalid':
                                                        form.errors.has(
                                                            'domain'
                                                        ),
                                                }"
                                                class="form-control rounded-pill border-0 shadow-sm px-4 text-primary"
                                                type="text"
                                                :placeholder="$t('domain')"
                                            />
                                            <span>{{ host }}</span>
                                        </div>
                                        <has-error
                                            :form="form"
                                            field="domain"
                                            class="ml-4"
                                        />
                                    </div>
                                    <!-- Company -->
                                    <div class="form-group mb-3">
                                        <input
                                            v-model="form.company"
                                            id="company"
                                            name="company"
                                            :class="{
                                                'is-invalid':
                                                    form.errors.has('company'),
                                            }"
                                            class="form-control rounded-pill border-0 shadow-sm px-4 text-primary"
                                            type="text"
                                            :placeholder="
                                                $t('Company Name')
                                            "
                                        />
                                        <has-error
                                            :form="form"
                                            field="company"
                                            class="ml-4"
                                        />
                                    </div>
                                    <!-- Password -->
                                    <div class="form-group mb-3">
                                        <input
                                            v-model="form.password"
                                            id="password"
                                            name="password"
                                            :class="{
                                                'is-invalid':
                                                    form.errors.has('password'),
                                            }"
                                            class="form-control rounded-pill border-0 shadow-sm px-4 text-primary"
                                            type="password"
                                            :placeholder="$t('password')"
                                        />
                                        <has-error
                                            :form="form"
                                            field="password"
                                            class="ml-4"
                                        />
                                    </div>
                                    <!-- Password -->
                                    <div class="form-group mb-3">
                                        <input
                                            v-model="form.password_confirmation"
                                            id="password_confirmation"
                                            name="password_confirmation"
                                            :class="{
                                                'is-invalid': form.errors.has(
                                                    'password_confirmation'
                                                ),
                                            }"
                                            class="form-control rounded-pill border-0 shadow-sm px-4 text-primary"
                                            type="password"
                                            :placeholder="
                                                $t('confirm_password')
                                            "
                                        />
                                        <has-error
                                            :form="form"
                                            field="password_confirmation"
                                            class="ml-4"
                                        />
                                    </div>

                                    <!-- terms and conditions -->
                                    <div class="row mb-5 ml-2">
                                        <checkbox
                                            v-model="form.terms_and_conditions"
                                            id="terms_and_conditions"
                                            name="terms_and_conditions"
                                            :class="{
                                                'is-invalid': form.errors.has(
                                                    'terms_and_conditions'
                                                ),
                                            }"
                                            required
                                        >
                                            {{ $t('register_policy') }}
                                        </checkbox>
                                        <has-error
                                            :form="form"
                                            field="terms_and_conditions"
                                        />
                                    </div>
                                    <!-- Submit Button -->
                                    <v-button
                                        :loading="form.busy"
                                        class="btn btn-primary btn-block text-uppercase mb-2 rounded-pill shadow-sm"
                                    >
                                        <i class="fas fa-sign-in-alt" />
                                        <strong>{{ $t('register') }}</strong>
                                    </v-button>
                                    <div class="row justify-content-between">
                                        <router-link
                                            :to="{ name: 'find-domain' }"
                                            class="mx-2"
                                        >
                                            {{ $t('already_registered') }}
                                        </router-link>
                                        <router-link
                                            :to="{ name: 'resend' }"
                                            class="mx-2"
                                        >
                                            {{ $t('resend_verification_link') }}
                                        </router-link>
                                    </div>
                                </form>

                                <div class="mt-5" v-else>
                                    <div
                                        v-if="message"
                                        class="alert"
                                        :class="
                                            type == 'success'
                                                ? 'alert-success'
                                                : 'alert-danger'
                                        "
                                    >
                                        {{ message }}
                                        <span v-if="type != 'success'">
                                            {{ $t('please') }}
                                            <router-link
                                                :to="{ name: 'find-domain' }"
                                                >{{ $t('login') }}</router-link
                                            >
                                        </span>
                                    </div>
                                    <h3>{{ $t('register_next_step') }}</h3>
                                    <p class="text-22 mb-4 mt-2">
                                        {{ $t('email_sent') }}
                                        <span class="text-indigo">
                                            {{ verificationForm.email }} </span
                                        >.
                                        {{ $t('confirm_account') }}
                                    </p>
                                    <p>
                                        {{ $t('check_email') }}
                                        <button
                                            @click="resendVerification"
                                            class="btn p-0 text-indigo"
                                        >
                                            {{ $t('resend_verification_link') }}
                                        </button>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End -->
                </div>
            </div>
            <!-- End -->
        </div>
    </div>
</template>
<script>
import Form from 'vform';
import { mapGetters } from 'vuex';

export default {
    layout: 'basic',
    middleware: 'guest',
    metaInfo() {
        return { title: this.$t('register') };
    },
    data: () => ({
        isDemoMode: window.config.isDemoMode,
        form: new Form({
            name: '',
            email: '',
            domain: '',
            password: '',
            password_confirmation: '',
            terms_and_conditions: false,
        }),
        appName: window.config.appName,
        host: location.host,
        verificationForm: new Form({
            email: '',
        }),
        message: '',
        type: null,
    }),
    // Map Getters
    computed: {
        ...mapGetters('operations', ['appInfo']),
    },
    methods: {
        async tenantRegister() {
            if (this.isDemoMode) {
                return toast.fire({
                    type: 'warning',
                    title: this.$t(
                        'You are not allowed to do this in demo version.'
                    ),
                });
            }
            // register the user.
            const { data } = await this.form.post('/api/register');
            if (data) {
                this.verificationForm.email = data.data.email;
            }
        },
        async resendVerification() {
            if (this.isDemoMode) {
                return toast.fire({
                    type: 'warning',
                    title: this.$t(
                        'You are not allowed to do this in demo version.'
                    ),
                });
            }
            await this.verificationForm
                .post('/api/email/resend')
                .then(({ data }) => {
                    this.message = data.message;
                    this.type = 'success';
                })
                .catch((e) => {
                    this.message = e.response.data.message;
                    this.type = 'danger';
                });
        },
    },
};
</script>
