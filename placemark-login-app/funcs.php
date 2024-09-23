<?php
// funcs.php

function sschk()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // セッションが設定されていないか、セッションIDが一致しない場合
    if (!isset($_SESSION["chk_ssid"]) || $_SESSION["chk_ssid"] != session_id()) {
        header("Location: login.php"); // ログインページにリダイレクト
        // exit("Login Error"); // ログインエラーのメッセージを表示して終了
    } else {
        // セッションIDの再生成と、再生成後のIDをセッションに保存
        session_regenerate_id(true);
        $_SESSION["chk_ssid"] = session_id();
    }
}

?>