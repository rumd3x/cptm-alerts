<?php
namespace CptmAlerts\Classes;

class SlackNotification
{
    public $text;
    public $channel;
    public $username;
    public $attachments;

    /**
     * @param string $text
     */
    public function __construct(string $text)
    {
        $this->text = $text;
        $this->channel = getenv('SLACK_CHANNEL');
        $this->username = getenv('SLACK_BOT_USERNAME');
        $this->attachments = collect();
    }

    /**
     * @param Attachment $attachment
     * @return self
     */
    public function attach(Attachment $attachment)
    {
        $this->attachments->push($attachment);
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = [
            'text' => $this->text,
            'channel' => $this->channel,
        ];

        if (!$this->attachments->isEmpty()) {
            $array['attachments'] = json_encode(
                $this->attachments->map(function ($item) {
                    return $item->toArray();
                })->all()
            );
        }

        return $array;
    }
}
