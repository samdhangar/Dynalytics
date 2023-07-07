<?php
App::uses('CakeEmail', 'Network/Email');

class SendEmailComponent extends Component
{
    var $components = array("Email");
    var $subject;
    var $body;
    var $to;
	var $controller = null;
    public function __construct(ComponentCollection $collection, $settings = array())
    {
        $this->settings = $settings;
        parent::__construct($collection, $settings);
    }

    // public function initialize(Controller $controller)
    // {
    //     $this->controller = $controller;
    // }

    function sendBranchNotifyEmail($arrData, $type, $companyAdmins = array())
    {
        $sessionData = getMySessionData();
        $emailTemplete = $this->_getTemplate('branch_' . $type);
        if (empty($emailTemplete) || empty($arrData['User']['email'])) {
            return false;
        }
        $replacement = array(
            '{COMPANY_NAME}' => isset($arrData['User']['first_name']) ? ucfirst($arrData['User']['first_name']) : '',
            '{PHONE_NUMBER}' => isset($arrData['User']['phone_no']) ? ucfirst($arrData['User']['phone_no']) : '',
            '{CONTACT_NAME}' => isset($arrData['User']['first_name']) ? ucfirst($arrData['User']['first_name']) : '',
            '{BRANCH_NAME}' => isset($arrData['Branch']['name']) ? ucfirst($arrData['Branch']['name']) : '',
            '{B_CONTACT_NAME}' => isset($arrData['Branch']['contact_name']) ? ucfirst($arrData['Branch']['contact_name']) : '',
            '{CONTACT_EMAIL}' => isset($arrData['Branch']['email']) ? ucfirst($arrData['Branch']['email']) : '',
            '{FTP_USERNAME}' => isset($arrData['Branch']['ftpuser']) ? $arrData['Branch']['ftpuser'] : '',
            '{FTP_PASSWORD}' => isset($arrData['Branch']['ftp_pass']) ? $arrData['Branch']['ftp_pass'] : '',
            '{BRANCH_ADR}' => isset($arrData['Branch']['final_address']) ? $arrData['Branch']['final_address'] : '',
//            '{ADDED_DETAIL}' => CakeSession::read('Auth.User.first_name'),
            '{ADDED_DETAIL}' => $sessionData['first_name'],
            '{SITE_LOGIN_URL}' => Router::url(array('controller' => 'users', 'action' => 'login'), true),
            '{SITE_NAME}' => Configure::read('Site.Name'),
            '{SITE_URL}' => Configure::read('Site.Url'),
        );
        $emailTemplete['body'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['body']);
        $emailTemplete['subject'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['subject']);
        $this->send($arrData['User']['email'], $emailTemplete['subject'], $emailTemplete['body'], $companyAdmins);
    }

    function sendBranchDealerEmail($arrData)
    {
        $emailTemplete = $this->_getTemplate('branch_dealer_assign');
        if (empty($emailTemplete) || empty($arrData)) {
            return false;
        }
        foreach ($arrData as $data) {
            if (!empty($arrData['Dealer']['email'])) {
                $replacement = array(
                    '{CLIENT_NAME}' => isset($data['Branch']['Company']['first_name']) ? ucfirst($data['Branch']['Company']['first_name']) : '',
                    '{DEALER_NAME}' => isset($data['Dealer']['first_name']) ? ucfirst($data['Dealer']['first_name']) : '',
                    '{BRANCH_NAME}' => isset($data['Branch']['name']) ? ucfirst($data['Branch']['name']) : '',
                    '{SITE_LOGIN_URL}' => Router::url(array('controller' => 'users', 'action' => 'login'), true),
                    '{SITE_NAME}' => Configure::read('Site.Name'),
                    '{SITE_URL}' => Configure::read('Site.Url'),
                );
                $emailTemplete['body'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['body']);
                $emailTemplete['subject'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['subject']);
                $this->send($arrData['Dealer']['email'], $emailTemplete['subject'], $emailTemplete['body']);
            }
        }
    }
    
