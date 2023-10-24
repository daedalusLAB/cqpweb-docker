<?php

# get as input: file={{video_id}}&start={{start}}&end={{end}}&api_key={{api_key}}
# and return the part of the video between start and end

# Create array with API Keys 
# Testing API keys
$API_KEYS = array(
    "1" => "9Rdu1aTASl5xHknYRD4Z9rh9CkvHI0n3tm4lRoNh",
    "2" => "oLBaEjd3OXipgWYYFHBc5dYIqKMqTrMDaLQGF5cl",
    "3" => "7b7UHzfyD7wjyktqJ6D9TTjPSmzkFLRANARRWI34"
);

# check if API key is valid
$api_key = $_GET['api_key'];
if (!in_array($api_key, $API_KEYS)) {
    echo "Error: API key is not valid";
    exit;
}

# get $VIDEOS_PATH and $TMP_PATH from ENV variables if are defined if not use default values
if (getenv('VIDEOS_PATH') != false) {
    $VIDEOS_PATH = getenv('VIDEOS_PATH');
} else {
    $VIDEOS_PATH = "/var/www/data";
}

if (getenv('TMP_PATH') != false) {
    $TMP_PATH = getenv('TMP_PATH');
} else {
    $TMP_PATH = "/var/www/tmp";
}

$video = $_GET['file'];
$start = $_GET['start'];
$end = $_GET['end'];


# use FILTER_SANITIZE with $video, $start, $end
$video = filter_var($video, FILTER_SANITIZE_STRING);
$start = filter_var($start, FILTER_SANITIZE_STRING);
$end = filter_var($end, FILTER_SANITIZE_STRING);



# check $file has .mp4 extension or .json extension
if ( (substr($video, -5) != '.json') && (substr($video, -4) != '.mp4') ){
    echo "Error: Extension error";
    exit;
}

# if $video ends with .mp4, change to .json 
if (substr($video, -5) == '.json') {
    $video = substr($video, 0, -5);
    $video = $video . ".mp4";
}

# having video filename 2023-10-18_1410_ES_La1_AhoraONunca.mp4 and $VIDEOS_PATH
# return $VIDEOS_PATH/2023/2023-10/2023-10-18/2023-10-18_1410_ES_La1_AhoraONunca.mp4
$video_full_path = $VIDEOS_PATH . "/" . substr($video, 0, 4) . "/" . substr($video, 0, 7) . "/" . substr($video, 0, 10) . "/" . $video;



# check $start and $end are numbers
if (!is_numeric($start) || !is_numeric($end)) {
    echo "Error: start and end must be numbers";
    exit;
}

# check $start and $end are positive
if ($start < 0 || $end < 0) {
    echo "Error: start and end must be positive";
    exit;
}

# check $start is smaller than $end
if ($start >= $end) {
    echo "Error: start must be smaller than end";
    exit;
}

# remove .mp4 extension from $video
$videonoextension = substr($video, 0, -4);

# check $video_full_path exists - .json + .mp4 exist
if (!file_exists(substr($video_full_path, 0, -4) . ".mp4")) {
    #echo "Error: file " . substr($video_full_path, 0, -4) . ".mp4" . " does not exist";
    echo "Error: file does not exist";
    exit;
}

# check end - start is smaller than 30
if ($end - $start > 30) {
    echo "Error: video must be smaller than 30 seconds";
    exit;
}

# Check if file already exists IN tmp folder
if (!file_exists("$TMP_PATH/$videonoextension" . "_" . "$start" . "_" . "$end.mp4")) {
    $command = "ffmpeg -i $video_full_path   -ss $start -to $end  $TMP_PATH/$videonoextension" . "_" . "$start" . "_" . "$end.mp4 -y ";
    exec($command);
}

# return the video as .mp4 file
$file_size = filesize("$TMP_PATH/$videonoextension" . "_" . "$start" . "_" . "$end.mp4");
header('Content-Type: video/mp4');
header('Content-Length: ' . $file_size);
header('Content-Disposition: inline; filename="' . $videonoextension . "_" . $start . "_" . $end . '.mp4"');
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');
readfile("$TMP_PATH/$videonoextension" . "_" . "$start" . "_" . "$end.mp4");

# ramdomize: 1 on 100 times, check for files in $TMP_PATH older than 1 week and delete them
if (rand(1, 100) == 1) {
    $files = glob("$TMP_PATH/*");
    $now = time();
    foreach ($files as $file) {
        if (is_file($file)) {
            if ($now - filemtime($file) >= 60 * 60 * 24 * 7) { // 7 days
                unlink($file);
            }
        }
    }
}

?>