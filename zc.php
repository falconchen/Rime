#!/usr/bin/env php
<?php
$dictFile = '/Users/falcon/Library/Rime/wubi86_jidian_user.dict.yaml';
if($argc<3){
	exit("Arg Number Error");
}

$word = $raw = $argv[1];
$code = strtolower($argv[2]);

if(!preg_match('#[a-z]+#',$code)){
	exit("Arg Error");
}
$weight = $argv[3] ?? 1;

$content = file_get_contents($dictFile);
if(substr($content,-1) !== PHP_EOL){
	$word=PHP_EOL.$word;
}

$row = sprintf("%s	%s	%d".PHP_EOL,$word,$code,$weight);
if(file_put_contents($dictFile,$row,FILE_APPEND)){
	echo "...".PHP_EOL;
	echo tail($dictFile,2);
	$reload = '/Library/Input\ Methods/Squirrel.app/Contents/MacOS/Squirrel --reload';
	shell_exec($reload);
	$dir = dirname($dictFile);
	$file = basename($dictFile);
	echo PHP_EOL;
	$cmd = "cd $dir && git add $file && git commit -m 'add new word `$raw`,code:`$code`' && git push ";
	shell_exec($cmd);
	
	

}

function tail($file, $num) {
    $fp = fopen($file, "r");
    $pos = -2;
    $eof = "";
    $head = false; //当总行数小于Num时，判断是否到第一行了
    $lines = array();
    while ($num > 0) {
        while ($eof != "\n") {
            if (fseek($fp, $pos, SEEK_END) == 0) {
                //fseek成功返回0，失败返回-1
                $eof = fgetc($fp);
                $pos--;
            } else {
                //当到达第一行，行首时，设置$pos失败
                fseek($fp, 0, SEEK_SET);
                $head = true; //到达文件头部，开关打开
                break;
            }

        }
        array_unshift($lines, fgets($fp));
        if ($head) {break;} //这一句，只能放上一句后，因为到文件头后，把第一行读取出来再跳出整个循环
        $eof = "";
        $num--;
    }
    fclose($fp);
    // return $lines;
    $str = join('', $lines);
    return $str;
}
