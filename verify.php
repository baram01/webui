<?php
switch ($option) {
case 1:
	if (verifyPassword($type, $uid, $oldpass, $dbi) > 0) {
		echo "<script language=\"JavaScript\"> alert('Password for $uid is good');";
	}
	else {
		echo "<script language=\"JavaScript\"> alert('Password for $uid is not good');";
	}
	echo "close();</script>";
}

?>
<div id="verify" class="_table">
       <form name="verify_password" method="post" action="?module=verify&option=1">
	   <fieldset class=" collapsible"><legend>Verify Password</legend>
           <table align="left" border=0>
	      <tr><td>Username:</td><td><input type="text" name="uid"></td></tr>
	      <tr><td>Password:</td><td><input type="password" name="oldpass"></td></tr>
	      <tr><td>Verify:  </td><td><select name="type">
		  <option value="password">password</option>
		  <option value="enable">enable</option>
		  <option value="pap">pap</option>
		                   </select>
	      <tr><td></td><td><input type="submit" value="Verify"></td></tr>
	  </table>
	  </fieldset>
       </form>
</div>
