<?php
// App/Views/Elements/Ajaxs/ajaxupdated.ctp

//バリデーション失敗
if (!empty( $valerror)){
    //クライアントでバリデーションエラーか識別するためのフラグ
    $valerror['error'] = true;
    echo $this->Js->object($valerror);
} else {
    //クライアントでバリデーションエラーか識別するためのフラグ
    $result['error'] = false;

    //CSRF通過ようにjsヘルパーでポストリンク作ってタスク生成時の削除ボタンにする
    $delBtn = $this->Form->postLink(__('削除'), array('action' => 'delete', $result['Task']['id']), array('class' =>'delete-task'), __('本当に消しちゃっていいの？まあ最初からやらんと思ってたけどな。', $result['Task']['id']));
    $result['delBtn'] = $delBtn;

    //正常時値を返す
    echo $this->Js->object($result);

    // echo $this->Js->object($result);
    //echo h( $this->data['Task']['body']). 'が追加されました';
}