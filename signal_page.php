<?php

// Start_session, check if user is logged in or not, and connect to the database all in one included file
include_once("scripts/checkuserlog.php");
// Include the class files for auto making links out of full URLs and for Time Ago date formatting
include_once("wi_class_files/autoMakeLinks.php");
include_once ("wi_class_files/agoTimeFormat.php");
// Create the two new objects before we can use them below in this script
$activeLinkObject = new autoActiveLink;
$myObject = new convertToAgo;
?>
<?php 
// ------- INITIALIZE SOME VARIABLES ---------
// they must be initialized in some server environments or else errors will get thrown
$id = "";
$username = "";
$firstname = "";
$lastname = "";
$mainNameLine = "";
$country = "";	
$state = "";
$city = "";
$zip = "";
$bio_body = "";
$website = "";
$locationInfo = "";
$user_pic = "";
$sigDisplayList = "";
$interactionBox = "";
$cacheBuster = rand(999999999,9999999999999); // Put on an image URL will help always show new when changed
// ------- END INITIALIZE SOME VARIABLES ---------

// ------- ESTABLISH THE PAGE ID ACCORDING TO CONDITIONS ---------
if (isset($_GET['id'])) {
	 $id = preg_replace('#[^0-9]#i', '', $_GET['id']); // filter everything but numbers
} else if (isset($_SESSION['idx'])) {
	 $id = $logOptions_id;
} else {
   header("location: index.php");
   exit();
}
// ------- END ESTABLISH THE PAGE ID ACCORDING TO CONDITIONS ---------

// ------- FILTER THE ID AND QUERY THE DATABASE --------
$id = preg_replace('#[^0-9]#i', '', $id); // filter everything but numbers on the ID just in case
$sql = mysql_query("SELECT * FROM myMembers WHERE id='$id' LIMIT 1"); // query the member
// ------- FILTER THE ID AND QUERY THE DATABASE --------

// ------- MAKE SURE PERSON EXISTS IN DATABASE ---------
$existCount = mysql_num_rows($sql); // count the row nums
 if ($existCount == 0) { // evaluate the count
	 header("location: index.php?msg=user_does_not_exist");
     exit();
}
// ------- END MAKE SURE PERSON EXISTS IN DATABASE ---------

// ------- WHILE LOOP FOR GETTING THE MEMBER DATA ---------
while($row = mysql_fetch_array($sql)){ 
    $username = $row["username"];
	$firstname = $row["firstname"];
	$lastname = $row["lastname"];
	$country = $row["country"];	
	$state = $row["state"];
	$city = $row["city"];
	$sign_up_date = $row["sign_up_date"];
    $sign_up_date = strftime("%b %d, %Y", strtotime($sign_up_date));
	$last_log_date = $row["last_log_date"];
    $last_log_date = strftime("%b %d, %Y", strtotime($last_log_date));	
	$bio_body = $row["bio_body"];	
	$bio_body = str_replace("&amp;#39;", "'", $bio_body);
	$bio_body = stripslashes($bio_body);
	$website = $row["website"];
	$friend_array = $row["friend_array"];
	///////  Mechanism to Display Pic. See if they have uploaded a pic or not  //////////////////////////
	$check_pic = "members/$id/image01.jpg";
	$default_pic = "members/0/image01.jpg";
	if (file_exists($check_pic)) {
    $user_pic = "<img src=\"$check_pic?$cacheBuster\" width=\"218px\" />"; 
	} else {
	$user_pic = "<img src=\"$default_pic\" width=\"218px\" />"; 
	}
	///////  Mechanism to Display Real Name Next to Username - real name(username) //////////////////////////
	if ($firstname != "" && $lastname != "") {
        $mainNameLine = "$firstname $lastname";
		$username = $firstname;
	} else {
		$mainNameLine = $username;
	}
    ///////  Mechanism to Display Website URL or not  //////////////////////////
	if ($website == "") {
    $website = "";
	} else {
	$website = '<br /><br /><img src="images/websiteIcon.jpg" width="18" height="12" alt="Website URL for ' . $username . '" /> <strong>Website:</strong><br /><a href="http://' . $website . '" target="_blank">' . $website . '</a>'; 
	}
	///////  Mechanism to Display About me text or not  //////////////////////////
	if ($bio_body == "") {
    $bio_body = "";
	} else {
	$bio_body = '<div class="infoBody">' . $bio_body . '</div>'; 
	}
	///////  Mechanism to Display Location Info or not  //////////////////////////
	if ($country == "" && $state == "" && $city == "") {
    $locationInfo = "";
	} else {
	$locationInfo = "$city &middot; $state<br />$country ".'<a href="#" onclick="return false" onmousedown="javascript:toggleViewMap(\'google_map\');">view map</a>'; 
	}
} // close while loop
// ------- END WHILE LOOP FOR GETTING THE MEMBER DATA ---------

