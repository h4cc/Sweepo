<?php

namespace Sweepo\CoreBundle\Service\MailerTransport;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

use Buzz\Browser as Buzz;

/**
 *  See the do of the API :
 *  https://mandrillapp.com/api/docs/messages.html
 */
class Mandrill
{
    const GENERAL_ERROR = 'GeneralError';
    const INVALID_KEY   = 'Invalid_Key';

    private $buzz;
    private $from_name;
    private $html;
    private $text;
    private $async = false;
    private $headers;
    private $open = true;
    private $click = true;
    private $auto_text;
    private $url_strip_qs;
    private $preserve_recipients;
    private $bcc_address;
    private $google_analytics_domains;
    private $google_analytics_campaign;
    private $metadata;
    private $rcpt;
    private $rcpt_values;
    private $attachments_type;
    private $attachments_name;
    private $attachments_content;

    private $key;
    private $from_email;
    private $subject;
    private $to;
    private $message;

    /**
     *  @param  Buzz          $buzz       SensioBuzz service
     *  @param  string        $apiKey     Mandrill API key (from parameter.yml)
     */
    public function __construct(Buzz $buzz, $apiKey)
    {
        $this->buzz = $buzz;
        $this->key = $apiKey;
    }

    /**
     *  Send the message
     *
     *  @return boolean
     */
    public function send()
    {
        $this->message['to'] = $this->to;

        // Serialize the data
        $serializer = new Serializer([new GetSetMethodNormalizer()], [new JsonEncoder()]);
        $data = $serializer->serialize($this, 'json');

        // Mandrill
        return $this->buzz->post('https://mandrillapp.com/api/1.0/messages/send.json', array(), $data);
    }

    /**
     *  Clean the data
     */
    public function clean()
    {
        $this->html = null;
        $this->text = null;
        $this->subject = null;
        $this->to = [];
    }

    // ---- Methods for Serialize ---- //

    /**
     *  Set apiKey
     *
     *  A valid API key
     *
     *  @param  string  $apiKey
     */
    public function setKey($apiKey)
    {
        $this->key = $apiKey;
    }

    /**
     *  Get apiKey
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     *  Set message
     *
     *  The information on the message to send
     *
     *  @param  array  $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     *  Get message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     *  Set Async
     *
     *  @param  boolean  $async
     */
    public function setAsync($async)
    {
        $this->async = $async;
    }

    /**
     *  Get Async
     */
    public function getAsync()
    {
        return $this->async;
    }

    // ---- Methods for Validate ---- //

    /**
     *  Set from
     *  @param  string  $email  the sender email address.
     *  @param  string  $name   optional from name to be used
     */
    public function setFrom($email, $name = null)
    {
        $this->from_email = $this->message['from_email'] = $email;

        if (!empty($name)) {
            $this->from_name = $this->message['from_name'] = $name;
        }
    }

    /**
     *  Set the html message
     *
     *  The full HTML content to be sent
     *
     *  @param  sting  $html
     */
    public function setHtml($html)
    {
        $this->html = $this->message['html'] = $html;
    }

    /**
     *  Set text
     *
     *  Optional full text content to be sent
     *
     *  @param  string text
     */
    public function setText($text)
    {
        $this->text = $this->message['text'] = $text;
    }

    /**
     *  Set subject
     *
     *  The message subject required
     *
     *  @param  string  $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $this->message['subject'] = $subject;
    }

    /**
     *  Set to
     *
     *  An array of recipient information.
     *
     *  @param  string  $email  the email address of the recipient required
     *  @param  string  $name   the optional display name to use for the recipient
     */
    public function setTo($email, $name = null)
    {
        $to = ['email' => $email];

        if (!empty($name)) {
            $to['name'] = $name;
        }

        $this->to[] = $to;
    }

    /**
     *  Set headers
     *
     *  Optional extra headers to add to the message
     *  (currently only Reply-To and X-* headers are allowed)
     *
     *  @param  array  $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $this->message['headers'] = $headers;
    }

    /**
     *  Set track opens
     *
     *  Whether or not to turn on open tracking for the message
     *
     *  @param  boolean  $open
     */
    public function setTrackOpens($open)
    {
        $this->open = $this->message['track_opens'] = $open;
    }

    /**
     *  Set track clicks
     *
     *  Whether or not to turn on click tracking for the message
     *
     *  @param  boolean  $click
     */
    public function setTrackClicks($click)
    {
        $this->click = $this->message['track_clicks'] = $click;
    }

