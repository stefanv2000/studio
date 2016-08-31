<?php
/**
 * author stefanvalea@gmail.com
 */

namespace Application\Infrastructure\Email;


use Entities\Mapper\EmailMapper;
use Zend\Mail\Message as MailMessage;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Mime as ZendMime;
use Zend\Mail\Transport\Sendmail as SendmailTransport;
class EmailSender {

    protected $body;
    protected $emails;
    protected $images;
    protected $fromemail = "noreply@ studiogroup.com";
    protected $fromname = " studio Group";
    protected $subject = "Become a model application";

    /**
     * @var MimeMessage
     */
    protected $mimebody;

    /**
     * EmailSender constructor.
     * @param $body
     * @param $emails
     * @param $images
     */
    public function __construct($body, $emails, $images) {
        $this->body = $body;
        $this->emails = $emails;
        $this->images = $images;
    }




    protected function prepare(){
        $body = $this->body;
        foreach ($this->images as $image){
            $body.='<img src="'.$image.'" />';
        }

        $this->body = $body;

        $images = array();
        //replace all images from the body
        if (preg_match_all('/<img.*src="(.*)".*\/>/isU', $this->body, $matches)) {
            foreach ($matches[1] AS $path) {
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $file_name = basename($path);
                $this->body = str_replace($path, "cid:" . $file_name, $this->body);
                $images[] = array($path, $file_name);
            }
        }
        $this->imagesI = $images;

    }

    public function send(){
        $this->prepare();

        $htmlPart = new MimePart ( $this->body );
        $htmlPart->type = "text/html";

        $textPart = new MimePart ( $this->body );
        $textPart->type = "text/plain";

        $body = new MimeMessage ();
        $body->setParts ( array (
            $textPart,
            $htmlPart
        ) );

        foreach ( $this->imagesI as $image ) {
            $attachment = new MimePart ( file_get_contents ( $image [0] ) );
            $attachment->type = ZendMime::TYPE_OCTETSTREAM;
            $attachment->disposition = ZendMime::DISPOSITION_ATTACHMENT;
            $attachment->encoding = ZendMime::ENCODING_BASE64;
            $attachment->filename = $image [1];
            $attachment->id = $image [1];
            $body->addPart ( $attachment );
        }

        $message = new MailMessage ();
        $message->setFrom ( $this->fromemail, $this->fromname );
        foreach ($this->emails as $email){
            $message->addTo($email);
        }
        $message->setSubject ( $this->subject );

        $message->setEncoding("UTF-8");
        $message->setBody($body);
        $message->getHeaders()->get('content-type')->setType('multipart/alternative');

        $transport = new SendmailTransport();
        $transport->send($message);

    }


}

?>