<?PHP

if (!$db = @mysql_connect('localhost', 'root', '96contour')) {
   printf('Error connecting to database<BR>%s', mysql_error());
}

?>