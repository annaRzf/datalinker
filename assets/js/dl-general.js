let $
if (typeof $ === 'undefined') {
    $ = jQuery
}
const DLGeneral = {
    init: () => {
        // bind element
        DLGeneral.initDropdown()
        DLGeneral.binTaxonomy()
    },
    initDropdown: () => {
        $('.dl-dropdown-select').select2({
            placeholder: 'Search...',
            allowClear: true,
            templateResult: DLGeneral.formatOption,
            templateSelection: DLGeneral.formatOption
        })
    },
    formatOption: (option) => {
        if (!option.id) {
            return option.text
        }
        const iconClass = $(option.element).data('icon')
        if (iconClass) {
            return $('<span><i class="' + iconClass + '" style="margin-right: 8px"></i> ' + option.text + '</span>')
        }
        return option.text
    },
    binTaxonomy: () => {
        $('.dl-dropdown-select[name="post_type"]').change(function() {
            const postType = $(this).val()
            console.log(postType)
            const taxonomySelectContainer = $('.dl-dropdown-select[name="taxonomy"]').parents('.form-group')
            console.log(taxonomySelectContainer)
            if( postType == 'taxonomies')
                taxonomySelectContainer.removeClass('hidden')
            else
                taxonomySelectContainer.addClass('hidden')
        })
    },
}
export default DLGeneral
