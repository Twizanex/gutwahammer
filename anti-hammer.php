<?php        //  ۞ text{ encoding:utf-8; bom:no; linebreaks:unix; tabs:4; }  ۞//
/* direct access -> \/ */						 $anti_hammer_version = '0.9.3';

if (realpath($_SERVER['SCRIPT_FILENAME']) == realpath(__FILE__)) { die(
'This script is designed to run as a php auto-prepend, like so (in .htaccess)..<br /><br />
<tt>php_value auto_prepend_file "/real/full/server/path/to/anti-hammer.php"</tt>'); }

/*
	Anti-Hammer

	Automatically set temporary bans for web site hammering.
	Protect your valuable server resources for genuine clients.

	Full details here..

		http://www.twizanex.com/profile/Thomas/info
		
		http://corz.org/serv/tools/anti-hammer/
		

	Have fun!

	;o) Cor
    
	© 2013-> http://www.twizanex.com/
	© 2007-> corz.org

*/




/*
prefs.. */
// include the Elgg engine


require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

//global $CONFIG;

global $CONFIG;

if (!isset($CONFIG)) {

$CONFIG = new stdClass;

}
/*
	Anti-Hammer data directory

	[default: $_SERVER['DOCUMENT_ROOT'].'/Anti-Hammer/anti-hammer';]

	When using Anti-Hammer's built-in client-tracking (the default), files will
	be stored, in this directory..

	If you are using the built-in sessions, and this directory isn't writable, 
	Anti-Hammer won't work.

													*/
$anti_hammer['info_path'] = $_SERVER['DOCUMENT_ROOT'].'/mod/gutwahammer/anti-hammer';

//echo $anti_hammer['info_path']; // TM


/*
	Client ID File Prefix			 
	
	[default: $anti_hammer['ID_prefix'] = 'HammerID_';]

	This text is placed before the client ID in the ID filename. e.g..

		"HammerID_06fa71c938a108f4a2b1f1ef091653ef"

	You may wish to use a different name..
	
	
*/	


	
$CONFIG->anti_hammer_prefix = 'HammerID_'; // This ID's should be the same	
	
														
$anti_hammer['ID_prefix'] = 'HammerID_'; // This ID's should be the same




// final time $anti_hammer['final_time']  default should be 0


$CONFIG->anti_hammer_final_time = 0; // will be used to set the retry header on killed page (503)

/*
	Hammer Time!

	[default: $anti_hammer['hammer_time'] = 100;]	(One Second)

	If they make two requests within this time, the counter increases by one.

	The faster and more capable your server, the lower this setting can be.
	The higher you set this, the more likely they are to get a warning.

	100 is a reasonable setting for a fast server, enabling one-hit-per-second 
	spidering, but penalizing anything faster. 
	
	Enter an integer, representing 100th/s.. 
											*/
$anti_hammer['hammer_time'] = 90;		



/*
	Trigger levels.		
	
	[default: $anti_hammer['trigger_levels'] = '5,10,20,30';]

	Enter the number of violations that will trigger each of the four levels..

	i.e. At the default settings, they get their first warning after five 
	violations (with a ban time of three seconds, set below). The time penalty 
	increases after ten and twenty violations, up to the maximum level of 30 
	violations (which imposes the maximum ban time of 20 seconds). You can set 
	the actual times in the next preference.

	Specify four integer values, separated by commas, whole thing in quotes.
																*/  

$anti_hammer['trigger_levels'] = '2,10,20,30'; //TM: 

/*
	Ban Times.		
	
	[default: $anti_hammer['waiting_times'] = '3,5,10,20';]

	This list sets the individual times that offenders will be 'banned' for.
	They will have to wait *this* long before they can try again.

	Each of the four setting corresponds to one of the above trigger_levels.

	Specify four integer values, separated by commas, whole thing in quotes.
*/
$anti_hammer['waiting_times'] = '3,5,10,20';



/*
	Rolling Trigger Times
	
	[default: $anti_hammer['rolling_trigger'] = false;]

	This increases the ban time automatically with EACH hammer.

	<hit> 
		You must wait three seconds..
	<hit>
		You must wait four seconds..
	<hit> 
		You must wait five seconds..

	And so on.

*/
//$anti_hammer['rolling_trigger'] = false;

$anti_hammer['rolling_trigger'] = true;

/*
	Cut-Off

	[default: $anti_hammer['cut_off'] = '']

	You can also set an absolute cut-off point.

	Anyone receiving this many hammer violations is simply dropped, and from 
	that point onward, their pages die before they even begin - blank.

	This works with both preset and rolling triggers.

	Leave blank to disable the cut-off.
							*/
$anti_hammer['cut_off'] = '';



/* 
	Bye Bye! Message.

	[default: $anti_hammer['cut_off_msg'] = '<h1>Bye Now!</h1>';]

	A final word from our sponsor?

	This is the final message they see before it all goes blank.
	No other text is presented.
												*/
$anti_hammer['cut_off_msg'] = '<h1>Bye Now!</h1>';



/*
	Ban Time

	[default: $anti_hammer['ban_time'] = '12';]

	And for how many hours will the above cut-off (ban) last?
															*/
$anti_hammer['ban_time'] = '12';

//	NOTE:	If you set your Garbage Collection age to any less than this, you 
//			effectively reset all bans older than THAT figure.
//
//			In other words, ensure your garbage collection age ('GC_age', below)
//			is larger than your 'ban_time' setting here, probably x2.
//			Think: if GC happened one minute after someone was banned, and their 
//			session ID file was >= GC_age, it would be cleaned up! Then no ban!
//
// Also Note: Humans are daily creatures, for them a 12h ban, is effectively 24!



/*
	Log File

	[default: $anti_hammer['log'] = $_SERVER['DOCUMENT_ROOT'].'/log/.ht_hammers';]

	We will log each banned hit, for reference.
	Enter full path to log location..		

	NOTE: If the parent directory does not exist, Anti-Hammer will not attempt 
	to create it, and you will get no logging.
																			*/
$anti_hammer['log'] = $_SERVER['DOCUMENT_ROOT'].'/mod/gutwahammer/log/.ht_hammers';

//			 It is recommend you watch this log very carefully for the first 
//	NOTE:	 few minutes/ days after installation, in case of unexpected side-
//			 effects. And in that case, please do mail me about it! 



/*

	Kill Message.

	[default: $anti_hammer['kill_msg'] = 'Please do not hammer this site.<br />';]

	When a request is killed - send this message (before the other text).
	You can use any calid HTML in here, header tags, or whatever you like..
																		*/
$anti_hammer['kill_msg'] = 'Please do not hammer this site!<br />';

/* NOTE: No <br /> is placed after this text.
		 If you aren't using <h> tags, and want a break, add it yourself. */

/*

	Page Title.

	[default: $anti_hammer['page_title'] = 'Please do not hammer this site!';]

	This is what is displayed in the title bar of their browser.
	Keep this one plain text.
															*/
$anti_hammer['page_title'] = 'Please do not hammer this site!';


/*
	WebMaster's Name

	[default: $anti_hammer['webmaster'] = 'the webmaster';]

	Name of the webmaster, will be included in the kill page.
	e.g. "If you believe this is in error, please mail <Insert Name> about it!"
															*/
$anti_hammer['webmaster'] = 'the webmaster';



/*
	Admin Bypass

	[default: $anti_hammer['admin_agent_string'] = 'MyCrazyUserAgentString';]

	If you insert this exact string into your web browser's user-agent string 
	(just tag it onto the end), you can bypass the hammer altogether.
	Very handy for busy webmasters.
																	*/
$anti_hammer['admin_agent_string'] = 'MyCrazyUniqueUserAgentString';	


// 			It's not advisable to go messing with the main body of your
//	NOTE:	browser's user agent string. Lots of web designers rely on this 
//			information to serve you beautiful, functional web pages.



/*
	WebMaster email address (string).

	[default: $anti_hammer['error_mail'] = 'bugs at mydomain dot com';]

	The usual text format of so-and-so at such-and-such dot com works well.
	This is tagged on to the end of the massage inside <> angle brackets,
	to look like an address.
																		*/
$anti_hammer['error_mail'] = 'bugs at mydomain dot com';



/*
	Lookup Failures.

	When an event worth logging occurs, we can lookup the host name of the
	client to add to our logs. This takes a moment, but only occurs while
	logging bad clients, and can be useful in quickly identifying abusers
	(or good bots using bad user agent string - to come)
													*/
$anti_hammer['lookup_failures'] = true;


/*

	Allow known bots?

	[default: $anti_hammer['allow_bots'] = false;]

	We can allow certain bots to bypass the Anti-Hammer.

	Do do this, specify the expected user agent strings in..

		path-to/anti-hammer/exemptions/exemptions.ini
		
	and then supply an IP-mask file where said user agent is expected to be 
	making requests FROM, one ip per line, in the standard Spider IP list format 
	as found here..

		http://www.iplists.com/
		http://www.iplists.com/nw/	<- updated, reorganised, with msnbot+more

		A blog URI is listed there, where list updates are posted. 
		(this doesn't happen a lot, maybe 2-3 times a year)

	NOTE:	User agent string matches are CaSe SenSiTivE! If you want to match 
			"msnbot" and "MSNBOT", you need two entries. (a case-insensitive 
			test is roughly five times slower than case-sensitive; so testing 
			two separate entries is much faster)

	NOTE:	If cooking up your own anti-hammer.ini, you probably do not want to 
			include the generic user agent strings (e.g. Yahoo's "Mozilla/4.0"), 
			which would create a lot of processing overhead, as ALL browsers 
			send that. Doh! (More notes within that file.)


	You can set this to "true" (no quotes), in which case, all specified bots
	are simply allowd to bypass the hammer. You can also set it to an integer, 
	e.g..

		$anti_hammer['allow_bots'] = 50;
	
	..that integer representing the hammer_time that will apply to the specified 
	clients. "50" would enable 2 hits-per-second spidering, but nothing faster,
	which is half the normal hammer_time of One Second (hammer_time=100).


									*/
//$anti_hammer['allow_bots'] = false;

$anti_hammer['allow_bots'] = true;

/*
	The following two preferences control Anti-Hammer's built-in Client session
	Garbage collection routines..
*/


/*
	Garbage Collection Limit

	[default: $anti_hammer['GC_limit'] = 10000;]

	To prevent your server's hard drive filling up with stale client sessions,
	we run a periodic garbage collection routine to sweep up the old files.

	How periodically, is up to you. By default, Anti-Hammer will check for
	garbage every 10,000 hits. I'm thinking this would be around a 2-daily hit 
	rate for a small site (@ 5000 hits per day).

	Obviously, you can chage this number to anything you like, depending on how
	busy your site is, and how much space you have on the disks. 
	
	If you don't want Anti-Hammer to clean up its garbage, set this to 0.

	Remember to ensure that this limit falls well outside your longest ban time,
	probably at least 2x that.
								*/
$anti_hammer['GC_limit'] = 10500;


/*

	GarbAge!

	[default: $anti_hammer['GC_age'] = 24;]

	How old, in hours, is considered "stale"?
	Any ID files older than this will be swept away (deleted).
																*/
$anti_hammer['GC_age'] = 24; // 


$CONFIG->anti_hammer_GC_age = 24; // TM: working like cham


/*
	NOTE: The previous two preferences have no effect if you set the following
	preference ('use_php_sessions') to true. They are only for AntiHammer's 
	built-in client session files.

*/


/*
	Use php sessions..

	[default: $anti_hammer['use_php_sessions'] = false;]

	You would think it might be a nice idea to detect if the client has cookies 
	enabled, and if so, use php sessions, only falling-back to some other method 
	when they have not. However, it is not possible to detect whether or not a 
	client has cookies enabled, with a single request. You need Two. Clearly, 
	that isn't a lot of use for a protection mechanism designed to operate 
	before they have even had one. So you gotta choose, now..

	By default, anti-hammer will use its own session mechanism, writing client-
	unique data to files in a directory of your choosing, irrespective of their 
	ability to accept cookies. As it is an independant system, it in no way 
	interferes with any session magic you may have running on your site, and in 
	most scenarios is just as fast as php's own session handling.

	However, you may wish to use that, instead; particularly if you have 
	millions of hits a day, and your web server stores the php sessions in a 
	some uberfast /tmp space you can't otherwise get to, where the difference 
	might be worth it. Or if in-website space is extremely limited. At any rate, 
	you have a choice.

	NOTE: if you enable this, you will ALWAYS start a php session with each 
	request. This usually presents no problems, but you and your server may know 
	better. Testing is always advised! I ran it this way for may months on 
	corz.org, with no issues whatsoever, and I use php sessions all over the 
	site. If you use proper names in your session, everything should work fine.

	Also NOTE: With this enabled, if the client/spider/script kiddie/etc. has 
	cookies disabled in their web browser, they bypass anti-hammer protection!
	This is why, by default, Anti-Hammer uses its own session mechanism.

	There should be no performance concerns; Anti-Hammer writes the data in the 
	same way as a php session -  it's a simple serialized array in a flat file. 

*/
$anti_hammer['use_php_sessions'] = false; 



/*
:end prefs: */


// let's go..
//


$killpage = false;
$gentime = explode(' ', microtime());
$anti_hammer['now_time'] = $gentime[1].substr($gentime[0], 6, -2);				//	 1/100th of a second accuracy!
settype($anti_hammer['now_time'], "double");									// scientifically tested!
//$anti_hammer['final_time'] = 0;	// will be used to set the retry header on killed page (503)

$anti_hammer_final_time = $CONFIG->anti_hammer_final_time;
$hammerfinaltime = trim($anti_hammer_final_time); // ouput => 0;	// will be used to set the retry header on killed page (503)




// Collect all usable client data..
$anti_hammer['remote_ip']		= $_SERVER['REMOTE_ADDR'];
$anti_hammer['user_agent']		= @$_SERVER['HTTP_USER_AGENT'];
$anti_hammer['referrer']		= @$_SERVER['HTTP_REFERER'];
$anti_hammer['request']			= $_SERVER['REQUEST_URI'];
$anti_hammer['user_accept']		= @$_SERVER['HTTP_ACCEPT'];
$anti_hammer['user_charset']	= @$_SERVER['HTTP_ACCEPT_CHARSET'];
$anti_hammer['user_encoding']	= @$_SERVER['HTTP_ACCEPT_ENCODING'];
$anti_hammer['user_language']	= @$_SERVER['HTTP_ACCEPT_LANGUAGE'];



// Configure the variables to be used in the kill page function

$CONFIG->anti_hammer_log = $anti_hammer['log'];
$CONFIG->anti_hammer_lookup_failures = $anti_hammer['lookup_failures'];
$CONFIG->anti_hammer_remote_ip = $anti_hammer['remote_ip'];
$CONFIG->anti_hammer_request = $anti_hammer['request'];
$CONFIG->anti_hammer_client_id = $anti_hammer['client_id'];
$CONFIG->anti_hammer_user_agent = $anti_hammer['user_agent'];
$CONFIG->anti_hammer_user_accept = $anti_hammer['user_accept'];
$CONFIG->anti_hammer_referrer = $anti_hammer['referrer'];


// Admin Bypass..

// Is this the admin user? let's see..
//if (stristr($anti_hammer['user_agent'], $anti_hammer['admin_agent_string'])) { 
//	return; 
//}


// local server access (for readfile() requests..
// (and as a potential catch-all for user pref errors!))
if ($anti_hammer['remote_ip'] == $_SERVER['SERVER_ADDR']) { 
	return;
}

/*
	  A note about readfile()..

		If you use readfile() to include resources on your pages, remember,
		those requests will come in right after the first, and as they are
		technically brand new hits, they count towards the hammer. 

		Use of include() is preferred. 

		However, the code right above this notice should prevent any issues. If
		it does /not/, and include() isn't working, you might want to hack in 
		the actual IP Address of the local server. See my debug-report.zip for
		a way to easily get this sort of information in your browser.
		
		NOTE: If you are having difficulty include()ing URI resource in your 
		pages, remember you need to enable BOTH php allow_url_* flags (this is 
		the .htaccess version of those two switches..)

		php_flag allow_url_fopen on
		php_flag allow_url_include on
*/



// skip protection for known bots and spiders..
//
// okay, this is some cute code! simple, but effective.
// we load an ini file of user-agent=ip-mask-file pairs, and check our client's
// user agent string for a match (at must match the beginning of the string 
// exactly). If there is a match, we load the associated IP Mask file, and
// run through the IP/masks, again looking for a perfect match at the start of
// the two strings. Commented lines are no problem. We use strpos() for both
// tests, so it's nice and fast, and the IP test covers our comments, too!
//
// having said (coded) all this, you gotta ask yourself, why are they hammering? 
// Surely it would be better get them to slow down, instead!


$IP_file = '';
$anti_hammer['ini_file'] = $anti_hammer['info_path'].'/exemptions/exemptions.ini';


if ($anti_hammer['allow_bots']) {
	$bot_agent_array = read_bots_ini($anti_hammer['ini_file']);
	if (is_array($bot_agent_array)) {
		foreach ($bot_agent_array as $bot_agent_string => $IP_file) {
			if ($bot_agent_string and strpos($anti_hammer['user_agent'], $bot_agent_string) === 0) {
				break;
			}
		}
		if ($IP_file) {
			$ip_array = file($anti_hammer['info_path'].'/exemptions/'.$IP_file);
		}
		if (is_array($ip_array)) {
			foreach($ip_array as $bot_ip) {
				if (@strpos($anti_hammer['remote_ip'], trim($bot_ip)) === 0) {
					if ($anti_hammer['allow_bots'] > 1) {
						$anti_hammer['hammer_time'] = $anti_hammer['allow_bots'];
					} else {
						return;
					}
				}
			}
		}
	}
}

// User prefs..
// Get user values into usable arrays, do some error-checking.


// trigger thresholds..
if (!stristr($anti_hammer['trigger_levels'], ',') or (str_word_count($anti_hammer['trigger_levels'], 0, "0123456789") != 4)) { 
	$anti_hammer['trigger_levels'] = '5,10,20,30';
}
// A neat way to create a array from numeric prefs..
$anti_hammer['trigger_levels'] = str_word_count($anti_hammer['trigger_levels'], 1, "0123456789");

// Get user penalty times into correct values..
if (!stristr($anti_hammer['waiting_times'], ',') or (str_word_count($anti_hammer['waiting_times'], 0, "0123456789") != 4)) { 
	$anti_hammer['waiting_times'] = '3,5,10,20';
}
$anti_hammer['waiting_times'] = str_word_count($anti_hammer['waiting_times'], 1, "0123456789");


//if ($ah_type_ok /* still! *//* == false) { return; } */



/*

	okay, let's do it..

						*/



// read session data..
$session = array();

if ($anti_hammer['use_php_sessions']) {

	// Regular php session..
	session_start();
	$session = $_SESSION['anti_hammer'];


} else {

	// Anti-Hammer's built-in session mechanism..

	// Create a unique Client ID for this client..
	// we simply MD5 all the browser data concatenated together (and blanks are not a problem)..
	$anti_hammer['client_id'] = md5($anti_hammer['user_agent'].
									$anti_hammer['user_accept'].
									$anti_hammer['user_language'].
									$anti_hammer['user_encoding'].
									$anti_hammer['user_charset'].
									$anti_hammer['remote_ip']);
	$fake_sess_file = $anti_hammer['info_path'].'/'.$anti_hammer['ID_prefix'].$anti_hammer['client_id'];
	

	
	if (file_exists($fake_sess_file)) {
	
	
                
		$session = read_fake_session($fake_sess_file);  // If we have the file, let us read it
		

	}
		
}
/*
	Useful use of a "cat"..

	It seems to me that I unwittingly created a system whereby the less 
	information a client is wiling to give, the more likely they are to be 
	banned. I say "seems", because we create an md5 of this information, so the 
	actual likelyhood of colliding session ID's is astronomically low. However, 
	I like the *principle* of the thing.
*/



// Calculate the Hammer Rate..


//

// How much time since their last request (in 100/th Second)
//$hammer_rate = $anti_hammer['now_time'] - @$session['start_time'] + 1; // Original

$hammer_rate = $anti_hammer['now_time'] - @$session['start_time'] + 1; // TM

// Their ban has elapsed (but GC has not swept up their session)..
if ($hammer_rate > ($anti_hammer['ban_time']*60*60*100)) {	// 8640000 = 24 hours (in 100th/second)
	$session['start_time'] = $anti_hammer['now_time'] - 1;
	
//var_dump($session['start_time']); //TM: output nothing
	
	$hammer_rate = $anti_hammer['hammer_time'];
	$session['hammer'] = $anti_hammer['trigger_levels'][0]-1; // repeat-offenders do not get to start from 0!
	unset($session['cut_off']);
	// do not return here - we still need to write the updated session data.
}


// CUT_OFF has already been set -- BYE NOW!
if ($anti_hammer['cut_off'] and isset($session['cut_off'])) { die(); }


// okay, still here..

// Start with Garbage Collection..
if (!$anti_hammer['use_php_sessions']) { 
	CollectGarbage($anti_hammer['info_path'].'/Counter', $anti_hammer['GC_limit']);
}

// Anti-Hammer Protection has been activated!
if ($hammer_rate < $anti_hammer['hammer_time']) 
{

	$retry_str = 'a few ';
	@$session['hammer'] += 1;

	if ($session['hammer'] > ($anti_hammer['trigger_levels'][0]-1)) {

		// cut-off..
		if ($anti_hammer['cut_off'] and $session['hammer'] > $anti_hammer['cut_off']) {
			$anti_hammer['kill_msg'] = $anti_hammer['cut_off_msg'];
			$session['cut_off'] = true;
		}
		if ($anti_hammer['cut_off'] and $session['hammer'] == $anti_hammer['cut_off']) {
			$anti_hammer['kill_msg'] = '<h1>THIS IS YOUR LAST WARNING!</h1>'.$anti_hammer['kill_msg'];
		}

		// rolling ban time, increments with each hammer..
		if ($anti_hammer['rolling_trigger']) {
			$session['start_time'] = $anti_hammer['now_time'] + (($session['hammer']*100)-1);
			$retry_str = ah_int2eng($session['hammer']);
		} else {
			// predefined ban levels.. these are more effective, as they shock the user with increasing jumps!
			if (($session['hammer'] > $anti_hammer['trigger_levels'][0]) and ($session['hammer'] <= $anti_hammer['trigger_levels'][1])) {
				// we simply nudge their start time forward by *this* many seconds (into the future!)..
				$session['start_time'] = $anti_hammer['now_time'] + (($anti_hammer['waiting_times'][0]*100)-1); // 299 = Three second penalty.
				$retry_str = ah_int2eng($anti_hammer['waiting_times'][0]);

			} elseif (($session['hammer'] > $anti_hammer['trigger_levels'][1]) and ($session['hammer'] <= $anti_hammer['trigger_levels'][2])) {
				$session['start_time'] = $anti_hammer['now_time'] + (($anti_hammer['waiting_times'][1]*100)-1); // Five second penalty! (by default)
				$retry_str = ah_int2eng($anti_hammer['waiting_times'][1]);

			} elseif (($session['hammer'] >= $anti_hammer['trigger_levels'][2]) and ($session['hammer'] <= $anti_hammer['trigger_levels'][3])) {
				$session['start_time'] = $anti_hammer['now_time'] + (($anti_hammer['waiting_times'][2]*100)-1); // Ten second penalty! (etc.)
				$retry_str = ah_int2eng($anti_hammer['waiting_times'][2]);

			} elseif ($session['hammer'] >= $anti_hammer['trigger_levels'][3]) {
				$session['start_time'] = $anti_hammer['now_time'] + (($anti_hammer['waiting_times'][3]*100)-1); // Twenty second penalty!
				$retry_str = ah_int2eng($anti_hammer['waiting_times'][3]);
			}
		}
		$killpage = true;
	}

} else {
	$session['start_time'] = $anti_hammer['now_time'];
	
}


// write client session data..

// TM: START CODE

$CONFIG->anti_hammer_session_hammer = $session['hammer'];

   $array["start_time"] =  $session['start_time'];
   $array["hammer"] = $session['hammer'];
    

write_fake_session($fake_sess_file, $array); // Write function working perfect



if ($killpage) {
	$km = '<!DOCTYPE HTML SYSTEM><html><head><title>'.$anti_hammer['page_title'].'</title><style> 

body{font-family:Lucida Grande, Verdana, Geneva, Sans-serif;font-size:14px;color:#333;background-color:#fff;margin:0;padding:0}a{color:#0134c5;background-color:transparent;text-decoration:none;font-weight:400;outline-style:none}a:visited{color:#0134c5;background-color:transparent;text-decoration:none;outline-style:none}a:hover{color:#000;text-decoration:none;background-color:transparent;outline-style:none}#babakichwa{border-bottom:1px solid #999;margin:0 40px 0 35px;padding:0 0 0 6px}#babakichwa h1{background-color:transparent;color:#e13300;font-size:18px;font-weight:400;margin:0;padding:0 0 6px}#zoezi{margin:20px 40px 0;padding:0}#zoezi p{margin:12px 20px 12px 0}#zoezi h1{color:#e13300;border-bottom:1px solid #666;background-color:transparent;font-weight:400;font-size:24px;margin:0 0 20px;padding:3px 0 7px 3px}#zoezi h2{background-color:transparent;border-bottom:1px solid #999;color:#000;font-size:18px;font-weight:700;margin:28px 0 16px;padding:5px 0 6px}#zoezi h3{background-color:transparent;color:#333;font-size:16px;font-weight:700;margin:16px 0 15px;padding:0}#zoezi h4{background-color:transparent;color:#444;font-size:14px;font-weight:700;margin:22px 0 0;padding:0}#zoezi img{margin:auto;padding:0}#zoezi .path{background-color:#EBF3EC;border:1px solid #99BC99;color:#005702;text-align:center;margin:0 0 14px;padding:5px 10px 5px 8px}#zoezi dfn{font-family:Lucida Grande, Verdana, Geneva, Sans-serif;color:#00620C;font-weight:700;font-style:normal}#zoezi var{font-family:Lucida Grande, Verdana, Geneva, Sans-serif;color:#8F5B00;font-weight:700;font-style:normal}#zoezi samp{font-family:Lucida Grande, Verdana, Geneva, Sans-serif;color:#480091;font-weight:700;font-style:normal}#zoezi kbd{font-family:Lucida Grande, Verdana, Geneva, Sans-serif;color:#A70000;font-weight:700;font-style:normal}#zoezi ul{list-style-image:url(images/arrow.gif);margin:10px 0 12px}li.reactor{list-style-image:url(images/reactor-bullet.png)}#zoezi li{margin-bottom:9px}#zoezi li p{margin-left:0;margin-right:0}#zoezi .tableborder{border:1px solid #999}#zoezi th{font-weight:700;text-align:left;font-size:12px;background-color:#666;color:#fff;padding:4px}#zoezi .td{font-weight:400;font-size:12px;background-color:#f3f3f3;padding:6px}#zoezi .tdpackage{font-weight:400;font-size:12px}#zoezi .borasana{background:#FBE6F2;border:1px solid #D893A1;color:#333;margin:10px 0 5px;padding:10px}#zoezi .borasana p{margin:6px 0 8px;padding:0}#zoezi .borasana .leftpad{padding-left:20px;margin:6px 0 8px}#zoezi .critical{background:#FBE6F2;border:1px solid #E68F8F;color:#333;margin:10px 0 5px;padding:10px}#zoezi .critical p{margin:5px 0 6px;padding:0}#zoezi code,#zoezi pre{font-family:Monaco, Verdana, Sans-serif;font-size:12px;background-color:#f9f9f9;border:1px solid #D0D0D0;color:#002166;display:block;margin:14px 0;padding:12px 10px}
	
</style></head><body><div id="babakichwa"><table cellpadding:0; cellspacing:0; border:0; style= "width:100%" ><tr><td><h1>'.$anti_hammer['kill_msg'].'</h1></td></tr></table></div>';	
	
	
	if (!isset($session['cut_off'])) {
		$km .= '
	<!-- START HAMMER CONTENT -->
	<div id="zoezi">	
		
		
	<h1>You must wait '.$retry_str.'seconds before trying again.<br /></h1>
	<br />
	<p class="borasana"><strong>Note:</strong> If you believe this is in error,<strong> please mail</strong> '.$anti_hammer['webmaster'].' about it!<br />
		&lt;'.$anti_hammer['error_mail'].'&gt;<br /></p>
		
	</div>
	<!-- END HAMMER CONTENT -->
		
<span style="font-size:x-small;position:fixed;bottom:1em;right:1em;"><a title="Automatically ban web site hammers! Protect your valuable server resources for *genuine* clients" 
id="link-Get-Elgg-Hammer" href="http://community.elgg.org/profile/Thomasondiba/">Get Twizanex-Hammer protection for your own site!</a></span></body></html>';
	}
	
	kill_page($km);
}


if (function_exists('debug')) { debug('out');  } //:debug:


//2do..
// include auto-ban.php ? hmm.



/*

	fin

			*/


// You're outta here!
function kill_page($msg) {

//global $anti_hammer;

global $CONFIG;

// anti_hammer_file prefix to be checked

$anti_hammer_kill_time = $CONFIG->anti_hammer_final_time;
$hammerfinalkilltime = trim($anti_hammer_kill_time); // ouput => 0;


// THis is counts $GLOBALS['session']['hammer']

$anti_hammer_session_hammer = $CONFIG->anti_hammer_session_hammer;
$hammersession = trim($anti_hammer_session_hammer);

	$r_host = '';
	if ($CONFIG->anti_hammer_lookup_failures) {
		$r_host = gethostbyaddr($CONFIG->anti_hammer_remote_ip).' ';

	}
	if (file_exists(dirname($CONFIG->anti_hammer_log))) {
		$this_hit = ''
		."page:   "."\t".$CONFIG->anti_hammer_request."\n"
		."time:   "."\t".date('Y.m.d h:i:s A')."\t".'ID: '.$CONFIG->anti_hammer_client_id."\t"."x ".$hammersession."\n"
		."visitor:"."\t".$r_host.'['.$CONFIG->anti_hammer_remote_ip.']'."\t"."(".$CONFIG->anti_hammer_user_agent.")"."\n"
		."accepts:"."\t".$CONFIG->anti_hammer_user_accept."\n"
		."referer:"."\t".$CONFIG->anti_hammer_referrer."\n"
		;
		add_data($CONFIG->anti_hammer_log, $this_hit."\n");
	}
	header('Content-Type: text/html; charset=utf-8');	// Old IE probably still won't play ball, though.
	header('HTTP/1.1 503 Service Temporarily Unavailable');
	// For CGI/*suexec use..
	if (substr(php_sapi_name(), 0, 3) == 'cgi') { header('Status: 503 Service Temporarily Unavailable'); }
	header('Retry-After: '.($hammerfinalkilltime +1)); // the calculation needs to be enclosed in braces to work.
	die($msg);	
}

// write the updated hammer info to the fake/session file..
function SetHammer() {


	if ($GLOBALS['anti_hammer']['use_php_sessions']) {		
		$_SESSION['anti_hammer']['start_time'] = $GLOBALS['session']['start_time'];
		$_SESSION['anti_hammer']['hammer'] = $GLOBALS['session']['hammer'];
		$_SESSION['anti_hammer']['cut_off'] = $GLOBALS['session']['cut_off'];
	} 
}


/*
	Append data to a file.
	Pass true as the 3rd paramater to wipe the file.
											*/
function add_data($file, $data, $wipe=false) {
	// if it's not there, try to create it..
	if (!file_exists($file)) $fp = fopen($file, 'wb');
	
	$flag = 'ab'; 
	if ($wipe) { $flag = 'wb'; }

	if (is_writable($file)) {
		$fp = fopen($file, $flag);
		$lock = flock($fp, LOCK_EX);
		if ($lock) {
			fwrite($fp, $data); 
			flock ($fp, LOCK_UN);
		} else { 
			$GLOBALS['errors']['add_data'] = "couldn't lock $file"; 
		}
		fclose($fp);
	} else { 
		$GLOBALS['errors']['add_data'] = "can't write to $file"; 
	}
}


// read serialized array data from a file, and return as an array..
function read_fake_session($no_cookie_file) {

	if (file_exists($no_cookie_file)) {
		$file_handle = fopen($no_cookie_file, 'rb');
		$file_contents = @fread($file_handle, filesize($no_cookie_file));
		fclose($file_handle);
	} else { return false; }
	$file_contents = unserialize($file_contents);
	if (is_array($file_contents)) { 
		return $file_contents; 
	}
}

// serialize an array and write the string data to a file..
function write_fake_session($no_cookie_file, $array) {

	$data = serialize($array);
	if (empty($data)) { return; }
	$fp = @fopen($no_cookie_file, 'wb'); 
	if ($fp) {
		$lock = flock($fp, LOCK_EX);
		if ($lock) {
			fwrite($fp, $data); 
			flock ($fp, LOCK_UN);
		}
		fclose($fp);
		clearstatcache();
		return (1);
	}
}



/*

	CollectGarbage

	You couldtransplant this into another web app fairly easily.
	Useful.

*/
function CollectGarbage($count_file, $limit) {

global $CONFIG;

// anti_hammer_file prefix to be checked

$keronchehost = $CONFIG->anti_hammer_prefix;
$datakeronche = trim($keronchehost);

// Gc_age limits

$keroncheage = $CONFIG->anti_hammer_GC_age;
$agekeronche = trim($keroncheage);

	if ($limit === 0) { return; }
	if (increment_hit_counter($count_file) >= $limit) {

		$file_list = array();
		if ($the_dir = @opendir(dirname($count_file))) {
			while (false != ($file = readdir($the_dir))) {
                                if ((ord($file) != 46) and strpos($file, $datakeronche) === 0) { // TM
					$file_path = dirname($count_file).'/'.$file;
					if (filemtime($file_path)  < (time() - $agekeronche*60*60)) {
						unlink($file_path);
					}
				}
			}
		} 
		increment_hit_counter($count_file, 0, 1); // reset the counter
	}
}

//2do..
//		Run this in another thread? Or maybe a simple http request, perhaps 
//		with $_GET, to flip Ant-Hammer to GC mode in the Background - this task 
//		could be done after the request is already sent, even simultaneously;
//		there may be a *lot* of files in this directory.
//
//		Having said that, it's *very* fast, and only runs once per 10,000 or so
//		($limit) hits.
//


/*
increment a counter()	
from my "file-tools.php", available elsewhere.
																			*/
function increment_hit_counter($count_file, $report_only=false, $reset=false) {

	$count = false;

	if (!file_exists($count_file) or $reset) {
		$file_pointer = fopen($count_file, 'wb');
		fwrite ($file_pointer, '0');
		fclose ($file_pointer);
	}

	// now the counter..
	if (file_exists($count_file)) {

		// read in the old score..
		$count = trim(file_get_contents($count_file));
		if ($report_only) { return $count; }
		if (!$count) { $count = 0; }
		$count++;
		
		// write out new score..
		if (is_writable($count_file)) {
			$file_pointer = fopen($count_file, 'wb+');
			$lock = flock($file_pointer, LOCK_EX);
				if ($lock) {
					fwrite($file_pointer, $count); 
					flock ($file_pointer, LOCK_UN);
				}
				fclose($file_pointer);
				clearstatcache();
		}
	}
	return $count;
}



/*
	Integers To English Words.

	Converts 1145432 into..

	"one million, one hundred and forty five thousand, four hundred and thirty two"

	Fairly groovy. ;o)

	The regular version is in my "text-func.php", with some other stuff.

							*/
function ah_int2eng($number) {

global $CONFIG;

// anti_hammer_file prefix to be checked

$keronchetime = $CONFIG->anti_hammer_final_time;
$timekeronche = trim($keronchetime);

	$output = '';
	if ($number < 1) $number = 1;

    $timekeronche = $number;
    

	$units = array(' ', 'one ', 'two ', 'three ', 'four ', 'five ', 'six ', 'seven ', 'eight ', 'nine ');
	$teens = array('ten ', 'eleven ', 'twelve ', 'thirteen ', 'fourteen ', 'fifteen ', 'sixteen ', 'seventeen ', 'eighteen ', 'nineteen ');
	$tenners = array('', '', 'twenty ', 'thirty ', 'fourty ', 'fifty ', 'sixty ', 'seventy ', 'eighty ', 'ninety ');

	$lint = strlen($number);
	if ($lint > 2) $bigger = true;

	for ($x = $lint ; $x >= 1 ; $x--) {	
	
		$last = substr($output, -5, 4);
		$digit = substr($number, 0, 1);
		$number = substr($number, 1);
	
		if ($x % 3 == 2) {
		
			if ($digit == 1) { // 10-19..
				$digit = substr($number, 0, 1);
				$number = substr($number, 1);
				$x--;
				if ($last == 'sand') { $output .= 'and '; }
				$output .= $teens[$digit];
				
			} else { // 20-99..
			
				if (($last == 'sand') ) { $output .= 'and '; }
				$output .= $tenners[$digit];
			}
		} else {
			if (($x % 3 != 1) and ($digit > 0) and (!empty($output))) { $output .= ', '; }
			$output .= $units[$digit];
		}
		if ((strlen($number) % 3) == 0) {
			$bignum = ah_bignumbers(strlen($number) / 3);
			if (($last == 'dred') and ($bignum != 'thousand')) { $output .= 'and ';}
			$output .= $bignum;
		}
		if ((strlen($number) % 3) == 2 and $digit > 0) {
			$output .= 'hundred and ';
		}
	}
	
	// clean up the output..
	$output = str_replace('  ', ' ', $output);
	$output = str_replace('red and thou', 'red thou', $output);
	$output = str_replace('red and mill', 'red mill', $output);
	$output = str_replace('lion thousand', 'lion ', $output);
	if (substr($output, -5) == ' and ') { $output = substr($output, 0, -5).' '; }
	
return $output;
}


/*
it just looks better, okay!	*/

	function ah_bignumbers($test) {
		switch ($test) {
			case 0:
			$test = "";
			break;
			case 1:
			$test = "thousand";
			break;
			case 2:
			$test = "million";
			break;
			case 3:
			$test = "trillion"; // <- that's a lot of comments!
			break;
		}
	return $test;
}


/*
	function read_ini()		[from my 'ini-tools.php']

	pull the data from the ini file and return as an array

	Usage: array (string {path to file})

	returns false on failure.
								*/
function read_bots_ini($data_file) {
	$ini_array = array();
	if (is_readable($data_file)) {
		$file = file($data_file);
		foreach($file as $conf) {
			// if first real character isn't '#' or ';' and there is a '=' in the line..
			if ( (substr(trim($conf),0,1) != '#')
				and (substr(trim($conf),0,1) != ';')
				and (substr_count($conf,'=') >= 1) ) {
				$eq = strpos($conf, '=');
				$ini_array[trim(substr($conf,0,$eq))] = trim(substr($conf, $eq + 1));
			}
		}
		unset($file);
		return $ini_array;
		
		
		
	} else {
		$GLOBALS['errors']['read_bots_ini'] = "ini file: $file does not exist.";
		return false;
	}
}