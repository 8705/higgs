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
        '<li id="task_'+data.result.id+'" class="list-group-item notyet" style="display:none;background-color:'+getHsl(data.result.d_param, data.all_d)+'" data-task-id="'+ data.result.id +'">\n' +
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
        '<ul class="ul-divide no-empty-form" data-parent-id="'+parentId+'">\n'+
        '<li class="li-divide list-group-item clearfix">\n'+
            '<span><input class="body edit-input no-empty-body" type="text" value="" placeholder="タスクを入力して下さい"/></span>\n'+
            '<span><input class="start_time edit-input datepicker no-empty-cal" type="text" value="" placeholder="2014-01-01" readonly /></span>\n'+
            '<span class="divide-del btn btn-danger">☓</span>\n'+
            '<input class="parent_id" type="hidden" name="parent_id" value="'+parentId+'" />'+
        '</li>\n'+
        '<li class="divide-btn-area list-group-item clearfix">'+
            '<span class="divide-more btn btn-success no-empty-submit">＋</span>\n'+
            '<span class="divide-push btn btn-primary no-empty-submit">OK</span>\n'+
        '</li>\n'+
        '</ul>'
    );
    return elm;
}
//divide-moreで挿入されるエレメント
function htmlDivideLi(parentId) {
    var elm = $(
        '<li class="li-divide li-divide-more list-group-item clearfix">\n'+
            '<span><input class="body edit-input no-empty-body" type="text" value="" placeholder="タスクを入力して下さい"/></span>\n'+
            '<span><input class="start_time edit-input datepicker no-empty-cal" type="text" value="" placeholder="2014-01-01" readonly /></span>\n'+
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
function getHsl(d_param, all_d) {
    var amount = 100 - 70 * d_param / all_d;
    return 'hsl(0,100%,'+amount+'%);';
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
//ulの中身が空になると空タスクを挿入する
function createEmpty() {
    var arr = new Array('today', 'tomorrow', 'dayaftertomorrow');
    for(i in arr) {
        if($('#task-list-'+arr[i]).find('li').length == 0) {
            $('#task-list-'+arr[i]).append(htmlEmptyElm());
        }
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

    var elm = htmlAddElm(data);
    $('#task-list-parents').append(elm);
    //正常時
    //トップページ
    if($('table.calendar').length == 0) {

        //日付によって描画する場所を変える

        appendToDay(data.result.start_time, elm);
    //カレンダー
    } else {
        $('td[data-cal-date='+data.result.start_time+']').append(
            '<p class="calendartask notyet id="task_'+data.result.id+'" data-cal-task-id="'+data.result.id+'">'+data.result.body+'</p>'
        );
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
    if ($('#TaskBody').val() != '' && $('#TaskStartTime') != '') {
        $('#TaskIndexForm .submit input').removeAttr('disabled');
    }
    //inputが空白でなくなったらsubmitボタン有効化
    $('#TaskBody').keyup(function(){
        if('' === $('#TaskBody').val() || '' === $('#TaskStartTime').val()) {
            $('#TaskIndexForm .submit input').attr({disabled : "disabled"});
        }else {
            $('#TaskIndexForm .submit input').removeAttr('disabled');
        }
    });
    // $(document).on('keyup', '.no-empty-body',function(){
    //     if('' === $('.no-empty-body').val() || '' === $('.no-empty-cal').val()) {
    //         $('.no-empty-submit').attr({disabled : "disabled"});
    //     }else {
    //         $('.no-empty-submit').removeAttr('disabled');
    //     }
    // })

    //タスク新規追加時にトップ画面のタスクリストから適切なシーケンスを取得
    $('#TaskIndexForm .submit').mouseover(function(){
        //日付がいつか判定
        var day = getAddDay($('#TaskStartTime').val());
        //日付に基づくシーケンス取得
        var sequence = $('#task-list-'+day).find('li:last .sequence').text();
        if(sequence == '') {sequence = 0;}
        //.sequenceにセット
        $('#TaskIndexForm').find('.sequence').attr({'value':sequence});
    })

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
                ajastDBar(data.all_d);
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
        // var body        = $('#task_' + taskId).find('.body').text();
        // var start_time  = $('#task_' + taskId).find('.start_time').text();
        // var status      = $('#task_' + taskId).find('.status').text();
        // var d_param     = $('#task_' + taskId).find('.d_param').text();
        var checked = '';

        $.ajax({
            url: '/tasks/check/'+ taskId,
            type: 'POST',
            timeout:5000,
            data : {
            },
            beforeSend : function() {
                $('#task_' + taskId +' .check-task').html('<img src="/img/ajax-loader.gif" alt="" />');
            },
            success:function(data){
                data = $.parseJSON(data);
                //チェックを外した時
                if (data.result.status == 'notyet') {
                    $('#task_'+taskId).removeClass('done').addClass('notyet');
                    //最終的に消す
                    $('#task_'+taskId).find('.status').text(data.result.status);

                    //親タスクのbtnを元に戻す
                    $('#task_'+taskId).find('.disable-edit').replaceWith('<span class="edit-task btn btn-default">編集</span>');
                    $('#task_'+taskId).find('.disable-divide').replaceWith('<span class="divide-task btn btn-default">分割</span>');
                //チェックを入れた時
                } else if (data.result.status == 'done'){
                    $('#task_'+taskId).removeClass('notyet').addClass('done');
                    //最終的に消す
                    $('#task_'+taskId).find('.status').text(data.result.status);

                    //親タスクのbtnを止める
                    $('#task_'+taskId).find('.edit-task').replaceWith('<span class="disable-edit btn btn-default btn-disabled">編集</span>');
                    $('#task_'+taskId).find('.divide-task').replaceWith('<span class="disable-divide btn btn-default btn-disabled">分割</span>');

                    checked = 'checked';
                }
                ajastDBar(data.all_d);
            },
            complete : function() {
                $('#task_' + taskId +' .check-task').html('<input type="checkbox" '+ checked +'/>');
            },
        });
    });
    function ajastDBar(amount) {
        var d = (100 * amount) / 1000;
        $('#d-bar').css({'width':d+'%'});
    }

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

        //今日の日付をプリセット
        $("input.datepicker").val(getFutureDate(0));
        $('ul[data-parent-id='+taskId+']').find('.body').focus();

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

        //今日の日付をプリセット
        $("input.datepicker").val(getFutureDate(0));

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
                //バリデーションエラー
                if(data.error === true ) {
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
                        '<li id="task_'+data.result[i].id+'" class="list-group-item notyet" style="display:none;background-color:'+getHsl(data.result[i].d_param, data.all_d)+'" data-task-id="'+ data.result[i].id +'">\n' +
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
                //エラーまたかく
                popUpPanel(true, 'サーバー');
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
                    var checked = '';
                    if(data.result[i].status == 'done') {checked = 'checked'};
                    $('#task-list-'+day).append(
                        '<li id="task_'+data.result[i].id+'" class="list-group-item '+data.result[i].status+'" style="display:none;background-color:'+getHsl(data.result[i].d_param, data.all_d)+'" data-task-id="'+ data.result[i].id +'">\n' +
                        '<span class="check-task"><input type="checkbox" '+checked+'></span>\n'+
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
                createEmpty();

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

    //ポップアップ操作中にパネル以外をクリック時、パネル消す
    var edit_flg = false;  //1:true パネル出現、2:false パネル無し
    $('#calendarPanel').hover(function(){
        edit_flg = true;
    }, function(){
        edit_flg = false;
    })
    //パネル外のカレンダーのマウスオーバー/リーブも考慮
    $(document).on({
        mouseenter : function(){
            edit_flg = true;
        },
        mouseleave : function(){
            edit_flg = false;
        }
    }, '.Zebra_DatePicker');

    $('body').mouseup(function(){
        if(!edit_flg) {
            $('#calendarPanel').removeClass('edit').fadeOut(100,function(){
                $(this).html('');
            });
        }
    })
    //マウスの動きを追う
    var x;
    var y;
    $("html").mousemove(function(e){
        x = e.clientX;
        y = e.clientY;
    });

    //カレンダー
    //パネル表示
    $(document).on('click','.calendartask',function(e){
        cancelEvent(e);
        var id = $(this).data('task-id');
        $.ajax({
            url : '/calendars/status/'+id,
            type : 'POST',
            dataType : 'json',
            timeout  : 5000,
            data : {
            },
            beforeSend : function() {
                $('#calendarPanel').fadeOut(100,function(){
            $(this).html('');
        });
            },
            success : function(data) {
                if(data.result.status == 'notyet') {
                    var editElm = '<a data-cal-task-id="'+data.result.id+'" class="edit-cal-task action-icon"><span class="glyphicon glyphicon-edit"></span>編集</a>\n';
                    var checked = '';
                } else {
                    var editElm = '';
                    var checked = 'checked';
                }
                $('#calendarPanel').addClass(data.result.status).append(
                    '<div class="body-area">\n'+
                    '<p class="task-body"><span class="check-cal-task" data-cal-task-id="'+data.result.id+'"><input type="checkbox" '+checked+'/></span><span class="body">'+data.result.body+'</span></p>\n'+
                    '<p class="task-date"><span>'+data.result.start_time+'</span></p>\n'+
                    '</div>\n'+
                    '<div class="action-area clearfix">\n'+
                    editElm +
                    '<a data-cal-task-id="'+data.result.id+'" class="delete-cal-task action-icon"><span class="glyphicon glyphicon-trash"></span>削除</a>\n'+
                    '</div>\n'+
                    '\n'+
                    '<a class="cal-panel-cancel"><span class="glyphicon glyphicon-remove"></span></a>\n'
                );
                var windowWidth = $(window).width();
                var windowHeight = $(window).height();
                var panelWidth = $('#calendarPanel').width();
                var panelHeight = $('#calendarPanel').height();
                if((50 + panelHeight + 40) > y) { //パネルが上にはみでちゃうパターン
                    y += 40;
                } else {
                    y -= (40 + panelHeight);
                }
                if((20 + (panelWidth / 2)) > x) { //パネルが左にはみでちゃうパターン
                    x += 20;
                } else if ((windowWidth - (panelWidth / 2)) < x) {
                    x -= (panelWidth + 20);
                } else {
                    x -= (panelWidth / 2);
                }
                $('#calendarPanel').css({'top':y+'px', 'left':x+'px'});
                $('#calendarPanel').fadeIn(100);
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {

            },
            complete : function() {

            },
        });
    })

    //Cal Delete Task
    $(document).on('click', '.cal-panel-cancel',function(e){
        cancelEvent(e);
        $('#calendarPanel').removeClass('edit').fadeOut(100,function(){
            $(this).html('');
        });
    });
    //Cal Edit Task
    $(document).on('click', '.edit-cal-task',function(e){
        cancelEvent(e);
        var id = $(this).data('cal-task-id');
        $.ajax({
            url : '/calendars/edit/status/'+id,
            type : 'POST',
            dataType : 'json',
            timeout  : 5000,
            data : {
            },
            beforeSend : function() {
                $('.edit-cal-task').html('<img src="/img/ajax-loader.gif" alt="" />');
            },
            success : function(data) {
                $('#calendarPanel').addClass('edit');
                $('#calendarPanel .task-body').html('<textarea data-cal-task-id="'+data.result.id+'" type="text">'+data.result.body+'</textarea>');
                $('#calendarPanel .task-date').html('<input data-cal-task-id="'+data.result.id+'" class="datepicker" type="text" value="'+data.result.start_time+'"/>');
                $('#calendarPanel .action-area').html('');
                $('#calendarPanel .action-area').append(
                    '<a class="cal-edit-push" data-cal-task-id="'+data.result.id+'"><span class="btn btn-primary">OK</span></a>'
                );
                $('#calendarPanel .body-area').append(
                    '<input type="hidden" class="old_start_time" valur="'+data.result.start_time+'"/>'
                    )
                makeDatePicker();
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {

            },
            complete : function() {
                $('.edit-cal-task').html('<span class="glyphicon glyphicon-edit"></span>編集');
            },
        });
    })

    //Cal Push Task
    $(document).on('click', '.cal-edit-push',function(e){
        cancelEvent(e);
        var id = $(this).data('cal-task-id');
        $.ajax({
            url : '/calendars/edit/push/'+id,
            type : 'POST',
            dataType : 'json',
            timeout  : 5000,
            data : {
                body : $('textarea[data-cal-task-id='+id+']').val(),
                start_time : $('input[data-cal-task-id='+id+']').val()
            },
            beforeSend : function() {
                $('.cal-edit-push').html('<img src="/img/ajax-loader.gif" alt="" />');
            },
            success : function(data) {
                $('#calendarPanel').removeClass('edit');
                $('#calendarPanel .task-body').html('<span><input data-cal-task-id="'+data.result.id+'" type="checkbox" /></span><span class="body">'+data.result.body+'</span>')
                $('#calendarPanel .task-date').html('<span>'+data.result.start_time+'</span>');
                $('#calendarPanel .action-area').html('');
                $('#calendarPanel .action-area').append(
                    '<a data-cal-task-id="'+data.result.id+'" class="edit-cal-task action-icon"><span class="glyphicon glyphicon-edit"></span>編集</a>'+
                    '<a data-cal-task-id="'+data.result.id+'" class="delete-cal-task action-icon"><span class="glyphicon glyphicon-trash"></span>削除</a>\n'
                );
                $('#task_'+data.result.id).text(data.result.body);
                $('#calendarPanel .action-area').append(
                    '<span class="success">（保存しました）</span>'
                )

                //カレンダーの表示タスクを適切な日付の枠へ移動
                var old_day  = $('#task_'+data.result.id).parent().data('cal-date');
                if(old_day != data.result.start_time) {
                    $('#task_'+data.result.id).fadeOut(300, function(){
                        $(this).remove();
                    })
                    $('td[data-cal-date='+data.result.start_time+']').append(
                        '<p class="calendartask notyet" id="task_'+data.result.id+'" data-task-id="'+data.result.id+'">'+data.result.body+'</p>'
                    );
                    $('#task_'+data.result.id).show(200);
                }
                //送信後パネルの高さが縮んでマウスがパネル外に行くから
                edit_flg = false;
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {

            },
            complete : function() {
                $('.edit-cal-task').html('<span class="glyphicon glyphicon-edit"></span>編集');
            },
        });
    })

    //Cal Delete Task
    $(document).on('click', '.delete-cal-task',function(e){
        cancelEvent(e);
        var id = $(this).data('cal-task-id');
        if(!confirm('「'+$('#task_'+id).text() + '」を削除しますか？')){
            return false;
        }
        $.ajax({
            url : '/tasks/delete/'+id,
            type : 'POST',
            dataType : 'json',
            timeout  : 5000,
            data : {
            },
            beforeSend : function() {
                $('.delete-cal-task').html('<img src="/img/ajax-loader.gif" alt="" />');
            },
            success : function(data) {
                popUpPanel(false, '「'+data.result.body+'」を削除しました');
                $('#task_'+data.result.id).fadeOut(200,function(){
                    $(this).remove();
                })
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
                popUpPanel(true, 'サーバーエラーで削除失敗しました');
            },
            complete : function() {
                $('#calendarPanel').fadeOut(300,function(){
                    $(this).html('');
                })
                //パネルが消えてマウスが外にだされる
                edit_flg = false;
            },
        });
    })

    //Cal Check Task
    $(document).on('click', '.check-cal-task',function(e){
        cancelEvent(e);
        var id = $(this).data('cal-task-id');
        var checked = '';
        $.ajax({
            url : '/tasks/check/'+id,
            type : 'POST',
            dataType : 'json',
            timeout  : 5000,
            data : {
            },
            beforeSend : function() {
                $('.check-cal-task').html('<img src="/img/ajax-loader.gif" alt="" />');
            },
            success : function(data) {
                $('#calendarPanel').removeAttr('class').addClass(data.result.status);
                if(data.result.status == 'done') {
                    $('#calendarPanel .action-area .edit-cal-task').remove();
                    $('#task_'+data.result.id).removeClass('notyet').addClass('done');
                    checked = 'checked';
                } else {
                    $('#calendarPanel .action-area').prepend(
                        '<a data-cal-task-id="'+data.result.id+'" class="edit-cal-task action-icon"><span class="glyphicon glyphicon-edit"></span>編集</a>'
                    );
                    $('#task_'+data.result.id).removeClass('done').addClass('notyet');
                }
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
                popUpPanel(true, 'サーバーエラーで削除失敗しました');
            },
            complete : function() {
                $('.check-cal-task').html('<input type="checkbox" '+checked+'/>');
            },
        });
    })

    //カレンダーDnD
    var count = 1;
    $('#taskcalendar td').sortable({
        connectWith : '.connected',
        opacity : 0.6,
        update : function() {
            if(count == 1) {
                date = start_date;
            } else if(count == 2) {
                date = move_date;
            }
            $.ajax({
                url : '/calendars/sort',
                type : 'POST',
                timeout : 5000,
                data : {
                    date : date,
                    sequence : $(this).sortable('serialize'),
                },
                beforeSend : function() {

                },
                success : function() {

                },
                error : function() {

                },
                complete : function() {

                }
            })
            if(count == 1) {
                count++;
            }
        },
        start : function() {
            count = 1;
            start_date = $(this).data('cal-date');
        },
        over : function() {
            move_date = $(this).data('cal-date');
        },
    }).disableSelection();

    //編集や削除ボタンを押した時のアコーディオンの反応を消す
    $(document).on({
        mouseenter : function(){
            open_flg = true;
        },
        mouseleave : function(){
            open_flg = false;
        }
    }, '.children-ul span');
    //一族リストの折込
    $(document).on('click','li',function(e){
        // cancelEvent(e);
        //子持ちししゃも判定
        if($(this).next().hasClass('children-ul')) {
            var id = $(this).data('task-id');
            if(!open_flg) {
                $('ul[data-children-ul-id='+id+']').animate({
                    height: 'toggle'
                },250);
                $('#task_'+id).toggleClass('close-ul');
                $('ul[data-children-ul-id='+id+']').toggleClass('close-ul');
            }
        }
    })
});
