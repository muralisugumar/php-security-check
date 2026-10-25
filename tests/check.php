<?php
/** Offline checks only; no network requests or exploit traffic. */
ob_start();
require dirname(__DIR__).'/php-runtime-check.php';
$html=ob_get_clean();
$count=0;
function verify($condition,$message){global $count;if(!$condition){fwrite(STDERR,'FAIL: '.$message."\n");exit(1);}$count++;}
$cases=array(
 '8.2.34'=>'warning','8.2.35'=>'match','8.2.36'=>'match',
 '8.3.35'=>'warning','8.3.36'=>'match','8.3.37'=>'match',
 '8.4.26'=>'warning','8.4.27'=>'match','8.4.28'=>'match',
 '8.5.11'=>'warning','8.5.12'=>'match','8.5.13'=>'match',
 '8.1.99'=>'warning','7.4.33'=>'warning','8.6.0'=>'review',
 '8.6.0RC4'=>'review','8.5.12RC1'=>'review','8.5.13-dev'=>'review'
);
foreach($cases as $version=>$expected)verify(runtime_patch_assessment($version)[0]===$expected,'version boundary '.$version);
foreach(array('0','Off','false','no','') as $value)verify(runtime_upload_state($value)==='off','disabled setting');
foreach(array('1','On','true') as $value)verify(runtime_upload_state($value)==='on','enabled setting');
verify(runtime_upload_state(false)==='unknown','unavailable setting is not marked disabled');
verify(runtime_escape('<script>"test"</script>')==='&lt;script&gt;&quot;test&quot;&lt;/script&gt;','HTML escaping');
verify(substr_count($html,'<table>')===2,'both issue and update tables render');
verify(strpos($html,'https://muralisugumar.com')!==false,'author website credited');
verify(strpos($html,'CVE-2026-109618')!==false && strpos($html,'CVE-2026-109619')!==false,'both critical issues present');
verify(strpos($html,'CVE-2026-115560')!==false,'separate follow-up documented');
verify(strpos($html,'Delete this PHP file')!==false,'public removal guidance present');
verify(strpos($html,'Command-line PHP only')!==false,'CLI distinction visible');
verify(runtime_fixed_versions()===array('8.2'=>'8.2.35','8.3'=>'8.3.36','8.4'=>'8.4.27','8.5'=>'8.5.12'),'published version table');
echo $count." checks passed. No server settings or databases were changed.\n";
