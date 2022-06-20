<?php
############################################################################################################################################################
# Raúl Sánchez <raul@um.es> 
# This file checks every 10 seconds if the .zip file is downloaded. If it is, show a link to download it.
############################################################################################################################################################

$file = $_GET['file'];
$download_url = $_GET['download_url'];

# get path from download_url
$path = parse_url($download_url, PHP_URL_PATH);


if (empty($file)) {
    echo "";
} else {
   
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body, html {
  height: 100%;
  margin: 0;
}

.bg {
  /* The image used */
  background-image: url("/CQPweb/downloads/library.jpg");

  /* Full height */
  height: 100%; 

  /* Center and scale the image nicely */
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
}

/*# h1 with color white and in the 25% up of thee page */
h1 {
  color: #CCCCCC;
  position: absolute;
  top: 25%;
  left: 50%;
  transform: translate(-50%, -50%);
}

h3 {
  color: #CCCCCC;
  position: absolute;
  top: 30%;
  left: 50%;
  transform: translate(-50%, -50%);
}

a {
  color: black;
  text-decoration: none;
  font: 20px/30px Helvetica, Sans-Serif;
}

#button {
  background-color: #eeeeee;
  position: absolute;
  color: black;
  cursor: pointer;
  top: 40%;
  left: 50%;
  transform: translate(-50%, -50%);
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 26px;
  border-radius: 8px;
}

</style>

<script>

/* Set interval function to check if the file is downloaded each 5 seconds 
and show button with id = button in html page */
  setInterval(function() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("button").style.display = "block";
            document.getElementById("text3").innerHTML = "Your zip file is downloaded. Click the button to download it.";
            /* set interval function to 0 seconds*/
            console.log("Text changed");
            isdone = true;
        }
    };
    xhttp.open("GET", "<?php echo $path; echo $file; ?>", true);
    xhttp.send();
  }, 1000) 
</script>
</head>
<body>

<div class="bg">
  <h1 id="text1">Thanks for using getClips plugin for CQPweb</h1>
  <h3 id="text3">Please wait. Your zip file is downloading...</h3>
  <!-- Hide button with downloadurl until download is complete -->
  <div id="button" style="display: none;">
    <a href="<?php echo ("$download_url"); echo ($file) ?> ">Download</a>    
  </div>
</div>

</body>

</html>

<?php
}
?>