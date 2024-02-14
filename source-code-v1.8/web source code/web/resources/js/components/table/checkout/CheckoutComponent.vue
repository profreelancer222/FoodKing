<template>
    <LoadingComponent :props="loading"/>
    <section class="pt-8 pb-16">
        <div class="container max-w-[965px]">
            <router-link :to="{ name: 'table.menu.table', params : {slug : this.$route.params.slug}}"
                         class="text-xs font-medium inline-flex mb-3 items-center gap-2 text-primary">
                <i class="lab lab-undo lab-font-size-16"></i>
                <span>{{ $t('label.back_to_home') }}</span>
            </router-link>

            <div class="row">
                <div class="col-12 md:col-7">
                    <div class="mb-6 rounded-2xl shadow-xs bg-white">
                        <h3 class="capitalize font-medium p-4 border-b border-gray-100">{{ $t('label.table') }}</h3>
                        <p class="capitalize p-4 text-heading">{{ $t('label.inside') }} - {{ table.name }}</p>
                    </div>


                    <div class="mb-6 rounded-2xl shadow-xs bg-white">
                        <h3 class="capitalize font-medium p-4 border-b border-gray-100">{{ $t('label.payment') }}</h3>
                        <ul class="p-4 flex flex-col gap-5">
                            <li class="flex items-center gap-1.5">
                                <div class="custom-radio">
                                    <input type="radio" id="cash" v-model="paymentMethod" value="cashCard"
                                           class="custom-radio-field">
                                    <span class="custom-radio-span border-gray-400"></span>
                                </div>
                                <label for="cash" class="db-field-label text-heading">{{ $t('label.cash_card') }}</label>
                            </li>
                            <li class="flex items-center gap-1.5">
                                <div class="custom-radio">
                                    <input type="radio" id="digital" v-model="paymentMethod" value="digitalPayment"
                                           class="custom-radio-field">
                                    <span class="custom-radio-span border-gray-400"></span>
                                </div>
                                <label for="digital" class="db-field-label text-heading">{{ $t('label.digital_payment') }}</label>
                            </li>
                        </ul>
                    </div>

                    <button type="button"
                            class="hidden md:block w-full rounded-3xl capitalize font-medium leading-6 py-3 text-white bg-primary"
                            @click="orderSubmit">
                        {{ $t('button.place_order') }}
                    </button>
                </div>

                <div class="col-12 md:col-5">
                    <div class="rounded-2xl shadow-xs bg-white">
                        <div class="p-4 border-b">
                            <h3 class="capitalize font-medium mb-3 text-center">
                                {{ $t('label.cart_summary') }}
                            </h3>
                            <div class="pl-3">
                                <div v-for="cart in carts"
                                     class="mb-3 pb-3 border-b last:mb-0 last:pb-0 last:border-b-0 border-gray-2">
                                    <div class="flex items-center gap-3 relative">
                                        <h3 class="absolute top-5 -left-3 text-sm w-[26px] h-[26px] leading-[26px] text-center rounded-full text-white bg-heading">
                                            {{ cart.quantity }}</h3>
                                        <img :src="cart.image" alt="thumbnail"
                                             class="w-16 h-16 rounded-lg flex-shrink-0">
                                        <div class="w-full">
                                            <span
                                                class="text-sm font-medium capitalize transition text-heading">
                                                {{ cart.name }}
                                            </span>
                                            <p v-if="Object.keys(cart.item_variations.variations).length !== 0"
                                               class="capitalize text-xs mb-1.5">
                                                <span v-for="(variation, variationName) in cart.item_variations.names">
                                                    {{ variationName }}: {{ variation }}, &nbsp;
                                                </span>
                                            </p>
                                            <h4 class="text-xs font-semibold">
                                                {{
                                                    currencyFormat(cart.total, setting.site_digit_after_decimal_point,
                                                        setting.site_default_currency_symbol, setting.site_currency_position)
                                                }}
                                            </h4>
                                        </div>
                                    </div>
                                    <ul v-if="cart.item_extras.extras.length > 0 || cart.instruction !== ''"
                                        class="flex flex-col gap-1.5 mt-2">
                                        <li v-if="cart.item_extras.extras.length > 0" class="flex gap-1">
                                            <h3 class="capitalize text-xs w-fit whitespace-nowrap">
                                                {{ $t('label.extras') }}:
                                            </h3>
                                            <p class="text-xs">
                                                <span v-for="extra in cart.item_extras.names">
                                                    {{ extra }}, &nbsp;
                                                </span>
                                            </p>
                                        </li>
                                        <li v-if="cart.instruction !== ''" class="flex gap-1">
                                            <h3 class="capitalize text-xs w-fit whitespace-nowrap">
                                                {{ $t('label.instruction') }}:
                                            </h3>
                                            <p class="text-xs">{{ cart.instruction }}</p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="rounded-xl mb-6 border border-[#EFF0F6]">
                                <ul class="flex flex-col gap-2 p-3 border-b border-dashed border-[#EFF0F6]">
                                    <li class="flex items-center justify-between text-heading">
                                        <span class="text-sm leading-6 capitalize">
                                            {{ $t('label.subtotal') }}
                                        </span>
                                        <span class="text-sm leading-6 capitalize">
                                            {{
                                                currencyFormat(subtotal, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)
                                            }}
                                        </span>
                                    </li>
                                </ul>
                                <div class="flex items-center justify-between p-3">
                                    <h4 class="text-sm leading-6 font-semibold capitalize">
                                        {{ $t('label.total') }}
                                    </h4>
                                    <h5 class="text-sm leading-6 font-semibold capitalize">
                                        {{
                                            currencyFormat(subtotal, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)
                                        }}
                                    </h5>
                                </div>
                            </div>
                            <button type="button"
                                    class="block md:hidden w-full rounded-3xl capitalize font-medium leading-6 py-3 text-white bg-primary"
                                    @click="orderSubmit">
                                {{ $t('button.place_order') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>


<script>

import LoadingComponent from "../../table/components/LoadingComponent.vue";
import appService from "../../../services/appService";
import sourceEnum from "../../../enums/modules/sourceEnum";
import _ from "lodash";
import OrderTypeEnum from "../../../enums/modules/orderTypeEnum";
import IsAdvanceOrderEnum from "../../../enums/modules/isAdvanceOrderEnum";
import router from "../../../router";
import alertService from "../../../services/alertService";

export default {
    name: "CheckoutComponent",
    components: {LoadingComponent},
    data() {
        return {
            loading: {
                isActive: false,
            },
            placeOrderShow: false,
            paymentMethod: null,
            checkoutProps: {
                form: {
                    dining_table_id: null,
                    customer_id: 2,
                    branch_id: null,
                    subtotal: 0,
                    discount: 0,
                    delivery_charge: 0,
                    delivery_time: null,
                    total: 0,
                    order_type: OrderTypeEnum.DINING_TABLE,
                    is_advance_order: IsAdvanceOrderEnum.NO,
                    source: sourceEnum.WEB,
                    address_id: null,
                    coupon_id: null,
                    items: []
                }
            },
        }
    },
    mounted() {
        if (this.$store.getters['tableCart/lists'].length === 0) {
            this.$router.push({name: 'table.menu.table', params: {slug: this.$route.params.slug}});
        }
    },
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        carts: function () {
            return this.$store.getters['tableCart/lists'];
        },
        subtotal: function () {
            return this.$store.getters['tableCart/subtotal'];
        },
        table: function () {
            return this.$store.getters['tableCart/table'];
        }
    },
    methods: {
        currencyFormat: function (amount, decimal, currency, position) {
            return appService.currencyFormat(amount, decimal, currency, position);
        },
        orderSubmit: function () {
            this.loading.isActive = true;
            this.checkoutProps.form.dining_table_id = this.table.id;
            this.checkoutProps.form.branch_id = this.table.branch_id;
            this.checkoutProps.form.subtotal  = this.subtotal;
            this.checkoutProps.form.total     = parseFloat(this.subtotal).toFixed(this.setting.site_digit_after_decimal_point);
            this.checkoutProps.form.items     = [];
            _.forEach(this.carts, (item, index) => {
                let item_variations = [];
                if (Object.keys(item.item_variations.variations).length > 0) {
                    _.forEach(item.item_variations.variations, (value, index) => {
                        item_variations.push({
                            "id": value,
                            "item_id": item.item_id,
                            "item_attribute_id": index,
                        });
                    });
                }

                if (Object.keys(item.item_variations.names).length > 0) {
                    let i = 0;
                    _.forEach(item.item_variations.names, (value, index) => {
                        item_variations[i].variation_name = index;
                        item_variations[i].name           = value;
                        i++;
                    });
                }

                let item_extras = [];
                if (item.item_extras.extras.length) {
                    _.forEach(item.item_extras.extras, (value) => {
                        item_extras.push({
                            id: value,
                            item_id: item.item_id,
                        });
                    });
                }

                if (item.item_extras.names.length) {
                    let i = 0;
                    _.forEach(item.item_extras.names, (value) => {
                        item_extras[i].name = value;
                        i++;
                    });
                }

                this.checkoutProps.form.items.push({
                    item_id: item.item_id,
                    item_price: item.convert_price,
                    branch_id: this.checkoutProps.form.branch_id,
                    instruction: item.instruction,
                    quantity: item.quantity,
                    discount: item.discount,
                    total_price: item.total,
                    item_variation_total: item.item_variation_total,
                    item_extra_total: item.item_extra_total,
                    item_variations: item_variations,
                    item_extras: item_extras
                });
            });
            this.checkoutProps.form.items = JSON.stringify(this.checkoutProps.form.items);

            this.$store.dispatch('tableDiningOrder/save', this.checkoutProps.form).then(orderResponse => {
                this.checkoutProps.form.subtotal = 0;
                this.checkoutProps.form.discount = 0;
                this.checkoutProps.form.delivery_charge = 0;
                this.checkoutProps.form.delivery_time = null;
                this.checkoutProps.form.total = 0;
                this.checkoutProps.form.items = [];

                this.$store.dispatch('tableCart/resetCart').then(res => {
                    this.loading.isActive = false;
                    this.$store.dispatch('tableCart/paymentMethod', this.paymentMethod).then().catch();
                    router.push({name: "table.menu.table", params: {slug : this.table.slug}, query: {id: orderResponse.data.data.id}});
                }).catch();
            }).catch((err) => {
                this.loading.isActive = false;
                if (typeof err.response.data.errors === 'object') {
                    _.forEach(err.response.data.errors, (error) => {
                        alertService.error(error[0]);
                    });
                }
            })
        }
    }

}
</script>