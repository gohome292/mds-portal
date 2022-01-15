<?php
function getStatusName($status){
    
        switch ($status) {
        // 作成中
        case '1':
            $status_name = '作成中';
            break;
        // 申請中
        case '2':
            $status_name = '提出済';
            break;
        // 受付済み
        case '3':
            $status_name = '受付済み';
            break;
        // 完了
        case '4':
            $status_name = '完了';
            break;
        // その他
        default:
            $status_name = null;
            break;
        }
        return($status_name);

}