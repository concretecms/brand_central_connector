(function ($) {
    $(function () {
        // custom import handler
        if (typeof ConcreteEvent !== 'undefined') {
            ConcreteEvent.subscribe('ExternalFileProvider.SelectFile', function (e, data) {
                // only catch events for the brand_central connector
                if (data.externalFileProviderTypeHandle === "brand_central") {
                    jQuery.fn.dialog.open({
                        title: data.externalFileProviderName,
                        href: CCM_DISPATCHER_FILENAME + "/ccm/brand_central_connector/select_asset_file/" + data.externalFileProviderId + "/" + data.selectedFile.fID + "?externalFileProviderUploadDirectoryId=" + encodeURI(data.externalFileProviderUploadDirectoryId),
                        width: '80%',
                        modal: true,
                        height: '500',
                        onOpen: function () {
                            ConcreteEvent.subscribe('AjaxFormSubmitSuccess', function (e, data) {
                                ConcreteEvent.publish('FileManagerSelectFile', {
                                    fID: [data.response.importedFileId]
                                });

                                ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess');
                            });
                        }
                    });

                }
            });
        }
    });
})(jQuery);