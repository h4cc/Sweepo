$(function() {
    $('#disconnect_toggle').on('click', function() {
        var self = $(this);
        self.hide();
        $('#disconnect').show().delay(4000).fadeOut(function() {
            self.show();
        });
    });
});