    function sendTicketClosedEmail($arrData)
    {
        $emailTemplete = $this->_getTemplate('ticket_closed');
        if (empty($emailTemplete) || empty($arrData)) {
            return false;
        }
        foreach ($arrData['Admins'] as $data) {
            if (!empty($data['User']['email'])) {
                $replacement = array(
                    '{CLIENT_NAME}' => isset($arrData['Company']['first_name']) ? ucfirst($arrData['Company']['first_name']) : '',
                    '{BRANCH_NAME}' => isset($arrData['Branch']['name']) ? ucfirst($arrData['Branch']['name']) : '',
                    '{STATION}' => isset($arrData['Ticket']['station']) ? $arrData['Ticket']['station'] : '',
                    '{TICKET_ID}' => isset($arrData['Ticket']['id']) ? $arrData['Ticket']['id'] : '',
                    '{TICKET_DATE}' => isset($arrData['Ticket']['ticket_date']) ? showdate($arrData['Ticket']['ticket_date']) : '',
                    '{TICKET_CLOSE_DATE}' => isset($arrData['Ticket']['updated']) ? showdate($arrData['Ticket']['updated']) : '',
                    '{TICKET_ERROR}' => isset($arrData['Ticket']['error']) ? ucfirst($arrData['Ticket']['error']) : '',
                    '{SITE_LOGIN_URL}' => Router::url(array('controller' => 'users', 'action' => 'login'), true),
                    '{SITE_NAME}' => Configure::read('Site.Name'),
                    '{SITE_URL}' => Configure::read('Site.Url'),
                );
                $emailTemplete['body'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['body']);
                $emailTemplete['subject'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['subject']);
                $this->send($data['User']['email'], $emailTemplete['subject'], $emailTemplete['body']);
            }
        }
    }
    
    function sendTicketOpenedEmail($arrData)
    {
        $emailTemplete = $this->_getTemplate('ticket_opened');
        if (empty($emailTemplete) || empty($arrData)) {
            return false;
        }
        foreach ($arrData['Admins'] as $data) {
            if (!empty($data['User']['email'])) {
                $replacement = array(
                    '{CLIENT_NAME}' => isset($arrData['Company']['first_name']) ? ucfirst($arrData['Company']['first_name']) : '',
                    '{BRANCH_NAME}' => isset($arrData['Branch']['name']) ? ucfirst($arrData['Branch']['name']) : '',
                    '{STATION}' => isset($arrData['Ticket']['station']) ? $arrData['Ticket']['station'] : '',
                    '{DEALER_NAME}' => isset($arrData['Dealer']['first_name']) ? ucfirst($arrData['Dealer']['first_name']) : '',
                    '{DEALER_EMAIL}' => isset($arrData['Dealer']['email']) ? $arrData['Dealer']['email'] : '',
                    '{DEALER_PHONE}' => isset($arrData['Dealer']['phone_no']) ? $arrData['Dealer']['phone_no'] : '',
                    '{TICKET_ID}' => isset($arrData['Ticket']['id']) ? $arrData['Ticket']['id'] : '',
                    '{TICKET_DATE}' => isset($arrData['Ticket']['ticket_date']) ? showdate($arrData['Ticket']['ticket_date']) : '',
                    '{TICKET_CLOSE_DATE}' => isset($arrData['Ticket']['updated']) ? showdate($arrData['Ticket']['updated']) : '',
                    '{TICKET_ERROR}' => isset($arrData['Ticket']['error']) ? ucfirst($arrData['Ticket']['error']) : '',
                    '{SITE_LOGIN_URL}' => Router::url(array('controller' => 'users', 'action' => 'login'), true),
                    '{SITE_NAME}' => Configure::read('Site.Name'),
                    '{SITE_URL}' => Configure::read('Site.Url'),
                );
                $emailTemplete['body'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['body']);
                $emailTemplete['subject'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['subject']);
                $this->send($data['User']['email'], $emailTemplete['subject'], $emailTemplete['body']);
            }
        }
    }

    function sendAccountCreatedEmail($arrData)
    {
        $emailTemplete = $this->_getTemplate('account_created');
        if (empty($emailTemplete) || empty($arrData['email'])) {

            return false;
        }
        $replacement = array(
            '{FIRST_NAME}' => isset($arrData['first_name']) ? ucfirst($arrData['first_name']) : '',
//            '{LAST_NAME}' => isset($arrData['last_name']) ? ucfirst($arrData['last_name']) : '',
            '{LAST_NAME}' => '',
            '{USER_NAME}' => isset($arrData['email']) ? $arrData['email'] : '',
            '{USER_PWD}' => isset($arrData['password']) ? $arrData['password'] : '',
            '{ACC_TYPE}' => isset($arrData['acc_type']) ? $arrData['acc_type'] : '',
            '{SITE_LOGIN_URL}' => Router::url(array('controller' => 'users', 'action' => 'login'), true),
            '{SITE_NAME}' => Configure::read('Site.Name'),
            '{SITE_URL}' => Configure::read('Site.Url'),
        );
        $emailTemplete['body'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['body']);
        $emailTemplete['subject'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['subject']);

        $this->send($arrData['email'], $emailTemplete['subject'], $emailTemplete['body']);
    }

