let $
if (typeof $ === 'undefined') {
    $ = jQuery
}
let DLGeneral
import(`./dl-general.js?ver=${dl_object.version}`)
  .then(module => {
    DLGeneral = module.default;
});
const DLExport = {
    nRule : 1,
    init: () => {
        // bind element
        DLExport.bindRuleBtn()
        DLExport.bindTaxonomy()
        DLExport.bindPostTypeSelect()
        DLExport.bindFieldSelect()
    },
    bindRuleBtn: () => {
        // add rule row
        $(document).on('click','.add-rule-row',function(e){
            e.preventDefault()
            const ruleRow = $(this).parents('.rule-row').clone()
            $(this).parents('.rule-rows').append(ruleRow)
            // if the number of rule row is more than one we add the remove button
            if($(this).parents('.rule-rows').find('.rule-row').length > 1){
                ruleRow.find('.remove-rule-row').removeClass('invisible')
            }
            // remove existing select2 element
            DLExport.updateClonedRuleRow(ruleRow)
        })
        // remove rule row
        $(document).on('click','.remove-rule-row',function(e){
            e.preventDefault()
            // if the number of rule row is only one and the rule group is bigger than one we remove the or statement
            const orStatement = $(this).parents('.rule-group').find('.or-statement')
            if($(this).parents('.rule-rows').find('.rule-row').length == 1 && $('.rule-group').length > 1 ){
                orStatement.remove()
            }
            $(this).parents('.rule-row').remove()
        })
        // add rule group
        $(document).on('click','.add-rule-group',function(e){
            e.preventDefault()
            // create a rule group and rows element
            const ruleGroup = $('<div>').addClass('rule-group')
            const ruleRows = $('<div>').addClass('rule-rows')
            const ruleRow = $('.rule-row').first().clone()
            const orStatement = $('.or-statement').first().clone()
            // append elements
            ruleGroup.append(ruleRows)
            ruleRows.append(ruleRow)
            ruleGroup.append(orStatement)
            $('.rule-group-container').append(ruleGroup)
            // if the number of rule group is more than one we add the remove button
            if($('.rule-group').length > 1){
                ruleRow.find('.remove-rule-row').removeClass('invisible')
            }
            // remove existing select2 element
            DLExport.updateClonedRuleRow(ruleRow)
        })
    },
    updateClonedRuleRow: (ruleRow) => {
        // remove existing select2 element
        ruleRow.find('.dl-dropdown').each(function(){
            // update select name and id
            $(this).find('.dl-dropdown-select').attr('name',$(this).find('.dl-dropdown-select').attr('name') +'_'+ DLExport.nRule)
            DLExport.nRule++
            // remove select2
            $(this).find('.select2').remove()
        })
        DLGeneral.initDropdown()
    },
    bindTaxonomy: () => {
        $(document).on('change','.dl-dropdown-select[name="post_type"]',function() {
            const postType = $(this).val()
            const taxonomySelectContainer = $('.dl-dropdown-select[name="taxonomy"]').parents('.form-group')
            if( postType == 'taxonomies')
                taxonomySelectContainer.removeClass('hidden')
            else
                taxonomySelectContainer.addClass('hidden')
        })
    },
    bindPostTypeSelect: () => {
        $(document).on('change','.dl-dropdown-select[name="post_type"]',function() {
            const postType = $(this).val()
            if( postType !== 'taxonomies'){
                DLExport.fetchPosts(postType)
                DLExport.getFilters(postType)
            }
        })
        $(document).on('change','.dl-dropdown-select[name="taxonomy"]',function() {
            const postType = 'taxonomies'
            const taxonomy = $(this).val()
            DLExport.fetchPosts(postType,taxonomy)
            DLExport.getFilters(postType,taxonomy)
        })
    },
    bindFieldSelect: () => {
        $(document).on('change','.dl-dropdown-select[name="export_filters"]',function() {
            const field = $(this).val()
            DLExport.getFilterRules(field)
        })
    },
    loadForm : ( load = true) => {
        const form = $('.form-step')
        if( load ) 
            form.addClass('loading')
        else
            form.removeClass('loading')
    },
    fetchPosts: (postType, taxonomy = null) => {
        $.ajax({
            url: dl_object.ajaxUrl,
            method: 'POST',
            data: {
                action: 'dl_get_posts',
                security: dl_object.ajaxNonce,
                post_type: postType,
                taxonomy: taxonomy
            },
            beforeSend: function() {
                DLExport.loadForm()
            },
            success: function(response) {
                DLExport.loadForm(false)
                console.log(response)
            }
        })
    },
    getFilters: ( postType, taxonomy = null) => {
        const filterSection = $('.form-step .form-group#export-filters')
        $.ajax({
            url: dl_object.ajaxUrl,
            method: 'POST',
            data: {
                action: 'dl_get_filters',
                security: dl_object.ajaxNonce,
                post_type: postType,
                taxonomy: taxonomy
            },
            beforeSend: function() {
                DLExport.loadForm()
            },
            success: function(response) {
                DLExport.loadForm(false)
                filterSection.html(response.data.filters)
                DLGeneral.initDropdown()
                filterSection.removeClass('hidden')
            }
        })
    },
    getFilterRules: (field) => {
        const filterSection = $('.form-step .form-group#export-filters')
        const fieldSection = filterSection.find('.dl-dropdown:has(.dl-dropdown-select[name="export_filters"])')
        $.ajax({
            url: dl_object.ajaxUrl,
            method: 'POST',
            data: {
                action: 'dl_get_rules',
                security: dl_object.ajaxNonce,
                field: field
            },
            beforeSend: function() {
                DLExport.loadForm()
            },
            success: function(response) {
                DLExport.loadForm(false)
                // insert the rules after the field
                fieldSection.after(response.data.rules)
                DLGeneral.initDropdown()
            }
        })
    }
}
export default DLExport