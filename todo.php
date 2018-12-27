<html>
<head>
<title>Matts To Do List</title>
<link rel="shortcut icon" href="todo.ico" />
</head>

<body link="blue" alink="red" vlink="blue" background="ppbk014.jpg">

<center>
<H1><font color="green">Matt's To Do List</font></H1>
</center>

<BR>

<center>
<?php
require("todoOBJ.php");
$SELF = "todo.php";

echo '<table width="100%" border=0>';
echo '<tr><td width="50%">';
printf("<center><a href='%s?compl=yes'>View All</a></td>", $PHP_SELF);
echo '<td width="50%">';
printf("<center><a href='%s'>View Incomplete Only</a></td></tr>", $PHP_SELF);
echo "</table><br>";

if ($compl=='yes') {
  futuretasks('');
} else {
  futuretasks('new');
}
printf('<BR><BR><center><form method="post" action="%s">', $PHP_SELF); 
printf("<INPUT TYPE=submit NAME=taskadd VALUE='Add New Record'>");
printf("</FORM></center>");
echo '<table width="100%" border=0>';
echo '<tr><td width="50%">';
printf("<center><a href='%s?compl=yes'>View All</a></td>", $PHP_SELF);
echo '<td width="50%">';
printf("<center><a href='%s'>View Incomplete Only</a></td></tr>", $PHP_SELF);
echo "</table><br>";

// --------------------------------------------------------------
// TASKADD 
// Presents the form for entering a NEW record into the db.
// CALLS: putnew to save the record entered
// --------------------------------------------------------------   
if ($taskadd) {
  taskcreate();
  $taskadd = null;
}
if ($taskdelete) {
  remove2do($id, $PHP_SELF);
  $taskdelete = null;
  $id = null;
}
if ($edit) {
  edittodo($id, $PHP_SELF);
  $edit = null;
  $id = null;
}

// ---------------------------------------------------------------
// function that responds to a cancel button.  Only need so error
// does not post when cancel is hit and re-run this script fresh
// ---------------------------------------------------------------
if ($cancel) {
}

?>

</center>
</body>
</html>


<?PHP

function futuretasks($state) {
require("db_info.inc");
   $reccnt=0;
   $result = mysql_select_db("2do", $db);

   if ($state == 'new') {
      $sql = "select id, title, priority, duedate, status, datecomplete, comments from todo where status <> 'C' order by duedate asc, priority asc";
   } else {
      $sql = "select id, title, priority, duedate, status, datecomplete, comments from todo order by duedate asc, priority asc";
   }
   $results = mysql_query($sql);
   if ($results) {
     $reslts = new tododata;
     $reslts->headout();
      while ($myrow = mysql_fetch_array($results)) {
	     $reslts->todo($SELF, $myrow["id"], $myrow["title"], $myrow["priority"], $myrow["duedate"], $myrow["status"], $myrow["datecomplete"], $myrow["comments"]);
         $reslts->rowout();
		 $reccnt++;
      } // end while
      echo "</table>";
      echo "</center>";
	  // 8/22/05 added the following if statement
	  // added record count to display count when all records are displayed.
	  if ($state<>'new') {
         echo "<br><b>Records displayed = ". $reccnt . "</b>";
	  }

   } // end if
} //end function


function taskcreate() {
   echo "<center>";
   printf('<form method="post" action="%s"><table>', $PHP_SELF);
   printf('<tr><td>To Do Title                 </td><td><input type=text name="title"        value=""></td></tr>');
   printf('<tr><td>Priority (1-9)              </td><td><input type=text name="priority"     value=""></td></tr>');
   printf('<tr><td>Due Date (ccyy-mm-dd)       </td><td><input type=text name="duedate"      value=""></td></tr>');
   printf('<tr><td>Status (O/C)                </td><td><input type=text name="status"       value="O"></td></tr>');
   printf('<tr><td>Date Completed (ccyy-mm-dd) </td><td><input type=text name="datecomplete" value=""></td></tr>');
   printf('<tr><td>Comments                    </td><td><input type=text maxlength="255" size="50" name="comments"     value=""></td></tr>');
   echo '</table>';
   echo '<BR><INPUT TYPE=SUBMIT NAME="insertNewTask" VALUE="Create">';
   echo '<INPUT TYPE=SUBMIT NAME="cancel" value="Cancel Change">';         
   echo "</FORM>";
   echo "</center>"; 
} // end of task create


