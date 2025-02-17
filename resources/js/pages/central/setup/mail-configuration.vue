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
                        <div class="card-header setings-header">
                            <h3 class="card-title">
                                {{
                                    $t(
                                        'Mail Configuration'
                                    )
                                }}
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label for="mail_mailer"
                                        >{{
                                            $t(
                                                'MAIL MAILER'
                                            )
                                        }}
                                        <span class="required">*</span></label
                                    >
                                    <input
                                        id="mail_mailer"
                                        v-model="form.mail_mailer"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                form.errors.has('mail_mailer'),
                                        }"
                                        name="mail_mailer"
                                        :placeholder="
                                            $t(
                                                'MAIL MAILER'
                                            )
                                        "
                                        required
                                    />
                                    <has-error
                                        :form="form"
                                        field="mail_mailer"
                                    />
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="mail_host"
                                        >{{
                                            $t(
                                                'MAIL HOST'
                                            )
                                        }}
                                        <span class="required">*</span></label
                                    >
                                    <input
                                        id="mail_host"
                                        v-model="form.mail_host"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                form.errors.has('mail_host'),
                                        }"
                                        name="mail_host"
                                        :placeholder="
                                            $t(
                                                'MAIL HOST'
                                            )
                                        "
                                        required
                                    />
                                    <has-error :form="form" field="mail_host" />
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="mail_port"
                                        >{{
                                            $t(
                                                'MAIL PORT'
                                            )
                                        }}
                                        <span class="required">*</span></label
                                    >
                                    <input
                                        id="mail_port"
                                        v-model="form.mail_port"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                form.errors.has('mail_port'),
                                        }"
                                        name="mail_port"
                                        :placeholder="
                                            $t(
                                                'MAIL PORT'
                                            )
                                        "
                                        required
                                    />
                                    <has-error :form="form" field="mail_port" />
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="mail_username"
                                        >{{
                                            $t(
                                                'MAIL USERNAME'
                                            )
                                        }}
                                        <span class="required">*</span></label
                                    >
                                    <input
                                        id="mail_username"
                                        v-model="form.mail_username"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                form.errors.has(
                                                    'mail_username'
                                                ),
                                        }"
                                        name="mail_username"
                                        :placeholder="
                                            $t(
                                                'MAIL USERNAME'
                                            )
                                        "
                                        required
                                    />
                                    <has-error
                                        :form="form"
                                        field="mail_username"
                                    />
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="mail_password"
                                        >{{
                                            $t(
                                                'MAIL PASSWORD'
                                            )
                                        }}
                                        <span class="required">*</span></label
                                    >
                                    <input
                                        id="mail_password"
                                        v-model="form.mail_password"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                form.errors.has(
                                                    'mail_password'
                                                ),
                                        }"
                                        name="mail_password"
                                        :placeholder="
                                            $t(
                                                'MAIL PASSWORD'
                                            )
                                        "
                                        required
                                    />
                                    <has-error
                                        :form="form"
                                        field="mail_password"
                                    />
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="mail_encryption"
                                        >{{
                                            $t(
                                                'MAIL ENCRYPTION'
                                            )
                                        }}
                                        <span class="required">*</span></label
                                    >
                                    <input
                                        id="mail_encryption"
                                        v-model="form.mail_encryption"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                form.errors.has(
                                                    'mail_encryption'
                                                ),
                                        }"
                                        name="mail_encryption"
                                        :placeholder="
                                            $t(
                                                'MAIL ENCRYPTION'
                                            )
                                        "
                                        required
                                    />
                                    <has-error
                                        :form="form"
                                        field="mail_encryption"
                                    />
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="mail_from_address"
                                        >{{
                                            $t(
                                                'MAIL FROM ADDRESS'
                                            )
                                        }}
                                        <span class="required">*</span></label
                                    >
                                    <input
                                        id="mail_from_address"
                                        v-model="form.mail_from_address"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                form.errors.has(
                                                    'mail_from_address'
                                                ),
                                        }"
                                        name="mail_from_address"
                                        :placeholder="
                                            $t(
                                                'MAIL FROM ADDRESS'
                                            )
                                        "
                                        required
                                    />
                                    <has-error
                                        :form="form"
                                        field="mail_from_address"
                                    />
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="mail_from_name"
                                        >{{
                                            $t(
                                                'MAIL FROM NAME'
                                            )
                                        }}
                                    </label>
                                    <input
                                        id="mail_from_name"
                                        v-model="form.mail_from_name"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                form.errors.has(
                                                    'mail_from_address'
                                                ),
                                        }"
                                        name="mail_from_name"
                                        :placeholder="
                                            $t(
                                                'MAIL FROM ADDRESS'
                                            )
                                        "
                                    />
                                    <has-error
                                        :form="form"
                                        field="mail_from_name"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <v-button
                                :loading="form.busy"
                                class="btn btn-primary"
                            >
                                <i class="fas fa-edit" />
                                {{ $t('Save changes') }}
                            </v-button>
                            <button type="button" class="btn btn-secondary float-right" :class="{ 'btn-loading': loading }" @click="testConnection()">
                                <i class="fas fa-wifi"></i> {{ $t("Test Connection") }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import Form from 'vform';
import axios from 'axios';
import SettingsSidebar from '~/components/central/SettingsSidebar';

export default {
    layout: 'central',
    middleware: ['auth', 'check-permissions'],
    metaInfo() {
        return { title: this.$t('Mail Configuration') };
    },
    components: {
        SettingsSidebar,
    },
    data: () => ({
        isDemoMode: window.config.isDemoMode,
        breadcrumbsCurrent:
            'Mail Configuration',
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
                name: 'Mail Configuration',
                url: '',
            },
        ],
        form: new Form({
            mail_mailer: '',
            mail_host: '',
            mail_port: '',
            mail_username: '',
            mail_password: '',
            mail_encryption: '',
            mail_from_address: '',
            mail_from_name: '',
        }),
        loading : false,
    }),

    created() {
        this.getMailServerValues();
    },
    methods: {
        // get all current values
        async getMailServerValues() {
            const { data } = await axios.get(
                window.location.origin + '/api/mail-configuration/'
            );
            this.form.mail_mailer = data.mail_mailer.value;
            this.form.mail_host = data.mail_host.value;
            this.form.mail_port = data.mail_port.value;
            this.form.mail_username = data.mail_username.value;
            this.form.mail_password = data.mail_password.value;
            this.form.mail_encryption = data.mail_encryption.value;
            this.form.mail_from_address = data.mail_from_address.value;
            this.form.mail_from_name = data.mail_from_name.value;
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
            // for production
            await this.form
                .post(window.location.origin + '/api/update-mail-configuration')
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t(
                            'Settings updated successfully'
                        ),
                    });
                })
                .catch(() => {
                    toast.fire({
                        type: 'error',
                        title: this.$t('Opps...something went wrong'),
                    });
                });

            // action for demo
            // toast.fire({
            //   type: "warning",
            //   title: this.$t("You are not allowed to do this in demo version."),
            // });
        },

        testConnection() {
        this.loading = true;
        axios
            .get( window.location.origin + "/api/send-test-connection-email")
            .then(() => {
        this.loading = false;
            toast.fire({
                type: "success",
                title: this.$t("Email sent. Your connection is secure"),
            });
            })
            .catch(() => {
        this.loading = false;
            toast.fire({
                type: "error",
                title: this.$t("Opps...something went wrong"),
            });
            });
        }
    },
};
</script>
