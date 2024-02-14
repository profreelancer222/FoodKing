<template>
    <LoadingComponent :props="loading"/>

    <header class="shadow-xs bg-white ff-header">
        <div class="container flex flex-col lg:flex-row items-center justify-between">
            <div class="w-full flex items-center justify-between gap-5 xl:gap-8 lg:justify-start lg:w-fit">
                <router-link :to="{ name: 'table.menu.table', params : {slug : this.$route.params.slug}}">
                    <img class="w-32" :src="setting.theme_logo" alt="logo">
                </router-link>

                <button class="webcart flex lg:hidden items-center justify-center gap-1.5 w-fit rounded-3xl capitalize text-sm font-medium h-8 px-3 transition text-white bg-heading">
                    <i class="fa-solid fa-bag-shopping text-sm"></i>
                    <span class="whitespace-nowrap">
                        {{ currencyFormat(subtotal, setting.site_digit_after_decimal_point,
                        setting.site_default_currency_symbol, setting.site_currency_position) }}
                    </span>
                </button>
            </div>

            <div class="flex flex-col items-center justify-end gap-3 w-full mt-4 lg:flex-row lg:w-fit lg:mt-0">
                <form @submit.prevent="search" class="header-search-group group flex items-center justify-center border border-solid gap-2 px-2 w-full lg:w-52 h-8 rounded-3xl transition border-[#EFF0F6] bg-[#EFF0F6] focus-within:bg-white focus-within:border-primary">
                    <button type="submit" class="header-search-submit">
                        <i class="lab lab-search-normal"></i>
                    </button>
                    <input type="search" v-model="searchItem" :placeholder="$t('button.search')" class="header-search-field w-full h-full text-xs appearance-none placeholder:font-normal placeholder:text-paragraph text-heading">
                    <button type="button" @click.prevent="searchReset" class="header-search-button transition invisible group-focus-within:visible">
                        <i class="lab lab-close-circle-line lab-font-size-16 lab-font-weight-600 text-red-500"></i>
                    </button>
                </form>

                <div v-if="setting.site_language_switch === enums.activityEnum.ENABLE"
                     class="hidden lg:block relative dropdown-group w-full sm:w-fit">
                    <button
                        class="flex items-center justify-center gap-1.5 w-fit rounded-3xl capitalize text-sm font-medium h-8 px-3 border transition text-heading bg-white border-gray-200 dropdown-btn">
                        <img :src="language.image" alt="flag" class="w-4 h-4 rounded-full">
                        <span class="whitespace-nowrap">{{ language.name }}</span>
                    </button>
                    <ul v-if="languages.length > 0" class="p-2 min-w-[180px] rounded-lg shadow-xl absolute top-14 ltr:right-0 rtl:left-0 z-10 border border-gray-200 bg-white hidden dropdown-list">
                        <li @click="changeLanguage(language.id, language.code)" v-for="language in languages"
                            class="flex items-center gap-2 py-1.5 px-2.5 rounded-md cursor-pointer hover:bg-gray-100">
                            <img :src="language.image" alt="flag" class="w-4 h-4 rounded-full">
                            <span class="text-heading capitalize text-sm">{{ language.name }}</span>
                        </li>
                    </ul>
                </div>

                <button class="webcart hidden lg:flex items-center justify-center gap-1.5 w-fit rounded-3xl capitalize text-sm font-medium h-8 px-3 transition text-white bg-heading">
                    <i class="fa-solid fa-bag-shopping text-sm"></i>
                    <span class="whitespace-nowrap">
                        {{ currencyFormat(subtotal, setting.site_digit_after_decimal_point,
                                setting.site_default_currency_symbol, setting.site_currency_position) }}
                    </span>
                </button>
            </div>
        </div>
    </header>
</template>

<script>
import statusEnum from "../../../enums/modules/statusEnum";
import appService from "../../../services/appService";
import LoadingComponent from "../../frontend/components/LoadingComponent";
import activityEnum from "../../../enums/modules/activityEnum";

export default {
    name: "TableNavbarComponent",
    components: {LoadingComponent},
    data() {
        return {
            loading: {
                isActive: false,
            },
            searchItem: "",
            enums: {
                activityEnum: activityEnum,
            },
            languageProps: {
                paginate: 0,
                order_column: "id",
                order_type: "asc",
                status: statusEnum.ACTIVE
            }
        }
    },
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        language: function () {
            return this.$store.getters['frontendLanguage/show'];
        },
        languages: function () {
            return this.$store.getters['frontendLanguage/lists'];
        },
        subtotal: function () {
            return this.$store.getters['tableCart/subtotal'];
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch('frontendSetting/lists').then(res => {
            this.defaultLanguage = res.data.data.site_default_language;
            const globalState = this.$store.getters['globalState/lists'];

            if (globalState.language_id > 0) {
                this.defaultLanguage = globalState.language_id;
            }

            this.$store.dispatch('frontendLanguage/lists', this.languageProps).then().catch();
            this.$store.dispatch('frontendLanguage/show', this.defaultLanguage).then(res => {
                this.$i18n.locale = res.data.data.code;
                this.$store.dispatch("globalState/init", {
                    language_code: res.data.data.code
                });
            }).catch();

            window.setTimeout(() => {
                this.$store.dispatch('tableDiningTable/show', this.$route.params.slug).then(res => {
                    this.$store.dispatch('tableCart/initTable', res.data.data);
                }).catch((err) => {});
            }, 300);

            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });
    },
    methods: {
        changeLanguage: function (id, code) {
            this.defaultLanguage = id;
            this.$store.dispatch("globalState/set", {language_id: id, language_code: code}).then(res => {
                this.$store.dispatch('frontendLanguage/show', id).then(res => {
                    this.$i18n.locale = res.data.data.code;
                }).catch();
            }).catch();
        },
        currencyFormat(amount, decimal, currency, position) {
            return appService.currencyFormat(amount, decimal, currency, position);
        },
        search: function () {
            if (typeof this.searchItem !== "undefined" && this.searchItem !== "") {
                this.$router.push({name: "table.search", query: {s: this.searchItem}});
                this.searchItem = "";
            }
        },
        searchReset: function () {
            this.searchItem = "";
        },
    }
}
</script>
