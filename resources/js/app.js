import './bootstrap';

import Alpine from 'alpinejs';
import persist from '@alpinejs/persist'
import collapse from '@alpinejs/collapse'
import { post } from './http';

Alpine.plugin(persist)
Alpine.plugin(collapse)

window.Alpine = Alpine;

document.addEventListener("alpine:init", () => {
    Alpine.data("toast", () => ({
        visible: false,
        delay: 5000,
        percent: 0,
        interval: null,
        timeout: null,
        message: null,
        type: null,
        close() {
            this.visible = false;
            clearInterval(this.interval);
        },
        show(message, type) {
            this.visible = true;
            this.message = message;
            this.type = type;

            if (this.interval) {
                clearInterval(this.interval);
                this.interval = null;
            }
            if (this.timeout) {
                clearTimeout(this.timeout);
                this.timeout = null;
            }

            this.timeout = setTimeout(() => {
                this.visible = false;
                this.timeout = null;
            }, this.delay);
            const startDate = Date.now();
            const futureDate = Date.now() + this.delay;
            this.interval = setInterval(() => {
                const date = Date.now();
                this.percent = ((date - startDate) * 100) / (futureDate - startDate);
                if (this.percent >= 100) {
                    clearInterval(this.interval);
                    this.interval = null;
                }
            }, 30);
        },
    }));

    Alpine.data("productItem", (product) => {
        return {
            slug: product.slug,
            product,
            addToCart(quantity = 1) {
                post(this.product.addToCartUrl, {quantity})
                    .then((result) => {
                        this.$dispatch('cart-change', { count: result.count });
                        this.$dispatch('notify', {
                            message: 'The item was successfully added to cart',
                        });
                    })
                    .catch((response) => {
                        this.$dispatch('notify', {
                            message: response.message || 'Server Error. Please try again',
                            type: 'error'
                        });
                    })
            },
            removeItemFromCart() {
                post(this.product.removeUrl)
                    .then((result) => {
                        this.$dispatch('notify', {
                            message: 'The item was successfully removed from cart'
                        });
                        this.$dispatch('cart-change', { count: result.count });
                        this.cartItems = this.cartItems.filter((p) => p.id !== product.id);
                    })
            },
            changeQuantity() {
                post(this.product.updateQuantityUrl, { quantity: product.quantity })
                    .then((result) => {
                        this.$dispatch('cart-change', { count: result.count });
                        this.$dispatch('notify', {
                            message: 'The item quantity was successfully updated'
                        });
                    })
                    .catch((response) => {
                        this.$dispatch('notify', {
                            message: response.message || 'Server Error. Please try again',
                            type: 'error'
                        });
                    })
            },
        };
    });
});

Alpine.start();