    /**
     *  Set auto text
     *
     *  Whether or not to automatically generate a text part for
     *  messages that are not given text
     *
     *  @param  boolean  $auto_text
     */
    public function setAutoText($auto_text)
    {
        $this->auto_text = $this->message['auto_text'] = $auto_text;
    }

    /**
     *  Set url strip qs
     *
     *  Whether or not to strip the query string from URLs when
     *  aggregating tracked URL data
     *
     *  @param  boolean  $url_strip_qs
     */
    public function setUrlStripQs($url_strip_qs)
    {
        $this->url_strip_qs = $this->message['url_strip_qs'] = $url_strip_qs;
    }

    /**
     *  Set preserve recipients
     *
     *  Whether or not to expose all recipients in to "To" header
     *  for each email
     *
     *  @param  boolean  $preserve_recipients
     */
    public function setPreserveRecipients($preserve_recipients)
    {
        $this->preserve_recipients = $this->message['preserve_recipients'] = $preserve_recipients;
    }

    /**
     *  Set bcc_address
     *
     *  An optional address to receive an exact copy of each
     *  recipient's email
     *
     *  @param  string  $bcc_address
     */
    public function setBccAddress($bcc_address)
    {
        $this->bcc_address = $this->message['bcc_address'] = $bcc_address;
    }

    /**
     *  Set google analytics domains
     *
     *  An array of strings indicating for which any matching URLs
     *  will automatically have Google Analytics parameters appended
     *  to their query string automatically.
     *
     *  @param  array  $google_analytics_domains
     */
    public function setGoogleAnalyticsDomains($google_analytics_domains)
    {
        $this->google_analytics_domains = $this->message['google_analytics_domains'] = $google_analytics_domains;
    }

    /**
     *  Set google analytics campaign
     *
     *  Optional string indicating the value to set for the utm_campaign tracking parameter.
     *  If this isn't provided the email's from address will be used instead.
     *
     *  @param  array  $google_analytics_campaign
     */
    public function setGoogleAnalyticsCampaign($google_analytics_campaign)
    {
        $this->google_analytics_campaign = $this->message['google_analytics_campaign'] = $google_analytics_campaign;
    }

    /**
     *  Set metadata
     *
     *  Metadata an associative array of user metadata. Mandrill will store
     *  this metadata and make it available for retrieval.
     *  In addition, you can select up to 10 metadata fields to index and
     *  make searchable using the Mandrill search api.
     *
     *  @param  array  $metadata
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $this->message['metadata'] = $metadata;
    }

    /**
     *  Set recipient_metadata
     *
     *  Per-recipient metadata that will override the global values specified
     *  in the metadata parameter.
     *
     *  @param  string  $rcpt
     *  @param  array   $values
     */
    public function setRecipientMetadata($rcpt = null, $values = null)
    {
        $recipient_metadata = array();

        if (!empty($rcpt)) {
            $this->rcpt = $recipient_metadata['rcpt'] = $rcpt;
        }

        if (!empty($values)) {
            $this->rcpt_values = $recipient_metadata['values'] = $values;
        }

        $this->message['recipient_metadata'][] = $recipient_metadata;
    }

    /**
     *  Set attachments
     *
     *  An array of supported attachments to add to the message
     *
     *  @param  string  $type     the MIME type of the attachment - allowed types are text/*, image/*, and application/pdf
     *  @param  string  $name     the file name of the attachment
     *  @param  string  $content  the content of the attachment as a base64-encoded string
     */
    public function setAttachments($type = null, $name = null, $content = null)
    {
        $attachments = array();

        if (!empty($type)) {
            $this->attachments_type = $attachments['type'] = $type;
        }

        if (!empty($name)) {
            $this->attachments_name = $attachments['name'] = $name;
        }

        if (!empty($content)) {
            $this->attachments_content = $attachments['content'] = base64_encode(file_get_contents($content));
        }

        $this->message['attachments'][] = $attachments;
    }

    /**
     *  Extract emails and name from addresses
     *
     *  @param  mixed  $addresses
     */
    public function setRecipients($addresses)
    {
        $this->to = [];

        if (is_array($addresses)) {

            foreach ($addresses as $key => $email) {
                $name = null;

                if (!is_int($key)) {
                    $name = $key;
                }

                $this->setTo($email, $name);
            }

            return;
        }

        $this->setTo($addresses);
    }
}