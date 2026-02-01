//show loading on ajax calls
        $(document).ajaxStart(function () {
            $("#loading").show();
        });
        $(document).ajaxStop(function () {
            $("#loading").hide();

});