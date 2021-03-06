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
//タスクのhtml部品
function htmlAddElm(data) {
    if($('#task_'+data.result.Task.id).parent().hasClass('children-ul')) {
        var breadcrumbElm = '';
    } else {
        var breadcrumbElm = '<div class="bread-crumb">'+data.breadcrumb+'</div>';
    }
    if($('ul[data-children-ul-id='+data.result.Task.id+']').hasClass('children-ul') && data.result.Task.parent_id == null) {
        var checkElm = '<span class="origin glyphicon glyphicon-flag"></span>';
    } else if($('ul[data-children-ul-id='+data.result.Task.id+']').hasClass('children-ul')) {
        var checkElm = '<span class="accordion spread glyphicon glyphicon-expand"></span>\n';
    } else {
        var checkElm = '<span class="check-task"><input type="checkbox"></span>\n';
    }
    var elm =$(
        '<li id="task_'+data.result.Task.id+'" class="list-group-item notyet clearfix" style="display:none;" data-task-id="'+ data.result.Task.id +'">\n' +
        checkElm +'\n'+
        '<span class="body edit-task"><a href="/tasks/view/' + data.result.Task.id + '">'+ data.result.Task.body +'</a></span>\n' +
        '<span class="delete-task"><span class="glyphicon glyphicon-trash"></span><b>削除</b></span>\n' +
        '<span class="start_time">'+ roundStartTime(data.result.Task.start_time) +'</span>\n'+
        breadcrumbElm +
        '</li>'
    );
    return elm;
}

function htmlDivideUl(parentId){
    var elm = $(
        '<ul class="ul-divide no-empty-form" data-parent-id="'+parentId+'">\n'+
        '<li class="li-divide list-group-item clearfix">\n'+
            '<span><input class="body edit-input no-empty-body" type="text" value="" placeholder="細分化したタスクを入力して下さい"/></span>\n'+
            '<span><input class="start_time divide-input datepicker no-empty-cal" type="text" value="" placeholder="2014-01-01" readonly /></span>\n'+
            '<input class="parent_id" type="hidden" name="parent_id" value="'+parentId+'" />'+
        '</li>\n'+
        '<li class="divide-btn-area list-group-item clearfix">'+
            '<span class="divide-more btn btn-success no-empty-submit">＋</span>\n'+
            // '<span class="divide-push btn btn-primary no-empty-submit">OK</span>\n'+
        '</li>\n'+
        '</ul>'
    );
    return elm;
}
//divide-moreで挿入されるエレメント
function htmlDivideLi(parentId) {
    var elm = $(
        '<li class="li-divide li-divide-more list-group-item clearfix">\n'+
            '<span><input class="body edit-input no-empty-body" type="text" value="" placeholder="細分化したタスクを入力して下さい"/></span>\n'+
            '<span><input class="start_time divide-input datepicker no-empty-cal" type="text" value="" placeholder="2014-01-01" readonly /></span>\n'+
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
        first_day_of_week : 0
    });
}

//day日後の日付を返す
function getFutureDate(day) {
    var d = new Date();
    d.setDate(d.getDate() + day);
    year = d.getFullYear();
    month = d.getMonth() + 1;
    month = ('0'+month).slice(-2);
    date = d.getDate();

    return year +'-'+ month +'-'+ date;
}

// today or tomorrow or dayaftertomorrow を引数に渡すと空タスクを消す
function deleteEmpty(addDay) {
    if ($('#task-list-' + addDay+' .empty').length){
        //空の場合
        $('#task-list-' + addDay+' .empty').remove();
    }
}
//ulの中身が空になると空タスクを挿入する
function createEmpty() {
    var arr = new Array('today', 'tomorrow', 'dayaftertomorrow', 'projects', 'bombs');
    for(i in arr) {
        if ($('#task-list-'+arr[i]).find('li').length == 0) {
            $('#task-list-'+arr[i]).append(htmlEmptyElm());
        }
    }
}
function removeChildUl(id) {
    //タスクを消去した結果children-ulの中身がからっぽなら、ulも消去
    if ( $('ul[data-children-ul-id='+id+']').find('li:not(.delete)').length == 0) {
        $('ul[data-children-ul-id='+id+']').addClass('delete').hide();
        if($('#task_'+id).hasClass('notyet')){
            var checked = '';
        } else if ($('#task_'+id).hasClass('done')) {
            var checked = 'checked';
        }
        $('#task_'+id).find('.accordion').replaceWith('<span class="check-task"><input type="checkbox" '+ checked +'></span>');
        $('#task_'+id).find('.origin').replaceWith('<span class="check-task"><input type="checkbox" '+ checked +'></span>');
    }
}


