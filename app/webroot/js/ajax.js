/*
 *   ajax処理
 */
var addDay;     //today or tomorrow or dayaftertomorrow

//イベントデフォルトを抑止
function cancelEvent(e) {
    if (e.preventDefault) {
        e.preventDefault();
    } else if (window.event) {
        window.event.returnValue = false;
    }
}
//分割用タスクのhtml部品
function htmlAddElm(data) {
    var elm =$(
        '<li id="task_'+data.result.id+'" class="list-group-item notyet" style="display:none;" data-task-id="'+ data.result.id +'">\n' +
        '<span class="check-task"><input type="checkbox"></span>\n'+
        '<span class="body"><a href="/tasks/view/' + data.result.id + '">'+ data.result.body +'</a></span>\n' +
        '<span class="start_time">'+ data.result.start_time +'</span>\n'+
        '<span class="status">'+ data.result.status +'</span>\n'+
        '<span class="d_param">'+ data.result.d_param +'</span>\n'+
        '<span class="edit-task btn btn-default">編集</span>\n' +
        // '<span class="divide-task btn btn-default">分割</span>\n' +
        '<span class="delete-task"><span class="glyphicon glyphicon-trash"></span><b>削除</b></span>\n' +
        '<span class="sequence" style="display:none;">0</span>\n' +
        '</li>'
    );
    return elm;
}
function htmlDivideUl(parentId){
    var elm = $(
        '<ul class="ul-divide" data-parent-id="'+parentId+'">\n'+
        '<li class="li-divide list-group-item clearfix">\n'+
            '<span><input class="body edit-input" type="text" value="" placeholder="タスクを入力して下さい"/></span>\n'+
            '<span><input class="start_time edit-input datepicker" type="text" value="" placeholder="2014-01-01"/></span>\n'+
            '<span class="divide-del btn btn-danger">☓</span>\n'+
            '<input class="parent_id" type="hidden" name="parent_id" value="'+parentId+'" />'+
        '</li>\n'+
        '<li class="divide-btn-area list-group-item clearfix">'+
            '<span class="divide-more btn btn-success">＋</span>\n'+
            '<span class="divide-push btn btn-primary">OK</span>\n'+
        '</li>\n'+
        '</ul>'
    );
    return elm;
}
//divide-moreで挿入されるエレメント
function htmlDivideLi(parentId) {
    var elm = $(
        '<li class="li-divide li-divide-more list-group-item clearfix">\n'+
            '<span><input class="body edit-input" type="text" value="" placeholder="タスクを入力して下さい"/></span>\n'+
            '<span><input class="start_time edit-input datepicker" type="text" value="" placeholder="2014-01-01"/></span>\n'+
            '<span class="divide-del btn btn-danger">☓</span>\n'+
            '<input class="parent_id" type="hidden" name="parent_id" value="'+parentId+'" />\n'+
        '</li>\n'
    );
    return elm;
}

function htmlEmptyElm() {
    var elm = $(
        '<li class="empty list-group-item clearfix">タスクがありません</li>'
    );
    return elm;
}

function makeDatePicker() {
    $('input.datepicker').Zebra_DatePicker({
        direction : [getFutureDate(0), false]
    });
}

//day日後の日付を返す
function getFutureDate(day) {
    var d = new Date();
    d.setDate(d.getDate() + day);
    year = d.getFullYear();
    month = d.getMonth() + 1;
    date = d.getDate();

    return year +'-'+ month +'-'+ date;
}

// today or tomorrow or dayaftertomorrow を引数に渡すと空タスクを消す
function deleteEmpty(addDay) {
    if($('#task-list-' + addDay+' .empty').length){
        //空の場合
        $('#task-list-' + addDay+' .empty').remove();
    }
}
function removeChildUl(id) {
    //タスクを消去した結果children-ulの中身がからっぽなら、ulも消去
    if ( $('ul[data-children-ul-id='+id+']').find('li').length == 0) {
        $('ul[data-children-ul-id='+id+']').remove();
    }
}


