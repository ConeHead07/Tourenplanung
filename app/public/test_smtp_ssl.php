<?php
require 'test_index.php';
    
    $profile = 'mertens'; // google
    if (0) $profile = 'google'; // google
    
    switch($profile) {
    
        case 'mertens':
            $host = 'mail.mertens.ag';
            $from = 'move@mertens.ag';
            $config = array('ssl' => 'tls',    // tls',
                            'port' => 25,     // 25, // Optionale unterstützte Portnummer'auth' => 'login',
                            'auth' => 'login',
                            'username' => 'move@mertens.ag', // 'mertens\move',
                            'password' => 'move2010',
            );
            break;
    
        case 'google':
            $host = 'smtp.gmail.com';
            $from = 'frank.barthold@gmail.com';

            $config = array('ssl' => 'ssl',
                            'port' => 465, // Optionale unterstützte Portnummer'auth' => 'login',
                            'auth' => 'login',
                            'username' => 'frank.barthold',
                            'password' => 'rCL$qv79',
            );
            break;
    
        default:
            // Nothing
            die('no profile selected!');
    }
     
    $transport = new Zend_Mail_Transport_Smtp($host, $config);
    
    $sent = false;
    $mail = new Zend_Mail();
    
    try {
        $mail
        ->setBodyText('Das ist der Text des Mails.')
        ->setFrom($from, '')
        ->addTo('frank.barthold@gmail.com', '')
        ->setSubject('TestBetreff')
        ->send($transport);
        
        $sent = true;
    } catch(Zend_Mail_Protocol_Exception $e) {
        echo '#' . __LINE__ . ' Code: '  . $e->getCode()    . '<br/>' . PHP_EOL;
        echo '#' . __LINE__ . ' ERROR: ' . $e->getMessage() . '<br/>' . PHP_EOL;
    } catch(Zend_Mail_Storage_Exception $e) {
        echo '#' . __LINE__ . ' Code: '  . $e->getCode()    . '<br/>' . PHP_EOL;
        echo '#' . __LINE__ . ' ERROR: ' . $e->getMessage() . '<br/>' . PHP_EOL;      
    } catch(Zend_Mail_Transport_Exception $e) {
        echo '#' . __LINE__ . ' Code: '  . $e->getCode()    . '<br/>' . PHP_EOL;
        echo '#' . __LINE__ . ' ERROR: ' . $e->getMessage() . '<br/>' . PHP_EOL;     
    } catch(Zend_Mail_Exception $e) {
        echo '#' . __LINE__ . ' Code: '  . $e->getCode()    . '<br/>' . PHP_EOL;
        echo '#' . __LINE__ . ' ERROR: ' . $e->getMessage() . '<br/>' . PHP_EOL;
    } catch(Zend_Exception $e) {
        echo '#' . __LINE__ . ' Code: '  . $e->getCode()    . '<br/>' . PHP_EOL;
        echo '#' . __LINE__ . ' ERROR: ' . $e->getMessage() . '<br/>' . PHP_EOL;
    } catch(Exception $e) {
        echo '#' . __LINE__ . ' Code: '  . $e->getCode()    . '<br/>' . PHP_EOL;
        echo '#' . __LINE__ . ' ERROR: ' . $e->getMessage() . '<br/>' . PHP_EOL;
    }
    
    echo '#' . __LINE__ . ' sent ' . ($sent ? 'executed!' : 'failed!') . '<br/>' . PHP_EOL;


?>