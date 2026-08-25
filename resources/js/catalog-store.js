document.addEventListener('alpine:init', () => {
    Alpine.store('catalog', {
        all: [],          // full product list
        searchTerm: '',
        categoryId: null,
        page: 0,
        perPage: 24,

        init(products) {
            this.all = products || [];
            this.page = 0;
        },

        setCategory(id) {
            this.categoryId = id;
            this.page = 0; // reset to first page
        },

        get filtered() {
            let result = this.all;

            // Filter by Category
            if (this.categoryId) {
                result = result.filter(p => p.categoryId === this.categoryId);
            }

            // Filter by Search Term
            const term = this.searchTerm ? this.searchTerm.trim().toLowerCase() : '';
            if (term) {
                result = result.filter(p =>
                    p.name.toLowerCase().includes(term) ||
                    (p.sku && p.sku.toLowerCase().includes(term)) ||
                    (p.barcode && p.barcode.toLowerCase() === term)
                );
            }

            return result;
        },

        findByBarcode(barcode) {
            const exact = barcode.trim().toLowerCase();
            if (!exact) return null;
            // Prioritize strict barcode match, fallback to exact SKU match
            return this.all.find(p =>
                (p.barcode && p.barcode.toLowerCase() === exact) ||
                (p.sku && p.sku.toLowerCase() === exact)
            ) || null;
        },

        get paginated() {
            const start = this.page * this.perPage;
            return this.filtered.slice(start, start + this.perPage);
        },

        get totalPages() {
            return Math.max(1, Math.ceil(this.filtered.length / this.perPage));
        },

        nextPage() {
            if (this.page < this.totalPages - 1) this.page++;
        },

        prevPage() {
            if (this.page > 0) this.page--;
        },

        setPage(p) {
            if (p >= 0 && p < this.totalPages) this.page = p;
        },

        get hasProducts() {
            return this.all.length > 0;
        }
    });
});
