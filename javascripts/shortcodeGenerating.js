function generateShortcodeAjax() {

    var urlToShorten = $('#url_to_shorten').val();
    var useExistingCodeIfAvailable = $("#useExistingShortcodeIfAvailable").prop("checked");
    getShortcodeAndShowPopup(urlToShorten, useExistingCodeIfAvailable);
}

function getShortcodeAndShowPopup(url, useExistingCodeIfAvailable) {
    var postParams = {};
    postParams.url = url;
    postParams.useExistingCodeIfAvailable = useExistingCodeIfAvailable;
    postParams.tokenAuth = piwik.token_auth;
    var ajaxHandler = new ajaxHelper();
    ajaxHandler.addParams({
        module: 'API',
        format: 'json',
        method: 'ShortcodeTracker.generateShortcodeForUrl'
    }, 'GET');
    ajaxHandler.addParams(postParams, 'POST');
    ajaxHandler.setLoadingElement('#ajaxLoadingShortcodeTracker');
    ajaxHandler.setErrorElement('#ajaxErrorShortcodeTrakcer');
    ajaxHandler.setCallback(displayPopup)
    ajaxHandler.send(false);
}

function displayPopup(response) {
    var url = 'module=ShortcodeTracker&action=showShortcodePopup&shortcode=' + response.value;
    Piwik_Popover.createPopupAndLoadUrl(url, 'details');
}

$(document).ready(function () {
    $(document).on('click', '#url_to_shorten_submit', function () {
        generateShortcodeAjax();
    });
});