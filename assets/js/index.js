let DLGeneral, DLExport, DLImport;
import(`./dl-export.js?ver=${dl_object.version}`)
  .then(module => {
    DLExport = module.default;
});
import(`./dl-general.js?ver=${dl_object.version}`)
  .then(module => {
    DLGeneral = module.default;
});
import(`./dl-import.js?ver=${dl_object.version}`)
  .then(module => {
    DLImport = module.default;
});

jQuery(document).ready(function($) {
    DLGeneral.init();
    DLExport.init();
    DLImport.init();
});