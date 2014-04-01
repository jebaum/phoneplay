<html>
  <head>
    <title>play something</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <link href="style.css" rel="stylesheet" type="text/css" />

  </head>

<body>
  <div id="header">
    <a href="?cmd=tvon">on</a> |
    <a href="?cmd=tvoff">off</a> |
    <a href="?cmd=move">move</a> |
    <a href="?cmd=reset">reset</a><br />
    <a href="?">home</a> |
    <a href="?cmd=stop">stop</a> |
    <a href="?cmd=pause">play/pause</a><br />

    <a href="?cmd=smallforward">+10</a> |
    <a href="?cmd=smallback">-10</a> |
    <a href="?cmd=mediumforward">+60</a> |
    <a href="?cmd=mediumback">-60</a> |
    <a href="?cmd=bigforward">+600</a> |
    <a href="?cmd=bigback">-600</a><br />


    Volume: <a href="?cmd=volumeup">up</a> / <a href="?cmd=volumedown">down</a>
  </div>

  <div id="content">

<br /><hr /><br />
    <?php
      // TODO: functions for other commands (volume, seeking, etc)
      //       frames or some kind of mechanism so directory remains persistent
      //       show current state (played/paused/stopped, current file, progress) and volume somewhere
      //       playlist file that gets displayed somewhere, can be added to or removed from
      //         mpv iterates down this list
      //         click on item in the list to skip to that point in the list
      //       "add all in directory" function
      //       hide files that aren't mp3s or videos
      //       support entering youtube urls into some textbox
      //       bash script loop to check return of i3-msg focus for mpv, and move to 13: tv once we have focus
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

      # cmd empty, start a video
      if (is_file($path))
      {
        shell_exec('killall mpv');
        shell_exec('DISPLAY=:0.0 i3-msg workspace number 13: tv');
        shell_exec("DISPLAY=:0.0 ./play.sh \"$path\"");
        send_mpv_cmd("loadfile \"$path\"");
      }

      # default directory
      if (!is_dir($path))
        $path = '/home/everyone/';

      if ($handle = opendir($path))
      {
        print("<ul>\n");
        while (false !== ($entry = readdir($handle))) {
            if ($entry != ".") {
                print("<li><a class=\"entry\" href=\"?path=".urlencode(realpath("$path/$entry"))."\">$entry</a></li>\n");
            }
        }
        closedir($handle);
        print("</ul>\n");
      }
      exit(1);
    ?>
  </div>

</body>
</html>
