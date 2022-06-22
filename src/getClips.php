<?php
############################################################################################################################################################
## Raúl Sánchez <raul@um.es> based on Ellen Roberts and Peter Uhrig Download Rapid Annotator add-on for CQPweb (Andrew Hardie and collaborators)
## This plugin relies in curl and zip executables. So have access to execute scripts in your host machine.
## This can be dangerous if you don't know what you are doing.
## curl and zip executables must be in your PATH.
## Is recommended to create an index.html file inside download_path directory or configure apache to hide directory listing.
############################################################################################################################################################

class getClips extends QueryDownloaderBase implements QueryDownloader
{ ## This plugin builds on the existing CQPweb scripts QueryDownloaderBase and QueryDownloader - see CQPweb script plugin-lib.php for more detail about these functions

  ## CONFIGURATION SECTION
  ## Describe the pattern of the video link with {{video_id}}, {{start_time}} and {{end_time}}, e.g. as below for video_url_template. Change to suit needs.
  private array $settings = array(
    "headers_before_XML" => ["Number of hit", "Text ID", "Context before", "Query item", "Context after", "Tagged context before", "Tagged query item", "Tagged context after"],
    "headers_after_XML" => ["Matchbegin corpus position", "Matchend corpus position", "Video URL", "Video Snippet", "Video Snippet (long)", "Audio Snippet", "Audio Snippet (long)", "Screenshot"],
    "snippet_long_context" => 2, # Default here is seconds e.g. two seconds to either side
    "video_snippet_long_template" => "https://gallo.case.edu/cgi-bin/snippets/newsscape_mp4_snippet.cgi?file={{video_id}}&start={{start_time_long}}&end={{end_time_long}}",
    "download_url" => "http://kaneda.inf.um.es/CQPweb/downloads/",
    # download_path MUST exists and have right permissions. Web server must have write permissions to this directory.
    "download_path" => "/var/www/html/CQPweb/downloads/",
    "auto_download" => true, # If true, the download will be run automatically.
  );

  private array $xml_list;
  private int $next_hit = 1;

  private string $random_filename;
  private array $curl_lines;


