<template>
    <div class="card">
        <div class="card-header setings-header">
            <h3 class="card-title">{{ $t('Custom HTML') }}</h3>
        </div>
        <form
            class="form-horizontal"
            @submit.prevent="update"
            @keydown="form.onKeydown($event)"
        >
            <div class="card-body">
                <div class="form-group">
                    <label for="custom_html">
                        {{ $t('Custom HTML') }}
                        <span class="required">*</span>
                    </label>
                    <textarea
                        v-model="form.custom_html"
                        class="form-control"
                        :class="{
                            'is-invalid': form.errors.has('custom_html'),
                        }"
                        id="custom_html"
                        :placeholder="$t('Custom HTML (You can add SEO related tags, google analytics, facebook pixel codes etc here.)')"
                        rows="5"
                    ></textarea>
                    <has-error :form="form" field="custom_html" />
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
            custom_html: '',
        }),
        loading: true,
        isDemoMode: window.config.isDemoMode,
    }),
    created() {
        this.getData();
    },
    methods: {
        // get the user
        async getData() {
            const { data } = await axios.get(
                window.location.origin + '/api/settings/custom-html-settings'
            );
            this.form.fill(data.data);
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
                    window.location.origin +
                        '/api/settings/custom-html-settings'
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
