<?php defined('SYSPATH') or die('No direct script access.');

class Redmine
{
    const ISSUE_PROJECT_FEEDBACK = 13;

    const ISSUE_PRIORITY_NORMAL = 2;

    const ISSUE_STATUS_NEW = 1;

    const ISSUE_TRACKER_SUPPORT = 3;

    private $_client = null;

    public function __construct()
    {
        $config = Kohana::$config->load('config');

        $this->_client = new Redmine\Client('http://cp.webwalks.ru', $config['redmine_key']);
    }

    /**
     * создаем задачку
     *
     * @param $subject
     * @param $description
     * @param $attachments
     * @return bool
     */
    public function createIssue($subject, $description, $attachments = [])
    {
        $issue = $this->_client->issue->create([
            'subject'       => $subject,
            'description'   => $description,
            'project_id'    => self::ISSUE_PROJECT_FEEDBACK,
            'priority_id'   => self::ISSUE_PRIORITY_NORMAL,
            'status_id'     => self::ISSUE_STATUS_NEW,
            'tracker_id'    => self::ISSUE_TRACKER_SUPPORT,
        ]);

        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {

                $filename = '.' . $attachment['file'];

                if (is_file($filename)) {
                    $file = file_get_contents($filename);
                    $contentType = mime_content_type($filename);

                    $upload = json_decode($this->_client->attachment->upload($file));
                    $this->_client->issue->attach($issue->id, [
                        'token' => $upload->upload->token,
                        'filename' => $attachment['name'],
                        'content_type' => $contentType,
                    ]);
                }
            }
        }

        return !empty($issue->id) ? (string)$issue->id : (!empty($issue->error) ? (string)$issue->error : false);
    }
}