<?php

/**
 * ReadEmailComponent handle task related to send emails on various event like,
 * Signup, Forgot password, reset password, etc..
 */
class ReadEmailComponent extends Component
{
    var $components = array("Siteconfig");

    public $host_name = null;
    public $user_name = null;
    public $password = null;
    public $from_email = null;
    public $from_subject = null;
    public $last_sync = null;
    public $last_sync_email_no = null;
    public $folder = null;
    public $emails = null;
    public $notification_email = null;
    public $email_count = 0;
    public $email_details = array();

    /**
     * Initialize the component with some predefined config.
     * @param array $config
     */
    public function __construct(ComponentCollection $collection, $settings = array())
    {
        $this->settings = $settings;
        parent::__construct($collection, $settings);

        // In it Site Configuration
        
        // Setup Email setting to connect
        $this->host_name = "{imap.gmail.com:993/ssl}INBOX"; 
        //$this->user_name = 'arush929292@gmail.com';
        $this->user_name = 'dynalitics.files@gmail.com';
       // $this->password = 'arush@123';
        //$this->password = 'xdspbwrsmoewefta';
        /*$this->password = 'ibojpfjtzvaynkno';*/
        $this->password = 'F6Hqu6aF7lY7';        
        $this->from_email = Configure::read('ReadEmail.from_email');
        $this->last_sync =  !empty(Configure::read('Site.last_sync')) ? date("d-M-Y",strtotime(Configure::read('Site.last_sync'))) : '';
    }

    /**
    * Connect with email sever
    * 
    */
    public function connect()
    {
        $this->folder = imap_open($this->host_name, $this->user_name, $this->password) or die('Cannot connect due to : ' . imap_last_error());
    }

    /**
    * Get email details
    * 
    * @param $options Mix
    * @return Email Details with total Count
    */
    public function get_details($last_sync=null,$options = array())
    {
        $email_details = [];
        $critaria = [];
        //$critaria[] = !empty($this->from_subject) ? ' SUBJECT "' . $this->from_subject . '"' : '';
        //$critaria[] = ' SINCE "' . $this->last_sync . '"';
        
        $critaria = array_filter($critaria);
        
       /* if (!empty($critaria)) {
            $critaria = implode(" ",$critaria);
        }else{
            $critaria = 'ALL';
        }*/
        
        $email_count = 0;

        if (!empty($last_sync)) {
            $critaria = ' SINCE "' . $last_sync . '"';    
        }else{
            $critaria = 'ALL';   
        }

        $emails = imap_search($this->folder, $critaria);

        $this->all_branches = ClassRegistry::init('CompanyBranch')->find('list',array('recursive'=>1,'fields'=>array('id','ftpuser'),'conditions'=>array('CompanyBranch.status'=>'1','CompanyBranch.branch_status'=>'active')));
        
        if ($emails) {
            foreach ($emails as $key => $email_no) {
                $overview = imap_fetch_overview($this->folder, $email_no, 0);
                $overview = $overview[0];
                /*if(strpos($overview->subject,'youer') !== false){*/
                    /* get mail structure */

                    $arr = explode(' ',$overview->subject);
                    $kay = $arr[0]; //
                    
                    if (!empty($overview)) 
                    {
                        if (in_array($kay, $this->all_branches)) 
                        {
                           $attachments = $this->get_zip_attachment($email_no);
                       
                            foreach($attachments as $attachment)
                            {
                                if($attachment['is_attachment'] == 1)
                                {
                                    $filename = $attachment['name'];
                                   
                                    if(empty($filename)) $filename = $attachment['filename'];
                                    $savepath = WWW_ROOT."files/".$filename;
                                    echo $savepath;
                                    $extractPath = ROOT . DS . BRANCH_PATH . $kay . DS . 'queue';
                                    //Save file in webroot
                                    file_put_contents($savepath, $attachment);
                                    sleep(15);
                                    exec("unzip -o ".$savepath." -d ".$extractPath);
                                    //Extract file from webroot
                                    $zip = new \ZipArchive;
                                    $res = $zip->open($savepath);
                                   
                                    if (file_exists($savepath)) {
                                        //Remove zip file from location
                                        unlink($savepath);
                                    }else {
                                        echo 'file not found';
                                    }
                                }
                            }

                            sleep(5);
                            $files=glob($extractPath."/*.*");
                            foreach ($files as $key => $file) {
                                $ext = pathinfo($file, PATHINFO_EXTENSION);
                                if ($ext != 'txt') {
                                    unlink($file);
                                }else{
                                    chown($file, "ubuntu");
                                    chgrp($file, "ubuntu");

                                    $file = str_replace('\\', '/', $file);
                                    $filename = basename($file, ".txt");
                                    $filename = str_replace("(", "[", $filename);
                                    $filename = str_replace(")", "]", $filename);

                                    $renameFile = $extractPath."/".$filename.".txt";
                                    $renameFile = str_replace('\\', '/', $renameFile);
                                    rename($file, $renameFile);
                                }
                            }
                        }else{
                            $email_count++;
                            $email_details[] = array(
                                'subject' => $overview->subject,
                                'body' => $this->get_body($email_no),
                                'from' => $overview->from,
                                'to' => $overview->to,
                                'date' => $overview->date,
                                'message_id' => $overview->message_id,
                                'size' => $overview->size,
                                'uid' => $overview->uid,
                                'msgno' => $overview->msgno,
                                'recent' => $overview->recent,
                                'flagged' => $overview->flagged,
                                'answered' => $overview->answered,
                                'deleted' => $overview->deleted,
                                'seen' => $overview->seen,
                                'draft' => $overview->draft,
                                'udate' => $overview->udate,
                            );
                        }
                    }
                /*}*/
            }
        }
        return array('emails' => $email_details, 'totalEmails' => $email_count);
    }

