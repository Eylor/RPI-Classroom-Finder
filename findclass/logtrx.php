<?php
//
//	Log a transaction
//
function logTrx($trx) {
	$dh=new DB('poker');
	$ip=$_SERVER['REMOTE_ADDR'];
	$term=addslashes($trx['term']);
	$type=$trx['type'];
	$trxdate=$trx['when'];
	$bywho=$trx['who'];
	$numresults=$trx['nr'];
	$dh->Exec("insert into fc_trx (ip,srch_term,trx_type,trx_date,bywho,num_results) values('$ip','$term','$type','$trxdate','$bywho','$numresults')");
}

