let $ = jQuery;
const PLImport = {
    formSection: $('#pl-import-form'),
    init: () => {
        PLImport.formSection.on('submit', function(e){
            e.preventDefault();
            let formData = new FormData(this);
            formData.append('action', 'dl_importer_insert_data');
            PLImport.uploadFile(formData);
        });
    },
    uploadFile: (formData) => {
        $.ajax({
            url: pl_import_object.ajax_url,
            data: formData,
            type: 'post',
            contentType: false,
            processData: false,
            xhr: function() {
                let xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        let percentComplete = evt.loaded / evt.total;
                        console.log(percentComplete);
                        PLImport.find('upload-progress').value = percentComplete;
                    }
                }, false);
                return xhr;
            },
            success: (response) => {
                console.log(response);
                console.log("Inserted rows: " + response.data.inserted_rows);
            }
        });
    }
}
export default PLImport;