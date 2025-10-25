<?php

namespace App\Traits;

trait HttpResponse {
    
    protected function success($data, $message = null, $code = 200)
    {
        return response()->json([
            'status' => 'ok',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function failed($data, $message = null, $code)
    {
        return response()->json([
            'status' => 'failed',
            'message' => $message,
            'data' => $data
        ], $code);
    }
}