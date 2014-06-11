<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class MailTask extends \Phalcon\CLI\Task
{
    public function sendmailAction( $params = [] )
    {
        $limit      = $this->filter->sanitize( $params['limit'], 'int' );

        $messages   = $this->models->getSendmail()->getMessagesFromQueue( $limit ? $limit : 30 );

        if( empty($messages) )
        {
            die( '['.date( 'd.m.Y H:i:s' ).'] nothing to send'."\n\n" );
        }

        foreach( $messages as $message )
        {
            echo( '['.date( 'd.m.Y H:i:s' ).'] sending message ID #'.$message['id'].'... ' );

            $email_from = 'robot@'.\config::getDomainByID( $message['domain_id'] );

            $header =
                'From: '.$email_from."\n".
                'MIME-Version: 1.0'."\n".
                'Content-type: text/html; charset=UTF-8'."\n".
                'Content-Transfer-Encoding: 8bit'."\n".
                'X-Mailer: PHP/'.phpversion();

            if(
                mail(
                    $message['email_to'],
                    $message['subject'],
                    $message['message'],
                    $header,
                    '-f '.$email_from
                    )
              )
            {
                $this->models->getSendmail()->deleteMessageFromQueue( $message['id'] );

                echo( "OK\n" );
            }
            else
            {
                $message['errors_count']++;

                if( $message['errors_count'] > 2 )
                {
                    $this->models->getSendmail()->deleteMessageFromQueue( $message['id'] );
                }
                else
                {
                    $this->models->getSendmail()->updateMessageErrorsCount( $message['id'], $message['errors_count'] );
                }

                echo( "ERROR\n" );
            }
        }

        die( "\n" );
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////