    function sendCompanyCreatedEmail($arrData)
    {
        $emailTemplete = $this->_getTemplate('company_created');
        if (empty($emailTemplete) || empty($arrData['email'])) {

            return false;
        }
        $replacement = array(
            '{FIRST_NAME}' => isset($arrData['first_name']) ? ucfirst($arrData['first_name']) : '',
            '{LAST_NAME}' => isset($arrData['last_name']) ? ucfirst($arrData['last_name']) : '',
            '{USER_NAME}' => isset($arrData['email']) ? $arrData['email'] : '',
            '{USER_PWD}' => isset($arrData['password']) ? $arrData['password'] : '',
            '{ACC_TYPE}' => isset($arrData['acc_type']) ? $arrData['acc_type'] : '',
            '{SITE_LOGIN_URL}' => Router::url(array('controller' => 'users', 'action' => 'login'), true),
            '{SITE_NAME}' => Configure::read('Site.Name'),
            '{SITE_URL}' => Configure::read('Site.Url'),
        );
        $emailTemplete['body'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['body']);
        $emailTemplete['subject'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['subject']);

        $this->send($arrData['email'], $emailTemplete['subject'], $emailTemplete['body']);
    }

    function sendPasswordResetEmail($arrData)
    {
        $emailTemplete = $this->_getTemplate('reset_password');
        if (empty($emailTemplete) || empty($arrData['email'])) {

            return false;
        }
        $replacement = array(
            '{FIRST_NAME}' => isset($arrData['first_name']) ? ucfirst($arrData['first_name']) : '',
            '{LAST_NAME}' => isset($arrData['last_name']) ? ucfirst($arrData['last_name']) : '',
            '{USER_NAME}' => isset($arrData['email']) ? $arrData['email'] : '',
            '{USER_PWD}' => isset($arrData['password']) ? $arrData['password'] : '',
            '{ACC_TYPE}' => isset($arrData['acc_type']) ? $arrData['acc_type'] : '',
            '{SITE_LOGIN_URL}' => Router::url(array('controller' => 'users', 'action' => 'login'), true),
            '{SITE_NAME}' => Configure::read('Site.Name'),
            '{SITE_URL}' => Configure::read('Site.Url'),
        );
        $emailTemplete['body'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['body']);
        $emailTemplete['subject'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['subject']);

        $this->send($arrData['email'], $emailTemplete['subject'], $emailTemplete['body']);
    }

    function sendPasswordChangedEmail($arrData)
    {
        $emailTemplete = $this->_getTemplate('password_changed');
        if (empty($emailTemplete) || empty($arrData['email'])) {
            return false;
        }
        $replacement = array(
            '{FIRST_NAME}' => isset($arrData['first_name']) ? ucfirst($arrData['first_name']) : '',
            '{LAST_NAME}' => isset($arrData['last_name']) ? ucfirst($arrData['last_name']) : '',
            '{EMAIL}' => isset($arrData['email']) ? $arrData['email'] : '',
            '{PASSWORD}' => isset($arrData['password']) ? $arrData['password'] : '',
            '{SITE_LOGIN_URL}' => Router::url(array('controller' => 'users', 'action' => 'login'), true),
            '{SITE_NAME}' => Configure::read('Site.Name'),
            '{SITE_URL}' => Configure::read('Site.Url'),
        );
        $emailTemplete['body'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['body']);
        $emailTemplete['subject'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['subject']);

        $this->send($arrData['email'], $emailTemplete['subject'], $emailTemplete['body']);
    }

    function sendRejectedUserEmail($arrData)
    {
        $emailTemplete = $this->_getTemplate('rejected_account');
        if (empty($emailTemplete) || empty($arrData['email'])) {
            return false;
        }
        $replacement = array(
            '{FIRST_NAME}' => isset($arrData['first_name']) ? ucfirst($arrData['first_name']) : '',
            '{LAST_NAME}' => isset($arrData['last_name']) ? ucfirst($arrData['last_name']) : '',
            '{EMAIL}' => $arrData['email'],
            '{ACTIVATION_LINK}' => Router::url(array('controller' => 'users', 'action' => 'verify', $arrData['activation_code']), true),
            '{SITE_LOGIN_URL}' => Router::url(array('controller' => 'users', 'action' => 'login'), true),
            '{SITE_NAME}' => Configure::read('Site.Name'),
            '{SITE_URL}' => Configure::read('Site.Url'),
        );
        $emailTemplete['body'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['body']);
        $emailTemplete['subject'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['subject']);

        $this->send($arrData['email'], $emailTemplete['subject'], $emailTemplete['body']);
    }

