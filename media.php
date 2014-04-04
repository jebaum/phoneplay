<html>
  <head>
    <title>play something</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <link href="style.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript">
      var toggle = function(elementid) {
        var div = document.getElementById(elementid);
        if (div.style.display !== 'none') {
          div.style.display = 'none';
        }
        else {
          div.style.display = 'block';
        }
      };
    </script>
  </head>

<body>
  <div id="viewmanagement">
    <input type='button' id='showhide' value='show/hide controls' onclick="toggle('header');">
    <input type='button' id='showhide' value='show/hide browsing' onclick="toggle('content');">
  </div>

<?php
    $hideheader = $_GET['hideheader'];
    if (!$hideheader)
      print("<div id='header'>");
    else
      print("<div id='header' style='display:none;'>");
?>
<?php
  $linkpath = $_GET['path'];
  if (is_file($linkpath)) {
    $linkpath = dirname($linkpath);
  }

  $linkpath = urlencode($linkpath);

  print("<a href='?cmd=tvon&path=".$linkpath."' class='button'>on</a>\n");
  print("<a href='?cmd=tvoff&path=".$linkpath."' class='button'>off</a>\n");
  print("<a href='?cmd=move&path=".$linkpath."' class='button'>move</a>\n");
  print("<a href='?cmd=reset&path=".$linkpath."' class='button'>reset</a><br />\n");
  print("<a href='?' class='button'>home</a>\n");
  print("<a href='?cmd=stop&path=".$linkpath."' class='button'>stop</a>\n");
  print("<a href='?cmd=pause&path=".$linkpath."' class='button'>play/pause</a><br />\n");

  print("<a href='?cmd=smallforward&path=".$linkpath."' class='button'>+10</a>\n");
  print("<a href='?cmd=smallback&path=".$linkpath."' class='button'>-10</a>\n");
  print("<a href='?cmd=mediumforward&path=".$linkpath."' class='button'>+60</a>\n");
  print("<a href='?cmd=mediumback&path=".$linkpath."' class='button'>-60</a>\n");
  print("<a href='?cmd=bigforward&path=".$linkpath."' class='button'>+600</a>\n");
  print("<a href='?cmd=bigback&path=".$linkpath."' class='button'>-600</a><br />\n");

?>
    Volume: <a href="?cmd=volumeup" class="button">up</a>
            <a href="?cmd=volumedown" class="button">down</a><br />

<!--<a class="button" style="padding: 3px 5px 3px 5px;"><img src="play_small.png" /></a><br />-->
  </div>

  <div id="content">

<br /><br />
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
