#!/bin/bash

mkfifo /home/everyone/mpvfifo
mpv -input file=/home/everyone/mpvfifo </dev/null >/dev/null 2>&1 "${1}" &

echo "Playing!" >> testfile

