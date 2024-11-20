let $
if (typeof $ === 'undefined') {
    $ = jQuery;
}
const PLExport = {
    formSection: $('#pl-export-form'),
    init: () => {
        // bind element
        PLExport.bindRuleBtn();
    },
    bindRuleBtn: () => {
        // add rule row
        $(document).on('click','.add-rule-row',function(e){
            e.preventDefault();
            const ruleRow = $(this).parents('.rule-row').clone();
            $(this).parents('.rule-rows').append(ruleRow);
            // if the number of rule row is more than one we add the remove button
            if($(this).parents('.rule-rows').find('.rule-row').length > 1){
                ruleRow.find('.remove-rule-row').removeClass('invisible');
            }

        });
        // remove rule row
        $(document).on('click','.remove-rule-row',function(e){
            e.preventDefault();
            // if the number of rule row is only one and the rule group is bigger than one we remove the or statement
            const orStatement = $(this).parents('.rule-group').find('.or-statement');
            if($(this).parents('.rule-rows').find('.rule-row').length == 1 && $('.rule-group').length > 1 ){
                orStatement.remove();
            }
            $(this).parents('.rule-row').remove();

        });
        // add rule group
        $(document).on('click','.add-rule-group',function(e){
            e.preventDefault();
            // create a rule group and rows element
            const ruleGroup = $('<div>').addClass('rule-group');
            const ruleRows = $('<div>').addClass('rule-rows');
            const ruleRow = $('.rule-row').first().clone();
            const orStatement = $('.or-statement').first().clone();
            // append elements
            ruleGroup.append(ruleRows);
            ruleRows.append(ruleRow);
            ruleGroup.append(orStatement);
            $('.rule-group-container').append(ruleGroup);
            // if the number of rule group is more than one we add the remove button
            if($('.rule-group').length > 1){
                ruleRow.find('.remove-rule-row').removeClass('invisible');
            }
        });
    }
}
export default PLExport;