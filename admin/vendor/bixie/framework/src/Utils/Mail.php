<?php

namespace Bixie\Framework\Utils;


class Mail
{

    protected $template;

    protected $bcc = [];
    protected $maildata = [];

    /**
     * @param array $bcc
     * @param string $template
     */
    public function __construct (array $bcc, $template) {
        $this->bcc = $bcc;
        $this->template = $template;
    }

    /**
     * @param $recipient
     * @return array
     * @internal param array|string $receiver
     */
    public function getMaildata ($recipient) {
        //clean string
        if (!is_array($recipient)) {
            $recipient = array_map('trim', explode(';', $recipient));
        }
        $config = \JFactory::getConfig();
        $this->maildata['fromname'] = $config->get('fromname');
        $this->maildata['mailfrom'] = $config->get('mailfrom');
        $this->maildata['sitename'] = $config->get('sitename');
        $this->maildata['siteurl'] = \JUri::root();
        $this->maildata['subject'] = '';
        $this->maildata['body'] = '';
        $this->maildata['recipient'] = $recipient;
        $this->maildata['cc'] = [];
        $this->maildata['bcc'] = $this->bcc;
        $this->maildata['bcc'][] = 'admin@bixie.nl';
        $this->maildata['attachment'] = null;
        return $this->maildata;
    }

    /**
     * mailen
     * @param $maildata
     * @return bool
     */
    public function sendMail ($maildata) {
        $this->maildata['body'] = str_replace('{site_url}', $maildata['siteurl'], $maildata['body']);
        $this->maildata['body'] = str_replace('{site_naam}', $maildata['sitename'], $maildata['body']);
        $this->maildata['attachment'] = Arr::get($maildata, 'attachment', null);
        $body = nl2br($maildata['body']);
        //template
        if (file_exists($this->template)) {
            $tmpl = file_get_contents($this->template);
            $body = str_replace('{mail_tekst}', $body, $tmpl);
        }
        //send mail
        return \JFactory::getMailer()->sendMail(
            $maildata['mailfrom'],
            $maildata['fromname'],
            $maildata['recipient'],
            $maildata['subject'],
            $body,
            true,
            $maildata['cc'],
            $maildata['bcc'],
            $maildata['attachment']
        );
    }


}