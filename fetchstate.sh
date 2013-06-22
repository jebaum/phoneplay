#!/bin/bash

FIFO="/home/everyone/mplayerfifo";
LOG="/home/everyone/log";
STRIP="sed -e s/ANS[A-Z_]*=//g -e s/^'// -e s/'$//";
LINES="11";

# TODO: only need to fetch meta info if filename ends in mp3 (or flac?), otherwise we're dealing with video
echo "get_meta_title"  > $FIFO;
echo "get_meta_track"  > $FIFO;
echo "get_meta_artist" > $FIFO;
echo "get_meta_album"  > $FIFO;
echo "get_meta_genre"  > $FIFO;
echo "get_time_pos"    > $FIFO;
echo "get_time_length" > $FIFO;
echo "get_percent_pos" > $FIFO;
echo "get_file_name"   > $FIFO;

echo "get_video_bitrate"      > $FIFO
echo "get_video_resolution"   > $FIFO

cat $LOG | tail -$LINES | $STRIP;