    function sendTicketSupportNotifyEmail($arrData)
    {
        $emailTemplete = $this->_getTemplate('ticket_notify');
        if (empty($emailTemplete) || empty($arrData['Dealer']['email'])) {
            return false;
        }
        $replacement = array(
            '{DEALER_NAME}' => isset($arrData['Dealer']['name']) ? ucfirst($arrData['Dealer']['name']) : '',
            '{CLIENT_NAME}' => isset($arrData['Company']['first_name']) ? ucfirst($arrData['Company']['first_name']) : '',
            '{BRANCH_NAME}' => isset($arrData['Branch']['name']) ? ucfirst($arrData['Branch']['name']) : '',
            '{TICKET_DATE}' => isset($arrData['Ticket']['ticket_date']) ? showdatetime($arrData['Ticket']['ticket_date']) : '',
            '{TEXT}' => isset($arrData['Ticket']['text']) ? showdatetime($arrData['Ticket']['text']) : '',
            '{SITE_LOGIN_URL}' => Router::url(array('controller' => 'users', 'action' => 'login'), true),
            '{SITE_NAME}' => Configure::read('Site.Name'),
            '{SITE_URL}' => Configure::read('Site.Url'),
        );
        $emailTemplete['body'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['body']);
        $emailTemplete['subject'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['subject']);

        $this->send($arrData['Dealer']['email'], $emailTemplete['subject'], $emailTemplete['body']);
    }

    function sendBranchDealerNotifyEmail($arrData)
    {
        $emailTemplete = $this->_getTemplate('branch_dealer_notify');
        if (empty($emailTemplete) || empty($arrData['Dealer']['email'])) {
            return false;
        }
        $replacement = array(
            '{DEALER_NAME}' => isset($arrData['Dealer']['name']) ? ucfirst($arrData['Dealer']['name']) : '',
            '{CLIENT_NAME}' => isset($arrData['Company']['first_name']) ? ucfirst($arrData['Company']['first_name']) : '',
            '{BRANCH_NAME}' => isset($arrData['Branch']['name']) ? ucfirst($arrData['Branch']['name']) : '',
            '{NOTIFY_LINK}' => isset($arrData['notify_link']) ? $arrData['notify_link'] : '',
            '{SITE_LOGIN_URL}' => Router::url(array('controller' => 'users', 'action' => 'login'), true),
            '{SITE_NAME}' => Configure::read('Site.Name'),
            '{SITE_URL}' => Configure::read('Site.Url'),
        );
        $emailTemplete['body'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['body']);
        $emailTemplete['subject'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['subject']);

        $this->send($arrData['Dealer']['email'], $emailTemplete['subject'], $emailTemplete['body']);
    }

    public function sendNotificationEmailToCompany($data)
    {
        $emailTemplete = $this->_getTemplate('company_notify');
        if (empty($emailTemplete) || empty($data['Company']['email'])) {
            return false;
        }
        $note = ($data['BranchDealer']['status'] == 'Accept') ? '' : 'With below Note: <br/><br/>' . $data['BranchDealer']['note'];
        $replacement = array(
            '{DEALER_NAME}' => isset($data['Dealer']['name']) ? ucfirst($data['Dealer']['name']) : '',
            '{CLIENT_NAME}' => isset($data['Company']['first_name']) ? ucfirst($data['Company']['first_name']) : '',
            '{BRANCH_NAME}' => isset($data['Branch']['name']) ? ucfirst($data['Branch']['name']) : '',
            '{STATUS}' => isset($data['BranchDealer']['status']) ? ucfirst($data['BranchDealer']['status']) : '',
            '{NOTE}' => $note,
            '{SITE_LOGIN_URL}' => Router::url(array('controller' => 'users', 'action' => 'login'), true),
            '{SITE_NAME}' => Configure::read('Site.Name'),
            '{SITE_URL}' => Configure::read('Site.Url'),
        );
        $emailTemplete['body'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['body']);
        $emailTemplete['subject'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['subject']);

        $this->send($data['Company']['email'], $emailTemplete['subject'], $emailTemplete['body']);
    }

