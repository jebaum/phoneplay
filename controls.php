<html>
  <head>
    <title>play something</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <link href="style.css" rel="stylesheet" type="text/css" />
<body>
  <div id="header">
<?php
  $linkpath = $_GET['path'];
  if (is_file($linkpath)) {
    $linkpath = dirname($linkpath);
  }

  $linkpath = urlencode($linkpath);

  print("<a href='?cmd=tvon&path=".$linkpath."' class='button'>on</a>\n");
  print("<a href='?cmd=tvoff&path=".$linkpath."' class='button'>off</a>\n");
  print("<a href='?cmd=move&path=".$linkpath."' class='button'>move</a>\n");
  print("<a href='?cmd=reset&path=".$linkpath."' class='button'>reset</a>\n");
  print("<br />\n");

  print("<a href='browse.php' target='browse' class='button'>home</a>\n");
  print("<a href='?cmd=stop&path=".$linkpath."' class='button'>stop</a>\n");
  print("<a href='?cmd=pause&path=".$linkpath."' class='button'>play/pause</a>\n");
  print("<a href='?cmd=volumeup&path=".$linkpath."' class='button'>vol up</a>\n");
  print("<a href='?cmd=volumedown&path=".$linkpath."' class='button'>vol down</a>\n");
  print("<br />\n");

  print("<a href='?cmd=smallforward&path=".$linkpath."' class='button'>+10</a>\n");
  print("<a href='?cmd=smallback&path=".$linkpath."' class='button'>-10</a>\n");
  print("<a href='?cmd=mediumforward&path=".$linkpath."' class='button'>+60</a>\n");
  print("<a href='?cmd=mediumback&path=".$linkpath."' class='button'>-60</a>\n");
  print("<a href='?cmd=bigforward&path=".$linkpath."' class='button'>+600</a>\n");
  print("<a href='?cmd=bigback&path=".$linkpath."' class='button'>-600</a><br />\n");

?>

<!--<a class="button" style="padding: 3px 5px 3px 5px;"><img src="play_small.png" /></a><br />-->
  </div>

    <?php
      // TODO:
      //       show current state (played/paused/stopped, current file, progress) and volume somewhere
      //       playlist file that gets displayed somewhere, can be added to or removed from
      //         mpv iterates down this list
      //         click on item in the list to skip to that point in the list
      //       "add all in directory" function
      //       support entering youtube urls into some textbox
      //       bash script loop to check return of i3-msg focus for mpv, and move to 13: tv once we have focus
      //       slightly different colors for files and directories, and maybe move the 'back' link?
      $path = $_GET['path'];
      $cmd = $_GET['cmd'];
      function send_mpv_cmd($str)
      {
        $fifo = '/home/everyone/mpvfifo';
        $f = fopen($fifo, "w");
        if (!$f) die ("unable to open pipe");
        $o = fwrite($f, $str."\n");
        fclose($f);

        if ($o === FALSE)
          die("unable to write to the pipe");

        return $o;
      }
      if ($cmd) {
        switch ($cmd)
        {
          case 'tvon':
            // shell_exec('DISPLAY=:0.0 hdmi.sh on');
            // shell_exec('DISPLAY=:0.0 mpv -input file=/home/everyone/mpvfifo &');
            break;

          case 'tvoff':
            // shell_exec('DISPLAY=:0.0 hdmi.sh off');
            // shell_exec('killall mpv');
            // shell_exec('/usr/bin/device.sh xfi');
            break;

          case 'reset':
            shell_exec('killall mpv');
            shell_exec('rm /home/everyone/mpvfifo');
            shell_exec('mkfifo /home/everyone/mpvfifo');
            shell_exec('DISPLAY=:0.0 mpv --profile=hdmi >/home/everyone/log &');
            break;

          case 'move':
            shell_exec('DISPLAY=:0.0 i3-msg \[class="mpv"\] focus');
            shell_exec('DISPLAY=:0.0 i3-msg move workspace number 13: tv');
            shell_exec('DISPLAY=:0.0 i3-msg fullscreen');
            break;

          case 'smallback':     send_mpv_cmd("seek -10 0"); break;
          case 'mediumback':    send_mpv_cmd("seek -60 0"); break;
          case 'bigback':       send_mpv_cmd("seek -600 0"); break;
          case 'smallforward':  send_mpv_cmd("seek +10 0"); break;
          case 'mediumforward': send_mpv_cmd("seek +60 0"); break;
          case 'bigforward':    send_mpv_cmd("seek +600 0"); break;
          case 'volumeup':      send_mpv_cmd("volume +10"); break;
          case 'volumedown':    send_mpv_cmd("volume -10"); break;
          case 'stop':          send_mpv_cmd("stop"); break;
          case 'pause':         send_mpv_cmd("cycle pause"); break;
          case 'play':          send_mpv_cmd("pause"); break;
        }
      }

?>
</body>
</html>

<!-- vim: shiftwidth=2 softtabstop=2 tabstop=2
