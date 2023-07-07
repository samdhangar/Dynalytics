<?php
/**
 *
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 2.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * This is email configuration file.
 *
 * Use it to configure email transports of CakePHP.
 *
 * Email configuration class.
 * You can specify multiple configurations for production, development and testing.
 *
 * transport => The name of a supported transport; valid options are as follows:
 *  Mail - Send using PHP mail function
 *  Smtp - Send using SMTP
 *  Debug - Do not send the email, just return the result
 *
 * You can add custom transports (or override existing transports) by adding the
 * appropriate file to app/Network/Email. Transports should be named 'YourTransport.php',
 * where 'Your' is the name of the transport.
 *
 * from =>
 * The origin email. See CakeEmail::from() about the valid values
 *
 */
class EmailConfig
{
    // public $default = array(
    //     'transport' => 'Mail',
    //     'from' => 'info@securemetasys.com',
    //     //'charset' => 'utf-8',
    //     //'headerCharset' => 'utf-8',
    // );
    public $smtp = array(
        'transport' => 'Smtp',
        'from' => array('samdhangar1411@gmail.com' => 'Dynalitics Team'),
        //'from' => array('amit@mindpowerit.com' => 'Dynalitics Team'),
        'host' => 'ssl://smtp.gmail.com',
        'port' => 465 ,
        'timeout' => 30,
        // 'username' => 'santosh@securemetasys.com',
        // 'password' => '3SIVo9ogdCirwgVAHAjfKg',
        'username' => 'samdhangar1411@gmail.com',
        'password' => 'mrxjxhbsrnlqidlu',
        'client' => null,
        'auth' => true,
        'context' => [
            'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
            ]
            ],
        'log' => false,
            'charset' => 'utf-8',
            'headerCharset' => 'utf-8',
    );

    public $fast = array(
        'from' => 'you@localhost',
        'sender' => null,
        'to' => null,
        'cc' => null,
        'bcc' => null,
        'replyTo' => null,
        'readReceipt' => null,
        'returnPath' => null,
        'messageId' => true,
        'subject' => null,
        'message' => null,
        'headers' => null,
        'viewRender' => null,
        'template' => false,
        'layout' => false,
        'viewVars' => null,
        'attachments' => null,
        'emailFormat' => null,
        'transport' => 'smtp',
        'host' => 'localhost',
        'port' => 25,
        'timeout' => 300,
        'username' => 'user',
        'password' => 'secret',
        'client' => null,
        'log' => true,
        //'charset' => 'utf-8',
        //'headerCharset' => 'utf-8',
    );
    // public $gmail = array(
    //     'host' => 'ssl://smtp.gmail.com',
    //     'port' => 465,
    //     'username' => 'securemetasys.test@gmail.com',
    //     'password' => '$m$Developers@2',
    //     'transport' => 'Smtp'
    // );
    public $gmail = array(
        // 'transport' => 'Smtp',
        // 'from' => array('info@ballonmand.dk' => 'Ballonmand.dk'),
        // 'host' => 'ssl://smtp.gmail.com',
        // 'port' => 465,
        // 'timeout' => 30,
        // 'username' => 'info@ballonmand.dk',
        // 'password' => "ggsva553xv",
        // 'client' => null,
        // 'log' => false,
        // 'charset' => 'utf-8',
        //'headerCharset' => 'utf-8',
        'transport' => 'Smtp',
        'from' => array('samdhangar1411@gmail.com'),
        'host' => 'ssl://smtp.gmail.com',
        'port' => 465,
        'timeout' => 30,
        'username' => 'samdhangar1411@gmail.com',
        'password' => 'mrxjxhbsrnlqidlu',
        'client' => null,
        // 'tls' => 'null',
        'context' => [
        'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
        ]
        ],
        // 'url' => env('EMAIL_TRANSPORT_DEFAULT_URL', null),
        'log' => false,
        'charset' => 'utf-8',
        );

}
