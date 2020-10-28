/**
 * @api
 */
define([
    'underscore',
    'Magento_Ui/js/form/element/ui-select'
], function (_, Component) {
    return Component.extend({
        defaults: {
            labelsDecoration: true
        },

        initialize: function() {
            this._super();
        },

        toggleOptionSelected: function (data) {
            var self = this;

            if (data.optgroup) {
                _.each(data.optgroup, function (element) {
                    if (!self.isSelected(data.value)) {
                        self.value(_.without(self.value(), element.value));
                    }

                    self.toggleOptionSelected(element);
                });
            }

            return this._super(data);
        }
    });
});
