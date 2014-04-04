<html>
  <head>
    <title>play something</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <link href="style.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript">
        function hideControls()
        {
            parent.document.getElementById("header").style.display='none';
        }
        function showControls()
        {
            parent.document.getElementById("header").style.display='block';
        }
    </script>
  </head>

<body>
  <div id="viewmanagement">
    <input type='button' id='showhide' value='hide controls' onclick="hideControls();">
    <input type='button' id='showhide' value='show controls' onclick="showControls();">
  </div>



</body>
</html>

<!-- vim: shiftwidth=2 softtabstop=2 tabstop=2