if ($insertNewTask) {
   if ($datecomplete == "") {
      $datecomplete="0000-00-00";
   }
   if ($priority == "") {
      $priority="5";
   }
   $sql = "insert into todo (id, title, priority, duedate, status, datecomplete, comments) values (0, '$title', '$priority', '$duedate', '$status', '$datecomplete', '$comments')";

   if (!$results = mysql_query($sql)) {
      printf('<BR><center>insert of todo info failed <BR>%s</center>', mysql_error());
   } else {
      printf('<center>Insert of new todo info successful</center>');
      // in three seconds, the page refreshes on its own with the line below   
      echo "<meta http-equiv=\"refresh\" content=\"3 url=$PHP_SELF\">"; 	  
   }
   printf("<center><A HREF=\"%s\">Refresh Page </A></center><BR>", $caller);
  
}  // end of task create

function remove2do($itemno, $caller) {
   $sql = "delete from todo where id=$itemno"; 
   if (! $result = mysql_query($sql)) {
      printf("<center><P>An error occurred.  Record NOT Deleted</P>%s<BR></center>", mysql_error());
   } else {
      printf("<center><P>Record Deleted</P></center>");
      // in three seconds, the page refreshes on its own with the line below 
      echo "<meta http-equiv=\"refresh\" content=\"3 url=$caller\">";
   } 
   printf("<center><A HREF=\"%s\">Refresh Page </A></center>", $caller);

} // end of remove2do

function edittodo($itemid, $caller) {
   // --------------------------------------------------------------
   // TASKEDIT 
   // Presents the form for editing a record into the db.
   // CALLS: putnew to save the record entered
   // -------------------------------------------------------------- 
   require("db_info.inc");  
   $result = mysql_select_db("2do", $db);
   if (! @$result=mysql_query("SELECT * FROM todo where id=$itemid",$db)) {
      printf("<center><font color=red>Unable to select information from database<BR>%s</font></center>", mysql_error());
   }
   $reslts = new tododata;
   $myrow = mysql_fetch_array($result);
   $reslts->todo($caller, $itemid, $myrow["title"], $myrow["priority"], $myrow["duedate"], $myrow["status"], $myrow["datecomplete"], $myrow["comments"]);
   printf('<form method="post" action="%s"><center><table>', $caller);
   printf('<tr><td>Title                 </td><td><input type=text name="title"        value="%s"></td></tr>', $reslts->get(title));
   printf('<td>Priority (1-9)            </td><td><input type=text name="priority"     value="%s"></td></tr>', $reslts->get(priority));
   printf('<td>Due Date (ccyy-mm-dd)     </td><td><input type=text name="duedate"      value="%s"></td></tr>', $reslts->get(duedate));
   printf('<td>Status (O/C)              </td><td><input type=text name="status"       value="%s"></td></tr>', $reslts->get(status));
   printf('<td>Date Complete (ccyy-mm-dd)</td><td><input type=text name="datecomplete" value="%s"></td></tr>', $reslts->get(datecomplete));
   printf('<td>Comments                  </td><td><input type=text maxlength="255" size="50" name="comments"     value="%s"></td></tr>', $reslts->get(comments));
   printf('</table>');


   echo '<BR><INPUT TYPE=SUBMIT NAME="saveedit" VALUE="Submit Changes">';
   printf('<input type=hidden name="itemnum" value="%s">', $itemid);
   echo '<INPUT TYPE=SUBMIT NAME="cancel" value="Cancel Change">';
   echo "</FORM></cetner>"; 
}

if ($saveedit){
   $sql = "update todo set title='$title', priority='$priority', duedate='$duedate', status='$status', datecomplete='$datecomplete', comments='$comments' where id=$itemnum";

   if (!$results = mysql_query($sql)) {
      printf('insert of task failed <BR>%s', mysql_error());
   } else {
      printf('<center>Edit Successful<BR>');
      printf('<a href=%s>Refresh Screen</center>',$caller);
      // in three seconds, the page refreshes on its own with the line below
      echo "<meta http-equiv=\"refresh\" content=\"3 url=$PHP_SELF\">";
   }
} // end of savedata

?>
 