<?php

namespace pxlrbt\Wordpress\Notifier;


/**
 * Notification helper class for wordpress.
 * Every notification is only shown once and deleted afterwards.
 */
class Notifier
{
    private static $counter = 0;

    protected $title;
    protected $transientName;

    protected $notifications;



    public function __construct($title)
    {
        $this->title = $title;
        $this->notifications = [];
        $this->transientName = "notifications_" . self::$counter++;

        $this->init();
    }



    /**
     * Load notifications
     * Hook into wordpress
     */
    private function init()
    {
        $notifications = get_transient($this->transientName);

        if ($notifications !== false) {
            $this->notifications = $notifications;
        }

        add_action('admin_notices', [$this, 'printNotifications']);
    }



    public function error($msg)
    {
        $this->notify($msg, 'error');
    }



    public function warning($msg)
    {
        $this->notify($msg, 'warning');
    }



    public function info($msg)
    {
        $this->notify($msg, 'info');
    }



    public function success($msg)
    {
        $this->notify($msg, 'success');
    }



    /**
     * Add an message
     */
    public function notify($msg, $type = 'error')
    {
        $notification = new \stdClass;
        $notification->type = $type;
        $notification->body = $msg;
        $notification->dismissible = true;

        $this->notifications[] = $notification;
        set_transient($this->transientName, $this->notifications);
    }



    /**
     * Print admin notifications
     */
    public function printNotifications()
    {
        $message = "";
        $notifications = get_transient($this->transientName);

        if (empty($notifications)) {
            return;
        }

        foreach ($notifications as $n) {
            $this->printNotification($n->type, $n->body);
        }

        delete_transient($this->transientName);
        // echo $message;
    }



    public function printNotification($type, $message)
    {
        echo '<div class="notice notice-' . $type . ' is-dismissible">'
            . '<p>'
            . ($this->title !== '' ? '<strong>' . $this->title . '</strong> ' : '')
            . $message
            . '</p></div>';
    }
}
