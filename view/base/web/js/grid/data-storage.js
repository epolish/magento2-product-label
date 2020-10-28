define([
    'jquery',
    'underscore',
    'mageUtils',
    'Magento_Ui/js/grid/data-storage'
], function ($, _, utils, Component) {
    return Component.extend({
        initialize: function() {
            this._super();
        },

        requestData: function (params) {
            if (params.filters.store_ids) {
                params.filters.store_ids = _.reject(params.filters.store_ids, _.isArray)
            }

            return this._super(params);
        },
    });
});
