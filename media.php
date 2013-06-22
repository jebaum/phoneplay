<html>
  <head>
    <title>play something</title>
    <link href="phoneplay.css" rel="stylesheet" type="text/css" />

<script>
  function fetchstate()
  {
    document.getElementById("stateinfo").innerHTML=("tits");
    if (window.XMLHttpRequest)
    { // IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp=new XMLHttpRequest();
    }
    else
    { // IE6, IE5
      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function()
    {
      if (xmlhttp.readyState==4 && xmlhttp.status==200)
      {
        document.getElementById("stateinfo").innerHTML=xmlhttp.responseText;
      }
    }
    xmlhttp.open("GET","fetchstate.php",true);
    xmlhttp.send();
  }

  // if a bunch of people view the page at once, multiple people are calling this every 4 seconds
  // this can cause fetchstate.sh to be run in parallel, and output info isn't parsed correctly
  // will need more sophisticated fetchstate function to:
  //      only fetch needed info (when a thing is playing, don't need to keep fetching its name until a new thing plays)
  //      parse the entire output of commands sent to mplayer instead of just stripping them, so right information always gets displayed
  //      ideal behavior would be daemon like: fetchstate.sh is only run once and continually feeds info to the page, but isn't called by everyone viewing the page. i don't know how to do this yet

  window.setInterval(function()
  {
    fetchstate();
  }, 4000);
</script>
  </head>

<body>
  <div id="header">
    <a href="?">home</a>
    <a href="?cmd=stop">stop</a>
    <a href="?cmd=pause">toggle</a>
    Volume: <a href="?cmd=volumeup">up</a> / <a href="?cmd=volumedown">down</a>
    <hr />
  </div>

  <div id="content"> <br />
<p><span id="stateinfo"></span></p>

<br /><hr /><br />
    <?php

      // TODO: functions for other commands (volume, seeking, etc)
      //       frames or some kind of mechanism so directory remains persistent
      //       show current state (played/paused/stopped, current file, progress) and volume somewhere
      //       playlist file that gets displayed somewhere, can be added to or removed from
      //         mplayer iterates down this list
      //         click on item in the list to skip to that point in the list
      //       "add all in directory" function
      //       hide files that aren't mp3s or videos
      //       css so it's less ugly
      //       support entering youtube urls into some textbox
      //       if mplayer isn't running with playserver settings, say so
      //         should you still be able to edit the playlist if playserver isn't running?
      $path = $_GET['path'];
      $cmd = $_GET['cmd'];
      function send_mplayer_cmd($str)
      {
        $fifo = '/home/everyone/mplayerfifo';
        $f = fopen($fifo, "w");
        if (!$f) die ("unable to open pipe");
        $o = fwrite($f, $str."\n");
        fclose($f);

        if ($o === FALSE)
        {
          die("unable to write to the pipe");
        }

        return $o;
      }

      if ($cmd == 'volumeup')
      {
        send_mplayer_cmd("volume +1");
      }

      if ($cmd == 'volumedown')
      {
        send_mplayer_cmd("volume -1");
      }

      if ($cmd == 'stop')
      {
        send_mplayer_cmd("stop");
      }

      if ($cmd == 'pause')
      {
        send_mplayer_cmd("pause");
      }

      if ($cmd == 'play')
      {
        send_mplayer_cmd("pause");
      }

      # cmd empty, start a video
      if (is_file($path))
      {
        # turn screen on
        system('DISPLAY=:0 /usr/bin/sudo -E -u ren /usr/bin/xset dpms force on');
        send_mplayer_cmd("loadfile \"$path\"");
        //print("<a class=\"pause\" href=\"?path=".urlencode($path)."&cmd=pause\">pause</a>\n");
      }

      # default directory
      if (!is_dir($path))
        $path = '/home/everyone/';

      if ($h = opendir($path))
      {
        print("<ul>\n");
        while (false !== ($entry = readdir($h))) {
          print("<li><a href=\"?path=".urlencode(realpath("$path/$entry"))."\">$entry</a></li>\n");
        }
        closedir($handle);
        print("</ul>\n");
      }
      fetchState();
      exit(1);
    ?>
  </div>

</body>
</html>