// ------- DETECT USER DEVICE ----------
$user_device = "";
$agent = $_SERVER['HTTP_USER_AGENT'];
if (preg_match("/iPhone/", $agent)) {
   $user_device = "iPhone Mobile";
} else if (preg_match("/Android/", $agent)) {
    $user_device = "Android Mobile";
} else if (preg_match("/IEMobile/", $agent)) {
    $user_device = "Windows Mobile";
} else if (preg_match("/Chrome/", $agent)) {
    $user_device = "Google Chrome";
} else if (preg_match("/MSIE/", $agent)) {
    $user_device = "Internet Explorer";
} else if (preg_match("/Firefox/", $agent)) {
    $user_device = "Firefox";
} else if (preg_match("/Safari/", $agent)) {
    $user_device = "Safari";
} else if (preg_match("/Opera/", $agent)) {
    $user_device = "Opera";
}
$OSList = array
(
        // Match user agent string with operating systems
        'Windows 3.11' => 'Win16',
        'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)',
        'Windows 98' => '(Windows 98)|(Win98)',
        'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
        'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
        'Windows Server 2003' => '(Windows NT 5.2)',
        'Windows Vista' => '(Windows NT 6.0)',
        'Windows 7' => '(Windows NT 6.1)|(Windows NT 7.0)',
        'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
        'Windows ME' => 'Windows ME',
        'Open BSD' => 'OpenBSD',
        'Sun OS' => 'SunOS',
        'Linux' => '(Linux)|(X11)',
        'Mac OS' => '(Mac_PowerPC)|(Macintosh)',
        'QNX' => 'QNX',
        'BeOS' => 'BeOS',
        'OS/2' => 'OS/2',
		'Mac OS' => 'Mac OS',
        'Search Bot'=>'(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(MSNBot)|(Ask Jeeves/Teoma)|(ia_archiver)'
);
 
// Loop through the array of user agents and matching operating systems
foreach($OSList as $CurrOS=>$Match) {
        // Find a match
        if (eregi($Match, $agent)) {
                break;
        } else {
			$CurrOS = "Unknown OS";
		}
}
$device = "$user_device : $CurrOS";
// ------- END DETECT USER DEVICE ----------

// ------- POST NEW sig TO DATABASE ---------
$sig_outout_msg = "";
if (isset($_POST['sig_field']) && $_POST['sig_field'] != "" && $_POST['sig_field'] != " "){
	
	 $sigWipit = $_POST['sigWipit'];
     $sessWipit = base64_decode($_SESSION['wipit']);
	 if (!isset($_SESSION['wipit'])) {
		 
	 } else if ($sigWipit == $sessWipit) {
	 	 // Delete any sigs over 50 for this member
	 	 $sqlDeletesigs = mysql_query("SELECT * FROM sig WHERE mem_id='$id' ORDER BY sig_date DESC LIMIT 50");
	 	 $bi = 1;
		  while ($row = mysql_fetch_array($sqlDeletesigs)) {
		 	 $blad_id = $row["id"];
			  if ($bi > 20) {
			  	 $deletesigs = mysql_query("DELETE FROM sig WHERE id='$blad_id'");
		 	 }
		 	 $bi++;
		  }
		  // End Delete any sigs over 20 for this member
	 	 $sig_field = $_POST['sig_field'];
	 	 $sig_field = stripslashes($sig_field);
	 	 $sig_field = strip_tags($sig_field);
	 	 $sig_field = mysql_real_escape_string($sig_field);
	 	 $sig_field = str_replace("'", "&#39;", $sig_field);

		//take the signals from the sig_field
		 preg_match_all("/#\w+/i", $sig_field, $matches);
		 
		 
		//store the signals into a variable called $signals
		 foreach( $matches[0] as &$value) {
			$signals = $signals . " " . $value;
		}

		 $sql = mysql_query("INSERT INTO sig (mem_id, the_sig, sig_date, sig_type, device, signals ) VALUES('$id','$sig_field', now(),'a','$device', '$signals')") or die (mysql_error());
	 	 $sig_outout_msg = "";

	 	 }
}
// ------- END POST NEW Sig TO DATABASE ---------

