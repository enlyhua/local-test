<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/14
 * Time: 上午12:57
 */

namespace ctrl;

class Login
{
    public function login($request)
    {
        $post = isset($request->post) ? $request->post : [];
        return 'login sucess';
    }
}