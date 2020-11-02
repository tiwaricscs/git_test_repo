<?php

namespace mod_myurlactivity\task;

class clean_data extends \core\task\scheduled_task{

  public function get_name(){
  	
    return get_string('clean_after_30_day','myurlactivity');
  }

  public function execute(){
 
    global $DB;
        
    $sql = "DELETE FROM {logstore_standard_log} WHERE component = 'mod_myurlactivity' and objecttable ='myurlactivity' and timecreated < '".strtotime("-30 day")."'";
    
    $DB->execute($sql);

  }

}