// ------- MEMBER sigS OUTPUT CONSTRUCTION ---------
///////  Mechanism to Display Pic
	if (file_exists($check_pic)) {
    $sig_pic = '<div style="overflow:hidden; height:40px;"><a href="signal_page.php?id=' . $id . '"><img src="' . $check_pic . '" width="40px" border="0" /></a></div>'; 
	} else {
	$sig_pic = '<div style="overflow:hidden; height:40px;"><a href="signal_page.php?id=' . $id . '"><img src="' . $default_pic . '" width="40px" border="0" /></a></div>'; 
	}
///////  END Mechanism to Display Pic	
$sql_sigs = mysql_query("SELECT id, mem_id, the_sig, sig_date, sig_type, device, signals FROM sig WHERE mem_id='$id' ORDER BY sig_date DESC LIMIT 30");

while($row = mysql_fetch_array($sql_sigs)){
	
	$sigid = $row["id"];
	$sig_id = $row["mem_id"];
	$the_sig = $row["the_sig"];
	$the_sig = ($activeLinkObject -> makeActiveLink($the_sig));
	$sig_date = $row["sig_date"];
	$convertedTime = ($myObject -> convert_datetime($sig_date));
    $whensig = ($myObject -> makeAgo($convertedTime));
	$sig_date = $row["sig_date"];
	$sig_type = $row["sig_type"];
	$sig_device = $row["device"];
	$display_signals = $row["signals"];
	
	// Make a break for the spaces

	$display_signals2 = str_replace(" ", "<br />", $display_signals);
	
	
				$sigDisplayList .= '
			        <table style="background-color:#FFF; border:#999 1px solid; border-top:none;" cellpadding="5" width="100%">
					<tr>
					<td width="10%" valign="top">' . $sig_pic . '</td>
					<td width="90%" valign="top" style="line-height:1.5em;">
					<span class="liteGreyColor textsize9">' . $whensig . ' <a href="signal_page.php?id=' . $sig_id . '"><strong>' . $mainNameLine . '</strong></a> via <em>' . $sig_device . '</em></span><br />
					 ' . $the_sig . '
            </td>
            </tr></table>';
	
}
// ------- END MEMBER sigS OUTPUT CONSTRUCTION ---------

// ------- ESTABLISH THE PROFILE INTERACTION TOKEN ---------
$thisRandNum = rand(9999999999999,999999999999999999);
$_SESSION['wipit'] = base64_encode($thisRandNum); // Will always overwrite itself each time this script runs
// ------- END ESTABLISH THE PROFILE INTERACTION TOKEN ---------

