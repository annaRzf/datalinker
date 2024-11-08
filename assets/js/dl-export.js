let $ = jQuery;
const PLExport = {
    formSection: $('#pl-export-form'),
    init: () => {
        PLExport.formSection.on('submit', function(e){
            e.preventDefault();
            const postType = PLExport.formSection.find('select[name="post_type"]').val();
            PLExport.fetchPosts(postType);
        });
        // bind element
        PLExport.bindRuleBtn();
    },
    fetchPosts: (postType) => {
        // send data through AJAX
        $.ajax({
            url: pl_export_object.ajax_url,
            data: {
                action: 'dl_exporter_fetch_data',
                post_type: postType
            },
            type: 'post',
            success: (response) => {
                let csvData = response.data.csv_data;
                let blob = new Blob([csvData], { type: 'text/csv;charset=utf-8;' });
                let url = URL.createObjectURL(blob);
                let downloadLink = $('<a></a>')
                    .attr('href', url)
                    .attr('download', response.data.file_name) // set the file name
                    .appendTo(PLExport.formSection);
                downloadLink[0].click();
                downloadLink.remove();
            }
        })
    },
    bindRuleBtn: () => {
        // add rule row
        $(document).on('click','.add-rule-row',function(e){
            e.preventDefault();
            const ruleRow = $(this).parents('.rule-row').clone();
            $(this).parents('.rule-rows').append(ruleRow);
            // if the number of rule row is more than one we add the remove button
            if($(this).parents('.rule-rows').find('.rule-row').length > 1){
                ruleRow.find('.remove-rule-row').show();
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
                ruleRow.find('.remove-rule-row').show();
            }
        });
    }
}
export default PLExport;