let $ = jQuery;
const PLExport = {
    formSection: $('#pl-export-form'),
    init: () => {
        PLExport.formSection.on('submit', function(e){
            e.preventDefault();
            const postType = PLExport.formSection.find('select[name="post_type"]').val();
            PLExport.fetchPosts(postType);
        });
    },
    fetchPosts: (postType) => {
        // send data through AJAX
        $.ajax({
            url: pl_export_object.ajax_url,
            data: {
                action: 'pl_exporter_fetch_data',
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
    }
}
export default PLExport;