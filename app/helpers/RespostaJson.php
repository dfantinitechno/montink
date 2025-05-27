<?php

if (!function_exists('responderJson')) {
    function responderJson(int $statusHttp, array $dados): void {
        http_response_code($statusHttp);
        header("Content-Type: application/json");
        echo json_encode($dados);
        exit;
    }
}
