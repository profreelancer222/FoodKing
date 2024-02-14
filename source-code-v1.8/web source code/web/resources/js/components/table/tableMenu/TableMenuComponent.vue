<template>
    <section class="mb-16 mt-4">
        <div class="container">
            <LoadingComponent :props="loading"/>

            <div class="swiper mb-7 menu-swiper" v-if="categories.length > 1">
                <div class="swiper-wrapper">
                    <Carousel :settings="settings" :breakpoints="breakpoints">
                        <Slide class="swiper-slide" v-for="(category, index) in categories" :key="category"
                               :class="category.id === itemProps.search.item_category_id || (category.id === 0 && itemProps.search.item_category_id ==='') ? 'pos-group' : ''">
                            <router-link v-if="index === 0" to="#" @click.prevent="allCategory(category)"
                                         class="swiper-slide w-32 flex flex-col items-center text-center gap-4 p-3 rounded-2xl border-b-2 border-transparent transition hover:bg-[#FFEDF4] bg-[#F7F7FC] overflow-hidden">
                                <img class="h-10 drop-shadow-category" :src="category.thumb" alt="category">
                                <h3 class="w-full text-xs leading-[16px] whitespace-nowrap overflow-hidden text-ellipsis font-medium font-rubik">{{ category.name }}</h3>
                            </router-link>
                            <router-link v-else to="#" @click.prevent="setCategory(category.id, category.slug)"
                                         class="swiper-slide w-32 flex flex-col items-center text-center gap-4 p-3 rounded-2xl border-b-2 border-transparent transition hover:bg-[#FFEDF4] bg-[#F7F7FC] overflow-hidden">
                                <img class="h-10 drop-shadow-category" :src="category.thumb" alt="category">
                                <h3 class="w-full text-xs leading-[16px] whitespace-nowrap overflow-hidden text-ellipsis font-medium font-rubik">{{ category.name }}</h3>
                            </router-link>
                        </Slide>
                    </Carousel>
                </div>
            </div>

            <div v-if="categories.length > 0" class="flex flex-wrap gap-3 w-full mb-5 veg-navs">
                <button
                    :disabled="itemProps.property.type !== null && itemProps.property.type === enums.itemTypeEnum.VEG"
                    @click.prevent="itemProps.property.type === enums.itemTypeEnum.NON_VEG ? itemTypeReset() : itemTypeSet(enums.itemTypeEnum.NON_VEG)"
                    :class="itemProps.property.type === enums.itemTypeEnum.NON_VEG ? 'veg-active' : ''" type="button"
                    class="flex items-center gap-3 w-fit pl-3 pr-4 py-1.5 rounded-3xl transition hover:shadow-filter hover:bg-white bg-[#EFF0F6]">
                    <img :src="setting.image_vag" alt="category" class="h-6">
                    <span class="capitalize text-sm font-medium text-heading">{{ $t('label.frontend_non_veg') }}</span>
                    <i
                        class="lab-close-circle-line text-xl text-red-500 transition opacity-0 -ml-8 clear-item-type-filter font-fill-danger lab-font-size-24"></i>
                </button>
                <button
                    :disabled="itemProps.property.type !== null && itemProps.property.type === enums.itemTypeEnum.NON_VEG"
                    @click.prevent="itemProps.property.type === enums.itemTypeEnum.VEG ? itemTypeReset() : itemTypeSet(enums.itemTypeEnum.VEG)"
                    :class="itemProps.property.type === enums.itemTypeEnum.VEG ? 'veg-active' : ''" type="button"
                    class="flex items-center gap-3 w-fit pl-3 pr-4 py-1.5 rounded-3xl transition hover:shadow-filter hover:bg-white bg-[#EFF0F6]">
                    <img :src="setting.image_non_vag" alt="category" class="h-6">
                    <span class="capitalize text-sm font-medium text-heading">{{ $t('label.veg') }}</span>
                    <i
                        class="lab-close-circle-line text-xl text-red-500 transition opacity-0 -ml-8 font-fill-danger lab-font-size-24"></i>
                </button>
            </div>

            <div v-if="Object.keys(category).length > 0"
                 class="flex gap-4 items-center justify-between mb-6">
                <h2 class="capitalize text-[26px] leading-[40px] font-semibold text-center sm:text-left text-primary">
                    {{ category.name }}
                </h2>
                <div class="flex items-center gap-3">
                    <button type="button" class="lab lab-row-vertical lab-font-size-20 text-xl"
                            v-on:click="itemProps.property.design = enums.itemDesignEnum.LIST"
                            :class="itemProps.property.design === enums.itemDesignEnum.LIST ? 'text-heading' : 'text-[#A0A3BD]'"></button>
                    <button type="button" class="lab lab-element-3 lab-font-size-20 text-xl"
                            v-on:click="itemProps.property.design = enums.itemDesignEnum.GRID"
                            :class="itemProps.property.design === enums.itemDesignEnum.GRID ? 'text-heading' : 'text-[#A0A3BD]'"></button>
                </div>
            </div>

            <ItemComponent :items="items" :type="itemProps.property.type" :design="itemProps.property.design"/>
        </div>
    </section>



    <div v-if="Object.keys(order).length > 0" ref="confirmOrder" id="confirm-order" class="modal confirm-order ff-modal">
        <div class="modal-dialog max-w-[360px] relative">
            <button class="modal-close fa-regular fa-circle-xmark absolute top-5 right-5" @click.prevent="closeModal"></button>
            <div class="modal-body">
                <h3 class="capitalize text-base font-medium text-center mt-2 mb-3">
                    {{ $t('message.order_thank_you') }}
                </h3>
                <img class="w-[120px] mx-auto mb-3" :src="setting.image_confirm" alt="gif">
                <h3 class="capitalize text-lg font-medium text-center mb-3 text-primary">
                    {{ $t('label.order_confirmed') }}
                </h3>
                <p class="text-sm leading-6 mb-4">
                    {{ $t('message.order_confirm') }}
                    <b class="font-medium">{{
                            $t('label.dining_table') }}.
                    </b>
                    <strong class="font-normal" v-if="setting.site_online_payment_gateway === enums.activityEnum.ENABLE && order.transaction === null && order.payment_status === enums.paymentStatusEnum.UNPAID && paymentMethod === 'digitalPayment'">
                        {{ $t('message.choosing_payment_options') }}
                    </strong>
                </p>

                <div class="flex gap-6" v-if="setting.site_online_payment_gateway === enums.activityEnum.ENABLE && order.transaction === null && order.payment_status === enums.paymentStatusEnum.UNPAID && paymentMethod === 'digitalPayment' ">
                    <router-link @click.prevent="closeModal"
                                 class="w-full rounded-3xl text-center font-medium leading-6 py-3 border border-primary text-primary bg-white"
                                 :to="{ name: 'table.tableOrder.details', params: { slug : this.$route.params.slug, id: order.id } }">
                        {{ $t('button.go_to_order') }}
                    </router-link>
                    <a :href="'/payment/' + order.id + '/pay'"
                       class="w-full rounded-3xl text-center font-medium leading-6 py-3 text-white bg-primary">
                        {{ $t('button.pay_now') }}
                    </a>
                </div>

                <router-link v-else @click.prevent="closeModal"
                             class="w-full rounded-3xl text-center font-medium leading-6 py-3 text-white bg-primary"
                             :to="{ name: 'table.tableOrder.details', params: { slug : this.$route.params.slug, id: order.id } }">
                    {{ $t('button.go_to_order') }}
                </router-link>

            </div>
        </div>
    </div>



