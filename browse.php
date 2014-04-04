<html>
  <head>
    <title>play something</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <link href="style.css" rel="stylesheet" type="text/css" />
  </head>

<body>

  <div id="content">

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

      # cmd empty, start a video
      if (is_file($path))
      {
        shell_exec('killall mpv');
        shell_exec('DISPLAY=:0.0 i3-msg workspace number 13: tv');
        shell_exec("DISPLAY=:0.0 ./play.sh \"$path\"");
        send_mpv_cmd("loadfile \"$path\"");
        $path = dirname($path);
      }

      # default directory
      if (!is_dir($path))
        $path = '/home/everyone/';

      $directories = array();
      $files       = array();

      $dir_contents = scandir($path);
      foreach($dir_contents as $file)
      {
          if ($file != '.')
          {
              if(is_dir($path.'/'.$file))
              {
                  $directories[] = $file;
              }
              else
              {
                  $files[] = $file;
              }
          }
      }

      // sort case insensitively
      sort($directories, SORT_FLAG_CASE | SORT_STRING);
      sort($files, SORT_FLAG_CASE | SORT_STRING);
      print("<ul>\n");
      foreach($directories as $directory) {
          if ($directory != 'zmisc')
            print("<li><a class=\"entry\" href=\"?path=".urlencode(realpath("$path/$directory"))."\">$directory</a></li>\n");
      }

      foreach($files as $file) {
          if ($file != 'fetchstate.sh' && $file != 'mpvfifo')
            print("<li><a class=\"entry\" href=\"?path=".urlencode(realpath("$path/$file"))."\">$file</a></li>\n");
      }
      print("</ul>\n");

    ?>
  </div>

</body>
</html>

<!-- vim: shiftwidth=2 softtabstop=2 tabstop=2
