<template>
    <LoadingComponent :props="loading" />
    
    <footer class="bg-primary">
        <div class="container">
            <div class="flex flex-col md:flex-row items-center justify-between gap-5 py-8">
                <p class="text-sm text-white">{{ setting.site_copyright }}</p>
                <nav v-if="pages.length > 0"  class="flex items-center gap-6">
                    <router-link v-for="page in pages" class="text-sm capitalize text-white"
                        :to="{ name: 'table.page', params: { slug: this.$route.params.slug , pageSlug: page.slug } }">
                        {{ page.title }}
                    </router-link>
                </nav>
            </div>
        </div>
    </footer>
</template>


<script>
import statusEnum from "../../../enums/modules/statusEnum";
import menuSectionEnum from "../../../enums/modules/menuSectionEnum";
import LoadingComponent from "../../frontend/components/LoadingComponent";

export default {
    name: "TableFooterComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            }
        }
    },
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        pages: function () {
            return this.$store.getters['frontendPage/lists'];
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch("frontendPage/lists", {
            paginate: 0,
            order_column: "id",
            order_type: "asc",
            menu_section_id: menuSectionEnum.FOOTER,
            status: statusEnum.ACTIVE
        }).then(res => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });
    }
}
</script>