    function sendInvoiceMail($arrData = array(), $type = COMPANY)
    {
        $emailTemplete = $this->_getTemplate(strtolower($type) . '_invoice');
        if (empty($emailTemplete) || empty($arrData['User']['email'])) {
            return false;
        }
        $replacement = array(
            '{USER_NAME}' => isset($arrData['User']['first_name']) ? ucfirst($arrData['User']['first_name']) : '',
            '{INVOICE_NUMBER}' => isset($arrData['Invoice']['id']) ? showInvoiceNo($arrData['Invoice']['id']) : '',
            '{INVOICE_PERIOD}' => isset($arrData['Invoice']['billed_date']) ? date('M-Y', strtotime($arrData['Invoice']['billed_date'])) : '',
            '{INVOICE_DATE}' => isset($arrData['Invoice']['invoice_date']) ? showdate($arrData['Invoice']['invoice_date']) : '',
            '{BILLED_DATE}' => isset($arrData['Invoice']['billed_date']) ? showdate($arrData['Invoice']['billed_date']) : '',
            '{TOTAL_AMOUNT}' => isset($arrData['Invoice']['billed_amount']) ? showAmount($arrData['Invoice']['billed_amount']) : '',
            '{SITE_LOGIN_URL}' => Router::url(array('controller' => 'users', 'action' => 'login'), true),
            '{SITE_NAME}' => Configure::read('Site.Name'),
            '{SITE_URL}' => Configure::read('Site.Url'),
        );
        $emailTemplete['body'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['body']);
        $emailTemplete['subject'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['subject']);
        $invoiceUrl = getInvoicePath($arrData['Invoice']['invoice_id'], true);
        $this->send($arrData['User']['email'], $emailTemplete['subject'], $emailTemplete['body'], array(), $invoiceUrl);
    }
    public function sendinstructionMail($email)
    {
        $emailTemplete = $this->_getTemplate('instruction_to_support');
        if (empty($emailTemplete) || empty($email)) {
            return false;
        }
        $fileAttachment = WWW_ROOT . SAMPLE_FILE .'Onboarding process.pdf';
        $result = $this->send($email, $emailTemplete['subject'], $emailTemplete['body'],array(),$fileAttachment);
        if ($result == 1) {
            return true;
        }else{
            return false;
        }
    }
    function _getTemplate($emailTemplete)
    {
        $template = ClassRegistry::init("EmailTemplate")->find('first', array('conditions' => array('name' => $emailTemplete), 'fields' => 'body,subject'));
        if (!empty($template)) {
            return $template['EmailTemplate'];
        }
        return false;
    }
    function sendSerialNoNotExist($arrData)
    {
        // $response = $this->refresh_token();
        $replacement = array(
            '{SITE_LOGIN_URL}' => Configure::read('Site.Url'),
            '{SITE_NAME}' => Configure::read('Site.Name'),
            '{SITE_URL}' => Configure::read('Site.Url'),
        );
        $emailTemplete['body'] = str_replace(array_keys($replacement), array_values($replacement), $this->body_layout_for_sendSerial_no($arrData));
        $emailTemplete['subject'] = "Serial No Not Exist";
        $sendMail = 'joshc@addontechnologies.com;dev.adamj@addontechnologies.com;amit@vertivert.com';
        $this->send($sendMail, $emailTemplete['subject'], $emailTemplete['body'], array(), '', 'missing_serial_no');
        
    }
    function sendMissingFile($arrData)
    {
        // $response = $this->refresh_token();
        $replacement = array(
            '{SITE_LOGIN_URL}' => Configure::read('Site.Url'),
            '{SITE_NAME}' => Configure::read('Site.Name'),
            '{SITE_URL}' => Configure::read('Site.Url'),
        );
        $emailTemplete['body'] = str_replace(array_keys($replacement), array_values($replacement), $this->body_layout_for_missing_file($arrData));
        $emailTemplete['subject'] = "Files Missing";
        $sendMail = 'joshc@addontechnologies.com;dev.adamj@addontechnologies.com;amit@vertivert.com';
        $this->send($sendMail, $emailTemplete['subject'], $emailTemplete['body'], array(), '', 'missing_serial_no');
        
    }
    public static function body_layout_for_sendSerial_no($getDataArr)
    {
       // return false;
        $body_detailsLayout = '';
        foreach ($getDataArr as $key => $value) {
            $body_detailsLayout = $body_detailsLayout . "<ul>"
                . "<li>"
                . $value['read_files']['name']
                . "</li>"
                . "</ul>";
        }
        $layout_serialNo  = "
        Hello Team, <br><br> 
            Serial number not exist in this files."
            . $body_detailsLayout

            . "Please <a href='{SITE_LOGIN_URL}'>click here</a> to login."
            . "<br><br><br>
        Thanks and Regards,<br>
        Team {SITE_NAME}<br>";
        return $layout_serialNo;
    }

