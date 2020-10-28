define([
    'Magento_Ui/js/grid/provider'
], function (Component) {
    return Component.extend({
        defaults: {
            storageConfig: {
                component: 'Polish_ProductLabel/js/grid/data-storage',
            }
        },

        initialize: function() {
            this._super();
        }
    });
});
