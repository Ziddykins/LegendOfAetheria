<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    use Game\Account\Account;

    require_once 'functions.php';

    function send_registration_email(Account $account) {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOSTNAME'];
            $mail->Username   = $_ENV['SMTP_USERNAME'];
            $mail->Password   = $_ENV['SMTP_PASSWORD'];
            $mail->Port       = $_ENV['SMTP_PORT'];
            $mail->SMTPAuth   = true;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;        

            //Recipients
            $mail->setFrom(SYSTEM_EMAIL_ADDRESS, 'Mailer');
            $mail->addAddress($account->get_email(), get_full_name($account));

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Complete Your Registration';
            $mail->Body    = '<!-- https://stripo.email -->

<div dir="ltr" class="es-wrapper-color">
    <!--[if gte mso 9]>
        <v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t">
            <v:fill type="tile" color="#f8f9fd"></v:fill>
        </v:background>
    <![endif]-->
    <table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0">
        <tbody>
            <tr>
                <td class="esd-email-paddings" valign="top">
                    <table cellpadding="0" cellspacing="0" class="es-content esd-header-popover" align="center">
                        <tbody>
                            <tr>
                                <td class="esd-stripe" align="center" bgcolor="#071f4f"
                                    style="background-color: #071f4f; background-image: url(\"https://i.ibb.co/PMYhdHb/reg-background.png\" alt="reg-background" border="0">">
                                    <table bgcolor="#ffffff" class="es-content-body" align="center" cellpadding="0" cellspacing="0" width="600">
                                        <tbody>
                                            <tr>
                                                <td class="esd-structure es-p40t es-p40b es-p30r es-p30l" align="left">
                                                    <table cellpadding="0" cellspacing="0" width="100%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="540" class="esd-container-frame" align="center" valign="top">
                                                                    <table cellpadding="0" cellspacing="0" width="100%">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td align="center" class="esd-block-spacer" height="20"></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td align="left" class="esd-block-text es-p10b">
                                                                                    <h1 style="text-align: center; color: #ffffff;">One Step Away!<br></h1>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table cellpadding="0" cellspacing="0" class="es-content" align="center">
                        <tbody>
                            <tr>
                                <td class="esd-stripe" align="center" bgcolor="#f8f9fd" style="background-color: #f8f9fd;">
                                    <table bgcolor="transparent" class="es-content-body" align="center" cellpadding="0" cellspacing="0" width="600" style="background-color: transparent;">
                                        <tbody>
                                            <tr>
                                                <td class="esd-structure es-p20t es-p10b es-p20r es-p20l" align="left">
                                                    <table cellpadding="0" cellspacing="0" width="100%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="560" class="esd-container-frame" align="center" valign="top">
                                                                    <table cellpadding="0" cellspacing="0" width="100%">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td align="center" class="esd-block-text es-p10b">
                                                                                    <h1 style="font-size: 24px;">To complete your registration, click the link below!<br type="_moz"></h1>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td align="center" class="esd-block-text es-p10t es-p10b">
                                                                                    <p><a target="_blank" href="' . MAIN_SITE_BASEURL . '/verify?code=' . $account->get_verificationCode() . '">' . MAIN_SITE_BASEURL . '/verify?code=' . $account->get_verificationCode() . '</a><br></p>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table cellpadding="0" cellspacing="0" class="esd-footer-popover es-footer" align="center" style="background-position: center center;">
                        <tbody>
                            <tr>
                                <td class="esd-stripe" align="center">
                                    <table bgcolor="rgba(0, 0, 0, 0)" class="es-footer-body" align="center" cellpadding="0" cellspacing="0" width="600">
                                        <tbody>
                                            <tr>
                                                <td class="esd-structure es-p40t es-p40b es-m-p40t es-m-p40b es-m-p20r es-m-p20l" align="left">
                                                    <table cellpadding="0" cellspacing="0" width="100%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="600" class="esd-container-frame" align="center" valign="top">
                                                                    <table cellpadding="0" cellspacing="0" width="100%" bgcolor="#f0f3fe" style="background-color: #f0f3fe; border-radius: 20px; border-collapse: separate;">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td align="left" class="esd-block-text es-p25t es-p10b es-p20r es-p20l">
                                                                                    <h1 style="text-align: center; line-height: 150%;">MAIN SITE SHAMELESS SELF-PLUG<br></h1>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td align="center" class="esd-block-button es-p10t es-p25b es-p20r es-p20l es-m-p15t es-m-p20b es-m-p20r es-m-p20l"><span class="es-button-border"><a href="https://my.stripo.email/cabinet/" class="es-button es-button-1625641687239" target="_blank" style="padding: 10px 20px;">TRY IT OUT
                                                                                            <!--[if !mso]><!-- --><img src="https://i.ibb.co/4Sr2gJ3/reg-arrow.png" alt="reg-arrow" border="0">
                                                                                            <!--<![endif]-->
                                                                                        </a></span></td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>';
            $mail->AltBody = 'time2upgrade';

            $mail->send();
            
        } catch (Exception $e) {
            global $log;
            $log->error($e->getMessage());
        }
        header('Location: /setup/profiles');
        exit();
    }

    