function getAddDay(start_time) {
    if(start_time <= getFutureDate(0)) {
        return 'today';
    } else if ((start_time == getFutureDate(1))) {
        return 'tomorrow';
    } else if((start_time == getFutureDate(2))) {
        return 'dayaftertomorrow';
    }
}

//start_timeと追加エレメントを渡すとその日の場所にタスクを追加する
function appendToDay(start_time, elm) {

    addDay = getAddDay(start_time);

    switch(addDay) {
        case 'today' :
            deleteEmpty(addDay)
            $('#task-list-today').append(elm);
            break;
        case 'tomorrow' :
            deleteEmpty(addDay)
            $('#task-list-tomorrow').append(elm);
            break;
        case 'dayaftertomorrow' :
            deleteEmpty(addDay)
            $('#task-list-dayaftertomorrow').append(elm);
            break;
    }
    addDay = '';
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
    var elm = htmlAddElm(data);

    //日付によって描画する場所を変える
    appendToDay(data.result.start_time, elm);

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
    }, 2000);
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

    //ulの中身が空になると空タスクを挿入する
    function createEmpty() {
        var arr = new Array('today', 'tomorrow', 'dayaftertomorrow');
        for(i in arr) {
            if($('#task-list-'+arr[i]).find('li').length == 0) {
                $('#task-list-'+arr[i]).append(htmlEmptyElm());
            }
        }
    }

    //Delete Task
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
            success : function(data){

                $('#task_' + taskId).fadeOut(200, function(){
                    popUpPanel(false, 'タスクが削除されました');
                    //一族かトップかでul消すかどうかの処理がかわる

                    //view表示時
                    if($('#task_'+data.result.id).parent().hasClass('children-ul')) {
                        //子タスク内
                        if($('ul[data-children-ul-id=' + data.result.parent_id + ']').hasClass('children-ul')) {
                            $.when($('#task_'+data.result.id).remove()).then(removeChildUl(data.result.parent_id));
                        }
                    //トップページ表示時
                    } else {
                        $.when($(this).remove()).then(createEmpty());
                    }
                });

            },
            error : function() {
                popUpPanel(true, 'サーバーエラーでタスクを消去できませんでした');
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
            '<span><input class="start_time edit-input datepicker" type="text" value="'+start_time+'"/></span>\n'+
            '<span class="edit-push btn btn-default">変更</span>\n'+
            '<span class="edit-cancel btn btn-default">キャンセル</span>\n'+
            '<input class="status" type="hidden" name="status" value="'+status+'" />'+
            '<input class="d_param" type="hidden" name="d_param" value="'+d_param+'" />'
        );

        $('#task_' + taskId).empty().append(elm);
        $('#task_' + taskId +' input:eq(0)').focus();

        makeDatePicker();
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
        if ($('#task_' + taskId).parent().hasClass('children-ul')) {
            var divideSpan = '<span class="divide-task btn btn-default">分割</span>\n';
        } else {
            var divideSpan = '';
        }
        var elm = $(
            '<span class="check-task"><input type="checkbox"></span>\n'+
            '<span class="body"><a href="/tasks/view/' + taskId + '">'+ body +'</a></span>\n' +
            '<span class="start_time">'+ start_time +'</span>\n'+
            '<span class="status">'+ status +'</span>\n'+
            '<span class="d_param">'+ d_param +'</span>\n'+
            '<span class="edit-task btn btn-default">編集</span>\n' +
            divideSpan +
            '<span class="delete-task"><span class="glyphicon glyphicon-trash"></span><b>削除</b></span>'
        );

        $('#task_' + taskId).empty().append(elm);
    })

    //Edit Push Task
    $(document).on('click','.edit-push', function(e){
        cancelEvent(e);
        var taskId      = $(this).parent().data('task-id');
        var body        = $('#task_'+taskId).find('.body').val();
        var start_time  = $('#task_'+taskId).find('.start_time').val();
        // var status      = $('#task_'+taskId).find('.status').val();
        var status      = 'notyet';
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
                var elm = htmlAddElm(data);

                //トップページか、viewページかで挿入場所場合分け
                //viewページの場合
                if( $('#task_' + data.result.id).parent().hasClass('children-ul')) {
                    $('#task_' + data.result.id).hide().after(elm);
                    $('#task_' + data.result.id).remove();
                    $('#task_' + data.result.id).fadeIn('slow');
                //トップページの場合
                } else {
                    //編集前のリストがいた日にちの場所
                    oldDay = $('#task_' + data.result.id).parent().attr('id').substr(10);
                    addDay = getAddDay(data.result.start_time);
                    //日付が変更されないならその場で挿入、されるなら適切な場所に挿入
                    if(oldDay == addDay) {
                        $('#task_' + data.result.id).hide().after(elm);
                        $('#task_' + data.result.id).remove();
                        $('#task_' + data.result.id).fadeIn('slow');
                    } else {
                        $('#task_' + data.result.id).remove();
                        //start_timeによって適切な場所に
                        appendToDay(data.result.start_time, elm);
                        $('#task_' + data.result.id).fadeIn('slow');
                    }
                }

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
                    '<span class="delete-task"><span class="glyphicon glyphicon-trash"></span><b>削除</b></span>'
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
        var taskId   = $(this).parent().data('task-id');
        var elm      = htmlDivideUl(taskId);
        $('#task_' + taskId).after(elm);
        $('ul[data-parent-id='+taskId+']').show().animate({
            height : '115px',
            // padding : '10px 15px',
            borderWidth : '1px',
        }, 200);

        //分割ボタンを分割キャンセルボタンにする
        $('#task_'+taskId).find('.divide-task').replaceWith('<span class="divide-cancel btn btn-default">キャンセル</span>');

        //親タスクのbtnを止める
        $('#task_'+taskId).find('.edit-task').replaceWith('<span class="disable-edit btn btn-default btn-disabled">編集</span>');
        $('#task_'+taskId).find('.delete-task').fadeOut(1);
        makeDatePicker();
    })

    //Divide more
    $(document).on('click', '.divide-more', function(e){
        cancelEvent(e);
        var taskId      = $(this).parent().parent().data('parent-id');
        var elm = htmlDivideLi(taskId);
        $(this).parent().before(elm);
        $('ul[data-parent-id='+taskId+']').css("height", "100%");
        $('.li-divide-more').show().animate({
            height : '59px',
            padding : '10px 15px',
            borderWidth : '1px',
        }, 200);

        // //分割ボタンを分割キャンセルボタンにする
        // $('#task_'+taskId).find('.divide-task').replaceWith('<span class="divide-cancel btn btn-default">キャンセル</span>');

        // //親タスクのbtnを止める
        // $('#task_'+taskId).find('.edit-task').replaceWith('<span class="disable-edit btn btn-default btn-disabled">編集</span>');
        // $('#task_'+taskId).find('.delete-task').replaceWith('<span class="disable-delete btn btn-default btn-disabled">削除</span>');
        makeDatePicker();
    })

    //Divide del
    $(document).on('click', '.divide-del', function(e){
        cancelEvent(e);
        var taskId      = $(this).parent().parent().data('parent-id');
        $(this).parent().fadeOut(300, function(){
            $.when($(this).remove()).then(function(){
                if($('ul[data-parent-id='+taskId+']').find('.li-divide').length == 0) {
                    $('ul[data-parent-id='+taskId+']').fadeOut().remove();

                    //キャンセルボタンを分割ボタンにする
                    $('#task_'+taskId).find('.divide-cancel').replaceWith('<span class="divide-task btn btn-default">分割</span>');

                    //親タスクのbtnを元に戻す
                    $('#task_'+taskId).find('.disable-edit').replaceWith('<span class="edit-task btn btn-default">編集</span>');
                    $('#task_'+taskId).find('.delete-task').fadeIn(100);
                }
            });
        });
    })

    //Divie Cancel
    $(document).on('click', '.divide-cancel', function(e){
        cancelEvent(e);
        var taskId      = $(this).parent().data('task-id');
        $('ul[data-parent-id='+taskId+']').animate({
            height : '0px',
            padding : '0px 0px',
            // borderWidth : '0px',
        }, 100, function(){
            $(this).remove();
        });

        //キャンセルボタンを分割ボタンにする
        $('#task_'+taskId).find('.divide-cancel').replaceWith('<span class="divide-task btn btn-default">分割</span>');

        //親タスクのbtnを元に戻す
        $('#task_'+taskId).find('.disable-edit').replaceWith('<span class="edit-task btn btn-default">編集</span>');
        $('#task_'+taskId).find('.delete-task').fadeIn(100);
    })

    //Divide push
    $(document).on('click', '.divide-push', function(e){
        cancelEvent(e);
        var parentId      = $(this).parent().parent().data('parent-id');
        var divideArr   = [];
        var divideCount = Number($('ul[data-parent-id='+parentId+']').find('.li-divide').length);
<<<<<<< HEAD
        //var brotherCount = Number($('ul[data-children-ul-id='+parentId+']').find('li').length);
        //var parent_d    = Number($('#task_'+parentId).find('.d_param').text());
        //var d_param   = Math.ceil(parent_d / (divideCount + brotherCount));
        console.log('divideCount : '+divideCount);
        //console.log('brotherCount : '+brotherCount);
        //console.log('influence : '+influence);
=======
        var brotherCount = Number($('ul[data-children-ul-id='+parentId+']').find('li').length);
        var parent_d    = Number($('#task_'+parentId).find('.d_param').text());
        var influence   = Math.ceil(parent_d / (divideCount + brotherCount));
>>>>>>> 064eb3c588d0962aacb64b3c5ae03599703c3174

        for ( var i = 0; i <= divideCount - 1; i++) {
            divideArr.push(
                {
                    parent_id   : parentId,
                    body        : $('ul[data-parent-id='+parentId+']').find('.li-divide').eq(i).find('.body').val(),
                    start_time  : $('ul[data-parent-id='+parentId+']').find('.li-divide').eq(i).find('.start_time').val(),
                    //d_param     : d_param
                    //influence     : influence
                }
            )
        }
        var json = JSON.stringify(divideArr);
        $.ajax({
            url : '/tasks/divide/'+ parentId,
            type : 'POST',
            dataType : 'json',
            timeout : 5000,
            data : {
                json : json
            },
            beforeSend : function() {
                $('ul[data-parent-id=' + parentId +'] .divide-push').html('<img src="/img/ajax-loader.gif" alt="" />');
            },
            success : function(data) {
                console.log('success');
                console.log(data);
                //バリデーションエラー
                if(data.error === true ) {
                    // console.log('error');
                    //エラー内容取り出し $ エラーポップ
                    for (i in data.message) {
                        //ポップアップ通知
                        popUpPanel(data.error, data.message[i]);
                    }

                    return;
                }
                // $('li[data-parent-id='+parentId+']').remove();
                $('ul[data-parent-id='+data.result[0].parent_id+']').fadeOut(150, function(){
                    $(this).remove();
                })

                //分割を追加するタスクがすでに子持ちかどうか判別
                //持ってない場合children-ulを作る
                if(!$('#task_'+  data.result[0].parent_id).next().next().hasClass('children-ul')) {
                    $('#task_'+  data.result[0].parent_id).next().after(
                        '<ul class="children-ul" data-children-ul-id="'+data.result[0].parent_id+'"></ul>'
                    );
                }
                for(var i in data.result) {
                    $('ul[data-children-ul-id='+data.result[0].parent_id+']')
                    .append(
                        '<li id="task_'+data.result[i].id+'" class="list-group-item notyet" style="display:none;" data-task-id="'+ data.result[i].id +'">\n' +
                        '<span class="check-task"><input type="checkbox"></span>\n'+
                        '<span class="body"><a href="/tasks/view/' + data.result[i].id + '">'+ data.result[i].body +'</a></span>\n' +
                        '<span class="start_time">'+ data.result[i].start_time +'</span>\n'+
                        '<span class="status">notyet</span>\n'+
                        '<span class="d_param">'+ data.result[i].d_param +'</span>\n'+
                        '<span class="edit-task btn btn-default">編集</span>\n' +
                        '<span class="divide-task btn btn-default">分割</span>\n' +
                        '<span class="delete-task"><span class="glyphicon glyphicon-trash"></span><b>削除</b></span>\n' +
                        '</li>'
                    );
                    $('#task_'+data.result[i].id).fadeIn('slow');
                }

                $('#task_'+data.result.id).fadeIn('slow');

                //通知ポップ
                popUpPanel(false, '送信されました');

                //キャンセルボタンを分割ボタンにする
                $('#task_'+parentId).find('.divide-cancel').replaceWith('<span class="divide-task btn btn-default">分割</span>');

                //親タスクのbtnを元に戻す
                $('#task_'+parentId).find('.disable-edit').replaceWith('<span class="edit-task btn btn-default">編集</span>');
                $('#task_'+parentId).find('.disable-delete').replaceWith('<span class="delete-task"><span class="glyphicon glyphicon-trash"></span><b>削除</b></span>');
            },
            error : function(){
<<<<<<< HEAD
                console.log('error');
                //エラーまたかく
=======
                popUpPanel(true, 'サーバー');
>>>>>>> 064eb3c588d0962aacb64b3c5ae03599703c3174
            },
            complete : function() {
                //バリデーションエラー時、ボタン戻す
                $('ul[data-parent-id=' + parentId +'] .divide-push').html('作成');


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

    //Clean UP bombed Task
    $(document).on('click', '#clean-bomb', function(e){
        cancelEvent(e);
        var cleanArr = new Array();

        $('#tasks li.bomb').each(function(){
            cleanArr.push($(this).data('task-id'));
        });

        var json = JSON.stringify(cleanArr);
        $.ajax({
            url : '/tasks/clean',
            type : 'POST',
            dataType : 'json',
            timeout : 5000,
            data : {
                json : json
            },
            beforeSend : function(){
                $('#clean-bomb').html('<img src="/img/ajax-loader.gif" alt="" />');
            },
            success : function(data){
                for(i in data.result) {
                    $('#task_'+data.result[i]).fadeOut('slow',function(){
                        $.when($(this).remove()).then(createEmpty());
                    })
                    deleteEmpty('bombs');
                    $('#task-list-bombs').append(
                        '<li id="bomb_'
                        +data.result[i]
                        +'" class="bomb list-group-item clearfix" style="display:none" data-task-id="'
                        +data.result[i]+'">'
                        +'<span class="body"><a href="/tasks/view/'
                        + data.result[i]+ '">'
                        +$('#task_'+ data.result[i]).find('.body').text()
                        +'</a></span></li>\n'
                    );
                }
                $('#task-list-bombs').children().fadeIn('slow');
            },
            error : function(){
                popUpPanel(true, 'サーバーエラーでタスクを消去できませんでした');
            },
            complete : function(){
                $('#clean-bomb').html('done一括削除');
            },
        });

        /*var elm1 ='';
        for(i in cleanArr) {
         elm1 +=
         '<li id="bomb_'+data.result[i]+'" class="bomb list-group-item clearfix" data-task-id="'+data.result[i]+'">'
         +'<span class="body"><a href="/tasks/view/' + data.result[i] + '">'
         +$('#task_' + data.result[i]).find('.body').text()
         +'</a></span></li>\n';
        }
        $('#task-list-bombs').append(elm1)
$('#task_'+data.result.id).fadeIn('slow');*/

        // $('#tasks li.done').each(function(){
        //     $(this).fadeOut('slow', function(){
        //         $(this).remove();
        //     });
        // })
    });

    //sortable
    $('.sort-list').sortable({
        axis : 'y',
        opacity : 0.7,
        cursor : 'move',
        // grid : [30,30],
        update : function(){
            $.ajax({
                url : '/tasks/sort/manually',
                type : 'POST',
                timeout : 5000,
                data : {
                    sequence : $(this).sortable('serialize')
                },
                beforeSend : function() {
                    //全ての編集中のタスクを元に戻す。
                },
                success : function() {

                },
                error : function() {

                },
                complete : function() {

                }
            })
        }
    });

    $('.sort-link').click(function(e){
        cancelEvent(e);
        var day = $(this).attr('href').substr(14);
        $.ajax({
            url : $(this).attr('href'),
            type : 'POST',
            timeout : 5000,
            beforeSend : function() {
                //全ての編集中のタスクを元に戻す。
            },
            success : function(data) {
                var data = $.parseJSON(data);
                $('#task-list-'+day).html('');
                for(var i in data.result) {
                    $('#task-list-'+day).append(
                        '<li id="task_'+data.result[i].id+'" class="list-group-item notyet" style="display:none;" data-task-id="'+ data.result[i].id +'">\n' +
                        '<span class="check-task"><input type="checkbox"></span>\n'+
                        '<span class="body"><a href="/tasks/view/' + data.result[i].id + '">'+ data.result[i].body +'</a></span>\n' +
                        '<span class="start_time">'+ data.result[i].start_time +'</span>\n'+
                        '<span class="status">notyet</span>\n'+
                        '<span class="d_param">'+ data.result[i].d_param +'</span>\n'+
                        '<span class="edit-task btn btn-default">編集</span>\n' +
                        '<span class="delete-task"><span class="glyphicon glyphicon-trash"></span><b>削除</b></span>\n' +
                        '<span class="sequence">' + data.result[i].sequence + '</span>\n' +
                        '</li>'
                    )
                }
                $('#task-list-' +day+' li').fadeIn(150);

            },
            error : function() {

            },
            complete : function() {

            }
        })
    });

    //ソートをajaxで送る関数
    function sortAjax(url, data) {
        $.ajax({
            url : '/tasks/sort/d/today',
            type : 'POST',
            timeout : 5000,
            beforeSend : function() {
                //全ての編集中のタスクを元に戻す。
            },
            success : function(data) {
                var data = $.parseJSON(data);
                $('#task-list-today').html('');
                for(var i in data.result) {
                    $('#task-list-today').append(
                        '<li id="task_'+data.result[i].id+'" class="list-group-item notyet" style="display:none;" data-task-id="'+ data.result[i].id +'">\n' +
                        '<span class="check-task"><input type="checkbox"></span>\n'+
                        '<span class="body"><a href="/tasks/view/' + data.result[i].id + '">'+ data.result[i].body +'</a></span>\n' +
                        '<span class="start_time">'+ data.result[i].start_time +'</span>\n'+
                        '<span class="status">notyet</span>\n'+
                        '<span class="d_param">'+ data.result[i].d_param +'</span>\n'+
                        '<span class="edit-task btn btn-default">編集</span>\n' +
                        '<span class="delete-task"><span class="glyphicon glyphicon-trash"></span><b>削除</b></span>\n' +
                        '<span class="sequence">' + data.result[i].sequence + '</span>\n' +
                        '</li>'
                    )
                }
                $('#task-list-today li').fadeIn(150);

            },
            error : function() {

            },
            complete : function() {

            }
        })
    }
});
