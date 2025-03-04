define([
    'uiComponent'
], function (Component) {
    'use strict';

    return Component.extend({
        initialize: function (config) {
            this._super(config);
            window.decoRatesConfig = config.decoRatesConfig;
        }
    });
});