</template>

<script>
import LoadingComponent from "../../table/components/LoadingComponent.vue";
import statusEnum from "../../../enums/modules/statusEnum";
import {Carousel, Slide, Pagination, Navigation} from 'vue3-carousel';
import ItemComponent from "../components/ItemComponent.vue";
import itemDesignEnum from "../../../enums/modules/itemDesignEnum";
import itemTypeEnum from "../../../enums/modules/itemTypeEnum";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum";
import activityEnum from "../../../enums/modules/activityEnum";
import paymentStatusEnum from "../../../enums/modules/paymentStatusEnum";

export default {
    name: "TableMenuComponent",
    components: {
        ItemComponent,
        LoadingComponent,
        Carousel,
        Slide,
        Pagination,
        Navigation,
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            category: {
                id: 0,
                name: this.$t('label.all') + ' ' + this.$t('label.items')
            },
            categoryProps: {
                search: {
                    paginate: 0,
                    order_column: "id",
                    order_type: "asc",
                    status: statusEnum.ACTIVE
                },
            },
            settings: {
                itemsToShow: 8,
                wrapAround: false,
                snapAlign: "start"
            },
            breakpoints: {
                // 200px and up
                200: {
                    itemsToShow: 1.1,
                    wrapAround: false,
                    snapAlign: 'start',
                },
                // 250px and up
                250: {
                    itemsToShow: 1.5,
                    wrapAround: false,
                    snapAlign: 'start',
                },
                // 300px and up
                300: {
                    itemsToShow: 2.3,
                    wrapAround: false,
                    snapAlign: 'start',
                },
                // 375px and up
                375: {
                    itemsToShow: 2.5,
                    wrapAround: false,
                    snapAlign: 'start',
                },
                540: {
                    itemsToShow: 3.5,
                    wrapAround: false,
                    snapAlign: 'start',
                },
                // 700px and up
                700: {
                    itemsToShow: 4.5,
                    wrapAround: false,
                    snapAlign: 'start',
                },
                // 1024 and up
                1024: {
                    snapAlign: 'start',
                    itemsToShow: 7,
                    wrapAround: false,
                },
                // 1180 and up
                1180: {
                    snapAlign: 'start',
                    itemsToShow: 8,
                    wrapAround: false,
                }
            },
            itemProps: {
                search: {
                    paginate: 0,
                    order_column: "id",
                    order_type: "asc",
                    item_category_id: "",
                    status: statusEnum.ACTIVE
                },
                property: {
                    design: itemDesignEnum.LIST,
                    type: null
                }
            },
            enums: {
                activityEnum: activityEnum,
                paymentStatusEnum: paymentStatusEnum,
                itemTypeEnum: itemTypeEnum,
                itemDesignEnum: itemDesignEnum,
                orderTypeEnumArray: {
                    [orderTypeEnum.DELIVERY]: this.$t("label.delivery"),
                    [orderTypeEnum.TAKEAWAY]: this.$t("label.takeaway"),
                    [orderTypeEnum.DINING_TABLE]: this.$t("label.dining_table")
                },
            }
        }
    },
    computed: {
        categories: function () {
            return this.$store.getters["tableItemCategory/lists"];
        },
        items: function () {
            return this.$store.getters["frontendItem/lists"];
        },
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        order: function () {
            return this.$store.getters['tableDiningOrder/show'];
        },
        paymentMethod: function () {
            return this.$store.getters['tableCart/paymentMethod'];
        },
    },
    mounted() {
        this.loading.isActive = true;
        this.itemList();
        this.$store.dispatch("tableItemCategory/lists", this.categoryProps.search).then(res => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });

        if (Object.keys(this.$route.query).length > 0) {
            this.loading.isActive = true;
            this.$store.dispatch('tableDiningOrder/show', this.$route.query.id).then(res => {
                const modalTarget = this.$refs.confirmOrder;
                modalTarget?.classList?.add("active");
                document.body.style.overflowY = "hidden";
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        }

    },
    methods: {
        closeModal: function () {
            const modalTarget = this.$refs.confirmOrder;
            modalTarget?.classList?.remove("active");
            document.body.style.overflowY = "auto";
            this.loading.isActive = false;
        },
        allCategory: function (category) {
            this.itemProps.search.item_category_id = "";
            this.category                          = {
                id: 0,
                name: category.name
            }
            this.itemList();
        },
        setCategory: function (id, slug = null) {
            this.itemProps.search.item_category_id = id;
            this.itemList();
            if (slug !== null) {
                this.loading.isActive = true;
                this.$store.dispatch("tableItemCategory/show", {
                    slug: slug
                }).then((res) => {
                    this.category         = res.data.data;
                    this.loading.isActive = false;
                }).catch((err) => {
                    this.loading.isActive = false;
                });
            }
        },
        itemList: function () {
            this.loading.isActive = true;
            this.$store.dispatch("frontendItem/lists", this.itemProps.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        itemTypeSet: function (e) {
            this.itemProps.property.type = e;
        },
        itemTypeReset: function () {
            this.itemProps.property.type = null;
        },
    }
}

</script>
