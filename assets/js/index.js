let PLExport, PLImport;
import(`./pl-export.js?ver=${pl_object.version}`)
  .then(module => {
    PLExport = module.default;
});
import(`./pl-import.js?ver=${pl_object.version}`)
  .then(module => {
    PLImport = module.default;
});

jQuery(document).ready(function($) {
    // initialize importer and exporter
    PLExport.init();
    PLImport.init();
});