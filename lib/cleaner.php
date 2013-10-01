<?php

if (realpath($_SERVER['SCRIPT_FILENAME']) == realpath(__FILE__)) { die(
'This script is designed to run if some conditions are ment..<br /><br />
<tt>php_value auto_prepend_file "/real/full/server/path/to/cleaner.php"</tt>'); }


function CleanGarbage() {

// tom addition
 $muda_ngapi = 3600; // How old are the file since they were created
//  $seconds_old = 5;

$dir = dirname(__FILE__).'/requestBlocker';//TM: Added ## Create & set directory writeable!!!! 

if( !$dirtosha = @opendir($dir) )
                        return;

                while( false !== ($chafu = readdir($dirtosha)) ) {
                        if( $chafu != "." && $chafu != ".." ) {
                                $chafu = $dir. "/". $chafu;

                                if( @filemtime($chafu) < (time()-$muda_ngapi) )
                                
                                $twende = @unlink($chafu);


    if($twende=="1"){
        echo "The file was deleted successfully.";
    } else { 
    
        echo "There was an error trying to delete the file.";
    
    
     } 
	
       }
                }
				
				
				}