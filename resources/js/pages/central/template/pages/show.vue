<template>
  <div class="landing">
    <template-nav />
    <section class="section">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <h3 class="text-center">
              {{ name }}
            </h3>

            <div class="content" v-html="content && content"></div>
          </div>
        </div>
      </div>
    </section>
    <template-footer />
  </div>
</template>

<script>
import TemplateNav from "~/components/template/Navbar";
import TemplateFooter from "~/components/template/Footer";
import axios from "axios";

export default {
  layout: "template",
  metaInfo() {
    return { title: this.$t("Dashboard") };
  },

  components: {
    TemplateNav,
    TemplateFooter,
  },

  beforeRouteUpdate(to, from, next) {
    next();
    this.getData();
  },

  data: () => ({
    name: "",
    content: "",
  }),

  mounted() {
    this.getData();
  },

  methods: {
    async getData() {
      await axios
        .get(
          window.location.origin +
          "/api/pages-by-slug/" +
          this.$route.params.slug
        )
        .then(({ data }) => {
          this.content = data.data.content;
          this.name = data.data.name;
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
};
</script>
<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;500;600;700&display=swap");
@import "../../../../../sass/template.scss";
</style>
