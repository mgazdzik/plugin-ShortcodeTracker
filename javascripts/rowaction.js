function DataTable_RowActions_Shortener(dataTable) {
    this.dataTable = dataTable;
}

DataTable_RowActions_Shortener.prototype = new DataTable_RowAction;

DataTable_RowActions_Shortener.prototype.trigger = function (tr, e, subTableLabel) {
    var link = $(tr).data('urlLabel');
    var useExistingCodeIfAvailable = true;
    getShortcodeAndShowPopup(link, useExistingCodeIfAvailable);
}


DataTable_RowActions_Registry.register({

    name: 'Shorten URL',

    dataTableIcon: 'plugins/ShortcodeTracker/images/shorten_icon.png',
    dataTableIconHover: 'plugins/ShortcodeTracker/images/shorten_icon_hover.png',

    order: 70,

    dataTableIconTooltip: [
        _pk_translate('ShortcodeTracker_rowaction_tooltip_title'),
        _pk_translate('ShortcodeTracker_rowaction_tooltip')
    ],

    createInstance: function (dataTable) {
        return new DataTable_RowActions_Shortener(dataTable);
    },

    isAvailableOnReport: function (dataTableParams) {
        return DataTable_RowActions_Transitions.isPageUrlReport(dataTableParams.module, dataTableParams.action);
    },

    isAvailableOnRow: function (dataTableParams, tr) {
        return $(tr).data('urlLabel') !== '';
    }

});
