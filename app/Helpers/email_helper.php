<?php

function sendEmail($subject, $to, $view)
{
    $email = \Config\Services::email();
    $email->setTo($to);
    $email->setSubject($subject);
    $email->setMessage($view);
    if ($email->send(false)) {
        return true;
    }
    return false;
}

function sendEmailAttachment($subject, $to, $view, $attach)
{
    $email = \Config\Services::email();
    $email->setTo($to);
    $email->setSubject($subject);
    $email->setMessage($view);
    $email->attach($attach);
    if ($email->send(false)) {
        return true;
    }
    return false;
}