// ------- EVALUATE WHAT CONTENT TO PLACE IN THE MEMBER INTERACTION BOX -------------------
// initialize some output variables
$friendLink = "";
$the_sig_form = "";
if (isset($_SESSION['idx']) && $logOptions_id != $id) { // If SESSION idx is set, AND it does not equal the signal_page owner's ID

	// SQL Query the friend array for the logged in viewer of this signal_page if not the owner
	$sqlArray = mysql_query("SELECT friend_array FROM myMembers WHERE id='" . $logOptions_id ."' LIMIT 1"); 
	while($row=mysql_fetch_array($sqlArray)) { $iFriend_array = $row["friend_array"]; }
	 $iFriend_array = explode(",", $iFriend_array);
	if (in_array($id, $iFriend_array)) { 
	    $friendLink = '<a href="#" onclick="return false" onmousedown="javascript:toggleInteractContainers(\'remove_friend\');">Remove Friend</a>';
	} else {
	    $friendLink = '<a href="#" onclick="return false" onmousedown="javascript:toggleInteractContainers(\'add_friend\');">Add as Friend</a>';	
	}
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$interactionBox = '<br /><br /><div class="interactionLinksDiv">
		   ' . $friendLink . ' 
           <a href="#" onclick="return false" onmousedown="javascript:toggleInteractContainers(\'private_message\');">Private Message</a>
          </div><br />';
		  $the_sig_form = '<div style="background-color:#BDF; border:#999 1px solid; padding:8px;">
          <textarea name="sig_field" rows="3" style="width:99%;"></textarea>
          <strong>Write on ' . $username . '\'s Board (coming soon)</strong>
          </div>';
} else if (isset($_SESSION['idx']) && $logOptions_id == $id) { // If SESSION idx is set, AND it does equal the signal_page owner's ID
	$interactionBox = '<br /><br /><div class="interactionLinksDiv">
           <a href="#" onclick="return false" onmousedown="javascript:toggleInteractContainers(\'friend_requests\');">Friend Requests</a>
          </div><br />';
		  $the_sig_form = ' ' . $sig_outout_msg . '
          <div style="background-color:#BDF; border:#999 1px solid; padding:8px;">
          <form action="signal_page.php" method="post" enctype="multipart/form-data" name="sig_from">
          <textarea name="sig_field" rows="3" style="width:99%;"></textarea>
		  <input name="sigWipit" type="hidden" value="' . $thisRandNum . '" />
          <strong>sig away ' . $username . '</strong> (220 char max) <input name="submit" type="submit" value="sig" />
          </form></div>';
} else { // If no SESSION id is set, which means we have a person who is not logged in
	$interactionBox = '<div style="border:#CCC 1px solid; padding:5px; background-color:#E4E4E4; color:#999; font-size:11px;">
           <a href="register.php">Sign Up</a> or <a href="login.php">Log In</a> to interact with ' . $username . '
          </div>';
		  $the_sig_form = '<div style="background-color:#BDF; border:#999 1px solid; padding:8px;">
          <textarea name="sig_field" rows="3" style="width:99%;"></textarea>
          <a href="register.php">Sign Up</a> or <a href="login.php">Log In</a> to write on ' . $username . '\'s Board
          </div>';
}
// ------- END EVALUATE WHAT CONTENT TO PLACE IN THE MEMBER INTERACTION BOX -------------------

