import _ from "lodash";


export const tableCart = {
    namespaced: true,
    state: {
        lists: [],
        subtotal: 0,
        coupon: {},
        table: {},
        paymentMethod: null,
    },
    getters: {
        lists: function (state) {
            return state.lists;
        },
        subtotal: function (state) {
            return state.subtotal;
        },
        coupon: function (state) {
            return state.coupon;
        },
        table: function (state) {
            return state.table;
        },
        paymentMethod: function (state) {
            return state.paymentMethod;
        }
    },
    actions: {
        lists: function (context, payload) {
            context.commit("lists", payload);
            context.commit("subtotal");
        },
        quantity: function (context, payload) {
            context.commit("quantity", payload);
            context.commit("subtotal");
        },
        coupon: function (context, payload) {
            context.commit("coupon", payload);
        },
        destroyCoupon: function (context) {
            context.commit('coupon', {});
        },
        initTable: function (context, payload) {
            context.commit('tableInit', payload);
        },
        paymentMethod: function (context, payload) {
            return context.commit('paymentMethod', payload);
        },
        resetCart: function (context) {
            context.commit('resetCart');
        },
        resetPaymentMethod: function (context) {
            context.commit('resetPaymentMethod');
        },
    },
    mutations: {
        lists: function (state, payload) {
            if (payload.length > 0) {
                let isNew = false;
                let newChecker = [];
                let variationAndExtraChecker = [];
                _.forEach(payload, (pay) => {
                    if (state.lists.length === 0) {
                        isNew = true;
                    } else {
                        isNew = true;
                        _.forEach(state.lists, (list, listKey) => {
                            if (list.item_id === pay.item_id) {

                                if (state.lists[listKey].item_variations.variations !== "undefined") {
                                    if (Object.keys(state.lists[listKey].item_variations.variations).length !== 0) {
                                        _.forEach(state.lists[listKey].item_variations.variations, (variationId, variationKey) => {
                                            if (pay.item_variations.variations[variationKey] !== "undefined" && pay.item_variations.variations[variationKey] === variationId) {
                                                variationAndExtraChecker.push(true);
                                            } else {
                                                variationAndExtraChecker.push(false);
                                            }
                                        });
                                    }
                                }

                                if (pay.item_extras.extras.length !== 0 && state.lists[listKey].item_extras.extras.length !== 0) {
                                    _.forEach(pay.item_extras.extras, (payExtra) => {
                                        if (state.lists[listKey].item_extras.extras.includes(payExtra) && state.lists[listKey].item_extras.extras.length === pay.item_extras.extras.length) {
                                            variationAndExtraChecker.push(true);
                                        } else {
                                            variationAndExtraChecker.push(false);
                                        }
                                    });
                                } else {
                                    if (pay.item_extras.extras.length === state.lists[listKey].item_extras.extras.length) {
                                        variationAndExtraChecker.push(true);
                                    } else {
                                        variationAndExtraChecker.push(false);
                                    }
                                }

                                if (variationAndExtraChecker.includes(false)) {
                                    newChecker.push(false);
                                } else {
                                    newChecker.push(true);
                                    state.lists[listKey].quantity += pay.quantity;
                                }
                                variationAndExtraChecker = [];
                            } else {
                                newChecker.push(false);
                            }
                        });

                        _.forEach(newChecker, (check) => {
                            if (check) {
                                isNew = false;
                            }
                        });
                        newChecker = [];
                    }

                    if (isNew) {
                        state.lists.push({
                            discount: pay.discount,
                            image: pay.image,
                            instruction: pay.instruction,
                            item_extra_total: pay.item_extra_total,
                            item_extras: pay.item_extras,
                            item_id: pay.item_id,
                            item_variation_total: pay.item_variation_total,
                            item_variations: pay.item_variations,
                            name: pay.name,
                            currency_price: pay.currency_price,
                            convert_price: pay.convert_price,
                            quantity: pay.quantity
                        });
                        isNew = false;
                    }
                });
            }
        },
        subtotal: function (state) {
            if (state.lists.length > 0) {
                let subtotal = 0;
                _.forEach(state.lists, (list, listKey) => {
                    state.lists[listKey].total = ((list.convert_price + list.item_variation_total + list.item_extra_total) * list.quantity);
                    subtotal += state.lists[listKey].total;
                });
                state.subtotal = subtotal;
            } else {
                state.subtotal = 0;
            }
        },
        quantity: function (state, payload) {
            if (payload.status === "increment") {
                state.lists[payload.id].quantity++;
            } else if (payload.status === "decrement") {
                if (state.lists[payload.id].quantity === 1) {
                    state.lists.splice(payload.id, 1);
                } else {
                    state.lists[payload.id].quantity--;
                }
            } else {
                state.lists[payload.id].quantity = payload.status;
            }
        },
        tableInit: function (state, payload) {
            state.table = payload;
        },
        coupon: function (state, payload) {
            state.coupon = payload;
        },
        paymentMethod: function(state, payload) {
            state.paymentMethod = payload;
        },
        resetCart: function (state) {
            state.lists = [];
            state.subtotal = 0;
            state.coupon = {};
        },
        resetPaymentMethod: function(state) {
            state.paymentMethod = null;
        },
    },
};
