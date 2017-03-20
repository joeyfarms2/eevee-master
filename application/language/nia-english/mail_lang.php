<?
/** Editable Zone **/
$lang['default_logo_path'] = THEME_FRONT_PATH.'images/background/logo-mail.png';
$lang['default_url_path'] = WEB_URL;
$lang['default_postscript'] = ' Best regards,';
$lang['default_sender_name'] = ADMIN_EMAIL_NAME;
$lang['default_signature'] = ADMIN_EMAIL_SIGNATURE;
$lang['default_contact_email'] = CONTACT_EMAIL;
$lang['default_contact_email_html'] = '<a href="mailto:'.$lang['default_contact_email'].'" >'.$lang['default_contact_email'].'<a>';
$lang['default_problem_suggestion'] = 'If you have any inquires and troubles, please contact us at '.$lang['default_contact_email_html'];
/** End of Editable Zone **/

$lang['default_mail_header'] = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>'.$lang['default_url_path'].'</title>
<style type="text/css">
body{background-color:#FFF; margin:0; padding:0; color:#366092} 
td{padding:5px 0;font-size:12px; font-family:Tahoma; color:#366092;} 
.hcenter{text-align:center;}
.hright{text-align:right;}
.hleft{text-align:left;}
.linedown{border-bottom:1px solid #CCC;}
.header{font-size:12px;color:#FFF;background-color:#366092;font-weight:bold;}
.h2{font-size:24px;font-weight:normal;}
.footer{padding:0px}
.bill-header{font-size:12px;font-weight:normal;padding:0px;}
a.button{padding:10px;text-decoration:none;font-weight:bold;border-radius:5px;min-width:200px;}
a.confirm{color:#465821;background-color:#9CC746;}
a.cancel{color:#621C1A;background-color:#CE3B37;}

</style>
</head>
<body>
<table cellspacing="0" cellpadding="0" border="0" width="800">
<tr valign="bottom">
<td width="20">&nbsp;</td>
<td width="200" height="100" class=""><a href="'.$lang['default_url_path'].'"><img src="'.$lang['default_logo_path'].'" alt="'.$lang['default_url_path'].'" /></a></td>
<td height="100" class="">&nbsp;</td>
<td width="20">&nbsp;</td>
</tr>
<tr valign="bottom">
<td width="20">&nbsp;</td>
<td width="200" height="" class="hcenter h2">{doc_type}</td>
<td height="" class="">&nbsp;</td>
<td width="20">&nbsp;</td>
</tr>
<tr>
<td width="20">&nbsp;</td>
<td colspan="2">
';

$lang['default_mail_footer'] = '
</td>
<td width="20">&nbsp;</td>
</tr>
<tr>
<td class="footer" width="20">&nbsp;</td>
<td class="footer" colspan="2">&nbsp;</td>
<td class="footer" width="20">&nbsp;</td>
</tr>
<tr>
<td class="footer" width="20">&nbsp;</td>
<td class="footer hleft" colspan="2">'.$lang['default_postscript'].'</td>
<td class="footer" width="20">&nbsp;</td>
</tr>
<tr>
<td class="footer" width="20">&nbsp;</td>
<td class="footer hleft" colspan="2">'.$lang['default_signature'].'</td>
<td class="footer" width="20">&nbsp;</td>
</tr>
<tr>
<td class="footer" width="20">&nbsp;</td>
<td class="footer" colspan="2">&nbsp;</td>
<td class="footer" width="20">&nbsp;</td>
</tr>
<tr>
<td class="footer" width="20">&nbsp;</td>
<td class="footer hleft" colspan="2">Email : '.$lang['default_contact_email_html'].'</td>
<td class="footer" width="20">&nbsp;</td>
</tr>
<tr>
<td class="footer" width="20">&nbsp;</td>
<td class="footer hleft" colspan="2">Website : <a href="'.WEB_URL.'">'.WEB_URL.'</a></td>
<td class="footer" width="20">&nbsp;</td>
</tr>
</table>
</body>
</html>
';

//Email : Send welcome mail to user
$lang['mail_subject_new_user'] = 'Welcome to '.$lang['default_sender_name'];
$lang['mail_content_new_user'] = $lang['default_mail_header'].'
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr><td>Dear <strong>{name}</strong> [{username}],</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Thank you for signing up to '.$lang['default_signature'].'.</td></tr>
	<tr><td>You can use your registered {login_type} ({username}) and password to access to <a href="'.BASE_URL.'login">'.$lang['default_signature'].'</a></td></tr>
</table>
'.$lang['default_mail_footer'];

//Email : Send generate password to user
$lang['mail_subject_new_user_generate'] = 'Welcome to '.$lang['default_sender_name'];
$lang['mail_content_new_user_generate'] = $lang['default_mail_header'].'
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr><td>Dear <strong>{name}</strong> [{username}],</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Thank you for signing up to '.$lang['default_signature'].'.</td></tr>
	<tr><td>Use the information below to sign in at <a href="'.BASE_URL.'login">'.$lang['default_signature'].'</a></td></tr>
	<tr><td>{login_type} : {username}</td></tr>
	<tr><td>Password : {password}</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>For security reasons, Please change your password once you have logged in.</td></tr>
</table>
'.$lang['default_mail_footer'];

//Email : Send activation email to user
$lang['mail_subject_new_user_activate'] = $lang['default_signature'].' | Please activate your account to complete your signing up!';
$lang['mail_content_new_user_activate'] = $lang['default_mail_header'].'
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr><td class="hcenter h2">Account  Activation</td></tr>
	<tr><td>Dear <strong>{name}</strong> [{username}],</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Thank you for signing up to '.$lang['default_signature'].'. Your account is now pending for activation. Please activate your account by clicking on below link:</td></tr>
	<tr><td><a href="{url}">{url}</a></td></tr>
	<tr><td>After activation is completed, you can use your registered {login_type} ({username}) and password to access to <a href="'.BASE_URL.'login">'.$lang['default_signature'].'</a></td></tr>
	<tr><td>'.$lang['default_problem_suggestion'].'</td></tr>
</table>
'.$lang['default_mail_footer'];

//Email : Send activation by admin email to user
$lang['mail_subject_new_user_activate_by_admin'] = 'Welcome to '.$lang['default_sender_name'];
$lang['mail_content_new_user_activate_by_admin'] = $lang['default_mail_header'].'
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr><td class="hcenter h2">Account  Activation</td></tr>
	<tr><td>Dear <strong>{name}</strong> [{username}],</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Thank you for signing up to '.$lang['default_signature'].'.</td></tr>
	<tr><td>Please wait for activation process. We will let you know when activation is complete.</td></tr>
	<tr><td>After activation is completed, you can use your registered {login_type} ({username}) and password to access to <a href="'.BASE_URL.'login">'.$lang['default_signature'].'</a></td></tr>
</table>
'.$lang['default_mail_footer'];

//Email : Send reset password to user
$lang['mail_subject_reset_password'] = $lang['default_signature'].' | Request Reset Password';
$lang['mail_content_reset_password'] = $lang['default_mail_header'].'
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr><td class="hcenter h2">Request Reset Password</td></tr>
	<tr><td>Dear <strong>{name}</strong> [{username}],</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>You have got this email because your account has been requested to reset new password. For setting new password, please go to: </td></tr>
	<tr><td><a href="'.site_url('forgot/change/{username}/{confirm_code}').'">'.BASE_URL.'forgot/change/{username}/{confirm_code}</a></td></tr>
	<tr><td>If you suspect this request not made by you, please contact us immediately at '.$lang['default_contact_email_html'].'</td></tr>
</table>
'.$lang['default_mail_footer'];

//Email : Send invite for meeting (new)
$lang['mail_subject_meeting_invite_new'] = $lang['default_sender_name'].' : You\'ve invited to join the meeting on {date_start}.';
$lang['mail_content_meeting_invite_new'] = $lang['default_mail_header'].'
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr><td>Dear all,</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>You have been invited to the meeting as information below.</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Date : {date_full}</td></tr>
	<tr><td>Title : {title}</td></tr>
	<tr><td>Description : {description}</td></tr>
	<tr><td>Attendee : {attendee_all}</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Agenda :</td></tr>
	<tr><td>{agenda_list}</td></tr>
	<tr><td>&nbsp;</td></tr>
</table>
'.$lang['default_mail_footer'];

//Email : Send to confirm basket
$lang['mail_subject_confirm_basket'] = $lang['default_sender_name'].' : {doc_type} [{order_aid}]';
$lang['mail_content_confirm_basket'] = $lang['default_mail_header'].'
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
<td width="50%" class="bill-header"><strong>To:</strong></td>
<td width="50%" class="bill-header"><strong>{doc_type} </strong> : {order_aid}</td>
</tr>
<tr>
<td class="bill-header">{email}</td>
<td class="bill-header"><strong>Date</strong> : {date}</td>
</tr>
<tr>
<td class="bill-header">{name}</td>
<td class="bill-header"><strong>Order total</strong> : {total}</td>
</tr>
<tr>
<td class="bill-header">{address}</td>
<td class="bill-header"><strong>Method</strong> : {method}</td>
</tr>
<tr>
<td colspan="2">{order_table}</td>
</tr>
<tr>
<td colspan="2">{remark}</td>
</tr>
</table>
'.$lang['default_mail_footer'];

//Email : Send to confirm basket with point
$lang['mail_subject_confirm_basket_with_point'] = $lang['default_sender_name'].' : {doc_type} [{order_aid}]';
$lang['mail_content_confirm_basket_with_point'] = $lang['default_mail_header'].'
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr><td>เรียน <strong>คุณ{name}</strong></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>ขอบคุณสำหรับการสั่งซื้อหนังสือกับ '.WEB_URL.'</td></tr>
	<tr><td height="50"><strong><font color="#9F1E30" size="4">หมายเลขใบสั่งซื้อของคุณคือ {order_aid}</font></strong></td></tr>
	<tr><td>หนังสือที่คุณซื้อจะไปอยู่ใน <a href="'.BASE_URL.'my-bookshelf">My bookshelf</a> โดยอัตโนมัติ</td></tr>
	<tr><td>&nbsp;</td></tr>
</table>
'.$lang['default_mail_footer'];

//Email : Send confirmation contact receive to user
$lang['mail_subject_contact_confirm_to_user'] = $lang['default_signature'].' | Ask Librarian – {name}';
$lang['mail_content_contact_confirm_to_user'] = $lang['default_mail_header'].'
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr><td>Dear {name} [{email}],</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Thank you for your message, we will contact you as soon as possible.</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><strong>Reference:</strong></td></tr>
	<tr><td>Name : {name}</td></tr>
	<tr><td>Email : {email}</td></tr>
	<tr><td>Topic : {topic_name}</td></tr>
	<tr><td>Subject : {subject}</td></tr>
	<tr><td>Comment : {message}</td></tr>
	<tr><td>&nbsp;</td></tr>
</table>
'.$lang['default_mail_footer'];

//Email : Send confirmation reserve
$lang['mail_subject_reserve_confirm'] = $lang['default_signature'].' | Your turn!-Please confirm your reservation ';
$lang['mail_content_reserve_confirm'] = $lang['default_mail_header'].'
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr><td>Dear {name} [{email}],</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>You have recently made reservation of E-book/E-Magazines “{title}” from '.$lang['default_signature'].'. It is now your turn to read this one.</td></tr>
	<tr><td>Please reminder that <strong>YOU MUST CONFIRM</strong> your reservation <strong>WITHIN 24 HOURS</strong> after receiving this email in order to be able to download E-Books/E-Magazines into your personal shelf.</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>In order to complete your borrowing, please click on the following button:</td></tr>
	<tr><td><a class="button confirm" href="{url_confirm}">CONFIRM TO ENJOY READING</a></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>If you do not want to read this book at this time, please click on the following button:</td></tr>
	<tr><td><a class="button cancel" href="{url_cancel}">CANCEL MY RESERVATION</a></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Please remember, you may reserve and borrow circulating E-Books/E-Magazines up to 3 items.<BR />If your personal shelf dues to exceed limit, please remove some items before making reservation or borrowing.</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Happy Reading!</td></tr>
</table>
'.$lang['default_mail_footer'];

//Email : Success reserve
$lang['mail_subject_reserve_success'] = $lang['default_signature'].' | Your reservation completed. ';
$lang['mail_content_reserve_success'] = $lang['default_mail_header'].'
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr><td>Dear {name} [{email}],</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><img src="'.THEME_FRONT_PATH.'images/background/reserve-success.jpg" /></td></tr>
	<tr><td><strong>Congratulations</strong></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>This e-book/e-magazine has been already added into <a href="'.site_url('my-bookshelf').'">your personal book shelf</a>.</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><a href="'.site_url('my-bookshelf').'">Go to My Shelf</a></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Happy Reading!</td></tr>
</table>
'.$lang['default_mail_footer'];

//Email : Fail reserve - wrong url
$lang['mail_subject_reserve_fail'] = $lang['default_signature'].' | Your reservation failed. ';
$lang['mail_content_reserve_fail'] = $lang['default_mail_header'].'
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr><td>Dear {name} [{email}],</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><img src="'.THEME_FRONT_PATH.'images/background/reserve-fail.jpg" /></td></tr>
	<tr><td><strong>Oops!</strong></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>You did not confirm reservation within 24 hours or this book was added into <a href="'.site_url('my-bookshelf').'">your personal book shelf</a>.</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><a href="'.site_url('my-bookshelf').'">Go to My Shelf</a></td></tr>
</table>
'.$lang['default_mail_footer'];


//Email : Send email when new news has been published
$lang['mail_subject_new_news_publish'] = '{news_title}';
$lang['mail_content_new_news_publish'] = $lang['default_mail_header'].'
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr><td>Hello everyone,</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>We\'ve just published the new news on the website. Please check it out here, <a href="{news_url}">{news_title}</a></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>{img_cover_image}</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><em>* This is an automatic email from the system, please do not reply.</em></td></tr>
</table>
'.$lang['default_mail_footer'];


//Email : Send email when new questionaire has been published
$lang['mail_subject_new_questionaire_publish'] = '{questionaire_title}';
$lang['mail_content_new_questionaire_publish'] = $lang['default_mail_header'].'
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr><td>Hello everyone,</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>We\'ve just published the new questionaire on the website. Please check it out here, <a href="{questionaire_url}">{questionaire_title}</a>. It should only take not more than 10 minutes. Your answers will be treated with complete confidentiality.</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Thank you for your cooperation.</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><em>* This is an automatic email from the system, please do not reply.</em></td></tr>
</table>
'.$lang['default_mail_footer'];

//Email : Due Date Reminder
$lang['mail_subject_transaction_reminder'] = $lang['default_signature'].' | Due Date Reminder';
$lang['mail_content_transaction_reminder'] = $lang['default_mail_header'].'
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr><td><img src="'.THEME_FRONT_PATH.'images/background/transaction-due-date-reminder.jpg" /></td></tr>
	<tr><td>Dear {name} [{email}],</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>This e-mail is sent out to remind you that TOMORROW! ({date}) is due date of the book/material item(s) currently checked out to your account.</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>{product_list}</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>As a courtesy to other members, please return or renew book/material on time.</td></tr>
</table>
'.$lang['default_mail_footer'];

//Email : Overdue Notice
$lang['mail_subject_transaction_overdue'] = $lang['default_signature'].' | Overdue Notice';
$lang['mail_content_transaction_overdue'] = $lang['default_mail_header'].'
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr><td><img src="'.THEME_FRONT_PATH.'images/background/transaction-overdue-notice.jpg" /></td></tr>
	<tr><td>Dear {name} [{email}],</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Our library records indicate that the book(s) or material(s) as shown in the below list is/are still overdue. We would be grateful if you could either return or renew as soon as possible to avoid a suspension of your borrowing privilege.</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>{product_list}</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>
		Please be aware:
		<ul>
		<li>It is the reader\'s responsibility to return or renew books on time.</li>
		<li>If the book has been lost, you may buy a replacement copy.</li>
		<li>While the Library makes every effort to contact readers, we cannot confirm or guarantee delivery of reminder notices. The library will directly report to your line management.</li>
		</ul>
	</td></tr>
</table>
'.$lang['default_mail_footer'];

//Email : Send confirmation reserve product
$lang['mail_subject_reserve_product_request'] = $lang['default_signature'].' | Request reservation ';
$lang['mail_content_reserve_product_request'] = $lang['default_mail_header'].'
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr><td><img src="'.THEME_FRONT_PATH.'images/background/reserve-product-success.jpg" /></td></tr>
	<tr><td>Dear {name} [{email}],</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Please kindly to be informed that E-Library have recently received your request book/magazine/media reservation:</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>{product_list}</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>The library will acknowledge you back once the book is available. The library reserves the right to reject the request without prior notice.</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Happy Reading!</td></tr>
</table>
'.$lang['default_mail_footer'];

//Email : Send confirmation reserve product
$lang['mail_subject_reserve_product_confirm'] = $lang['default_signature'].' | Your turn!-Please come & get your reservation ';
$lang['mail_content_reserve_product_confirm'] = $lang['default_mail_header'].'
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr><td><img src="'.THEME_FRONT_PATH.'images/background/reserve-product-approve.jpg" /></td></tr>
	<tr><td>Dear {name} [{email}],</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Please kindly to be informed that your request book/magazine/media reservation has been approved:</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>{product_list}</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>You will have 3 days to pick up the book at the library.  If you are unable to pick it up within this timeframe, please contact the library and ask is special arrangements can be made.</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>If a requested book is not picked up, one of the following will happen:</td></tr>
	<tr><td>- If the book has been requested by someone else, they are notified and will be given three days to pick it up.</td></tr>
	<tr><td>- If there are no other requests, the book will be returned to the open shelves for normal borrowing.</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Happy Reading!</td></tr>
</table>
'.$lang['default_mail_footer'];

//Email : Send cancel reserve product
$lang['mail_subject_reserve_product_cancel'] = $lang['default_signature'].' | Your reservation was cancelled. ';
$lang['mail_content_reserve_product_cancel'] = $lang['default_mail_header'].'
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr><td>Dear {name} [{email}],</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Please kindly to be informed that your request book/magazine/media reservation has been cancelled:</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>{product_list}</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Happy Reading!</td></tr>
</table>
'.$lang['default_mail_footer'];


?>