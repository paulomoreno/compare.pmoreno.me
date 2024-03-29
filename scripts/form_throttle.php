<?php 
/* 	
If you see this text in your browser, PHP is not configured correctly on this hosting provider. 
Contact your hosting provider regarding PHP configuration for your site.

PHP file generated by Adobe Muse CC 2014.3.2.295
*/

function formthrottle_check()
{
	if (!function_exists("sqlite_open")) 
	{
		return '1';
	}

	$retCode ='5';
	if ($db = @sqlite_open('muse-throttle-db', 0666, $sqliteerror)) 
	{
	    $res = @sqlite_query($db, "SELECT 1 FROM sqlite_master WHERE type='table' AND name='Submission_History';",  $sqliteerror);
	    if ($res == null or @sqlite_num_rows($res) == 0 or @sqlite_fetch_single($res) != 1) 
	    {
			$created = @sqlite_exec($db, "CREATE TABLE Submission_History (IP VARCHAR(39), Submission_Date TIMESTAMP)",  $sqliteerror);
			if($created)
			{
				@sqlite_exec($db, "INSERT INTO Submission_History (IP,Submission_Date) VALUES ('256.256.256.256', DATETIME('now'))",  $sqliteerror);
			}
			else
			{
				$retCode = '2';
			}
		}
		if($retCode == '5')
		{
			$res = @sqlite_query($db, "SELECT COUNT(1) FROM Submission_History;",  $sqliteerror);
			if ($res != null and @sqlite_num_rows($res) > 0 and @sqlite_fetch_single($res) > 0) 
				$retCode = '0';
			else
				$retCode = '3';
		}
	    @sqlite_close($db);

	} 
	else
		$retCode = '4';
		
	return $retCode;
}	

function formthrottle_too_many_submissions($ip)
{
	$tooManySubmissions = false;
	if (function_exists("sqlite_open") and $db = @sqlite_open('muse-throttle-db', 0666, $sqliteerror)) 
	{
		$ip = @sqlite_escape_string($ip);
		@sqlite_exec($db, "DELETE FROM Submission_History WHERE Submission_Date < DATETIME('now','-2 hours')",  $sqliteerror);
		@sqlite_exec($db, "INSERT INTO Submission_History (IP,Submission_Date) VALUES ('$ip', DATETIME('now'))",  $sqliteerror);
		$res = @sqlite_query($db, "SELECT COUNT(1) FROM Submission_History WHERE IP = '$ip';",  $sqliteerror);
		if (@sqlite_num_rows($res) > 0 and @sqlite_fetch_single($res) > 25) 
			$tooManySubmissions = true;
		@sqlite_close($db);

	}
	return $tooManySubmissions;
}
?>