// ------- POPULATE FRIEND DISPLAY LISTS IF THEY HAVE AT LEAST ONE FRIEND -------------------
$friendList = "";
$friendPopBoxList = "";
if ($friend_array  != "") { 
	// ASSEMBLE FRIEND LIST AND LINKS TO VIEW UP TO 6 ON PROFILE
	$friendArray = explode(",", $friend_array);
	$friendCount = count($friendArray);
    $friendArray6 = array_slice($friendArray, 0, 6);
	
	$friendList .= '<div class="infoHeader">' . $username . '\'s Friends (<a href="#" onclick="return false" onmousedown="javascript:toggleViewAllFriends(\'view_all_friends\');">' . $friendCount . '</a>)</div>';
    $i = 0; // create a varible that will tell us how many items we looped over 
	 $friendList .= '<div class="infoBody" style="border-bottom:#666 1px solid;"><table id="friendTable" align="center" cellspacing="4"></tr>'; 
    foreach ($friendArray6 as $key => $value) { 
        $i++; // increment $i by one each loop pass 
		$check_pic = 'members/' . $value . '/image01.jpg';
		    if (file_exists($check_pic)) {
				$frnd_pic = '<a href="signal_page.php?id=' . $value . '"><img src="' . $check_pic . '" width="54px" border="1"/></a>';
		    } else {
				$frnd_pic = '<a href="signal_page.php?id=' . $value . '"><img src="members/0/image01.jpg" width="54px" border="1"/></a> &nbsp;';
		    }
			$sqlName = mysql_query("SELECT username, firstname FROM myMembers WHERE id='$value' LIMIT 1") or die ("Sorry we had a mysql error!");
		    while ($row = mysql_fetch_array($sqlName)) { $friendUserName = substr($row["username"],0,12); $friendFirstName = substr($row["firstname"],0,12);}
			if (!$friendUserName) {$friendUserName = $friendFirstName;} // If username is blank use the firstname... programming changes in v1.32 call for this
			if ($i % 6 == 4){
				$friendList .= '<tr><td><div style="width:56px; height:68px; overflow:hidden;" title="' . $friendUserName . '">
				<a href="signal_page.php?id=' . $value . '">' . $friendUserName . '</a><br />' . $frnd_pic . '
				</div></td>';  
			} else {
				$friendList .= '<td><div style="width:56px; height:68px; overflow:hidden;" title="' . $friendUserName . '">
				<a href="signal_page.php?id=' . $value . '">' . $friendUserName . '</a><br />' . $frnd_pic . '
				</div></td>'; 
			}
    } 
	 $friendList .= '</tr></table>
	 <div align="right"><a href="#" onclick="return false" onmousedown="javascript:toggleViewAllFriends(\'view_all_friends\');">view all</a></div>
	 </div>'; 
	// END ASSEMBLE FRIEND LIST... TO VIEW UP TO 6 ON PROFILE
	// ASSEMBLE FRIEND LIST AND LINKS TO VIEW ALL(50 for now until we paginate the array)
	$i = 0;
	$friendArray50 = array_slice($friendArray, 0, 50);
	$friendPopBoxList = '<table id="friendPopBoxTable" width="100%" align="center" cellpadding="6" cellspacing="0">';
	foreach ($friendArray50 as $key => $value) { 
        $i++; // increment $i by one each loop pass 
		$check_pic = 'members/' . $value . '/image01.jpg';
		    if (file_exists($check_pic)) {
				$frnd_pic = '<a href="signal_page.php?id=' . $value . '"><img src="' . $check_pic . '" width="54px" border="1"/></a>';
		    } else {
				$frnd_pic = '<a href="signal_page.php?id=' . $value . '"><img src="members/0/image01.jpg" width="54px" border="1"/></a> &nbsp;';
		    }
			$sqlName = mysql_query("SELECT username, firstname, country, state, city FROM myMembers WHERE id='$value' LIMIT 1") or die ("Sorry we had a mysql error!");
		    while ($row = mysql_fetch_array($sqlName)) { $funame = $row["username"]; $ffname = $row["firstname"]; $fcountry = $row["country"]; $fstate = $row["state"]; $fcity = $row["city"]; }
			if (!$funame) {$funame = $ffname;} // If username is blank use the firstname... programming changes in v1.32 call for this
				if ($i % 2) {
					$friendPopBoxList .= '<tr bgcolor="#F4F4F4"><td width="14%" valign="top">
					<div style="width:56px; height:56px; overflow:hidden;" title="' . $funame . '">' . $frnd_pic . '</div></td>
				     <td width="86%" valign="top"><a href="signal_page.php?id=' . $value . '">' . $funame . '</a><br /><font size="-2"><em>' . $fcity . '<br />' . $fstate . '<br />' . $fcountry . '</em></font></td>
				    </tr>';  
				} else {
				    $friendPopBoxList .= '<tr bgcolor="#E0E0E0"><td width="14%" valign="top">
					<div style="width:56px; height:56px; overflow:hidden;" title="' . $funame . '">' . $frnd_pic . '</div></td>
				     <td width="86%" valign="top"><a href="signal_page.php?id=' . $value . '">' . $funame . '</a><br /><font size="-2"><em>' . $fcity . '<br />' . $fstate . '<br />' . $fcountry . '</em></font></td>
				    </tr>';  
				}
    } 
	$friendPopBoxList .= '</table>';
	// END ASSEMBLE FRIEND LIST AND LINKS TO VIEW ALL(50 for now until we paginate the array)
}
// ------- END POPULATE FRIEND DISPLAY LISTS IF THEY HAVE AT LEAST ONE FRIEND -------------------
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="Description" content="Profile for <?php echo "$username"; ?>" />
<meta name="Keywords" content="<?php echo "$username, $city, $state, $country"; ?>" />
<meta name="rating" content="General" />
<meta name="ROBOTS" content="All" />
<title>Site Profile for <?php echo "$username"; ?></title>
<link href="style/main.css" rel="stylesheet" type="text/css" />
<link rel="icon" href="favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
<script src="js/jquery-1.4.2.js" type="text/javascript"></script>
<style type="text/css">
<!--
.infoHeader {
	background-color: #BDF;
	font-size:11px;
	font-weight:bold;
	padding:8px;
	border: #999 1px solid;
	border-bottom:none;
	width:200px;
}
.infoBody{
	background-color: #FFF;
	font-size:11px;
	padding:8px;
	border: #999 1px solid;
	border-bottom:none;
	width:200px;
}
/* ------- Interaction Links Class -------- */
.interactionLinksDiv a {
   border:#B9B9B9 1px solid; padding:5px; color:#060; font-size:11px; background-image:url(style/headerBtnsBG.jpg); text-decoration:none;
}
.interactionLinksDiv a:hover {
	border:#090 1px solid; padding:5px; color:#060; font-size:11px; background-image:url(style/headerBtnsBGover.jpg);
}
/* ------- Interaction Containers Class -------- */
.interactContainers {
	padding:8px;
	background-color:#BDF;
	border:#999 1px solid;
	display:none;
}
#add_friend_loader {
	display:none;
}
#remove_friend_loader {
	display:none;
}
#interactionResults {
	display:none;
	font-size:16px;
	padding:8px;
}
#friendTable td{
	font-size:9px;
}
#friendTable td a{
	color:#03C;
	text-decoration:none;
}
#view_all_friends {
	background-image:url(style/opaqueDark.png);
	width:270px;
	padding:20px;
	position:fixed;
	top:150px;
	display:none;
	z-index:100;
	margin-left:50px;
}
#google_map {
	background-image:url(style/opaqueDark.png);
	padding:20px;
	position:fixed;
	top:150px;
	display:none;
	z-index:100;
	margin-left:50px;
}
-->
</style>
<script language="javascript" type="text/javascript">
// jQuery functionality for toggling member interaction containers
function toggleInteractContainers(x) {
		if ($('#'+x).is(":hidden")) {
			$('#'+x).slideDown(200);
		} else {
			$('#'+x).hide();
		}
		$('.interactContainers').hide();
}
function toggleViewAllFriends(x) {
		if ($('#'+x).is(":hidden")) {
			$('#'+x).fadeIn(200);
		} else {
			$('#'+x).fadeOut(200);
		}
}
function toggleViewMap(x) {
		if ($('#'+x).is(":hidden")) {
			$('#'+x).fadeIn(200);
		} else {
			$('#'+x).fadeOut(200);
		}
}
// Friend adding and accepting stuff
var thisRandNum = "<?php echo $thisRandNum; ?>";
var friendRequestURL = "scripts_for_signal_page/request_as_friend.php";
function addAsFriend(a,b) {
	$("#add_friend_loader").show();
	$.post(friendRequestURL,{ request: "requestFriendship", mem1: a, mem2: b, thisWipit: thisRandNum } ,function(data) {
	    $("#add_friend").html(data).show().fadeOut(12000);
    });	
}
function acceptFriendRequest (x) {
	$.post(friendRequestURL,{ request: "acceptFriend", reqID: x, thisWipit: thisRandNum } ,function(data) {
            $("#req"+x).html(data).show();
    });
}
function denyFriendRequest (x) {
	$.post(friendRequestURL,{ request: "denyFriend", reqID: x, thisWipit: thisRandNum } ,function(data) {
           $("#req"+x).html(data).show();
    });
}
// End Friend adding and accepting stuff
// Friend removal stuff
function removeAsFriend(a,b) {
	$("#remove_friend_loader").show();
	$.post(friendRequestURL,{ request: "removeFriendship", mem1: a, mem2: b, thisWipit: thisRandNum } ,function(data) {
	    $("#remove_friend").html(data).show().fadeOut(12000);
    });	
}
// End Friend removal stuff
// Start Private Messaging stuff
$('#pmForm').submit(function(){$('input[type=submit]', this).attr('disabled', 'disabled');});
function sendPM ( ) {
      var pmSubject = $("#pmSubject");
	  var pmTextArea = $("#pmTextArea");
	  var sendername = $("#pm_sender_name");
	  var senderid = $("#pm_sender_id");
	  var recName = $("#pm_rec_name");
	  var recID = $("#pm_rec_id");
	  var pm_wipit = $("#pmWipit");
	  var url = "scripts_for_signal_page/private_msg_parse.php";
      if (pmSubject.val() == "") {
           $("#interactionResults").html('<img src="images/round_error.png" alt="Error" width="31" height="30" /> &nbsp; Please type a subject.').show().fadeOut(6000);
      } else if (pmTextArea.val() == "") {
		   $("#interactionResults").html('<img src="images/round_error.png" alt="Error" width="31" height="30" /> &nbsp; Please type in your message.').show().fadeOut(6000);
      } else {
		   $("#pmFormProcessGif").show();
		   $.post(url,{ subject: pmSubject.val(), message: pmTextArea.val(), senderName: sendername.val(), senderID: senderid.val(), rcpntName: recName.val(), rcpntID: recID.val(), thisWipit: pm_wipit.val() } ,           function(data) {
			   $('#private_message').slideUp("fast");
			   $("#interactionResults").html(data).show().fadeOut(10000);
			   document.pmForm.pmTextArea.value='';
			   document.pmForm.pmSubject.value='';
			   $("#pmFormProcessGif").hide();
           });
	  }
}
// End Private Messaging stuff
</script>
</head>
<body>
<?php include_once "header_template.php"; ?>
<table class="mainBodyTable" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="738" valign="top"><br />
      <table width="98%" border="0" align="center" cellpadding="6">
      <tr>
        <td width="33%" valign="top">
          <?php echo $user_pic; ?>
          <?php echo $bio_body; ?>
          <div class="infoHeader"><?php echo $username; ?>'s Signals</div>
          <div class="infoBody">
          <?php echo $locationInfo; ?>
          <?php echo $website; 
		  echo $display_signals2 
		  ?>
          
          </div>
          <?php echo $friendList; ?>
          <div id="view_all_friends">
              <div align="right" style="padding:6px; background-color:#FFF; border-bottom:#666 1px solid;">
                       <div style="display:inline; font-size:14px; font-weight:bold; margin-right:150px;">All Friends</div> 
                       <a href="#" onclick="return false" onmousedown="javascript:toggleViewAllFriends('view_all_friends');">close </a>
              </div>
              <div style="background-color:#FFF; height:240px; overflow:auto;">
                   <?php echo $friendPopBoxList; ?>
              </div>
              <div style="padding:6px; background-color:#000; border-top:#666 1px solid; font-size:10px; color: #0F0;">
                       Temporary programming shows 50 maximum. Navigating through the full list is coming soon.
              </div>
         </div>
         
         <!---div class="infoBody" style="border-bottom:#999 1px solid;"></div--->  
          </td>
        <td width="67%" valign="top">
          <span style="font-size:16px; font-weight:800;"><?php echo $mainNameLine; ?></span>
		 
		  <?php echo $interactionBox; ?>
 
          <div class="interactContainers" id="add_friend">
                <div align="right"><a href="#" onclick="return false" onmousedown="javascript:toggleInteractContainers('add_friend');">cancel</a> </div>
                Add <?php echo "$username"; ?> as a friend? &nbsp;
                <a href="#" onclick="return false" onmousedown="javascript:addAsFriend(<?php echo $logOptions_id; ?>, <?php echo $id; ?>);">Yes</a>
                <span id="add_friend_loader"><img src="images/loading.gif" width="28" height="10" alt="Loading" /></span>
          </div>
          
          <div class="interactContainers" id="remove_friend">
                <div align="right"><a href="#" onclick="return false" onmousedown="javascript:toggleInteractContainers('remove_friend');">cancel</a> </div>
                Remove <?php echo "$username"; ?> from your friend list? &nbsp;
                <a href="#" onclick="return false" onmousedown="javascript:removeAsFriend(<?php echo $logOptions_id; ?>, <?php echo $id; ?>);">Yes</a>
                <span id="remove_friend_loader"><img src="images/loading.gif" width="28" height="10" alt="Loading" /></span>
          </div>
          
          <!-- START DIV that serves as an interaction status and results container that only appears when we instruct it to -->
          <div id="interactionResults" style="font-size:15px; padding:10px;"></div>
          <!-- END DIV that serves as an interaction status and results container that only appears when we instruct it to -->
          
          <!-- START DIV that contains the Private Message form -->
          <div class="interactContainers" id="private_message" style="background-color: #EAF4FF;">
