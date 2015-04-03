/**
 * Created by KozminVA on 16.03.2015.
 */

jQuery(document).ready(function () {
    jQuery(".link_with_confirm").on(
        "click",
        function(event) {
            event.preventDefault();
            var oLink = jQuery(this),
                sText = oLink.attr('title'),
                oRet = confirm(sText + "?");
            console.dir(oRet);
            console.log("Event: " + (oRet ? "true" : "false"));
            if( oRet ) {
                window.location.href = oLink.attr('href');
                return true;
            }
            else {
                event.preventDefault();
                return false;
            }
        }
    );
});
