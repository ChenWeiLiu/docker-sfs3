$(document).ready((function () {
    var basegeturl = "http://localhost:8088/smartcard/get/32708600";
    $(document).ajaxComplete($.unblockUI);
    console.log(basegeturl);
    $("#btnRegXCA").click(function (event) {
        $.blockUI({message: $('#domMessage')});
        event.preventDefault();
        console.log(basegeturl);

        $.get(basegeturl,
                function (data) {
                    console.log(data);
                    alert(JSON.stringify(data));
                }
        ).error(
                function (err) {
                    alert('請確定微型伺服器己啟動');
                });
    });

}));