    public static function body_layout_for_missing_file($getDataArr)
    {
       // return false;
        $body_detailsLayout = '';
        foreach ($getDataArr as $key => $value) {
            $body_detailsLayout = $body_detailsLayout . "<ul>"
                . "<li>"
                . $value['Station']['serial_no']
                . "</li>"
                . "</ul>";
        }
        $layout_serialNo  = "
        Hello Team, <br><br> 
            Those serial number files are missing today, so please check"
            . $body_detailsLayout

            . "Please <a href='{SITE_LOGIN_URL}'>click here</a> to login."
            . "<br><br><br>
        Thanks and Regards,<br>
        Team {SITE_NAME}<br>";
        return $layout_serialNo;
    }

    
    function send1($to, $subject, $body, $cc = array(), $attachment = '')
    {
        if (is_array($to)) {
            $to = array_unique(array_map('trim', $to));
        }
        if (is_array($cc)) {
            $cc = array_unique(array_map('trim', $cc));
        }
        try {
            $replacement = array(
                '{SITE_NAME}' => Configure::read('Site.Name'),
                '{SITE_URL}' => Configure::read('Site.Url'),
                '{SITE_SUPPORT_EMAIL}' => Configure::read('Site.SupportEmail'),
                '{CURRENT_TIME}' => date('Y-m-d H:i:s'),
            );
            $body = str_replace(array_keys($replacement), array_values($replacement), $body);
            $subject = str_replace(array_keys($replacement), array_values($replacement), $subject);
            ini_set('max_execution_time', 15000);
            if (isLive()) {
                $Email = new CakeEmail('smtp');
            } else {
                $Email = new CakeEmail('gmail');
            }
            if (!empty($attachment)) {
                $Email->attachments($attachment);
            }
            
            $response = $Email->from(array(Configure::read('Site.FromEmail') => Configure::read('Site.FromName')))
                ->to($to)
                ->cc($cc)
                ->template('default')
                ->emailFormat('both')
                ->subject($subject)
                ->send($body);
                return true;
        } catch (Exception $e) {
            echo '<pre><b></b><br>';
            print_r($e->getMessage());echo '<br>';exit;
            return false;
        }
    }
    function send($to, $subject, $body, $cc = array(), $attachment = '', $missing_serial_no = '')
    {
        // $refresh_token =  Db::getInstance()->prepare("SELECT * FROM `outlook_access_token`");
        // $refresh_token->execute();
        // $tokenData = $refresh_token->fetch();
        $refresh_token = ClassRegistry::init("OutlookAccessToken")->find('first');
        if(!empty($refresh_token['OutlookAccessToken']['access_token'])){
            $access_token = $refresh_token['OutlookAccessToken']['access_token'];
        }else{
            $access_token = '';
        }
        if (is_array($to)) {
            $to = array_unique(array_map('trim', $to));
        }
        if (is_array($cc)) {
            $cc = array_unique(array_map('trim', $cc));
        }
        try {
            $replacement = array(
                '{SITE_NAME}' => Configure::read('Site.Name'),
                '{SITE_URL}' => Configure::read('Site.Url'),
                '{SITE_SUPPORT_EMAIL}' => Configure::read('Site.SupportEmail'),
                '{CURRENT_TIME}' => date('Y-m-d H:i:s'),
            );
            $headers = array(
                "User-Agent: php-tutorial/1.0",
                "Authorization: Bearer ".$access_token,
                "Accept: application/json",
                "client-request-id: ".$this->makeGuid(),
                "return-client-request-id: true"
            );
            $outlookApiUrl = "https://outlook.office.com/api/v2.0/Me";
            $response = $this->runCurl($outlookApiUrl, null, $headers);
            if($response['code'] != 200){
                $this->refresh_token();
            }
            $refresh_token = ClassRegistry::init("OutlookAccessToken")->find('first');
            if(!empty($refresh_token['OutlookAccessToken']['access_token'])){
                $access_token = $refresh_token['OutlookAccessToken']['access_token'];
            }else{
                $access_token = '';
            }
            $body = str_replace(array_keys($replacement), array_values($replacement), $body);
            $subject = str_replace(array_keys($replacement), array_values($replacement), $subject);
            ini_set('max_execution_time', 15000);
            $toSend = [];
            $toFromForm = explode(";", $to);
            foreach ($toFromForm as $eachTo) {
                if(strlen(trim($eachTo)) > 0) {
                    $thisTo = array(
                        "EmailAddress" => array(
                            "Address" => trim($eachTo)
                        )
                    );
                    array_push($toSend, $thisTo);
                }
            }
            $attachments = [];
            if(!empty($missing_serial_no) && $missing_serial_no == 'missing_serial_no'){
                $layout = $this->get_layout();
                $body = str_replace('{BODY}', $body, $layout);
            }else{
                $this->controller->set('body',$body);
                $this->controller->layout = false;
                $body = $this->controller->render('/Layouts/Emails/html/default');
            }
          
            $request = array(
                "Message" => array(
                    "Subject" =>$subject,
                    "ToRecipients" => $toSend,
                    "Attachments" => $attachments,
                    "Body" => array(
                        "ContentType" => "HTML",
                        "Content" => utf8_encode($body)
                    )
                )
            );
        
            $request = json_encode($request);
            $headers = array(
                "User-Agent: php-tutorial/1.0",
                "Authorization: Bearer ".$access_token,
                "Accept: application/json",
                "Content-Type: application/json",
                "Content-Length: ". strlen($request)
            );
            $api_url = "https://outlook.office.com/api/v2.0/me/sendmail";
            $response = $this->runCurl($api_url, $request, $headers);
            return  $response['code'];
        } catch (Exception $e) {
            echo '<pre><b></b><br>';
            print_r($e->getMessage());echo '<br>';exit;
            return false;
        }
    }