  ## Option to add extra configuration in here as needed
  public function __construct(array $extra_config = [])
  {
    # random filename
    $random = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 16);
    $date = date("Y-m-d-TH-i-s");
    $this->random_filename = $date . "_" . $random ;
    return;
  }

  ## Function for error management 
  private function logging($error_output)
  {
    $stderr = fopen('php://stderr', 'w');
    fwrite($stderr, $error_output);
    fclose($stderr);
  }

  ## Function containing required p-attributes
  public function list_required_annotations($available): array
  {
    $this->logging("FUNCTION list_required_annotations\n");
    return ['word', 'pos', 'startsecs', 'startcentisecs', 'endsecs', 'endcentisecs'];
  }

  ## not currently used in this workflow but could be added if desired
  # public function list_required_xml($available) : array
  #  { 
  #  return;
  #  }

  ## Function to prepare the raw CQP line for download. 'Extent' = span around the node, default is 100 words. 'Set LD/RD' is used to set the delimiter options. 
  public function prepare_cqp(CQP $cqp): void
  {
    $cqp->execute("set LD '--<<>>--'");
    $cqp->execute("set RD '--<<>>--'");
    global $Corpus;
    $xml_list_temp = list_xml_with_values($Corpus->handle);
    foreach ($xml_list_temp as $key => $value) {
      if ($key != "text_id") {
        $this->xml_list[$key] = $value;
      }
    }
    $cqp->set_option('PrintStructures', 'text_id,' . join(",", array_keys($this->xml_list)));
    $cqp->set_context('both', array("extent" => 100, "unit" => "words", "unit_is_s_attribute" => false));
    $this->logging("FUNCTION prepare_cqp\n");
  }

  ## Function get_standard_download_settings needs to return NULL to activate the plugin
  public function get_standard_download_settings(): ?array
  {
    $this->logging("FUNCTION get_standard_download_settings\n");
    return NULL;
  }

  ## Description of Plugin for CQPweb 
  public function description(): string
  {
    $this->logging("FUNCTION description\n");
    return 'DownloadClips containing CQPweb search results';
  }

  ## Function for the download button on CQPweb interface
  public function get_ui_trigger_label(): string
  {
    $this->logging("FUNCTION get_ui_trigger_label\n");
    return 'Download Clips';
  }

  ## Function to delete zip files in directory older than $days
  public function delete_zip_files_older_than(int $days, string $dir): void
  {
    $this->logging("FUNCTION delete_files_older_than $days days in $dir\n");
    $files = glob($dir . "*.zip");
    foreach ($files as $file) {
      if (filemtime($file) < time() - $days * 24 * 60 * 60) {
        unlink($file);
      }
    }
    return;
  }

  ## Function to define header layout for downloader. Default is values from 'Headers before XML', then contents of the XML_list, then the values of 'Headers after XML'.
  public function print_download_header(): ?string
  {
    $this->logging("FUNCTION print_download_header\n");

    $this->delete_zip_files_older_than(7, $this->settings["download_path"]);

    $headers = "########################################################################################## 
      ## Download process can last for long time. Please be patient.
      ## Please, wait some time and download the file from this URL:
      ## " . $this->settings["download_url"] . $this->random_filename . ".zip" . $this->eol . " 
      ## Zip files will be removed after 7 days.
      ## 
      ## Also you can download clips by yourself:" . $this->eol . "##########################################################################################" . $this->eol . $this->eol;

    return $headers;
  }

  ## Main function for printing output for the downloader.
  public function print_concordance_line(string $cqp_line, array $alignments = [], ?int $cpos_begin = NULL, ?int $cpos_end = NULL): ?string
  { 
    $this->logging("\n\nFUNCTION print_concordance_line\n\n");
    ## Extract the values from the raw CQPline and store in xml_values array
    $x = $this->settings;
    $xml_values = array();
    foreach ($this->xml_list as $key => $value) {
      preg_match("/<$key (.*?)>/", $cqp_line, $matches);
      $xml_values[] = $matches[1];
    }
    ## Extract the corresponding p-attribute name and store in the array xml_combined
    $xml_keys = array();
    foreach ($this->xml_list as $key => $value) {
      $xml_keys[] = $key;
    }
    $xml_combined = array_combine($xml_keys, $xml_values);
    ## Extract text_id value from CQPline and store in a variable
    preg_match("/<text_id (.*?)>/", $cqp_line, $m);
    $text_id_value = $m[1];

    ## Code from original CQPweb - developer: Andrew Hardie 
    ## Extraction of tagged and untagged elements from CQPweb line
    $CQP_INTERFACE_EXTRACT_TAG_REGEX       =  '/\A(.*)\/([^\/]*)\z/';
    $CQP_INTERFACE_WORD_REGEX              =  '|((<\S+?( [^>]*?)?>)*)([^ <]+)((</\S+?>)*) ?|';
    $kwiclimiter = "\t";

    $kwic_chunks = explode('--<<>>--', preg_replace("/\A\s*\d+: <.*?>:\s+/", '', $cqp_line));

    $untagged_bits = array();
    $tagged_bits   = array();
    $xml_before_string = $xml_after_string = '';

    /* process the chunk word by word, including XML viz if necessary */
    foreach ($kwic_chunks as $ix => $chunk) {
      preg_match_all($CQP_INTERFACE_WORD_REGEX, trim($chunk), $m, PREG_PATTERN_ORDER);
      $words = $m[4];
      $xml_before_array = $m[1];
      $xml_after_array  = $m[5];
      $ntok = count($words);
      $untagged_bits[$ix] = $tagged_bits[$ix] = array();

      for ($j = 0; $j < $ntok; $j++) { {
          preg_match('/\A(.*)\/([^\/]*\/[^\/]*\/[^\/]*\/[^\/]*\/[^\/]*)\z/', $words[$j], $m);
          $word = $m[1];
          $tag  = $m[2];
        }

        $untagged_bits[$ix][] .= $xml_before_string . $word . $xml_after_string;
        $tagged_bits[$ix][] .= $xml_before_string . $word . '_' . $tag . $xml_after_string;
      }

      /* arrays to strings now we have it all */
      $untagged_bits[$ix] = trim(implode(' ', $untagged_bits[$ix]));
      $tagged_bits[$ix]   = trim(implode(' ', $tagged_bits[$ix]));
      ## extract the offset value for the long context snippets
      $offset_value = $x["snippet_long_context"];
      /* if this chunk is the node, wrap in the deliminter. */
      if ($ix == 1) {
        $untagged_bits[$ix] = $hit_delimiter_before . $untagged_bits[$ix] . $hit_delimiter_after;
        $tagged_bits[$ix] = $hit_delimiter_before . $tagged_bits[$ix] . $hit_delimiter_after;

        $mytokens_temp = explode(' ', trim($tagged_bits[$ix]));
        $firsttoken = explode('/', $mytokens_temp[0]);
        $lasttoken = explode('/', array_values(array_slice($mytokens_temp, -1))[0]);

        ## extract start and end times for video urls - code from Peter Uhrig
        $reltime = $firsttoken[1];
        $starttime = $firsttoken[1];
        if ($firsttoken[2] != "NA") {
          $starttime .= "." . $firsttoken[2];
        };
        $endtime = $lasttoken[3];
        if ($lasttoken[4] != "NA") {
          $endtime .= "." . $lasttoken[4];
        };
        $screenshottime = $starttime + ($endtime - $starttime); # Time in the middle of the actual hit, used for screenshot
        if ($starttime == $endtime) {
          $endtime++;
        }
        ## calculate offset with the offset value from the config settings
        $starttimelong = floatval($starttime) - intval($offset_value);
        $endtimelong = floatval($endtime) + intval($offset_value);
      }
    }

    $untagged = implode($kwiclimiter, $untagged_bits);
    $tagged = "\t" . implode($kwiclimiter, $tagged_bits);

    ## End of original/modified CQPweb code 
    ## Extract video_url links and their corresponding values
    $url_template = array();

    $video_url_templates = ["video_snippet_long_template"];
    foreach ($video_url_templates as $value) {
      $url_template[] = $x[$value];
    }
    $url_templates = array_combine($video_url_templates, $url_template);


    #### RAUL CHANGES ####
    #$vid = $xml_combined['text_id'];
    $vid = $text_id_value;
    # Remove t_ from the start of the text_id
    $vid = substr($vid, 3);
    # Replace "_" with "-" in the text_id
    $vid = str_replace("_", "-", $vid);
    $this->logging($vid);

    $replaced_urls = array();
    $completed_urls = array();

    ## find the variables in the original urls and replace with the values retrieved from the original CQPweb line
    $find = ["{{video_id}}", "{{start_time}}", "{{end_time}}", "{{ss_start_time}}", "{{start_time_long}}", "{{end_time_long}}"];
    $replace = [$vid, $starttime, $endtime, $screenshottime, $starttimelong, $endtimelong];
    foreach ($url_templates as $value) {
      $replaced_urls[] = str_replace($find, $replace, $value);
    }
    $completed_urls = array_combine($video_url_templates, $replaced_urls);

    ## add the completed urls to the downloader output
    #$additional_columns = implode($kwiclimiter, array_values($completed_urls)); 
    $additional_columns = implode(array_values($completed_urls));
    # print $additional_columns array values

    $filename_text = $xml_values[4];
    # get only filename from $filename absolute file path
    $filename_text = basename($filename_text);
    # change extension of filename to .mp4
    $filename_text = substr($filename_text, 0, -4) . "-" . $starttimelong . "-" . $endtimelong .  ".mp4";

    #return $this->next_hit++ . "\t" . $text_id_value ."\t". $untagged . $tagged . "\t" . join("\t", $xml_values) . "\t" . $cpos_begin . "\t" . $cpos_end . "\t" . $additional_columns ."\t" . $this->eol ;
    $output = "curl -L -o " .  $filename_text . " \"" . $additional_columns . "\"" . $this->eol;
    # append $output to curl_lines array last row of the array
    $this->curl_lines[] = $output;
    return $output;
  }
  ## Function sets the file type output
  public function get_mime_type(): ?string
  {
    $this->logging("FUNCTION get_mime_type\n");
    return "text/plain";
  }
  public function get_info_request()
  {
    $this->logging("FUNCTION get_info_request\n");
    return false;
  }

  public function status_ok(): bool 
  {
    $this->logging("FUNCTION status_ok\n");
    return true;
  }


  # Function to download clips with curl and save them into a .zip file in downloads folder
  public function generate_zip()
  {
    $this->logging("FUNCTION generate_zip\n");
    
    # create a random tmp dir
    $tmp_dir = sys_get_temp_dir() . "/" . uniqid();
    mkdir($tmp_dir, 0777, true);
    # create file in dir with exec permission
    $tmp_file = $tmp_dir . "/" . uniqid();
    file_put_contents($tmp_file, "#!/bin/sh\n");
    file_put_contents($tmp_file, implode("\n", $this->curl_lines), FILE_APPEND);
    file_put_contents($tmp_file, "\nzip -j " . $this->settings["download_path"]  .  $this->random_filename . ".zip  " . $tmp_dir . "/*.mp4", FILE_APPEND);
    
    file_put_contents($tmp_file, "\nrm -f " . $tmp_dir ."/*.mp4" , FILE_APPEND);
    chmod($tmp_file, 0755);
    # change directory to tmp_dir
    chdir($tmp_dir);
    # exec tmp_file in background
    exec($tmp_file . ' >/dev/null 2>&1 &'); 

  }

  ## We will use this funcion to launch curl commands to download videos
  public function __destruct()
  {
    $this->logging("\nFUNCTION __destruct\n");
    if ($this->settings["auto_download"]) {
      $this->generate_zip();
      # HTTP Redirect to download url with $tmp_file as the query string
      header("Location: " . $this->settings["download_url"] . "index.php?file=" .$this->random_filename . ".zip&download_url=" . $this->settings["download_url"]); 
    }
  }
}
