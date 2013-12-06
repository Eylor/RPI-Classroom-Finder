<?php
$cyr=date('Y');
if ($cyr!="2005") $cyr="2005-$cyr";
print <<< END
</table>
<br><p align='center'><span class='footer'>Website design and programming &copy; Copyright $cyr by 
$copyright. All Rights Reserved.</span></p><br>
</td></tr></table>
</body>
</html>
END
?>
