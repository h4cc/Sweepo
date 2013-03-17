$(function() {
    $('#disconnect_toggle').on('click', function() {
        var self = $(this);
        self.hide();
        $('#disconnect, #user_edit').show().delay(4000).fadeOut(function() {
            self.show();
        });
    });
});