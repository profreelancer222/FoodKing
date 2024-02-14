<template>
    <LoadingComponent :props="loading" />
    <SmSidebarModalCreateComponent :props="addButton" />

    <div id="sidebar" class="drawer">
        <div class="drawer-dialog">
            <div class="drawer-header">
                <h3 class="drawer-title">{{ $t('menu.dining_tables') }}</h3>
                <button class="fa-solid fa-xmark close-btn" @click="reset"></button>
            </div>
            <div class="drawer-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-6">
                            <label for="name" class="db-field-title required">{{ $t("label.name") }}</label>
                            <input v-model="props.form.name" v-bind:class="errors.name ? 'invalid' : ''" type="text"
                                id="name" class="db-field-control">
                            <small class="db-field-alert" v-if="errors.name">{{ errors.name[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="size" class="db-field-title required">{{ $t("label.size") }}</label>
                            <input v-model="props.form.size" v-bind:class="errors.size ? 'invalid' : ''" type="number"
                                id="size" class="db-field-control">
                            <small class="db-field-alert" v-if="errors.size">{{ errors.size[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required" for="active">{{ $t('label.status') }}</label>
                            <div class="db-field-radio-group">
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.statusEnum.ACTIVE" v-model="props.form.status" id="active"
                                            type="radio" class="custom-radio-field">
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="active" class="db-field-label">{{ $t('label.active') }}</label>
                                </div>
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.statusEnum.INACTIVE" v-model="props.form.status" type="radio"
                                            id="inactive" class="custom-radio-field">
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="inactive" class="db-field-label">{{ $t('label.inactive') }}</label>
                                </div>
                            </div>
                        </div>


                        <div class="form-col-12">
                            <div class="flex flex-wrap gap-3 mt-4">
                                <button type="submit" class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-save"></i>
                                    <span>{{ $t("label.save") }}</span>
                                </button>
                                <button type="button" class="modal-btn-outline modal-close" @click="reset">
                                    <i class="lab lab-close"></i>
                                    <span>{{ $t("button.close") }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<script>
import SmSidebarModalCreateComponent from "../components/buttons/SmSidebarModalCreateComponent";
import LoadingComponent from "../components/LoadingComponent";
import statusEnum from "../../../enums/modules/statusEnum";
import alertService from "../../../services/alertService";
import appService from "../../../services/appService";

export default {
    name: "DiningTableCreateComponent",
    components: { SmSidebarModalCreateComponent, LoadingComponent },
    props: ['props'],
    data() {
        return {
            loading: {
                isActive: false
            },
            addButton: {
                title: this.$t("button.add_dining_table")
            },
            enums: {
                statusEnum: statusEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive")
                }
            },
            errors: {},
        }
    },
    computed: {
        branches: function () {
            return this.$store.getters['branch/lists'];
        },
    },
    mounted() {
        try {
            this.loading.isActive = true;
            this.$store.dispatch("defaultAccess/show").then((res) => {
                this.props.form.branch_id = res.data.data.branch_id;
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        } catch (err) {
            this.loading.isActive = false;
        }
    },

    methods: {
        reset: function () {
            appService.sideDrawerHide();
            this.$store.dispatch('diningTable/reset').then().catch();
            this.errors = {};
            this.$props.props.form = {
                name: "",
                size: "",
                status: statusEnum.ACTIVE
            }
        },

        save: function () {
            try {
                const tempId = this.$store.getters['diningTable/temp'].temp_id;
                this.loading.isActive = true;
                this.$store.dispatch('diningTable/save', this.props).then((res) => {
                    appService.sideDrawerHide();
                    this.loading.isActive = false;
                    alertService.successFlip((tempId === null ? 0 : 1), this.$t('menu.dining_tables'));
                    this.props.form.name = "";
                    this.props.form.size = "";
                    this.props.form.branch_id = this.props.form.branch_id;
                    this.props.form.status = statusEnum.ACTIVE;
                    this.errors = {};
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.errors;
                })
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err)
            }
        }
    }
}
</script>
