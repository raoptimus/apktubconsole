<?php
namespace app\components;

use yii\base\Event;

class CustomEvents extends Event{
    const EVENT_VIDEO_DELETED = 'video_has_been_deleted';
    const EVENT_VIDEO_UPDATED = 'video_has_been_updated';
    const EVENT_VIDEO_RAISED = 'video_has_been_raised';
    const EVENT_VIDEO_PUBLISHED = 'video_has_been_published';
    const EVENT_VIDEO_CREATED = 'video_has_been_created';
    const EVENT_VIDEO_FEATURED = 'video_has_been_featured';

    const EVENT_PUSH_UPDATED = 'push_has_been_updated';
    const EVENT_PUSH_CREATED = 'push_has_been_created';
    const EVENT_PUSH_STARTED = 'push_has_been_started';
    const EVENT_PUSH_STOPPED = 'push_has_been_stopped';
    const EVENT_PUSH_DELETED = 'push_has_been_deleted';
    const EVENT_PUSH_RESTORED = 'push_has_been_restored';
}