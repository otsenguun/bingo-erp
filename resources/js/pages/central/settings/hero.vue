<template>
    <div class="card">
        <form
            class="form-horizontal"
            @submit.prevent="update"
            @keydown="form.onKeydown($event)"
        >
            <div class="card-header setings-header">
                <h3 class="card-title">{{ $t('Hero') }}</h3>
            </div>
            <div class="card-body p-4">
                <div class="form-group">
                    <label for="name"
                        >{{ $t('Hero Tagline') }}
                        <span class="required">*</span></label
                    >
                    <input
                        type="text"
                        v-model="form.hero_tagline"
                        class="form-control"
                        :class="{
                            'is-invalid': form.errors.has('hero_tagline'),
                        }"
                        id="hero_tagline"
                        :placeholder="$t('Enter your hero tagline')"
                    />
                    <has-error :form="form" field="hero_tagline" />
                </div>

                <div class="form-group">
                    <label for="hero_title">
                        {{ $t('Hero Title') }}
                        <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        v-model="form.hero_title"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.has('hero_title') }"
                        id="hero_title"
                        :placeholder="$t('Enter your hero title')"
                    />
                    <has-error :form="form" field="hero_title" />
                </div>

                <div class="form-group">
                    <label for="hero_description">
                        {{ $t('Hero Description') }}
                        <span class="required">*</span>
                    </label>
                    <textarea
                        v-model="form.hero_description"
                        class="form-control"
                        :class="{
                            'is-invalid': form.errors.has('hero_description'),
                        }"
                        id="hero_description"
                        :placeholder="$t('Enter your hero description')"
                    ></textarea>
                    <has-error :form="form" field="hero_description" />
                </div>

                <div class="form-group">
                    <label for="hero_demo_button_text">
                        {{ $t('Hero Demo Button Text') }}
                        <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        v-model="form.hero_demo_button_text"
                        class="form-control"
                        :class="{
                            'is-invalid': form.errors.has(
                                'hero_demo_button_text'
                            ),
                        }"
                        id="hero_demo_button_text"
                        :placeholder="
                            $t('Enter Hero Demo Button Text')
                        "
                    />
                    <has-error :form="form" field="hero_demo_button_text" />
                </div>

                <div class="form-group">
                    <label for="hero_demo_button_link">
                        {{ $t('Hero Demo Button Link') }}
                        <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        v-model="form.hero_demo_button_link"
                        class="form-control"
                        :class="{
                            'is-invalid': form.errors.has(
                                'hero_demo_button_link'
                            ),
                        }"
                        id="hero_demo_button_link"
                        :placeholder="
                            $t('Enter a link to a demo page')
                        "
                    />
                    <has-error :form="form" field="hero_demo_button_link" />
                </div>

                <div class="form-group">
                    <label for="hero_get_started_button_text">
                        {{ $t('Hero Get Started Button Text') }}
                        <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        v-model="form.hero_get_started_button_text"
                        class="form-control"
                        :class="{
                            'is-invalid': form.errors.has(
                                'hero_get_started_button_text'
                            ),
                        }"
                        id="hero_get_started_button_text"
                        :placeholder="
                            $t(
                                'Enter Hero Get Started Button Text'
                            )
                        "
                    />
                    <has-error
                        :form="form"
                        field="hero_get_started_button_text"
                    />
                </div>

                <div class="form-group">
                    <label for="hero_get_started_button_link">
                        {{ $t('Hero Get Started Button Link') }}
                        <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        v-model="form.hero_get_started_button_link"
                        class="form-control"
                        :class="{
                            'is-invalid': form.errors.has(
                                'hero_get_started_button_link'
                            ),
                        }"
                        id="hero_get_started_button_link"
                        :placeholder="
                            $t(
                                'Enter a link to a page or a page template'
                            )
                        "
                    />
                    <has-error
                        :form="form"
                        field="hero_get_started_button_link"
                    />
                </div>

                <div class="form-group col-12 d-flex flex-wrap">
                    <div class="pr-5">
                      <toggle-button 
                            v-model="form.is_show_hero_section"
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
            hero_tagline: '',
            hero_title: '',
            hero_description: '',
            hero_demo_button_text: '',
            hero_demo_button_link: '',
            hero_get_started_button_text: '',
            hero_get_started_button_link: '',
            is_show_hero_section: false ,
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
                window.location.origin + '/api/settings/hero-settings'
            );
            this.user = data.data;
            this.form.hero_tagline = data.data.hero_tagline || '';
            this.form.hero_title = data.data.hero_title || '';
            this.form.hero_description = data.data.hero_description || '';
            this.form.hero_demo_button_text = data.data.hero_demo_button_text || '';
            this.form.hero_demo_button_link = data.data.hero_demo_button_link || '';
            this.form.hero_get_started_button_text = data.data.hero_get_started_button_text || '';
            this.form.hero_get_started_button_link = data.data.hero_get_started_button_link || '';
            this.form.is_show_hero_section = this.appInfo.is_show_hero_section;
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
                .patch(window.location.origin + '/api/settings/hero-settings')
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
