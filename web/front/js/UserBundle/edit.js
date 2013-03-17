$(document).ready(function() {

    $('#delete_account').on('click', function() {
        var $this = $(this);

        if ($this.hasClass('warning')) {
            $this.removeClass('warning');
            $this.html($this.attr('data-confirm'));

            return false;
        }
    });

});
