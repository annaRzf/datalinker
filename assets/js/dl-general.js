let $
if (typeof $ === 'undefined') {
    $ = jQuery;
}
const DLGeneral = {
    init: () => {
        // bind element
        DLGeneral.initDropdown();
    },
    initDropdown: () => {
        $('.dl-dropdown-select').select2({
            placeholder: 'Search...',
            allowClear: true,
            templateResult: DLGeneral.formatOption,
            templateSelection: DLGeneral.formatOption
        });
    },
    formatOption: (option) => {
        if (!option.id) {
            return option.text;
        }
        const iconClass = $(option.element).data('icon');
        if (iconClass) {
            return $('<span><i class="' + iconClass + '" style="margin-right: 8px;"></i> ' + option.text + '</span>');
        }
        return option.text;
    }
}
export default DLGeneral;
