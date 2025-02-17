<template>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $t('Update your profile') }}</h3>
        </div>
        <div class="card-body">
            <form
                class="form-horizontal"
                @submit.prevent="updateProfile"
                @keydown="form.onKeydown($event)"
            >
                <div class="form-group row">
                    <label for="name" class="col-sm-2 col-form-label text-right"
                        >{{ $t('Name') }}
                        <span class="required">*</span></label
                    >
                    <div class="col-sm-10">
                        <input
                            type="text"
                            v-model="form.name"
                            class="form-control"
                            :class="{ 'is-invalid': form.errors.has('name') }"
                            id="name"
                            :placeholder="$t('Enter a name')"
                        />
                        <has-error :form="form" field="name" />
                    </div>
                </div>
                <div class="form-group row">
                    <label
                        for="email"
                        class="col-sm-2 col-form-label text-right"
                        >{{ $t('Email') }}
                        <span class="required">*</span></label
                    >
                    <div class="col-sm-10">
                        <input
                            type="email"
                            v-model="form.email"
                            class="form-control"
                            :class="{ 'is-invalid': form.errors.has('email') }"
                            id="email"
                            readonly
                            :placeholder="$t('Enter your email address')"
                        />
                        <has-error :form="form" field="email" />
                    </div>
                </div>
                <div class="form-group row">
                    <label
                        for="currentPassword"
                        class="col-sm-2 col-form-label text-right"
                        >{{ $t('Current Password') }}</label
                    >
                    <div class="col-sm-10">
                        <input
                            type="password"
                            v-model="form.currentPassword"
                            class="form-control"
                            :class="{
                                'is-invalid':
                                    form.errors.has('currentPassword'),
                            }"
                            id="currentPassword"
                            :placeholder="$t('Current Password')"
                        />
                        <has-error :form="form" field="currentPassword" />
                    </div>
                </div>
                <div class="form-group row">
                    <label
                        for="newPassword"
                        class="col-sm-2 col-form-label text-right"
                        >{{ $t('New Password') }}</label
                    >
                    <div class="col-sm-10">
                        <input
                            type="password"
                            v-model="form.newPassword"
                            class="form-control"
                            :class="{
                                'is-invalid': form.errors.has('newPassword'),
                            }"
                            id="newPassword"
                            :placeholder="
                                $t('Enter new password')
                            "
                        />
                        <has-error :form="form" field="newPassword" />
                    </div>
                </div>
                <div class="form-group row">
                    <label
                        for="confirmPassword"
                        class="col-sm-2 col-form-label text-right"
                        >{{ $t('Confirm Password') }}</label
                    >
                    <div class="col-sm-10">
                        <input
                            type="password"
                            v-model="form.confirmPassword"
                            class="form-control"
                            :class="{
                                'is-invalid':
                                    form.errors.has('confirmPassword'),
                            }"
                            id="confirmPassword"
                            :placeholder="
                                $t('Enter confirm password')
                            "
                        />
                        <has-error :form="form" field="confirmPassword" />
                    </div>
                </div>
                <div class="form-group row">
                    <div class="offset-sm-2 col-sm-10">
                        <v-button :loading="form.busy" class="btn btn-primary">
                            <i class="fas fa-edit" />
                            {{ $t('Save changes') }}
                        </v-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import Form from 'vform';
import axios from 'axios';

export default {
    middleware: 'auth',
    metaInfo() {
        return { title: this.$t('Update Profile') };
    },
    data: () => ({
        isDemoMode: window.config.isDemoMode,
        breadcrumbsCurrent: 'Update Profile',
        breadcrumbs: [
            {
                name: 'Dashboard',
                url: 'home',
            },
            {
                name: 'Update',
                url: '',
            },
        ],
        form: new Form({
            name: '',
            email: '',
            currentPassword: '',
            newPassword: '',
            confirmPassword: '',
        }),
        loading: true,
        user: '',
    }),
    created() {
        this.getUser();
    },
    methods: {
        // get the user
        async getUser() {
            const { data } = await axios.get(
                window.location.origin + '/api/user'
            );
            this.user = data.data;
            this.form.name = data.data.name;
            this.form.email = data.data.email;
        },

        // update profile
        async updateProfile() {
            if (this.isDemoMode) {
                return toast.fire({
                    type: 'warning',
                    title: this.$t(
                        'You are not allowed to do this in demo version.'
                    ),
                });
            }
            await this.form
                .post(window.location.origin + '/api/update-profile')
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('Profile updated successfully'),
                    });
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

<style lang="scss" scoped></style>
