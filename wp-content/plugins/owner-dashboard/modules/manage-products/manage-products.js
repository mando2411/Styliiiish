window.ManageProductsModule = {

    init: function() {
        this.cacheDom();
        this.bindEvents();
        this.loadPage(1);
    },

    cacheDom: function() {
        this.container = $('#styliiiish-manage-products-content');
    },

    bindEvents: function() {
        const self = this;

        // Search
        $(document).on('keyup', '#styliiiish-search', function(){
            clearTimeout(self.searchTimer);
            self.searchTimer = setTimeout(function(){
                self.filters.search = $('#styliiiish-search').val();
                self.loadPage(1);
            }, 300);
        });

        // Pagination
        $(document).on('click', '.styliiiish-page-link', function(e){
            e.preventDefault();
            let page = $(this).data('page');
            self.loadPage(page);
        });

    },

    filters: {
        search: '',
        cat: '',
        status: '',
        page: 1
    },

    loadPage: function(page) {
        const self = this;

        self.filters.page = page || 1;
        self.showSkeleton();

        $.post(ajax_object.ajax_url, {
            action: 'styliiiish_manage_products_list',
            ...self.filters
        }, function(response){
            self.container.html(response);
        });
    },

    showSkeleton: function() {
        this.container.html('<div class="skeleton">Loading...</div>');
    }
};