<form action="javascript:sendPM();" name="pmForm" id="pmForm" method="post">
<font size="+1">Sending Private Message to <strong><em><?php echo "$username"; ?></em></strong></font><br /><br />
Subject:
<input name="pmSubject" id="pmSubject" type="text" maxlength="64" style="width:98%;" />
Message:
<textarea name="pmTextArea" id="pmTextArea" rows="8" style="width:98%;"></textarea>
  <input name="pm_sender_id" id="pm_sender_id" type="hidden" value="<?php echo $_SESSION['id']; ?>" />
  <input name="pm_sender_name" id="pm_sender_name" type="hidden" value="<?php echo $_SESSION['username']; ?>" />
  <input name="pm_rec_id" id="pm_rec_id" type="hidden" value="<?php echo $id; ?>" />
  <input name="pm_rec_name" id="pm_rec_name" type="hidden" value="<?php echo $username; ?>" />
  <input name="pmWipit" id="pmWipit" type="hidden" value="<?php echo $thisRandNum; ?>" />
  <span id="PMStatus" style="color:#F00;"></span>
  <br /><input name="pmSubmit" type="submit" value="Submit" /> or <a href="#" onclick="return false" onmousedown="javascript:toggleInteractContainers('private_message');">Close</a>
<span id="pmFormProcessGif" style="display:none;"><img src="images/loading.gif" width="28" height="10" alt="Loading" /></span></form>
          </div>
          <!-- END DIV that contains the Private Message form -->
          <div class="interactContainers" id="friend_requests" style="background-color:#FFF; height:240px; overflow:auto;">
            <div align="right"><a href="#" onclick="return false" onmousedown="javascript:toggleInteractContainers('friend_requests');">close window</a> &nbsp; &nbsp; </div>
            <h3>The following people are requesting you as a friend</h3>
    <?php
    $sql = "SELECT * FROM friends_requests WHERE mem2='$id' ORDER BY id ASC LIMIT 50";
	$query = mysql_query($sql) or die ("Sorry we had a mysql error!");
	$num_rows = mysql_num_rows($query); 
	if ($num_rows < 1) {
		echo 'You have no Friend Requests at this time.';
	} else {
        while ($row = mysql_fetch_array($query)) { 
		    $requestID = $row["id"];
		    $mem1 = $row["mem1"];
	        $sqlName = mysql_query("SELECT username FROM myMembers WHERE id='$mem1' LIMIT 1") or die ("Sorry we had a mysql error!");
		    while ($row = mysql_fetch_array($sqlName)) { $requesterUserName = $row["username"]; }
		    ///////  Mechanism to Display Pic. See if they have uploaded a pic or not  //////////////////////////
		    $check_pic = 'members/' . $mem1 . '/image01.jpg';
		    if (file_exists($check_pic)) {
				$lil_pic = '<a href="signal_page.php?id=' . $mem1 . '"><img src="' . $check_pic . '" width="50px" border="0"/></a>';
		    } else {
				$lil_pic = '<a href="signal_page.php?id=' . $mem1 . '"><img src="members/0/image01.jpg" width="50px" border="0"/></a>';
		    }
		    echo	'<hr />
<table width="100%" cellpadding="5"><tr><td width="17%" align="left"><div style="overflow:hidden; height:50px;"> ' . $lil_pic . '</div></td>
                        <td width="83%"><a href="signal_page.php?id=' . $mem1 . '">' . $requesterUserName . '</a> wants to be your Friend!<br /><br />
					    <span id="req' . $requestID . '">
					    <a href="#" onclick="return false" onmousedown="javascript:acceptFriendRequest(' . $requestID . ');" >Accept</a>
					    &nbsp; &nbsp; OR &nbsp; &nbsp;
					    <a href="#" onclick="return false" onmousedown="javascript:denyFriendRequest(' . $requestID . ');" >Deny</a>
					    </span></td>
                        </tr>
                       </table>';
        }	 
	}
    ?>
          </div>
          
            <?php echo $the_sig_form; ?>
          
            <div style="width:456px; overflow-x:hidden;">
              <?php echo $sigDisplayList; ?>
             
            </div>
          </td>
      </tr>
      <tr>
        <td colspan="2" valign="top">
    <div id="google_map">
<div align="right" style="padding:4px; background-color:#D2F0D3;"><a href="#" onclick="return false" onmousedown="javascript:toggleViewMap('google_map');">close map</a></div>
<iframe width="680" height="280" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=<?php echo "$city,+$state,+$country";?>&amp;ie=UTF8&amp;hq=&amp;hnear=<?php echo "$city,+$state,+$country";?>&amp;z=12&amp;output=embed"></iframe>
<div align="left" style="padding:4px; background-color:#D2F0D3;"><a href="#" onclick="return false" onmousedown="javascript:toggleViewMap('google_map');">close map</a></div>
    </div>
        </td>
        </tr>
      </table>
      <p><br />
        <br />
      </p></td>
    <td width="160" valign="top"><br /><br /><!---?php include_once("left_template.php"); ?></td--->
  </tr>
</table>
<?php include_once "footer_template.php"; ?>
<br />
<?php
$bDayMSG = "";
$today = date("Y-m-d");
$birthday = $row['birthday'];
if ($today == $birthday) {
	$bDayMSG = "Happy Birthday!";
}
$birthday = strftime("%b %d, %Y", strtotime($birthday));
echo $bDayMSG; // Wherever you need it on the page
?>
</body>
</html>