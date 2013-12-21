/*
 *   ajax処理
 */

//イベントデフォルトを抑止
function cancelEvent(e) {
    if (e.preventDefault) {
        e.preventDefault();
    } else if (window.event) {
        window.event.returnValue = false;
    }
}
//分割用タスクのhtml部品
function htmlDivideElm(parentId){
    var elm = $(
        '<li class="li-divide list-group-item clearfix" data-parent-id="'+parentId+'">'+
            '<span><input class="body edit-input" type="text" value="" placeholder="タスクを入力して下さい"/></span>\n'+
            '<span><input class="start_time edit-input" type="text" value="" placeholder="2014-01-01"/></span>\n'+
            '<span class="divide-push btn btn-default">作成</span>\n'+
            '<input class="status" type="hidden" name="status" value="notyet" />'+
            '<input class="d_param" type="hidden" name="d_param" value="1" />'+
            '<input class="parent_id" type="hidden" name="parent_id" value="'+parentId+'" />'+
        '</li>'
    );
    return elm;
}

//タスク描画処理 in success
function addTask(data, textStatus) {
    //バリデーションエラー
    if(data.error === true ) {
        //エラー内容取り出し $ エラーポップ
        for (i in data.message.body) {
            //ポップアップ通知
            popUpPanel(data.error, data.message.body[i]);
        }

        return;
    }
    //正常時
    //dom生成
    var elm =$(
        '<li id="task_'+data.result.id+'" class="list-group-item notyet" style="display:none;" data-task-id="'+ data.result.id +'">\n' +
        '<span class="check-task"><input type="checkbox"></span>\n'+
        '<span class="body"><a href="/tasks/view/' + data.result.id + '">'+ data.result.body +'</a></span>\n' +
        '<span class="start_time">'+ data.result.start_time +'</span>\n'+
        '<span class="status">'+ data.result.status +'</span>\n'+
        '<span class="d_param">'+ data.result.d_param +'</span>\n'+
        '<span class="edit-task btn btn-default">編集</span>\n' +
        '<span class="divide-task btn btn-default">分割</span>\n' +
        '<span class="delete-task btn btn-default">削除</span>\n' +
        '</li>'
    );

    //日付によって描画する場所を変える
    //day日後の日付を返す
    function getFutureDate(day) {
        var d = new Date();
        d.setDate(d.getDate() + day);
        year = d.getFullYear();
        month = d.getMonth() + 1;
        date = d.getDate();

        return year +'-'+ month +'-'+ date;
    }

    if(data.result.start_time == getFutureDate(0)) {
        $('#task-list-today').append(elm);
    } else if ((data.result.start_time == getFutureDate(1))) {
        $('#task-list-tomorrow').append(elm);
    } else if((data.result.start_time == getFutureDate(2))) {
        $('#task-list-dayaftertomorrow').append(elm);
    }

    $('#task_'+data.result.id).fadeIn('slow');

    //通知ポップ
    popUpPanel(false, '送信されました');
    //formリセット
    $('#TaskBody').val('');
    $('#TaskIndexForm .datepicker').val('');
}

