#! /usr/bin/env bash
# 当 swoole_server 接收到  USR1 时，会让自己所有的 worker 在处理完后正在处理的请求后重启。
ps aux | grep simple_route_master | awk '{print $2}' | xargs kill -USR1