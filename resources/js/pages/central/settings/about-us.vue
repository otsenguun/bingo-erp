<template>
    <div class="card">
        <form
            class="form-horizontal"
            @submit.prevent="update"
            @keydown="form.onKeydown($event)"
        >
            <div class="card-header setings-header">
                <h3 class="card-title">{{ $t('About Us') }}</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="about_us_tagline">
                        {{ $t('About Us Tagline') }}
                        <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        v-model="form.about_us_tagline"
                        class="form-control"
                        :class="{
                            'is-invalid': form.errors.has('about_us_tagline'),
                        }"
                        id="about_us_tagline"
                        :placeholder="$t('Enter about us tagline')"
                    />
                    <has-error :form="form" field="about_us_tagline" />
                </div>
                <div class="form-group">
                    <label for="about_us_title">
                        {{ $t('About Us Title') }}
                        <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        v-model="form.about_us_title"
                        class="form-control"
                        :class="{
                            'is-invalid': form.errors.has('about_us_title'),
                        }"
                        id="about_us_title"
                        :placeholder="$t('Enter about us title')"
                    />
                </div>
                <div class="form-group">
                    <label for="about_us_description">
                        {{ $t('About Us Description') }}
                        <span class="required">*</span>
                    </label>
                    <textarea
                        v-model="form.about_us_description"
                        class="form-control"
                        :class="{
                            'is-invalid': form.errors.has(
                                'about_us_description'
                            ),
                        }"
                        id="about_us_description"
                        :placeholder="
                            $t('Enter about us description')
                        "
                        rows="4"
                    ></textarea>
                    <has-error :form="form" field="about_us_description" />
                </div>

                <div class="form-group col-12 d-flex flex-wrap">
                    <div class="pr-5">
                      <toggle-button 
                            v-model="form.is_show_about_us_section"
                            :sync="true"
                        />
                        {{ $t("Show at landing page") }}
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <v-button :loading="form.busy" class="btn btn-primary">
                    <i class="fas fa-edit" /> {{ $t('Save changes') }}
                </v-button>
            </div>
        </form>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import Form from 'vform';
import axios from 'axios';

export default {
    layout: 'central',
    middleware: ['auth', 'check-permissions'],
    metaInfo() {
        return { title: this.$t('Landing Page Settings') };
    },
    data: () => ({
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
            about_us_tagline: '',
            about_us_title: '',
            about_us_description: '',
            is_show_about_us_section: false ,
        }),
        loading: true,
        user: '',
        isDemoMode: window.config.isDemoMode,
    }),

    computed: mapGetters({
        appInfo: 'operations/appInfo',
    }),

    created() {
        this.getData();
    },
    methods: {
        // get the user
        async getData() {
            const { data } = await axios.get(
                window.location.origin + '/api/settings/about-us-settings'
            );
            this.user = data.data;
            this.form.about_us_tagline = data.data.about_us_tagline || '';
            this.form.about_us_title = data.data.about_us_title || '';
            this.form.about_us_description = data.data.about_us_description || '';
            this.form.is_show_about_us_section = this.appInfo.is_show_about_us_section;
        },

        // update
        async update() {
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
                    window.location.origin + '/api/settings/about-us-settings'
                )
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('Landing Page Settings updated successfully'),
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
