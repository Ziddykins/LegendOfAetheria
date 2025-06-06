<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require 'vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
    

    function send_mail($email, $account) {
        $mail = new PHPMailer(true);
        try {
            
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();         
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->SMTPAuth   = true;
            $mail->Host       = $_ENV['SMTP_HOSTNAME'];
            $mail->Username   = $_ENV['SMTP_USERNAME'];
            $mail->Password   = $_ENV['SMTP_PASSWORD'];
            $mail->Port       = $_ENV['SMTP_PORT'];

            $mail->setFrom('noreply@memelife.ca', 'Legend Of Aetheria');
            $mail->addReplyTo('noreply@memelife.ca', 'Legend Of Aetheria');
            
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Verify Your LoA Account';
            $mail->Body    = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<!--[if gte mso 9]>
<xml>
  <o:OfficeDocumentSettings>
    <o:AllowPNG/>
    <o:PixelsPerInch>96</o:PixelsPerInch>
  </o:OfficeDocumentSettings>
</xml>
<![endif]-->
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="x-apple-disable-message-reformatting">
  <!--[if !mso]><!--><meta http-equiv="X-UA-Compatible" content="IE=edge"><!--<![endif]-->
  <title></title>
  
    <style type="text/css">
      @media only screen and (min-width: 620px) {
  .u-row {
    width: 600px !important;
  }
  .u-row .u-col {
    vertical-align: top;
  }

  .u-row .u-col-100 {
    width: 600px !important;
  }

}

@media (max-width: 620px) {
  .u-row-container {
    max-width: 100% !important;
    padding-left: 0px !important;
    padding-right: 0px !important;
  }
  .u-row .u-col {
    min-width: 320px !important;
    max-width: 100% !important;
    display: block !important;
  }
  .u-row {
    width: 100% !important;
  }
  .u-col {
    width: 100% !important;
  }
  .u-col > div {
    margin: 0 auto;
  }
}
body {
  margin: 0;
  padding: 0;
}

table,
tr,
td {
  vertical-align: top;
  border-collapse: collapse;
}

p {
  margin: 0;
}

.ie-container table,
.mso-container table {
  table-layout: fixed;
}

* {
  line-height: inherit;
}

a[x-apple-data-detectors=\'true\'] {
  color: inherit !important;
  text-decoration: none !important;
}

table, td { color: #000000; } #u_body a { color: #0000ee; text-decoration: underline; } #u_content_text_1 a { color: #5985ed; text-decoration: none; } @media (max-width: 480px) { #u_row_1 .v-row-background-image--inner { background-image: url(\'https://assets.unlayer.com/projects/246089/1723654911399-logo-banner-no-bg.webp\') !important; background-position: center top !important; background-repeat: no-repeat !important; } #u_row_1 .v-row-background-image--outer { background-position: center top !important; background-repeat: no-repeat !important; } #u_row_1.v-row-background-image--outer { background-position: center top !important; background-repeat: no-repeat !important; } #u_row_1.v-row-padding--vertical { padding-top: 11px !important; padding-bottom: 0px !important; } #u_content_heading_1 .v-container-padding-padding { padding: 90px 10px 33px !important; } #u_content_heading_1 .v-font-size { font-size: 15px !important; } #u_content_heading_1 .v-line-height { line-height: 50% !important; } #u_content_button_1 .v-size-width { width: 49% !important; } #u_content_button_1 .v-font-size { font-size: 10px !important; } #u_content_button_1 .v-line-height { line-height: 100% !important; } #u_content_text_1 .v-container-padding-padding { padding: 10px !important; } #u_content_text_1 .v-font-size { font-size: 10px !important; } #u_content_text_1 .v-line-height { line-height: 100% !important; } }
    </style>
  
  

<!--[if !mso]><!--><link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet" type="text/css"><link href="https://fonts.googleapis.com/css?family=Lobster+Two:400,700" rel="stylesheet" type="text/css"><!--<![endif]-->

</head>

<body class="clean-body u_body" style="margin: 0;padding: 0;-webkit-text-size-adjust: 100%;background-color: #ecf0f1;color: #000000">
  <!--[if IE]><div class="ie-container"><![endif]-->
  <!--[if mso]><div class="mso-container"><![endif]-->
  <table id="u_body" style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;min-width: 320px;Margin: 0 auto;background-color: #ecf0f1;width:100%" cellpadding="0" cellspacing="0">
  <tbody>
  <tr style="vertical-align: top">
    <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
    <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="center" style="background-color: #ecf0f1;"><![endif]-->
    
  
  
    <!--[if gte mso 9]>
      <table cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto;min-width: 320px;max-width: 600px;">
        <tr>
          <td background="https://assets.unlayer.com/projects/246089/1723653969757-logo-banner-no-bg-email.webp" valign="top" width="100%">
      <v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false" style="width: 600px;">
        <v:fill type="frame" src="https://assets.unlayer.com/projects/246089/1723653969757-logo-banner-no-bg-email.webp" /><v:textbox style="mso-fit-shape-to-text:true" inset="0,0,0,0">
      <![endif]-->
  
<div id="u_row_1" class="u-row-container v-row-padding--vertical v-row-background-image--outer" style="padding: 0px;background-image: url(\'https://assets.unlayer.com/projects/246089/1723653969757-logo-banner-no-bg-email.webp\');background-repeat: no-repeat;background-position: center top;background-color: transparent">
  <div class="u-row" style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
    <div class="v-row-background-image--inner" style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
      <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td class="v-row-background-image--outer" style="padding: 0px;background-image: url(\'https://assets.unlayer.com/projects/246089/1723653969757-logo-banner-no-bg-email.webp\');background-repeat: no-repeat;background-position: center top;background-color: transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px;"><tr class="v-row-background-image--inner" style="background-color: transparent;"><![endif]-->
      
<!--[if (mso)|(IE)]><td align="center" width="600" style="width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;" valign="top"><![endif]-->
<div class="u-col u-col-100" style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
  <div style="height: 100%;width: 100% !important;">
  <!--[if (!mso)&(!IE)]><!--><div style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;"><!--<![endif]-->
  
<table id="u_content_heading_1" style="font-family:\"Raleway\",sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
  <tbody>
    <tr>
      <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:70px 10px 33px;font-family:\"Raleway\",sans-serif;" align="left">
        
  <!--[if mso]><table width="100%"><tr><td><![endif]-->
    <h1 class="v-line-height v-font-size" style="margin: 0px; color: #002e9f; line-height: 100%; text-align: center; word-wrap: break-word; font-family: \'Lobster Two\',cursive; font-size: 39px; font-weight: 700;"><span><span><span><span><span><span><span><span><span><span><span><span><span><span><span><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><span style="line-height: 12.5px;"><br />Verify Your Account<br /></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></span></h1>
  <!--[if mso]></td></tr></table><![endif]-->

      </td>
    </tr>
  </tbody>
</table>

<table id="u_content_button_1" style="font-family:\'Raleway\',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
  <tbody>
    <tr>
      <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:0px 10px 10px;font-family:\'Raleway\',sans-serif;" align="left">
        
  <!--[if mso]><style>.v-button {background: transparent !important;}</style><![endif]-->
<div align="center">
  <!--[if mso]><table border="0" cellspacing="0" cellpadding="0"><tr><td align="center" bgcolor="#5985ed" style="padding:10px 20px;" valign="top"><![endif]-->
    <a href="https://loa.dankaf.ca/verification.php?code=' . $account['verification_code'] . '&email=' . $account['email'] . '" target="_blank" class="v-button v-size-width v-font-size" style="box-sizing: border-box;display: inline-block;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;color: #ffffff; background-color: #5985ed; border-radius: 4px;-webkit-border-radius: 4px; -moz-border-radius: 4px; width:38%; max-width:100%; overflow-wrap: break-word; word-break: break-word; word-wrap:break-word; mso-border-alt: none;border-top-width: 1px; border-top-style: solid; border-top-color: #000000; border-left-width: 1px; border-left-style: solid; border-left-color: #000000; border-right-width: 1px; border-right-style: solid; border-right-color: #000000; border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: #000000;font-size: 14px;">
      <span class="v-line-height" style="display:block;padding:10px 20px;line-height:120%;"><span style="line-height: 14px;">Verify Account<br /></span></span>
    </a>
    <!--[if mso]></td></tr></table><![endif]-->
</div>

      </td>
    </tr>
  </tbody>
</table>

<table id="u_content_text_1" style="font-family:\'Raleway\',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
  <tbody>
    <tr>
      <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:10px 90px 20px;font-family:\'Raleway\',sans-serif;" align="left">
        
  <div class="v-line-height v-font-size" style="font-size: 14px; line-height: 140%; text-align: center; word-wrap: break-word;">
    <p style="line-height: 140%;">If you are unable to see the button, <a rel="noopener" href="https://loa.dankaf.ca/verification.php?code=' . $account['verification_code'] . '&email=' . $account['email'] . '" target="_blank">click here</a> to verify</p>
<p style="line-height: 140%;"> </p>
<p style="line-height: 140%;"> </p>
<p style="line-height: 140%;"> </p>
<p style="line-height: 140%;"> </p>
<p style="line-height: 140%;"> </p>
<p style="line-height: 140%;"><a rel="noopener" href="https://loa.dankaf.ca/unsubscribe.php?email=' . $account['email'] . '" target="_blank">UNSUBSCRIBE</a></p>
  </div>

      </td>
    </tr>
  </tbody>
</table>

  <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
  </div>
</div>
<!--[if (mso)|(IE)]></td><![endif]-->
      <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
    </div>
  </div>
  </div>
  
    <!--[if gte mso 9]>
      </v:textbox></v:rect>
    </td>
    </tr>
    </table>
    <![endif]-->
    
  
  
<div class="u-row-container v-row-padding--vertical v-row-background-image--outer" style="padding: 0px;background-color: transparent">
  <div class="u-row" style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
    <div class="v-row-background-image--inner" style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
      <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td class="v-row-background-image--outer" style="padding: 0px;background-color: transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px;"><tr class="v-row-background-image--inner" style="background-color: transparent;"><![endif]-->
      
<!--[if (mso)|(IE)]><td align="center" width="600" style="background-color: #ffffff;width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;" valign="top"><![endif]-->
<div class="u-col u-col-100" style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
  <div style="background-color: #ffffff;height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
  <!--[if (!mso)&(!IE)]><!--><div style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;"><!--<![endif]-->
  
<table style="font-family:\'Raleway\',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
  <tbody>
    <tr>
      <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:0px;font-family:\'Raleway\',sans-serif;" align="left">
        
  <table height="0px" align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;border-top: 1px solid #BBBBBB;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%">
    <tbody>
      <tr style="vertical-align: top">
        <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;font-size: 0px;line-height: 0px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%">
          <span>&#160;</span>
        </td>
      </tr>
    </tbody>
  </table>

      </td>
    </tr>
  </tbody>
</table>

  <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
  </div>
</div>
<!--[if (mso)|(IE)]></td><![endif]-->
      <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
    </div>
  </div>
  </div>
  
    <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
    </td>
  </tr>
  </tbody>
  </table>
  <!--[if mso]></div><![endif]-->
  <!--[if IE]></div><![endif]-->
</body>

</html>
';
        
            $mail->send();
        } catch (Exception $e) {
            global $log;
            $log->error("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }
?>