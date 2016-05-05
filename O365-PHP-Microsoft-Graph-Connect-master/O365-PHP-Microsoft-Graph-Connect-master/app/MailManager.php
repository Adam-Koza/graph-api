<?php
/**
 *  Copyright (c) Microsoft. All rights reserved. Licensed under the MIT license.
 *  See LICENSE in the project root for license information.
 * 
 *  PHP version 5
 *
 *  @category Code_Sample
 *  @package  O365-PHP-Microsoft-Graph-Connect
 *  @author   Ricardo Loo <ricardol@microsoft.com>
 *  @license  MIT License
 *  @link     http://GitHub.com/OfficeDev/O365-PHP-Microsoft-Graph-Connect
 */

namespace Microsoft\Office365\UnifiedAPI\Connect;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'Constants.php';
require_once 'RequestManager.php';

/** 
 *  Handles the creation of the email and sends the request 
 *  to the Office 365 unified endpoint
 *
 *  @class    MailManager
 *  @category Code_Sample
 *  @package  O365-PHP-Microsoft-Graph-Connect
 *  @author   Ricardo Loo <ricardol@microsoft.com>
 *  @license  MIT License
 *  @link     http://GitHub.com/OfficeDev/O365-PHP-Microsoft-Graph-Connect
 */
class MailManager
{
    
    /**
     *  Builds the email message and uses RequestManager to send a POST request 
     *  to the sendmail endpoint in the Microsoft Graph.
     *
     *  @param string $recipient The recipient of the email.
     *
     *  @function sendWelcomeMail
     *  @return   Nothing, passes RuntimeException from RequestManager on error
     */
    public static function sendWelcomeMail($recipient)
    {
        $emailBody = file_get_contents('MailTemplate.html');
        
        // Use the given name if it exists, otherwise, use the alias
        $greetingName = isset($_SESSION['given_name'])
                        ? $_SESSION['given_name'] 
                        : explode('@', $_SESSION['unique_name'])[0];

        $emailBody = str_replace(
            '{given_name}',
            $greetingName,
            $emailBody
        );
        
        // Build the HTTP request payload (the Message object).
        $email = "{
            Message: {
            Subject: 'Welcome to Office 365 development with PHP',
            Body: {
                ContentType: 'HTML',
                Content: '{$emailBody}'
            },
            ToRecipients: [
                {
                    EmailAddress: {
                    Address: '{$recipient}'
                    }
                }
            ]
            },
            SaveToSentItems: true
            }";
            
        // Send the email request to the sendmail endpoint, 
        // which is in the following URI:
        // https://graph.microsoft.com/v1.0/me/microsoft.graph.sendmail
        // Note that the access token is attached in the Authorization header
        RequestManager::sendPostRequest(
            Constants::RESOURCE_ID . Constants::SENDMAIL_ENDPOINT,
            array(
                'Authorization: Bearer ' . $_SESSION['access_token'],
                'Content-Type: application/json;' . 
                              'odata.metadata=minimal;' .
                              'odata.streaming=true'
            ),
            $email
        );
    }
}
?>

