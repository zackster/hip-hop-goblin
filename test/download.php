<?php

include('../classes/DownloadManager.class.php');

$url = 'http://www.zshare.net/download/645679470a04f99d/';
$dm = new DownloadManager($url);
$dm->download();

echo $dm->filename;
echo "err?\n";
echo $dm->error;

