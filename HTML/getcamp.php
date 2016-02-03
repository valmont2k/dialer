<?php


$q=$_GET['q'];

echo "<form   id='contact-form' action='sdialer.php' method='post'>
<!-- Form -->
<div class='form'>


<p>
<label>Maximum Calls<span></span></label>
<input type=text class='field size' name='calls' id='calls' value=''/>
<input type=hidden name='campname' id='campname 'value='$q'/>
</p>
<p>
<label>Maximum Retries<span></span></label>
<input type=text class='field size' name='retry' id='retry' value=''/>
</p>
<p>
<label>Retry Time<span></span></label>
<input type=text class='field size' name='retrytime' id='retrytime' value=''/>
</p>
<p>
<label>Endpoint<span></span></label>
<input type=text class='field size' name='destination' id='destination' value=''/>
</p>
</div>";

echo "<div class=buttons>
<input name=button type=submit class=button value=Start />
</div>
</form>";


?>
