document.addEventListener('alpine:init', () => {
    Alpine.store('cart', {
        items: {},           // { [variantId]: { name, productName, sku, price, quantity, stock, discount, imageUrl, taxCategoryId } }
        taxRates: {},        // { [taxCategoryId]: rate }
        customerId: null,
        tableNumber: null,
        paymentMethod: 'cash',
        cashReceived: 0,

        init() {
            // Support persistence (optional but good for POS)
            const saved = localStorage.getItem('pos_cart');
            if (saved) {
                try {
                    const parsed = JSON.parse(saved);
                    this.items = parsed.items || {};
                    this.customerId = parsed.customerId || null;
                    this.tableNumber = parsed.tableNumber || null;
                    this.paymentMethod = parsed.paymentMethod || 'cash';
                    this.cashReceived = parsed.cashReceived || 0;
                } catch (e) {
                    console.error('Failed to load cart from localStorage', e);
                }
            }

            // Watch for changes and save to localStorage (Debounced to prevent UI freeze)
            Alpine.effect(() => {
                // Touch dependencies to register tracking
                const { items, customerId, tableNumber, paymentMethod, cashReceived } = this;
                
                clearTimeout(this._saveTimeout);
                this._saveTimeout = setTimeout(() => {
                    localStorage.setItem('pos_cart', JSON.stringify({
                        items, customerId, tableNumber, paymentMethod, cashReceived
                    }));
                }, 300);
            });

            // Listen for clear events from Livewire
            window.addEventListener('cart:clear', () => {
                this.clear();
            });
        },

        // ── Mutations ──
        add(variant) {
            const id = variant.id;
            if (this.items[id]) {
                if (this.items[id].quantity < this.items[id].stock) {
                    this.items[id].quantity++;
                }
            } else {
                this.items[id] = { ...variant, quantity: 1, discount: 0 };
            }
        },

        increment(variantId) {
            const item = this.items[variantId];
            if (item && item.quantity < item.stock) {
                item.quantity++;
            }
        },

        decrement(variantId) {
            const item = this.items[variantId];
            if (!item) return;
            if (item.quantity <= 1) {
                delete this.items[variantId];
            } else {
                item.quantity--;
            }
        },

        remove(variantId) {
            delete this.items[variantId];
        },

        clear() {
            this.items = {};
            this.customerId = null;
            this.tableNumber = null;
            this.paymentMethod = 'cash';
            this.cashReceived = 0;
        },

        // ── Computed (reactive getters) ──
        get count() {
            return Object.values(this.items).reduce((sum, i) => sum + i.quantity, 0);
        },

        get subtotal() {
            return Object.values(this.items).reduce(
                (sum, i) => sum + (i.price * i.quantity), 0
            );
        },

        get discountTotal() {
            return Object.values(this.items).reduce((sum, i) => sum + (i.discount || 0), 0);
        },

        get taxTotal() {
            return Object.values(this.items).reduce((sum, i) => {
                const rate = this.taxRates[i.taxCategoryId] || 0;
                const taxable = (i.price * i.quantity) - (i.discount || 0);
                return sum + (taxable * rate / 100);
            }, 0);
        },

        get grandTotal() {
            return this.subtotal - this.discountTotal + this.taxTotal;
        },

        get changeAmount() {
            return Math.max(0, this.cashReceived - this.grandTotal);
        },

        get isEmpty() {
            return Object.keys(this.items).length === 0;
        },

        get itemsArray() {
            return Object.entries(this.items).map(([id, item]) => ({
                id,
                ...item
            }));
        },

        // ── Formatter ──
        formatMoney(amount) {
            return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(amount);
        },

        // ── Serializer untuk Livewire ──
        toPayload() {
            const payload = {};
            for (const [id, item] of Object.entries(this.items)) {
                payload[id] = {
                    quantity: item.quantity,
                    discount: item.discount || 0,
                    discount_id: item.discount_id || null,
                    modifiers: item.modifiers || [],
                };
            }
            return payload;
        },

        // Load an open order (from Livewire)
        loadOrder(orderData) {
            this.clear();
            this.items = orderData.items || {};
            this.tableNumber = orderData.tableNumber || null;
            this.customerId = orderData.customerId || null;
            // Retain payment state or reset it? Usually reset for loaded order until they pay.
            this.paymentMethod = 'cash';
            this.cashReceived = 0;
        }
    });
});
