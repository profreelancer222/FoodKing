<template>
    <LoadingComponent :props="loading" />
    <button type="button" @click="tokenModal" data-modal="#tokenModal" class="db-btn h-[37px] text-white bg-primary">
        <i class="lab lab-add-circle-line"></i>
        <span class="text-sm capitalize text-white">{{ $t("button.add_token") }}</span>
    </button>

    <div id="tokenModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t("label.token") }}</h3>
                <button class="modal-close fa-solid fa-xmark text-xl text-slate-400 hover:text-red-500"
                    @click.prevent="resetModal"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="rejectOrder">
                    <div class="form-row">
                        <div class="form-col-12">
                            <label for="name" class="db-field-title">
                                {{ $t("label.token_no") }}
                            </label>
                            <input v-model="form.token" v-bind:class="error ? 'invalid' : ''" type="text" id="name"
                                class="db-field-control" />
                            <small class="db-field-alert" v-if="error">
                                {{ error }}
                            </small>
                        </div>
                        <div class="form-col-12">
                            <div class="modal-btns">
                                <button type="button" class="modal-btn-outline modal-close" @click.prevent="resetModal">
                                    <i class="lab lab-close"></i>
                                    <span>{{ $t("button.close") }}</span>
                                </button>

                                <button type="submit" class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-save"></i>
                                    <span>{{ $t("button.save") }}</span>
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
import appService from "../../../services/appService";
import alertService from "../../../services/alertService";
import orderStatusEnum from "../../../enums/modules/orderStatusEnum";
import LoadingComponent from "../components/LoadingComponent";

export default {
    name: "TableOrderTokenComponent",
    components: {
        LoadingComponent,
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            form: {
                token: "",
            },
            error: "",
        };
    },
    methods: {
        tokenModal: function () {
            appService.modalShow("#tokenModal");
        },
        resetModal: function () {
            appService.modalHide("#tokenModal");
            this.form.token = "";
            this.error = "";
        },
        rejectOrder: function () {
            try {
                this.loading.isActive = true;
                this.$store
                    .dispatch("tableOrder/tokenCreate", {
                        id: this.$route.params.id,
                        token: this.form.token,
                    })
                    .then((res) => {
                        this.loading.isActive = false;
                        appService.modalHide();
                        this.form = {
                            token: "",
                        };
                        this.error = "";
                        alertService.successFlip(0, this.$t("label.token"));
                    })
                    .catch((err) => {
                        this.loading.isActive = false;
                        this.error = err.response.data.message;
                    });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            }
        },
    },
};
</script>
