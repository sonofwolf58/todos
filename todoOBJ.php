
<?php 
class tododata {

  function todo($caller, $id, $title, $priority, $duedate, $status, $datecomplete, $comments) {
     $this->self = $caller;
     $this->id = $id;
     $this->title = $title;
     $this->priority = $priority;
     $this->duedate = $duedate;
     $this->status = $status;
     $this->datecomplete = $datecomplete;
     $this->comments = $comments;
  } // end todo
 
  function set($varname, $value) {
    if ($varname == "id") {
      // do nothing to the id field
    } else {
      // test if the status is specified. If not say open
      if ($varname == '$status') {
	if ($value <> "c" && $value <> "C") {
	  $value = "O";
	} else {
	  $value = "C";
	}
      }
       $this->$varname = $value;
    }
  } //end set
  

   function headout() {
      printf("
         <table border=1 cellpadding=4><tr bgcolor=#C0C0C0>
         <th>Title</th>
         <th>Priority (1-9)</th>
         <th>Date Due (ccyy-mm-dd)</th>
         <th>Status (O/C)</th>
         <th>Date Complete (ccyy-mm-dd)</th>
         <th>Comments</th>
         <th>Day Left</th>
         <th>&nbsp;</th>
         </tr>\n\n");	  
   } // end headout
  
  
   function rowout() {

    // for output, check due date if blank
    if ($this->duedate <> "") {

      // if the status is compelte, don't bother calculating days due/overdue
      if ($this->status <> "C" && $this->status <> "c") {

      // not blank so determine relation to todays date
      // format todays date into yyyymmdd
      $now = date("Ymd");
      $nowhole = date("Y-m-d");

      // make the todo date into the same format
      $dueasis = $this->duedate;
      $year  = substr($dueasis, 0,4);
      $month = substr($dueasis,5,2);
      $day   = substr($dueasis,8,2);
      $due = "$year$month$day";

      if ($due > $now) {
        $daydiff = $this->count_days($nowhole, $dueasis);
	$daycount = "<font color=green>$daydiff DAYS TO COMPLETE</font>";
      }
      if ($due < $now) {
        $daydiff = $this->count_days($dueasis, $nowhole);
	$daycount = "<font color=red>$daydiff DAYS OVERDUE</font>";
      }
      if ($now == $due) {
	$daycount = "<font color=lime><b>** DUE TODAY **</b></font>";
      }
      } else {
         $daycount = "<font color=blue>COMPLETED</font>";
      } // end of complete status test
    } // end of if blank due date test

    // here is where we do the actual printing of the information

    if ($this->status == "C" || $this->status == "c") {
      // allows no edit or delete of completed items
    printf("
       <TR><td><B>%s</B>&nbsp;</td>
       <td><center>%s&nbsp;</center></td>
       <td>%s&nbsp;</td>
       <td><center>%s&nbsp;</center></td>
	   <td>%s&nbsp;</td>
	   <td>%s&nbsp;</td>
	   <td>%s&nbsp;</td>
	   <td>&nbsp;</td>
	   </tr>\n\n",
	   $this->title, $this->priority, $this->duedate, $this->status, $this->datecomplete, $this->comments, $daycount);
    } else {
      // allows edit/delete of not completed items
    printf("
       <TR><td><a href=\"%s?id=%s&edit=yes\"><B>%s</B></a>&nbsp;</td>
	   <td><center>%s&nbsp;</center></td>
	   <td>%s&nbsp;</td>
	   <td><center>%s&nbsp;</center></td>
	   <td>%s&nbsp;</td>
	   <td>%s&nbsp;</td>
	   <td>%s&nbsp;</td>
	   <td><a href=%s?id=%s&taskdelete=yes>Delete</a>&nbsp;</td>
	   </tr>\n\n", 
	   $this->self, $this->id, $this->title, $this->priority, $this->duedate, $this->status, $this->datecomplete, $this->comments, $daycount, $this->self, $this->id);

    } // end of status determined print statement

  } // end rowout


  function get($varname) {
    $value = $this->$varname;
    return $value;
  } // end get

  function count_days($start, $end) {
     // this function count days between $start and $end dates in mysql format (yyyy-mm-dd)
     // if one of paramters is 0000-00-00 will return 0
     // $start date must be less then $end
     if( $start != '0000-00-00' and $end != '0000-00-00' ){
        $timestamp_start = strtotime($start);
        $timestamp_end = strtotime($end);
        if( $timestamp_start >= $timestamp_end ) return 0;
        $start_year = date("Y",$timestamp_start);
        $end_year = date("Y", $timestamp_end);
        $num_days_start = date("z",strtotime($start));
        $num_days_end = date("z", strtotime($end));
        $num_days = 0;
        $i = 0;
        if( $end_year > $start_year )
        {
           while( $i < ( $end_year - $start_year ) )
           {
              $num_days = $num_days + date("z", strtotime(($start_year + $i)."-12-31"));
              $i++;
           }
         }
         return ( $num_days_end + $num_days ) - $num_days_start;
    } else {
         return 0;
    }
  } // end of count_days

} // end of tododata class

?>

