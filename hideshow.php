<html>
  <head>
    <title>play something</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <link href="style.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript">
        function hideControls()
        {
            parent.document.getElementById("controlsframe").style.display='none';
            parent.document.getElementById("browseframe").height='88%';
        }
        function showControls()
        {
            parent.document.getElementById("controlsframe").style.display='block';
            parent.document.getElementById("browseframe").height='65%';
        }
    </script>
  </head>

<body>
  <div id="hideshow">
    <input type='button' id='showhide' value='hide controls' onclick="hideControls();">
    <input type='button' id='showhide' value='show controls' onclick="showControls();">
  </div>



</body>
</html>

<!-- vim: shiftwidth=2 softtabstop=2 tabstop=2
