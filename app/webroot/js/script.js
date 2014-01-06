$(function(){

    $(document).ready(function() {
        $('input.datepicker').Zebra_DatePicker({
            first_day_of_week : 0
        });
        $('input.datepicker').val(getFutureDate(0));
    });

    $(document).on({
        mouseenter: function () {
            var task_id = ($(this).attr('class')).split(" ")
            $('.'+task_id[0]).addClass("jshover-active");
        },
        mouseleave: function () {
            var task_id = ($(this).attr('class')).split(" ")
            $('.'+task_id[0]).removeClass("jshover-active");
        }
    }, ".jshover");

    // $('#viewWrapper ul#task-list').draggable({
    //     axis : 'x',
    //     cursor : 'pointer'
    // });
})