    /**
    * Fetch Email by Last Sync
    * 
    * @return list of emails    
    */
    public function fetch_by_last_sync($last_sync=nulll)
    {
        $this->connect();
        return $this->get_details($last_sync);
    }

    public function get_body($emailNumber = null)
    {
        $dataTxt = $this->get_part($this->folder, $emailNumber, "TEXT/PLAIN");
        $dataHtml = $this->get_part($this->folder, $emailNumber, "TEXT/HTML");

        if ($dataHtml != "") {
            $msgBody = $dataHtml;
        } else {
            $msgBody = preg_replace("\n", "<br>", $dataTxt);
        }
        return $msgBody;
    }

    public function get_mime_type(&$structure)
    {
        $primary_mime_type = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");
        if ($structure->subtype) {
            return $primary_mime_type[(int) $structure->type] . '/' . $structure->subtype;
        }
        return "TEXT/PLAIN";
    }

    public function get_part($stream, $msg_number, $mime_type, $structure = false, $part_number = false)
    {

        if (!$structure) {
            $structure = imap_fetchstructure($stream, $msg_number);
        }
        if ($structure) {
            if ($mime_type == $this->get_mime_type($structure)) {
                if (!$part_number) {
                    $part_number = "1";
                }
                $text = imap_fetchbody($stream, $msg_number, $part_number);
                if ($structure->encoding == 3) {
                    return imap_base64($text);
                } else if ($structure->encoding == 4) {
                    return imap_qprint($text);
                } else {
                    return $text;
                }
            }

            if ($structure->type == 1) {
                while (list($index, $sub_structure) = each($structure->parts)) {
                    $prefix = '';
                    if ($part_number) {
                        $prefix = $part_number . '.';
                    }
                    $data = $this->get_part($stream, $msg_number, $mime_type, $sub_structure, $prefix . ($index + 1));
                    if ($data) {
                        return $data;
                    }
                }
            }
        }
        return false;
    }


    public function get_zip_attachment($email_no)
    {
        $structure = imap_fetchstructure($this->folder, $email_no);
        $attachments = array();

        /* if any attachments found... */
        if(isset($structure->parts) && count($structure->parts)) 
        {
            for($i = 0; $i < count($structure->parts); $i++) 
            {
                $attachments[$i] = array(
                    'is_attachment' => false,
                    'filename' => '',
                    'name' => '',
                    'attachment' => ''
                );

                if($structure->parts[$i]->ifdparameters) 
                {
                    foreach($structure->parts[$i]->dparameters as $object) 
                    {
                        if(strtolower($object->attribute) == 'filename') 
                        {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['filename'] = $object->value;
                        }
                    }
                }

                if($structure->parts[$i]->ifparameters) 
                {
                    foreach($structure->parts[$i]->parameters as $object) 
                    {
                        if(strtolower($object->attribute) == 'name') 
                        {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['name'] = $object->value;
                        }
                    }
                }

                if($attachments[$i]['is_attachment']) 
                {
                    $attachments[$i]['attachment'] = imap_fetchbody($this->folder, $email_no, $i+1);

                    /* 3 = BASE64 encoding */
                    if($structure->parts[$i]->encoding == 3) 
                    { 
                        $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                    }
                    /* 4 = QUOTED-PRINTABLE encoding */
                    elseif($structure->parts[$i]->encoding == 4) 
                    { 
                        $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                    }
                }
            }
        }
        return $attachments;
    }

}

?>
