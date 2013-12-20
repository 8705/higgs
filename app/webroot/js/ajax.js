/*
    ajax処理
*/

//イベントデフォルトを抑止
function cancelEvent(e) {
    if (e.preventDefault) {
        e.preventDefault();
    } else if (window.event) {
        window.event.returnValue = false;
    }
}

//タスク描画処理 in success
function addTask(data, textStatus) {
     console.log(data);
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
    $('#task-list').append(elm);
    $('#task_'+data.result.id).fadeIn('slow');

    //通知ポップ
    popUpPanel(false, '送信されました');
    //formリセット
    $('#TaskBody').val('');
}

function popUpPanel(error, message) {
    // console.log(jqxhr);
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
    //エラーコードによって処理通知メッセージ振り分け
    //400 500
}

$(function(){
    //ajaxリクエスト時、ヘッダーにトークンを設置
    $( document ).ajaxSend(function(event, jqxhr, settings) {
        jqxhr.setRequestHeader('X-CSRF-Token', token);
    });

    //タスク追加inputに自動フォーカス
    $('#TaskBody').focus();

    //Delete
    $(document).on('click','.delete-task', function(e){
        cancelEvent(e);
        if(!confirm('消すなら書くな！書いたら消すな！＼(^^)／')){
            return false;
        }

        var taskId      = $(this).parent().data('task-id');
        console.log(taskId);
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
        var d_param      = $('#task_' + taskId).find('.d_param').text();

        //現在編集中のタスクかどうかを判別
        $('#task_' + taskId).addClass('edit');

        var elm = $(
            '<span><input class="body" type="text" value="'+body+'"/></span>\n'+
            '<span><input class="start_time" type="text" value="'+start_time+'"/></span>\n'+
            '<span class="edit-push btn btn-default">変更</span>\n'+
            '<span class="edit-cancel btn btn-default">キャンセル</span>\n'+
            '<input class="status" type="hidden" name="status" value="'+status+'" />'+
            '<input class="d_param" type="hidden" name="d_param" value="'+d_param+'" />'
        );

        $('#task_' + taskId).empty().append(elm);
        $('#task_' + taskId +' input:eq(1)').focus();
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
        // cancelEvent(e);
        var taskId      = $(this).parent().data('task-id');
        var body        = $('#task_' + taskId).find('.body').text();
        var start_time  = $('#task_' + taskId).find('.start_time').text();
        var status      = $('#task_' + taskId).find('.status').text();
        var d_param     = $('#task_' + taskId).find('.d_param').text();
        var checked     = '';

        $.ajax({
            url: '/tasks/check/'+ taskId,
            type: 'POST',
            timeout:5000,
            data : {

            },
            beforeSend : function() {
                $('#task_' + taskId +' .check-task').html('<img src="/img/ajax-loader.gif" alt="" />');
            },
            success:function(){
                if ($('#task_'+taskId).hasClass('done')) {
                    $('#task_'+taskId).removeClass('done').addClass('notyet');
                    $('#task_'+taskId).find('.status').text('notyet');
                } else if($('#task_'+taskId).hasClass('notyet')){
                    $('#task_'+taskId).removeClass('notyet').addClass('done');
                    $('#task_'+taskId).find('.status').text('done');
                    checked = 'checked';
                }
            },
            complete : function() {
                $('#task_' + taskId +' .check-task').html('<input type="checkbox" '+ checked +'/>');
            },
        });
    });
});