    public static function get_layout()
    {
        // return false;
        $year = date('Y');
        $todayDate = date('jS F Y');
        $siteUrl = Configure::read('Site.Url');
        $siteName =  Configure::read('Site.Name');
        $supportEmail = Configure::read('Site.SupportEmail');
        $noise = $siteUrl.'/img/noise.png';
        $email_layout = "<div>"
            . "<div style = 'overflow: hidden;'>"
            . "<div dir = 'ltr'>"
            . "<div style = 'font-family:verdana,sans-serif;font-size:small;color:#000000'><br></div>"
            . "<div>"
            . "<div style = 'background-color:#ffffff'>"
            . "<table width = '600' align = 'center' style = 'margin:20px auto;padding:20px 0;border-collapse:collapse'>"
            . "<tbody>"
            . "<tr>"
            . "<td style = 'margin:0;padding:0'></td>"
            . "<td bgcolor = '#FFFFFF' style = 'margin:0 auto;padding:0;border:1px solid #d5d5d5;display:block;max-width:600px;clear:both'>"
            . "<table bgcolor = '#FFFFFF' style = 'margin:0;padding:0;width:100%;border-bottom:1px solid #d5d5d5;background: #313D4F;'>"
            . "<tbody>"
            . "<tr>"
            . "<td style = 'margin:0;padding:10px 20px 10px 10px'>"
            . "<table style = 'margin:0;padding:0;width:100%'>"
            . "<tbody>"
            . "<tr>"
            . "<td width = '170' style = 'margin:0;padding:0;color:#fff;font-size:12px'>"
            . "<a target = '_blank' style = 'color:#fff' title = '$siteName' href = '$siteUrl'><img width = '170' style = 'max-width:100%' alt = '$siteName' src = 'https://dynalytics.site/img/logo_w.png'></a>"
            . "</td>"
            . "<td align = 'right' style = 'margin:0;padding:0;font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif;color:#fff;font-size:12px;line-height:14px;'> $todayDate "
            . "</td>"
            . "</tr>"
            . "</tbody>"
            . "</table>"
            . "</td>"
            . "</tr>"
            . "</tbody>"
            . "</table>"
            . "<table style='margin:0;padding:0;width:100%'>"
            . "<tbody>"
            . "<tr>"
            . "<td style='margin:0;padding:10px 15px 10px;color:#777;font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif'>"
            . "{BODY}"
            . "</td>"
            . "</tr>"
            . "</tbody>"
            . "<table cellspacing = '0' style = 'margin:0;padding:0;border-top:1px solid #e5e5e5;color:#7b7b7b;width:100%'>"
            . "<tbody>"
            . "<tr>"
            . "<td bgcolor = '#EEEEEE' style = 'background-image:url($noise);margin:0;padding:10px 15px;font-size:10px'>"
            . "<table style='margin:0;padding:0;width:100%'>"
            . "<tbody>"
            . "<tr>"
            . "<td style='margin:0;padding:0 10px 0 0;font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif;font-size:12px;line-height:18px'>"
            . "<strong>"
            . "<br>"
            . "<a target='_blank' style='text-decoration:none; color: #777777;' href='mailto:$supportEmail'>$supportEmail</a>"
            . "<br>"
            . "<a target='_blank' style='text-decoration:none; color: #777777;' href='$siteUrl/'>$siteName</a>"
            . "</strong>"
            . "</td>"
            . "</tr>"
            . "</tbody>"
            . "</table>"
            . "</td>"
            . "</tr>"
            . "</tbody>"
            . "</table>"
            . "</td>"
            . "<td style='margin:0;padding:0'></td>"
            . "</tr>"
            . "<tr>"
            . "<td style='margin:0;padding:0'></td>"
            . "<td style='text-align:center;clear:both;max-width:600px;display:block;padding:0px;margin:0px auto'>"
            . "<img width='100%' src='$siteUrl/img/email_shadow.png'>"
            . "</td>"
            . "<td style='margin:0;padding:0'></td>"
            . "</tr>"
            . "<tr>"
            . "<td style='margin:0;padding:0'></td>"
            . "<td align='center' style='clear:both;max-width:600px;display:block;padding:0px;margin:0px auto;color:#aaaaaa;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif'>"
            . "<p style='margin-bottom:10px;font-weight:normal;font-size:12px'>Questions? Please do not reply to this email; responses are not monitored. If you have questions, please use the contact information above."
            . "</p>"
            . "</td>"
            . "<td style='margin:0;padding:0'></td>"
            . "</tr>"
            . "<tr>"
            . "<td style='margin:0;padding:0'></td>"
            . "<td align='center' style='clear:both;max-width:600px;display:block;padding:0px;margin:0px auto;color:#aaaaaa;font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif'>"
            . "<p style='margin-bottom:10px;font-weight:normal;font-size:12px'>"
            . "Copyright Â&copy; $year DynaLytics. All Rights Reserved."
            . "</p>"
            . "</td>"
            . "<td style = 'margin:0;padding:0'></td>"
            . "</tr>"
            . "</tbody>"
            . "</table>"
            . "</div>"
            . "</div>"
            . "</div>"
            . "</div>"
            . "</div>";
        return $email_layout;
    }
    function runCurl($url, $post = null, $headers = null) {
        $http_code = '';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, $post == null ? 0 : 1);
        if($post != null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if($headers != null) {
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($http_code >= 400) {
            $this->refresh_token();
        }
        return array("code" => $http_code, "response" => $response);
    }

    function refresh_token() {
        $client_id = "f55f2aea-9433-4234-b4b2-c5472d659795";
        $client_secret = "PFQ8Q~frNh1NNPpqakhpLyhoYdGg9SLu6qWEuc3H";
        $redirect_uri = "https://schooltap.com/text_parsing/generate_code.php";
        $authority = "https://login.microsoftonline.com";
        $scopes = array("offline_access", "openid");
        if(true) {
            array_push($scopes, "https://outlook.office.com/mail.read");
        }
        /* If you need to send email, then need to add following scope */
        if(true) {
            array_push($scopes, "https://outlook.office.com/mail.send");
        }
        $auth_url = "/common/oauth2/v2.0/authorize";
        $auth_url .= "?client_id=".$client_id;
        $auth_url .= "&redirect_uri=".$redirect_uri;
        $auth_url .= "&response_type=code&scope=".implode(" ", $scopes);
        $token_url = "/common/oauth2/v2.0/token";
        $api_url = "https://outlook.office.com/api/v2.0";
        $refresh_token = ClassRegistry::init("OutlookAccessToken")->find('first');
        if(!empty($refresh_token['OutlookAccessToken']['access_token'])){
            $access_token = $refresh_token['OutlookAccessToken']['refresh_token'];
        }else{
            $access_token = '';
        }
        if(!empty($access_token)){
            $token_request_data = array (
                "grant_type" => "refresh_token",
                "refresh_token" => $access_token,
                "redirect_uri" => $redirect_uri,
                "scope" => implode(" ", $scopes),
                "client_id" => $client_id,
                "client_secret" => $client_secret
            );
            $body = http_build_query($token_request_data);
            $response = $this->runCurl($authority.$token_url, $body);
            $response = json_decode($response['response'],true);
            $this->store_token($response);
            return array("response" => $response);
        }else{
            echo "Please login again";
        }
        
    }

    function store_token($o) {
        $data = $o;
        $access_token = $o['access_token'];
        $refresh_token = $o['refresh_token'];
        $id_token = $o['id_token'];
        $data = array('OutlookAccessToken' => array('id' => 1, 'access_token' => $access_token,'refresh_token'=> $refresh_token,'id_token' =>$id_token));
        ClassRegistry::init("OutlookAccessToken")->save($data);
        return true;
        // file_put_contents("office_auth_config.txt", json_encode($o));
    }

    
    function makeGuid(){
        if (function_exists('com_create_guid')) {
            error_log("Using 'com_create_guid'.");
            return strtolower(trim(com_create_guid(), '{}'));
        }
        else {
            $charid = strtolower(md5(uniqid(rand(), true)));
            $hyphen = chr(45);
            $uuid = substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid, 12, 4).$hyphen
                .substr($charid, 16, 4).$hyphen
                .substr($charid, 20, 12);
            return $uuid;
        }
    }
}

?>