function getAddDay(start_time) {
    if (start_time <= getFutureDate(0)) {
        return 'today';
    } else if ((start_time == getFutureDate(1))) {
        return 'tomorrow';
    } else if ((start_time == getFutureDate(2))) {
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
function adjustCheckBtn(id) {
    if($('#task_'+id).hasClass('origin')) {
        return;
    }
    var parentId = $('#task_'+id).parent().data('children-ul-id');
    //兄弟に未完了タスクがある場合
    if($('ul[data-children-ul-id='+parentId+']').find('li.notyet').length > 0) {
        if($('#task_'+parentId).hasClass('done')) {
            $('#task_'+parentId)
            .removeClass('done')
            .addClass('notyet')
            .find('.body').addClass('edit-task');
            adjustCheckBtn(parentId);
        }
        return;
    } else if($('ul[data-children-ul-id='+parentId+']').find('li.done').length > 0){
        $('#task_'+parentId)
        .removeClass('notyet')
        .addClass('done')
        .find('.body').removeClass('edit-task');

        adjustCheckBtn(parentId);
    } else {    //.deleteしかない場合
        return;
    }
}
function roundStartTime(start_time) { //2013-12-29
    var t = start_time.substring(5,10).replace('-','/');
    return t;
}

//タスク描画処理 in success
function addTask(data, textStatus) {
    //バリデーションエラー
    if (data.error === true ) {
        //エラー内容取り出し $ エラーポップ
        for (i in data.message.body) {
            //ポップアップ通知
            popUpPanel(data.error, data.message.body[i]);
        }
        return;
    }
    var elm =$(
        '<li class="parent_'+data.result.Task.id+' jshover notyet list-group-item clearfix" style="display: none;" data-task-id="'+data.result.Task.id+'">'+
        '<span class="body"><a href="/tasks/view/' + data.result.Task.id + '">'+data.result.Task.body +'</a></span>\n'+
        '<span class="attainment">0%</span>'+
        '<span class="selfbomb"><b>自爆</b></span></li>'
    );
    if ($('#task-list-parents .empty').length == 1 ) {
        $('#task-list-parents .empty').html('');
    }
    $('#projects li:eq(-2)').after(elm);
    $('#projects li:eq(-2)').fadeIn('slow');

    var elm2 =$('<div class="parent_'+data.result.Task.id+' jshover add-bar progress-bar progress-bar-danger" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100" style="width: 10%; display:none;">10kg</div>');

    $('#d-bar').append(elm2);
    $('.add-bar').fadeIn('slow');
    //正常時
    //トップページ
    if ($('table.calendar').length == 0) {
        //日付によって描画する場所を変える
        var elm = htmlAddElm(data);
        appendToDay(data.result.Task.start_time, elm);
    //カレンダー
    } else {
        $('td[data-cal-date='+data.result.Task.start_time+']').append(
            '<p class="calendartask notyet id="task_'+data.result.Task.id+
            '" data-cal-task-id="'+data.result.Task.id+'">'+data.result.Task.body+'</p>'
        );
    }

    $('#task_'+data.result.Task.id).fadeIn('slow');
    $('.parent_'+data.result.Task.id).fadeIn('slow');

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
    //$('#TaskBody').focus();
    //初期でサブミットボタンを使用禁止
    $('#TaskIndexForm .submit input').attr({disabled : "disabled"});
    if ($('#TaskBody').val() != '' && $('#TaskStartTime') != '') {
        $('#TaskIndexForm .submit input').removeAttr('disabled');
    }
    //inputが空白でなくなったらsubmitボタン有効化
    $('#TaskBody').keyup(function(){
        if ('' === $('#TaskBody').val() || '' === $('#TaskStartTime').val()) {
            $('#TaskIndexForm .submit input').attr({disabled : "disabled"});
        }else {
            $('#TaskIndexForm .submit input').removeAttr('disabled');
        }
    });
    // $('#UserLoginForm .LoginUsername').focus();
    // //ログインフォームのバリデーション
    // if ($('#UserLoginForm .LoginUsername').val().length > 4 && $('#UserLoginForm .LoginPassword').val().length > 4) {

    //     $('#UserLoginForm button').removeAttr('disabled');
    // }
    // $('.UserLoginForm').keyup(function(){
    //     console.log('name : ' + $('#UserLoginForm .LoginUsername').val());
    //     if (('' == $('#UserLoginForm #UserUsername').val() || $('#UserLoginForm #UserUsername').val().length < 5) ||
    //         ('' == $('#UserLoginForm #inputPassword3').val() || $('#UserLoginForm #inputPassword3').val().length < 5) ) {
    //         $('#UserLoginForm button').attr({disabled : "disabled"});
    //     }else {
    //         $('#UserLoginForm button').removeAttr("disabled");
    //     }
    // })
    // //サインアップフォームのバリデーション
    // // $('#UserRegisterForm button').attr({disabled : "disabled"});
    // $('.UserRegisterForm').keyup(function(){
    //     if (('' == $('#UserRegisterForm #UserUsername').val() || $('#UserRegisterForm #UserUsername').val().length < 5) ||
    //         ('' == $('#inputEmail3').val() || !$('#inputEmail3').val().match(/^[A-Za-z0-9]+[\w-]+@[\w\.-]+\.\w{2,}$/)) ||
    //         ('' == $('#UserRegisterForm #inputPassword3').val() || $('#UserRegisterForm #inputPassword3').val().length < 5) ) {
    //         $('#UserRegisterForm button').attr({disabled : "disabled"});
    //     }else {
    //         $('#UserRegisterForm button').removeAttr("disabled");
    //     }
    // })

    //タスク新規追加時にトップ画面のタスクリストから適切なシーケンスを取得
    $('#TaskIndexForm .submit').mouseover(function(){
        //日付がいつか判定
        var day = getAddDay($('#TaskStartTime').val());
        //日付に基づくシーケンス取得
        var sequence = $('#task-list-'+day).find('li:last .sequence').text();
        if (sequence == '') {sequence = 0;}
        //.sequenceにセット
        $('#TaskIndexForm').find('.sequence').attr({'value':sequence});
    })

    //Delete Task
    $(document).on('click','.delete-task', function(e){
        cancelEvent(e);
        if (!confirm('このタスクを消去します')){
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
                    if ($('#task_' + taskId).next().is('ul')) {
                         $('#task_' + taskId).next().fadeOut(200);
                    }
                    popUpPanel(false, 'タスクが削除されました');
                    //一族かトップかでul消すかどうかの処理がかわる

                    //view表示時
                    if ($('#task_'+data.result.Task.id).parent().hasClass('children-ul')) {
                        //子タスク内
                        if ($('ul[data-children-ul-id=' + data.result.Task.parent_id + ']').hasClass('children-ul')) {
                            $('ul[data-children-ul-id=' + data.result.Task.id + ']').find('li').removeClass('notyet').removeClass('done').addClass('delete')
                            $.when(
                                $('#task_'+data.result.Task.id).removeClass('notyet').removeClass('done').addClass('delete').hide()
                            ).then(
                                removeChildUl(data.result.Task.parent_id)
                            );
                        }
                        adjustCheckBtn(data.result.Task.id);
                        $('.delete').remove();
                    //トップページ表示時
                    } else {
                        $.when($(this).remove()).then(createEmpty());
                    }
                });
                adjustDBar(data.all_d);
                adjustattainment(data.attainment);
            },
            error : function() {
                popUpPanel(true, 'サーバーエラーでタスクを消去できませんでした');
            },
            complete : function() {
                $('#task_' + taskId +' .delete-task').html('削除');
            }
        })
    });

    //Edit & Divide Task
    $(document).on('click', '.edit-task', function(e){
        cancelEvent(e);
        var taskId   = $(this).parent().data('task-id');

        //prepare for Edit
        $.ajax({
            url : '/tasks/edit/status/'+ taskId,
            type : 'POST',
            dataType : 'json',
            timeout : 5000,
            beforeSend : function() {

            },
            success : function(data) {
                //バリデーションエラー
                if (data.error === true ) {
                    //エラー内容取り出し $ エラーポップ
                    for (i in data.message.body) {
                        //ポップアップ通知
                        popUpPanel(data.error, data.message.body[i]);
                    }

                    return;
                }
                //現在編集中のタスクかどうかを判別
                $('#task_' + taskId).addClass('edit');

                var elm = $(
                    '<span><input class="body edit-input" type="text" value="'+data.result.Task.body+'"/></span>\n'+
                    '<span><input class="start_time edit-input datepicker" type="text" value="'+data.result.Task.start_time+'"/></span>\n'+
                    '<span class="edit-push btn btn-default">OK</span>\n'+
                    '<span class="edit-cancel btn btn-default">キャンセル</span>\n'
                );
                $('#task_' + taskId).empty().append(elm);

                //prepare for Divide
                var elm      = htmlDivideUl(taskId);
                $('#task_' + taskId).after(elm);
                $('ul[data-parent-id='+taskId+']').show().animate({
                    height : '115px',
                    // padding : '10px 15px',
                    borderWidth : '1px',
                }, 200);

                //今日の日付をプリセット
                $("input.divide-input").val(getFutureDate(0));
                $('ul[data-parent-id='+taskId+']').find('.body').focus();

                //親タスクのbtnを止める
                // $('#task_'+taskId).find('.edit-task').replaceWith('<span class="disable-edit btn btn-default btn-disabled">編集</span>');
                $('#task_'+taskId).find('.delete-task').fadeOut(1);
                makeDatePicker();

            },
            error : function() {
                popUpPanel(true, 'サーバーエラーでデータ取得失敗')
            },
            completed : function() {

            },
        });
    })

    //Edit & Divide more
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
        $(".li-divide:last input.divide-input").val(getFutureDate(0));

        makeDatePicker();
    })

    //Edit & Divide del
    $(document).on('click', '.divide-del', function(e){
        cancelEvent(e);
        var parentId      = $(this).parent().parent().data('parent-id');
        $(this).parent().fadeOut(300, function(){
            $(this).remove();
        });
    })

    //Edit & Divide Cancel
    $(document).on('click', '.edit-cancel', function(e){
        cancelEvent(e);
        var taskId      = $(this).parent().data('task-id');

        //Cancel Edit Area
        $.ajax({
            url : '/tasks/edit/cancel/'+taskId,
            type : 'POST',
            dataType : 'json',
            timeout : 5000,
            beforeSend : function() {

            },
            success : function(data) {
                //バリデーションエラー
                if (data.error === true ) {
                    //エラー内容取り出し $ エラーポップ
                    for (i in data.message.body) {
                        //ポップアップ通知
                        popUpPanel(data.error, data.message.body[i]);
                    }

                    return;
                }
                $('#task_' + taskId).removeClass('edit');

                if($('ul[data-children-ul-id='+data.result.Task.id+']').hasClass('children-ul') && data.result.Task.parent_id == null) {
                    var checkElm = '<span class="origin glyphicon glyphicon-flag"></span>';
                } else if($('ul[data-children-ul-id='+data.result.Task.id+']').hasClass('children-ul')) {
                    var checkElm = '<span class="accordion spread glyphicon glyphicon-expand"></span>\n';
                } else {
                    var checkElm = '<span class="check-task"><input type="checkbox"></span>\n';
                }
                if($('#task_'+data.result.Task.id).parent().hasClass('children-ul')) {
                    var breadcrumbElm = '';
                } else {
                    var breadcrumbElm = '<div class="bread-crumb">'+ data.breadcrumb +'</div>'
                }
                var elm = $(
                    checkElm +
                    '<span class="body edit-task"><a href="/tasks/view/' + data.result.Task.id + '">'+ data.result.Task.body +'</a></span>\n' +
                    '<span class="delete-task"><span class="glyphicon glyphicon-trash"></span>削除</span>\n' +
                    '<span class="start_time">'+ roundStartTime(data.result.Task.start_time) +'</span>\n' +
                    breadcrumbElm
                );

                $('#task_' + taskId).empty().append(elm);

                //Cancel Divide Area
                $('ul[data-parent-id='+taskId+']').animate({
                    height : '0px',
                    padding : '0px 0px',
                    // borderWidth : '0px',
                }, 100, function(){
                    $(this).remove();
                });

                $('#task_'+taskId).find('.delete-task').fadeIn(100);
            },
            error : function() {
                popUpPanel(true, 'サーバーエラーです');
            },
            complete : function() {

            }
        });

    })

    //Edit & Divide push
    $(document).on('click', '.edit-push', function(e){
        cancelEvent(e);
        var taskId      = $(this).parent().data('task-id');
        var isOrigin = $('#task_'+taskId).hasClass('origin')?true:false;
        //Edit push
        var body        = $('#task_'+taskId).find('.body').val();
        var start_time  = $('#task_'+taskId).find('.start_time').val();
        $.ajax({
            url: '/tasks/edit/push/'+ taskId,
            type : 'POST',
            dateType : 'json',
            timeout: 5000,
            data:{
                body : body,
                start_time : start_time,
            },
            beforeSend : function() {
                $('#task_' + taskId +' .edit-push').html('<img src="/img/ajax-loader.gif" alt="" />');
            },
            success : function(data, textStatus){
                data = $.parseJSON(data);
                //バリデーションエラー
                if (data.error === true ) {
                    //エラー内容取り出し $ エラーポップ
                    for (i in data.message.body) {
                        //ポップアップ通知
                        popUpPanel(200, data.error, data.message.body[i]);
                    }

                    return;
                }
                //正常時
                var elm = htmlAddElm(data);
                //トップページか、viewページかで挿入場所場合分け
                //viewページの場合
                if ( $('#task_' + data.result.Task.id).parent().hasClass('children-ul')) {
                    $('#task_' + data.result.Task.id).hide().after(elm);
                    $('#task_' + data.result.Task.id).remove();
                    $('#task_' + data.result.Task.id).fadeIn(50);
                    if(isOrigin) {
                        $('#task_' + data.result.Task.id).addClass('origin');
                    }
                //トップページの場合
                } else {
                    //編集前のリストがいた日にちの場所
                    oldDay = $('#task_' + data.result.Task.id).parent().attr('id').substr(10);
                    addDay = getAddDay(data.result.Task.start_time);
                    //日付が変更されないならその場で挿入、されるなら適切な場所に挿入
                    if (oldDay == addDay) {
                        $('#task_' + data.result.Task.id).hide().after(elm);
                        $('#task_' + data.result.Task.id).remove();
                        $('#task_' + data.result.Task.id).fadeIn('slow');
                    } else {
                        $('#task_' + data.result.Task.id).remove();
                        //start_timeによって適切な場所に
                        appendToDay(data.result.Task.start_time, elm);
                        $('#task_' + data.result.Task.id).fadeIn('slow');
                    }
                }

                //通知：内容が変更されていれば通知だす
                if (body != data.result.Task.body || start_time != data.result.Task.start_time) {
                    popUpPanel(false, 'タスクを変更しました');
                }
                $('#task_'+data.result.Task.id).removeClass('edit');

                //<<Divide Push Ajax Start>>
                var divideArr   = [];
                var divideCount = Number($('ul[data-parent-id='+taskId+']').find('.li-divide').length);

                for ( var i = 0; i <= divideCount - 1; i++) {
                    divideArr.push(
                        {
                            parent_id   : taskId,
                            body        : $('ul[data-parent-id='+taskId+']').find('.li-divide').eq(i).find('.body').val(),
                            start_time  : $('ul[data-parent-id='+taskId+']').find('.li-divide').eq(i).find('.start_time').val(),
                        }
                    )
                }
                var json = JSON.stringify(divideArr);
                $.ajax({
                    url : '/tasks/divide/'+ taskId,
                    type : 'POST',
                    dataType : 'json',
                    timeout : 5000,
                    data : {
                        json : json
                    },
                    beforeSend : function() {
                        //分割タスクが入力されてないと終了
                        divideStart = true;
                        $('.li-divide .edit-input').each(function(){
                            if ($(this).val().length == 0) {
                                divideStart = false;
                            }
                        });
                        if (!divideStart) {
                            return false;
                        }
                    },
                    success : function(data) {
                        //バリデーションエラー
                        if (data.error === true ) {
                            //エラー内容取り出し $ エラーポップ
                            for (i in data.message) {
                                //ポップアップ通知
                                popUpPanel(data.error, data.message[i]);
                            }

                            return;
                        }
                        //view表示の場合
                        if ($('#task_' + data.result[0].Task.parent_id).parent().hasClass('children-ul')) {
                            //分割を追加するタスクがすでに子持ちかどうか判別
                            //持ってない場合children-ulを作る
                            if (!$('#task_'+ data.result[0].Task.parent_id).next().hasClass('children-ul')) {
                                $('#task_'+ data.result[0].Task.parent_id).after(
                                    '<ul class="children-ul" data-children-ul-id="'+data.result[0].Task.parent_id+'"></ul>'
                                );
                            }
                            for(var i in data.result) {
                                $('ul[data-children-ul-id='+data.result[0].Task.parent_id+']')
                                .append(
                                    '<li id="task_'+data.result[i].Task.id+'" class="list-group-item notyet clearfix" style="display:none;" data-task-id="'+ data.result[i].Task.id +'">\n' +
                                    '<span class="check-task"><input type="checkbox"></span>\n'+
                                    '<span class="body edit-task"><a href="/tasks/view/' + data.result[i].Task.id + '">'+ data.result[i].Task.body +'</a></span>\n' +
                                    '<span class="status">notyet</span>\n'+
                                    '<span class="d_param">'+ data.result[i].Task.d_param +'</span>\n'+
                                    '<span class="delete-task"><span class="glyphicon glyphicon-trash"></span><b>削除</b></span>\n' +
                                    '<span class="start_time">'+ roundStartTime(data.result[i].Task.start_time) +'</span>\n'+
                                    '</li>'
                                );
                                $('#task_'+data.result[i].Task.id).fadeIn(100);
                            }
                            //分割生成元が教祖出ない場合、アイコンを開閉に置き換え
                            if($('#task_'+ data.result[0].Task.parent_id).hasClass('origin')) {
                                $('#task_'+data.result[0].Task.parent_id).find('.check-task').replaceWith('<span class="origin glyphicon glyphicon-flag"></span>');
                            } else {
                                $('#task_'+data.result[0].Task.parent_id).find('.check-task').replaceWith('<span class="accordion spread glyphicon glyphicon-expand"></span>');
                            }
                        //トップページの場合
                        }else {
                            // $('#task_'+taskId).hide();
                            $('#task_'+taskId).remove();
                            for(var i in data.result) {
                                var elm =$(
                                    '<li id="task_'+data.result[i].Task.id+'" class="list-group-item notyet clearfix" style="display:none;" data-task-id="'+ data.result[i].Task.id +'">\n' +
                                    '<span class="check-task"><input type="checkbox"></span>\n'+
                                    '<span class="body edit-task"><a href="/tasks/view/' + data.result[i].Task.id + '">'+ data.result[i].Task.body +'</a></span>\n' +
                                    '<span class="delete-task"><span class="glyphicon glyphicon-trash"></span><b>削除</b></span>\n' +
                                    '<span class="start_time">'+ roundStartTime(data.result[i].Task.start_time) +'</span>\n'+
                                    '<div class="bread-crumb">'+ data.result[i].Task.breadcrumb +'</div>'+
                                    '</li>'
                                );

                                $.when(appendToDay(data.result[i].Task.start_time, elm)).then(createEmpty());
                                $('#task_'+data.result[i].Task.id).fadeIn(10);
                            }
                        }


                        // $('#task_'+ data.result.Task.id).fadeIn('slow');

                        //通知ポップ
                        popUpPanel(false, 'タスクを追加ました');

                    },
                    error : function(){
                        popUpPanel(true, 'サーバーエラー');
                    },
                    complete : function() {
                    }
                });

                $('ul[data-parent-id='+data.result.Task.id+']').fadeOut(150, function(){
                    $(this).remove();
                })

            },
            error : function() {
                //dom生成
                var elm =$(
                    '<span class="check-task"><input type="checkbox"></span>\n'+
                    '<span class="body edit-task"><a href="/tasks/view/' + taskId + '">'+ body +'</a></span>\n' +
                    '<span class="start_time">'+ start_time +'</span>\n'+
                    '<span class="status">'+ status +'</span>\n'+
                    '<span class="d_param">'+ d_param +'</span>\n'+
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
                if (data.result.Task.status == 'notyet') {
                    $('#task_'+taskId).removeClass('done').addClass('notyet');
                    //最終的に消す
                    $('#task_'+taskId).find('.body').addClass('edit-task');

                //チェックを入れた時
                } else if (data.result.Task.status == 'done'){
                    $('#task_'+taskId).removeClass('notyet').addClass('done');
                    //最終的に消す
                    $('#task_'+taskId).find('.body').removeClass('edit-task');

                    checked = 'checked';
                }
                adjustDBar(data.all_d);
                adjustattainment(data.attainment);
                if($('#task_'+data.result.Task.id).parent().hasClass('children-ul')){
                    adjustCheckBtn(data.result.Task.id);
                }

            },
            error : function() {
                popUpPanel(true, 'サーバーエラー')
            },
            complete : function() {
                $('#task_' + taskId +' .check-task').html('<input type="checkbox" '+ checked +'/>');
            },
        });
    });

    function adjustDBar(dbar) {
        for(var id in dbar) {
            $('#d-bar .parent_'+id).css({'width':dbar[id]+'%'});
            $('#d-bar .parent_'+id).html(Math.round(dbar[id])+'kg');
        }
    }

    function adjustattainment(attainment) {
        for(var id in attainment) {
            if(attainment[id] != 100) {
                $('.parent_'+id+' .attainment').html(Math.round(attainment[id])+'%');
                $('.parent_'+id+' .attainment').removeClass('complete btn btn-danger');
            } else {
                $('.parent_'+id+' .attainment').html('Complete!!');
                $('.parent_'+id+' .attainment').addClass('complete btn btn-danger');
            }

        }
    }

    //Edit input Enter-Btn to sumit
    $(document).on('keydown', '.edit-input', function(e){
        if (13 == e.which /*&& e.which == keyDownCode*/) {
            var id = $(this).parent().parent().data('task-id');
            $('#task_'+id).find('.edit-push').click();
        }
    });

    //Clean UP bombed Task
    $(document).on('click', '#clean-bomb', function(e){
        cancelEvent(e);
        var cleanArr = new Array();

        $('#task-list-parents li.parent_bomb').each(function(){
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
                    $('.parent_'+data.result[i]).fadeOut('slow',function(){
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
                        +$('.parent_'+ data.result[i]).find('.body').text()
                        +'</a></span>'
                        +'<span class="delete-task"><span class="glyphicon glyphicon-trash"></span><b>削除</b></span></li>'
                    );
                }
                $('#task-list-bombs').children().fadeIn('slow');
            },
            error : function(){
                popUpPanel(true, 'サーバーエラーでタスクを消去できませんでした');
            },
            complete : function(){
                $('#clean-bomb').remove();
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
                    if (data.result[i].status == 'done') {checked = 'checked'};
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
        if (!edit_flg) {
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
                if (data.result.Task.status == 'notyet') {
                    var editElm = '<a data-cal-task-id="'+data.result.Task.id+'" class="edit-cal-task action-icon"><span class="glyphicon glyphicon-edit"></span>編集</a>\n';
                    var checked = '';
                } else {
                    var editElm = '';
                    var checked = 'checked';
                }
                $('#calendarPanel').addClass(data.result.Task.status).append(
                    '<div class="body-area">\n'+
                    // '<p class="task-body"><span class="check-cal-task" data-cal-task-id="'+data.result.Task.id+'"><input type="checkbox" '+checked+'/></span><span class="body">'+data.result.Task.body+'</span></p>\n'+
                    '<p class="task-body"><span class="body">'+data.result.Task.body+'</span></p>\n'+
                    '<p class="task-date"><span>'+data.result.Task.start_time+'</span></p>\n'+
                    '<p class="bread-crumb">'+data.breadcrumb+'</p>'+
                    '</div>\n'+
                    '<div class="action-area clearfix">\n'+
                    editElm +
                    '<a class="action-icon calendar-link" href="/calendars/selectcalendar/'+data.result.Task.id+'"><span class="glyphicon glyphicon-calendar"></span>このプロジェクトのみ表示</a>'+
                    '<a data-cal-task-id="'+data.result.Task.id+'" class="delete-cal-task action-icon"><span class="glyphicon glyphicon-trash"></span>削除</a>\n'+
                    '</div>\n'+
                    '\n'+
                    '<a class="cal-panel-cancel"><span class="glyphicon glyphicon-remove"></span></a>\n'
                );
                var windowWidth = $(window).width();
                var windowHeight = $(window).height();
                var panelWidth = $('#calendarPanel').width();
                var panelHeight = $('#calendarPanel').height();
                if ((50 + panelHeight + 40) > y) { //パネルが上にはみでちゃうパターン
                    y += 40;
                } else {
                    y -= (40 + panelHeight);
                }
                if ((20 + (panelWidth / 2)) > x) { //パネルが左にはみでちゃうパターン
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
    $('#calendarPanel').draggable();

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
                // $('#calendarPanel .task-body').html('<span><input data-cal-task-id="'+data.result.id+'" type="checkbox" /></span><span class="body">'+data.result.body+'</span>')
                $('#calendarPanel .task-body').html('<span class="body">'+data.result.body+'</span>')
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
                if (old_day != data.result.start_time) {
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
        if (!confirm('「'+$('#task_'+id).text() + '」を削除しますか？')){
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
                popUpPanel(false, '「'+data.result.Task.body+'」を削除しました');
                $('#task_'+data.result.Task.id).fadeOut(200,function(){
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
                $('#calendarPanel').removeAttr('class').addClass(data.result.Task.status);
                if (data.result.Task.status == 'done') {
                    $('#calendarPanel .action-area .edit-cal-task').remove();
                    $('#task_'+data.result.Task.id).removeClass('notyet').addClass('done');
                    checked = 'checked';
                } else {
                    $('#calendarPanel .action-area').prepend(
                        '<a data-cal-task-id="'+data.result.Task.id+'" class="edit-cal-task action-icon"><span class="glyphicon glyphicon-edit"></span>編集</a>'
                    );
                    $('#task_'+data.result.Task.id).removeClass('done').addClass('notyet');
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
            if (count == 1) {
                date = start_date;
            } else if (count == 2) {
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
            if (count == 1) {
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

    //一族リストのアコーディオン
    $(document).on('click','.accordion',function(e){
        // cancelEvent(e);
        var id = $(this).parent().data('task-id');
        $(this).toggleClass('spread');
        $('ul[data-children-ul-id='+id+']').animate({
            height: 'toggle'
        },250);
        $('#task_'+id).toggleClass('close-ul');
        $('ul[data-children-ul-id='+id+']').toggleClass('close-ul');
    })

    $(document).on('click','#tryagain', function(e){
        cancelEvent(e);
        var taskId      = $(this).parent().data('task-id');
        $.ajax({
            url : '/tasks/tryagain/' + taskId,
            type : 'POST',
            dataType : 'json',

            beforeSend : function() {
                $('#bomb_'+taskId+' #tryagain').html('<img src="/img/ajax-loader.gif" alt="" />');
            },
            success : function(data){
                if(data.error) {
                    popUpPanel(true, data.message.body);
                } else {
                    $('#bomb_'+taskId).fadeOut('slow',function(){
                        $.when($(this).remove()).then(createEmpty());
                    })
                    deleteEmpty('bombs');
                    $('#projects li:eq(-2)').after(
                        '<li class="parent_'+taskId+' jshover parent_notyet list-group-item clearfix" style="display:none;" data-task-id="'+taskId+'">'+
                        '<span class="body"><a href="/tasks/view/' + taskId + '">'+$('#bomb_'+taskId).find('.body').text() +'</a></span>\n'+
                        '<span class="attainment">0%</span>'+
                        '<span class="selfbomb"><b>自爆</b></span></li>'
                    );
                    $('#projects li:eq(-2)').fadeIn('slow');
                    var elm2 =$('<div class="add-bar progress-bar progress-bar-danger" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100" style="width: 10%; display:none;">10kg</div>');

                    $('#d-bar').append(elm2);
                    $('.add-bar').fadeIn('slow');
                }
            },
            error : function() {
                popUpPanel(true, 'サーバーエラーでタスクを消去できませんでした');
            },
            complete : function() {
                $('#bomb_'+taskId+' #tryagain').html('今度こそ！');
            }
        })
    });

    $(document).on('click', '.selfbomb', function(e){
        cancelEvent(e);
        if (!confirm('このプロジェクトを爆発させます')){
            return false;
        }
        var taskId = $(this).parent().data('task-id');
        $.ajax({
            url : '/tasks/selfbomb/'+taskId,
            type : 'POST',
            dataType : 'json',
            timeout : 5000,
            beforeSend : function(){
                $('.parent_'+taskId+' .selfbomb').html('<img src="/img/ajax-loader.gif" alt="" />');
            },
            success : function(data){
                $('.parent_'+taskId).fadeOut('slow',function(){
                    $.when($(this).remove()).then(createEmpty());
                })
                deleteEmpty('bombs');
                $('#task-list-bombs').append(
                    '<li id="bomb_'+taskId
                    +'" class="bomb list-group-item clearfix" style="display:none" data-task-id="'
                    +taskId+'">'
                    +'<span class="body"><a href="/tasks/view/'
                    +taskId+ '">'
                    +$('.parent_'+taskId).find('.body').text()
                    +'</a></span>'
                    +'<p id="tryagain" class="btn btn-danger">今度こそ！</p></li>'
                );
                $('#task-list-bombs').children().fadeIn('slow');
                adjustDBar(0);
            },
            error : function(){
                popUpPanel(true, 'サーバーエラーでタスクを消去できませんでした');
            },
            complete : function(){
                $('.parent_'+taskId+' .selfbomb').html('自爆');
            },
        });
    });

    $(document).on('click', '.complete', function(e){
        cancelEvent(e);
        var taskId = $(this).parent().data('task-id');
        $.ajax({
            url : '/tasks/complete/'+taskId,
            type : 'POST',
            dataType : 'json',
            timeout : 5000,
            beforeSend : function(){
                $('.parent_'+taskId+' .complete').html('<img src="/img/ajax-loader.gif" alt="" />');
            },
            success : function(data){
                $('.parent_'+taskId).fadeOut('slow',function(){
                    $.when($(this).remove()).then(createEmpty());
                })
                deleteEmpty('complete');
                $('#task-list-complete').append(
                    '<li id="complete_'+taskId
                    +'" class="list-group-item clearfix" style="display:none" data-task-id="'
                    +taskId+'">'
                    +'<span class="body"><a href="/tasks/view/'
                    +taskId+ '">'
                    +$('.parent_'+taskId).find('.body').text()
                    +'</a></span></li>'
                );
                $('#task-list-complete').children().fadeIn('slow');
                adjustDBar(0);
            },
            error : function(){
                popUpPanel(true, 'サーバーエラーでタスクを消去できませんでした');
            },
            complete : function(){
                $('.parent_'+taskId+' .complete').html('Complete!!');
            },
        });
    });
});
