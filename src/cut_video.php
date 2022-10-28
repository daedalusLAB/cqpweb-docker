<?php

# get as input: file={{video_id}}&start={{start}}&end={{end}}
# and return the part of the video between start and end

$VIDEOS_PATH="/var/www/data";

$video = $_GET['file'];
$start = $_GET['start'];
$end = $_GET['end'];

# use FILTER_SANITIZE with $video, $start, $end
$video = filter_var($video, FILTER_SANITIZE_STRING);
$start = filter_var($start, FILTER_SANITIZE_STRING);
$end = filter_var($end, FILTER_SANITIZE_STRING);


# check $file has .mp4 extension
if (substr($video, -4) != '.mp4') {
    echo "Error: file must be .mp4";
    exit;
}

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

# check $file exists
if (!file_exists("$VIDEOS_PATH/$video")) {
    echo "Error: file does not exist";
    exit;
}

# check end - start is smaller than 30
if ($end - $start > 30) {
    echo "Error: video must be smaller than 30 seconds";
    exit;
}


# use ffmpeg to cut $file between $start and $end
# and return the new file with name $video_start_end.mp4 in tmp folder
# use exec() to execute the command

# remove .mp4 extension from $video
$videonoextension = substr($video, 0, -4);

# create temporal video
$command = "ffmpeg -i $VIDEOS_PATH/$video -ss $start -to $end -c copy tmp/$videonoextension" . "_" . "$start" . "_" . "$end.mp4 -y ";
exec($command);


# return the video as .mp4 file and after that delete it
$file_size = filesize("tmp/$videonoextension" . "_" . "$start" . "_" . "$end.mp4");
header('Content-Type: video/mp4');
header('Content-Length: ' . $file_size);
header('Content-Disposition: inline; filename="' . $videonoextension . "_" . $start . "_" . $end . '.mp4"');
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');
readfile("tmp/$videonoextension" . "_" . "$start" . "_" . "$end.mp4");

# delete temporal video
unlink("tmp/$videonoextension" . "_" . "$start" . "_" . "$end.mp4");



?>







