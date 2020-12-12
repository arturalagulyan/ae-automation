<?php

namespace Api\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

/**
 * Class BaseApiMail
 * @package Api\Mail
 */
abstract class BaseApiMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    protected $data;

    /**
     * BaseApiMail constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->setTemplate()
            ->setSubject()
            ->setFrom()
            ->setCC()
            ->setBCC()
            ->setAttachments();
    }

    /**
     * @return $this
     */
    protected function setTemplate()
    {
        if (!empty($this->template)) {
            $this->view($this->template);
        }
        if (!empty($this->markdown)) {
            $this->markdown($this->markdown);
        }

        return $this->with($this->data);
    }

    /**
     * @return $this
     */
    protected function setCC()
    {
        $cc = Arr::get($this->data, 'cc');

        if (!empty($cc)) {
            if (!is_array($cc)) {
                $cc = explode(',', $cc);
            }

            $this->cc($cc);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function setBCC()
    {
        $bcc = Arr::get($this->data, 'bcc');

        if (!empty($bcc)) {
            if (!is_array($bcc)) {
                $bcc = explode(',', $bcc);
            }

            $this->bcc($bcc);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function setSubject()
    {
        $subject = Arr::get($this->data, 'subject');

        return $this->subject($subject);
    }

    /**
     * @return $this
     */
    protected function setFrom()
    {
        $name = Arr::get($this->data, 'name', config('mail.from.name'));
        $address = Arr::get($this->data, 'address', config('mail.from.address'));

        return $this->from($address, $name);
    }

    /**
     * @return $this
     */
    protected function setAttachments()
    {
        $files = Arr::get($this->data, 'files');

        if (empty($files)) {
            return $this;
        }

        foreach ($files as $file) {
            if (empty($file)) {
                continue;
            }

            $this->attach($file);
        }

        return $this;
    }
}