function popUpPanel(error, message) {
    if (!error) {
        alert = ' alert-success';
    }else {
        alert = ' alert-danger';
    }

    $('#noticePanel').append('<p class="alert'+alert+'">'+ message +'</p>').fadeIn('slow').queue(function(){
        setTimeout(function(){$('#noticePanel').dequeue();
    }, 3000);
    });
    $('#noticePanel').fadeOut('slow', function(){
        $('#noticePanel').empty();
    });
}
$(function(){
    //ajaxリクエスト時、ヘッダーにトークンを設置
    $( document ).ajaxSend(function(event, jqxhr, settings) {
        jqxhr.setRequestHeader('X-CSRF-Token', token);
    });

    //タスク追加inputに自動フォーカス
    $('#TaskBody').focus();
    //初期でサブミットボタンを使用禁止
    $('#TaskIndexForm .submit input').attr({disabled : "disabled"});
    if ($('#TaskBody').val() != '') {
        $('#TaskIndexForm .submit input').removeAttr('disabled');
    }
    //inputが空白でなくなったらsubmitボタン有効化
    $('#TaskBody').keyup(function(){
        if('' === $('#TaskBody').val()) {
            $('#TaskIndexForm .submit input').attr({disabled : "disabled"});
        }else {
            $('#TaskIndexForm .submit input').removeAttr('disabled');
        }
    })

    //Delete
    $(document).on('click','.delete-task', function(e){
        cancelEvent(e);
        if(!confirm('消すなら書くな！書いたら消すな！＼(^^)／')){
            return false;
        }

        var taskId      = $(this).parent().data('task-id');
        $.ajax({
            url : '/tasks/delete/' + taskId,
            type : 'POST',

            beforeSend : function() {
                $('#task_' + taskId +' .delete-task').html('<img src="/img/ajax-loader.gif" alt="" />');
            },
            success : function(){
                $('#task_' + taskId).fadeOut(200, function(){
                    popUpPanel(false, 'タスクが削除されました');
                });
            },
            complete : function() {
                $('#task_' + taskId +' .delete-task').html('削除');
            }
        })

    });

    //Edit Task
    $(document).on('click','.edit-task', function(e){
        cancelEvent(e);
        var taskId      = $(this).parent().data('task-id');
        var body        = $('#task_' + taskId).find('.body').text();
        var start_time  = $('#task_' + taskId).find('.start_time').text();
        var status      = $('#task_' + taskId).find('.status').text();
        var d_param     = $('#task_' + taskId).find('.d_param').text();

        //現在編集中のタスクかどうかを判別
        $('#task_' + taskId).addClass('edit');

        var elm = $(
            '<span><input class="body edit-input" type="text" value="'+body+'"/></span>\n'+
            '<span><input class="start_time edit-input" type="text" value="'+start_time+'"/></span>\n'+
            '<span class="edit-push btn btn-default">変更</span>\n'+
            '<span class="edit-cancel btn btn-default">キャンセル</span>\n'+
            '<input class="status" type="hidden" name="status" value="'+status+'" />'+
            '<input class="d_param" type="hidden" name="d_param" value="'+d_param+'" />'
        );

        $('#task_' + taskId).empty().append(elm);
        $('#task_' + taskId +' input:eq(0)').focus();
    })

    //Cancel Task
    $(document).on('click','.edit-cancel', function(e){
        cancelEvent(e);
        var taskId      = $(this).parent().data('task-id');
        var body = $('#task_'+taskId).find('.body').val();
        var start_time = $('#task_'+taskId).find('.start_time').val();
        var status = $('#task_'+taskId).find('.status').val();
        var d_param = $('#task_'+taskId).find('.d_param').val();

        $('#task_' + taskId).removeClass('edit');
        var elm = $(
            '<span class="check-task"><input type="checkbox"></span>\n'+
            '<span class="body"><a href="/tasks/view/' + taskId + '">'+ body +'</a></span>\n' +
            '<span class="start_time">'+ start_time +'</span>\n'+
            '<span class="status">'+ status +'</span>\n'+
            '<span class="d_param">'+ d_param +'</span>\n'+
            '<span class="edit-task btn btn-default">編集</span>\n' +
            '<span class="divide-task btn btn-default">分割</span>\n' +
            '<span class="delete-task btn btn-default">削除</span>'
        );

        $('#task_' + taskId).empty().append(elm);
    })

    //Edit Push Task
    $(document).on('click','.edit-push', function(e){
        cancelEvent(e);
        var taskId      = $(this).parent().data('task-id');
        var body        = $('#task_'+taskId).find('.body').val();
        var start_time  = $('#task_'+taskId).find('.start_time').val();
        var status      = $('#task_'+taskId).find('.status').val();
        var d_param     = $('#task_'+taskId).find('.d_param').val();

        $.ajax({
            url: '/tasks/edit/'+ taskId,
            type : 'POST',
            // dateType : 'json',
            timeout: 5000,
            data:{
                body : body,
                start_time : start_time,
                status : status,
                d_param : d_param
            },
            beforeSend : function() {
                $('#task_' + taskId +' .edit-push').html('<img src="/img/ajax-loader.gif" alt="" />');
            },
            success : function(data, textStatus){
                data = $.parseJSON(data);
                //バリデーションエラー
                if(data.error === true ) {
                    //エラー内容取り出し $ エラーポップ
                    for (i in data.message.body) {
                        //ポップアップ通知
                        popUpPanel(200, data.error, data.message.body[i]);
                    }

                    return;
                }
                //正常時
                //dom生成
                var elm =$(
                    '<span class="check-task"><input type="checkbox"></span>\n'+
                    '<span class="body"><a href="/tasks/view/' + data.result.id + '">'+ data.result.body +'</a></span>\n' +
                    '<span class="start_time">'+ data.result.start_time +'</span>\n'+
                    '<span class="status">'+ data.result.status +'</span>\n'+
                    '<span class="d_param">'+ data.result.d_param +'</span>\n'+
                    '<span class="edit-task btn btn-default">編集</span>\n' +
                    '<span class="divide-task btn btn-default">分割</span>\n' +
                    '<span class="delete-task btn btn-default">削除</span>'
                );
                $('#task_'+data.result.id).empty().append(elm);

                //通知
                popUpPanel(false, 'タスクを変更しました');

                $('#task_'+data.result.id).removeClass('edit');

            },
            error : function() {
                //dom生成
                var elm =$(
                    '<span class="check-task"><input type="checkbox"></span>\n'+
                    '<span class="body"><a href="/tasks/view/' + taskId + '">'+ body +'</a></span>\n' +
                    '<span class="start_time">'+ start_time +'</span>\n'+
                    '<span class="status">'+ status +'</span>\n'+
                    '<span class="d_param">'+ d_param +'</span>\n'+
                    '<span class="edit-task btn btn-default">編集</span>\n' +
                    '<span class="divide-task btn btn-default">分割</span>\n' +
                    '<span class="delete-task btn btn-default">削除</span>'
                );
                $('#task_'+taskId).empty().append(elm);
                popUpPanel(true, 'サーバーエラーでタスクを変更出来ませんでした。');
            },
            complete : function() {
                $('#task_' + taskId +' .edit-push').html('変更');
            }
        })
    });

    //Check Task
    $(document).on('click','.check-task', function(e){
        cancelEvent(e);
        var taskId      = $(this).parent().data('task-id');
        var body        = $('#task_' + taskId).find('.body').text();
        var start_time  = $('#task_' + taskId).find('.start_time').text();
        var status      = $('#task_' + taskId).find('.status').text();
        var d_param     = $('#task_' + taskId).find('.d_param').text();
        var checked;
        if ($('#task_'+taskId).hasClass('done')) {
            status      = 'notyet';
            checked     = '';
        } else if($('#task_'+taskId).hasClass('notyet')){
            status      = 'done';
            checked = 'checked';
        }

        $.ajax({
            url: '/tasks/check/'+ taskId,
            type: 'POST',
            timeout:5000,
            data : {
                status : status,

            },
            beforeSend : function() {
                $('#task_' + taskId +' .check-task').html('<img src="/img/ajax-loader.gif" alt="" />');
            },
            success:function(data){
                data = $.parseJSON(data);
                if (data.result.status == 'notyet') {
                    $('#task_'+taskId).removeClass('done').addClass('notyet');
                    $('#task_'+taskId).find('.status').text(data.result.status);

                    //親タスクのbtnを元に戻す
                    $('#task_'+taskId).find('.disable-edit').replaceWith('<span class="edit-task btn btn-default">編集</span>');
                    $('#task_'+taskId).find('.disable-divide').replaceWith('<span class="divide-task btn btn-default">分割</span>');
                } else if (data.result.status == 'done'){
                    $('#task_'+taskId).removeClass('notyet').addClass('done');
                    $('#task_'+taskId).find('.status').text(data.result.status);

                    //親タスクのbtnを止める
                    $('#task_'+taskId).find('.edit-task').replaceWith('<span class="disable-edit btn btn-default btn-disabled">編集</span>');
                    $('#task_'+taskId).find('.divide-task').replaceWith('<span class="disable-divide btn btn-default btn-disabled">分割</span>');
                }
            },
            complete : function() {
                $('#task_' + taskId +' .check-task').html('<input type="checkbox" '+ checked +'/>');
            },
        });
    });

    //Divide Task
    $(document).on('click', '.divide-task', function(e){
        cancelEvent(e);
        var taskId      = $(this).parent().data('task-id');
        var elm = htmlDivideElm(taskId);
        $('#task_'+taskId).after(elm);
        $('li[data-parent-id='+taskId+']').show().animate({
            height : '59px',
            padding : '10px 15px',
            borderWidth : '1px',
        }, 200);

        //分割ボタンを分割キャンセルボタンにする
        $('#task_'+taskId).find('.divide-task').replaceWith('<span class="divide-cancel btn btn-default">キャンセル</span>');

        //親タスクのbtnを止める
        $('#task_'+taskId).find('.edit-task').replaceWith('<span class="disable-edit btn btn-default btn-disabled">編集</span>');
        $('#task_'+taskId).find('.delete-task').replaceWith('<span class="disable-delete btn btn-default btn-disabled">削除</span>');
    })

    //Divie Cancel
    $(document).on('click', '.divide-cancel', function(e){
        cancelEvent(e);
        var taskId      = $(this).parent().data('task-id');
        $('li[data-parent-id='+taskId+']').animate({
            height : '0px',
            padding : '0px 15px',
            // borderWidth : '0px',
        }, 200, function(){
            $(this).remove();
        });

        //キャンセルボタンを分割ボタンにする
        $('#task_'+taskId).find('.divide-cancel').replaceWith('<span class="divide-task btn btn-default">分割</span>');

        //親タスクのbtnを元に戻す
        $('#task_'+taskId).find('.disable-edit').replaceWith('<span class="edit-task btn btn-default">編集</span>');
        $('#task_'+taskId).find('.disable-delete').replaceWith('<span class="delete-task btn btn-default">削除</span>');
    })

    //Divide push
    $(document).on('click', '.divide-push', function(e){
        cancelEvent(e);
        var taskId      = $(this).parent().data('parent-id');
        var body        = $('li[data-parent-id='+taskId+']').find('.body').val();
        var start_time  = $('li[data-parent-id='+taskId+']').find('.start_time').val();
        var status      = $('li[data-parent-id='+taskId+']').find('.status').val();
        var d_param     = $('li[data-parent-id='+taskId+']').find('.d_param').val();

        $.ajax({
            url : '/tasks/divide/'+ taskId,
            type : 'POST',
            dataType : 'json',
            timeout : 5000,
            data : {
                user_id : 3,
                parent_id : taskId,
                body : body,
                start_time : start_time,
                status : status,
                d_param : d_param
            },
            beforeSend : function() {
                $('li[data-parent-id=' + taskId +'] .divide-push').html('<img src="/img/ajax-loader.gif" alt="" />');
            },
            success : function(data) {
                //バリデーションエラー
                if(data.error === true ) {
                    //エラー内容取り出し $ エラーポップ
                    for (i in data.message) {
                        //ポップアップ通知
                        popUpPanel(data.error, data.message[i]);
                    }

                    return;
                }
                $('li[data-parent-id='+taskId+']').remove();

                //dom生成
                var elm =$(
                    '<li id="task_'+data.result.id+'" class="list-group-item notyet" style="display:none;" data-task-id="'+ data.result.id +'">\n' +
                    '<span class="check-task"><input type="checkbox"></span>\n'+
                    '<span class="body"><a href="/tasks/view/' + data.result.id + '">'+ data.result.body +'</a></span>\n' +
                    '<span class="start_time">'+ data.result.start_time +'</span>\n'+
                    '<span class="status">'+ data.result.status +'</span>\n'+
                    '<span class="d_param">'+ data.result.d_param +'</span>\n'+
                    '<span class="edit-task btn btn-default">編集</span>\n' +
                    '<span class="divide-task btn btn-default">分割</span>\n' +
                    '<span class="delete-task btn btn-default">削除</span>\n' +
                    '</li>'
                );
                $('#task_'+taskId).after(elm);
                $('#task_'+data.result.id).fadeIn('slow');

                //通知ポップ
                popUpPanel(false, '送信されました');

                //キャンセルボタンを分割ボタンにする
                $('#task_'+taskId).find('.divide-cancel').replaceWith('<span class="divide-task btn btn-default">分割</span>');

                //親タスクのbtnを元に戻す
                $('#task_'+taskId).find('.disable-edit').replaceWith('<span class="edit-task btn btn-default">編集</span>');
                $('#task_'+taskId).find('.disable-delete').replaceWith('<span class="delete-task btn btn-default">削除</span>');
            },
            error : function(){
                //エラーまたかく
            },
            complete : function() {

                //バリデーションエラー時、ボタン戻す
                $('li[data-parent-id=' + taskId +'] .divide-push').html('作成');


            }
        });

    });

    //Edit input Enter-Btn to sumit
    $(document).on('keydown', '.edit-input', function(e){
        if(13 == e.which /*&& e.which == keyDownCode*/) {
            var id = $(this).parent().parent().data('task-id');
            $('#task_'+id).find('.edit-push').click();
        }
    });
});
