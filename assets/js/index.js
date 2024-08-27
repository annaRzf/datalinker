let PLExport, PLImport;
import(`./dl-export.js?ver=${dl_object.version}`)
  .then(module => {
    PLExport = module.default;
});
import(`./dl-import.js?ver=${dl_object.version}`)
  .then(module => {
    PLImport = module.default;
});

jQuery(document).ready(function($) {
    // initialize importer and exporter
    PLExport.init();
    PLImport.init();
});