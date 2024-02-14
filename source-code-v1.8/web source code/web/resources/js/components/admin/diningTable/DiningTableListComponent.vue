<template>
    <LoadingComponent :props="loading" />

    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t('menu.dining_tables') }}</h3>
                <div class="db-card-filter">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                    <FilterComponent />
                    <div class="dropdown-group">
                        <ExportComponent />
                        <div class="dropdown-list db-card-filter-dropdown-list">
                            <PrintComponent :props="printObj" />
                            <ExcelComponent :method="xls" />
                        </div>
                    </div>
                    <DiningTableCreateComponent :props="props" />
                </div>
            </div>

            <div class="table-filter-div">
                <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="name" class="db-field-title after:hidden">{{
                                $t("label.name")
                            }}</label>
                            <input id="name" v-model="props.search.name" type="text" class="db-field-control" />
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="size" class="db-field-title after:hidden">{{
                                $t("label.size")
                            }}</label>
                            <input id="size" v-on:keypress="numberOnly($event)" v-model="props.search.size" type="text"
                                class="db-field-control" />
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStatus" class="db-field-title after:hidden">{{
                                $t("label.status")
                            }}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchStatus"
                                v-model="props.search.status" :options="[
                                    { id: enums.statusEnum.ACTIVE, name: $t('label.active') },
                                    { id: enums.statusEnum.INACTIVE, name: $t('label.inactive') },
                                ]" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        </div>

                        <div class="col-12">
                            <div class="flex flex-wrap gap-3 mt-4">
                                <button class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-search-line lab-font-size-16"></i>
                                    <span>{{ $t("button.search") }}</span>
                                </button>
                                <button class="db-btn py-2 text-white bg-gray-600" @click="clear">
                                    <i class="lab lab-cross-line-2 lab-font-size-22"></i>
                                    <span>{{ $t("button.clear") }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="db-table-responsive">
                <table class="db-table stripe" id="print">
                    <thead class="db-table-head">
                        <tr class="db-table-head-tr">
                            <th class="db-table-head-th">{{ $t('label.name') }}</th>
                            <th class="db-table-head-th">{{ $t('label.size') }}</th>
                            <th class="db-table-head-th">{{ $t('label.status') }}</th>
                            <th class="db-table-head-th hidden-print"
                                v-if="permissionChecker('dining_tables_show') || permissionChecker('dining_tables_edit') || permissionChecker('dining_tables_delete')">
                                {{ $t('label.action') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="diningTables.length > 0">
                        <tr class="db-table-body-tr" v-for="diningTable in diningTables" :key="diningTable">
                            <td class="db-table-body-td">{{ diningTable.name }}</td>
                            <td class="db-table-body-td">{{ diningTable.size }}</td>
                            <td class="db-table-body-td">
                                <span :class="statusClass(diningTable.status)">
                                    {{ enums.statusEnumArray[diningTable.status] }}
                                </span>
                            </td>
                            <td class="db-table-body-td hidden-print"
                                v-if="permissionChecker('dining_tables_show') || permissionChecker('dining_tables_edit') || permissionChecker('dining_tables_delete')">
                                <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                    <SmIconQrCodeComponent :link="diningTable.qr" />
                                    <SmIconViewComponent :link="'admin.diningTable.show'" :id="diningTable.id"
                                        v-if="permissionChecker('dining_tables_show')" />
                                    <SmIconSidebarModalEditComponent @click="edit(diningTable)"
                                        v-if="permissionChecker('dining_tables_edit')" />
                                    <SmIconDeleteComponent @click="destroy(diningTable.id)"
                                        v-if="permissionChecker('dining_tables_delete')" />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6">
                <PaginationSMBox :pagination="pagination" :method="list" />
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <PaginationTextComponent :props="{ page: paginationPage }" />
                    <PaginationBox :pagination="pagination" :method="list" />
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import LoadingComponent from "../components/LoadingComponent";
import DiningTableCreateComponent from "./DiningTableCreateComponent";
import alertService from "../../../services/alertService";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent";
import PaginationBox from "../components/pagination/PaginationBox";
import PaginationSMBox from "../components/pagination/PaginationSMBox";
import appService from "../../../services/appService";
import statusEnum from "../../../enums/modules/statusEnum";
import TableLimitComponent from "../components/TableLimitComponent";
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent";
import SmIconSidebarModalEditComponent from "../components/buttons/SmIconSidebarModalEditComponent";
import SmIconQrCodeComponent from "../components/buttons/SmIconQrCodeComponent";
import SmIconViewComponent from "../components/buttons/SmIconViewComponent";
import ExportComponent from "../components/buttons/export/ExportComponent";
import PrintComponent from "../components/buttons/export/PrintComponent";
import ExcelComponent from "../components/buttons/export/ExcelComponent";
import FilterComponent from "../components/buttons/collapse/FilterComponent";

export default {
    name: "DiningTableListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        DiningTableCreateComponent,
        LoadingComponent,
        SmIconDeleteComponent,
        SmIconSidebarModalEditComponent,
        SmIconQrCodeComponent,
        SmIconViewComponent,
        ExportComponent,
        PrintComponent,
        ExcelComponent,
        FilterComponent
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            printLoading: true,
            printObj: {
                id: "print",
                popTitle: this.$t("menu.dining_tables"),
            },
            enums: {
                statusEnum: statusEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive")
                }
            },
            props: {
                form: {
                    branch_id: null,
                    name: "",
                    size: "",
                    status: statusEnum.ACTIVE,
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    order_type: 'desc',
                    name: "",
                    size: "",
                    status: null,
                }
            }
        }
    },
    computed: {
        diningTables: function () {
            return this.$store.getters['diningTable/lists'];
        },
        pagination: function () {
            return this.$store.getters['diningTable/pagination'];
        },
        paginationPage: function () {
            return this.$store.getters['diningTable/page'];
        }
    },
    mounted() {
        this.list();
    },
    methods: {
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        numberOnly: function (e) {
            return appService.floatNumber(e);
        },
        statusClass: function (status) {
            return appService.statusClass(status);
        },
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.$store.dispatch('diningTable/lists', this.props.search).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        search: function () {
            this.list();
        },
        clear: function () {
            this.props.search.paginate = 1;
            this.props.search.page = 1;
            this.props.search.name = "";
            this.props.search.size = "";
            this.props.search.status = null;
            this.list();
        },
        edit: function (diningTable) {
            appService.sideDrawerShow();
            this.loading.isActive = true;
            this.$store.dispatch('diningTable/edit', diningTable.id);
            this.props.form = {
                branch_id: diningTable.branch_id,
                name: diningTable.name,
                size: diningTable.size,
                status: diningTable.status,
            };
            this.loading.isActive = false;
        },
        destroy: function (id) {
            appService.destroyConfirmation().then((res) => {
                try {
                    this.loading.isActive = true;
                    this.$store.dispatch('diningTable/destroy', { id: id, search: this.props.search }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t('menu.dining_tables'));
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.message);
                    })
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }).catch((err) => {
                this.loading.isActive = false;
            })
        },
        xls: function () {
            this.loading.isActive = true;
            this.$store.dispatch("diningTable/export", this.props.search).then((res) => {
                this.loading.isActive = false;
                const blob = new Blob([res.data], {
                    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                });
                const link = document.createElement("a");
                link.href = URL.createObjectURL(blob);
                link.download = this.$t("menu.dining_tables");
                link.click();
                URL.revokeObjectURL(link.href);
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });
        },
    }
}
</script>

<style scoped>
@media print {
    .hidden-print {
        display: none !important;
    }
}
</style>
