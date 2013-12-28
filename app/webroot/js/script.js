$(document).ready(function() {
    $('input.datepicker').Zebra_DatePicker({
        first_day_of_week : 0
    });
    $('input.datepicker').val(getFutureDate(0));
});
$(function(){
    $(".jshover").hover(function(){
        var task_id = ($(this).attr('class')).split(" ")
        $('.'+task_id[0]).addClass("hover");
    },function(){
        var task_id = ($(this).attr('class')).split(" ")
        $('.'+task_id[0]).removeClass("hover");
    })
})