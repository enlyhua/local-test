#! /bin/bash

rsync -az --no-perms -O -e 'ssh -i ~/.ssh/id_rsa' -v --exclude=".idea" --exclude=".git" --exclude=vendor ./* root@192.168.0.105:/data/code/test
