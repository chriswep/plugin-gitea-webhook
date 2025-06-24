<?php

namespace Kanboard\Plugin\GiteaWebhook;

use Kanboard\Core\Base;
use Kanboard\Event\GenericEvent;

/**
 * Gitea Webhook
 *
 * @author   Frederic Guillot (GogsWebhook)
 * @author   Chris Metz (GiteaWebhook)
 */
class WebhookHandler extends Base
{
    /**
     * Events
     *
     * @var string
     */
    const EVENT_COMMIT_REF = 'gitea.webhook.commit_ref';
    const EVENT_COMMIT_CLOSE = 'gitea.webhook.commit_close';

    /**
     * Project id
     *
     * @access private
     * @var integer
     */
    private $project_id = 0;

    /**
     * Set the project id
     *
     * @access public
     * @param  integer   $project_id   Project id
     */
    public function setProjectId($project_id)
    {
        $this->project_id = $project_id;
    }

    /**
     * Parse incoming events
     *
     * @access public
     * @param  string  $type      Gitea event type
     * @param  array   $payload   Gitea event
     * @return boolean
     */
    public function parsePayload($type, array $payload)
    {
        if ($type === 'push') {
            return $this->handlePush($payload);
        }

        return false;
    }

    /**
     * Parse push events
     *
     * @access public
     * @param  array   $payload
     * @return boolean
     */
    public function handlePush(array $payload)
    {
        $results = array();

        if (isset($payload['commits'])) {
            foreach ($payload['commits'] as $commit) {
                $results[] = $this->handleCommit($commit);
            }
        }

        return in_array(true, $results, true);
    }

    /**
     * Parse commit
     *
     * @access public
     * @param  array   $commit   Gitea commit
     * @return boolean
     */
    public function handleCommit(array $commit)
    {
        // $task_id = $this->taskModel->getTaskIdFromText($commit['message']);
        $re = '/(refs|closes|implements|fixes) #([0-9]*)/m';

        preg_match_all($re, $commit['message'], $matches, PREG_SET_ORDER, 0);

        foreach($matches as $taskRef) {
            $task_id = $taskRef[2];
            if (empty($task_id)) {
                return false;
            }

            $task = $this->taskFinderModel->getDetails($task_id);

            if (empty($task)) {
                return false;
            }

            if ($task['project_id'] != $this->project_id) {
                return false;
            }

            $action = $taskRef['1'];
            if(!in_array($action, array('refs', 'closes', 'implements', 'fixes'))) {
                return false;
            }
            
            $event = ($action === 'refs' ? self::EVENT_COMMIT_REF : self::EVENT_COMMIT_CLOSE);
            $user = $this->userModel->getByEmail($commit['author']['email']);

            $this->dispatcher->dispatch(
                new GenericEvent(array(
                    'task_id' => $task_id,
                    'task' => $task,
                    'user_id' => $user['id'],
                    'commit_message' => $commit['message'],
                    'commit_url' => $commit['url'],
                    'comment' => "[".t('%s committed on Gitea', $commit['author']['name'] ?: $commit['author']['username']).']('.$commit['url'].'): '.trim($commit['message']),
                ) + $task),
                $event
            );
        }

        return true;
    }
}
