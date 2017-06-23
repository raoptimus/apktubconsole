<?php
namespace app\components\behaviors;

use app\components\tubeserver\v1\ActiveRecord;
use app\models\Journal;
use yii\base\Behavior;
use app\components\CustomEvents;
use yii\base\Event;

class consoleLogger extends Behavior
{
    public $actions;

    public function events()
    {
        return [
            CustomEvents::EVENT_VIDEO_DELETED => 'videoDeleted',
            CustomEvents::EVENT_VIDEO_UPDATED => 'videoUpdated',
            CustomEvents::EVENT_VIDEO_RAISED => 'videoRaised',
            CustomEvents::EVENT_VIDEO_PUBLISHED => 'videoPublished',
            CustomEvents::EVENT_VIDEO_CREATED => 'videoCreated',
            CustomEvents::EVENT_PUSH_UPDATED => 'pushUpdated',
            CustomEvents::EVENT_PUSH_CREATED => 'pushCreated',
            CustomEvents::EVENT_PUSH_STARTED => 'pushStarted',
            CustomEvents::EVENT_PUSH_STOPPED => 'pushStopped',
            CustomEvents::EVENT_VIDEO_FEATURED => 'videoFeatured',
            CustomEvents::EVENT_PUSH_DELETED => 'pushDeleted',
            CustomEvents::EVENT_PUSH_RESTORED => 'pushRestored',
        ];
    }

    public function videoDeleted(Event $event) {
        Journal::newEvent('Video', 'VideoDeleted',$event->sender->id,$event->sender->getAttributes());
    }
    public function videoFeatured(Event $event) {
        Journal::newEvent('Video', 'VideoUpdated',$event->sender->id,$event->sender->getAttributes());
    }
    public function videoUpdated(Event $event) {

        Journal::newEvent('Video', 'VideoUpdated',$event->sender->id,$event->sender->getAttributes());
    }
    public function videoRaised(Event $event) {
        Journal::newEvent('Video', 'VideoRaised',$event->sender->id,$event->sender->getAttributes());
    }
    public function videoPublished(Event $event) {
        Journal::newEvent('Video', 'VideoPublished',$event->sender->id,$event->sender->getAttributes());
    }
    public function videoCreated(Event $event) {
        Journal::newEvent('Video', 'VideoCreated',$event->sender->id,$event->sender->getAttributes());
    }
    public function pushUpdated(Event $event) {
        Journal::newEvent('Push', 'PushUpdated',$event->sender->id,$event->sender->getAttributes());
    }
    public function pushCreated(Event $event) {
        Journal::newEvent('Push', 'PushCreated',$event->sender->id,$event->sender->getAttributes());
    }
    public function pushStarted(Event $event) {
        Journal::newEvent('Push', 'PushStarted',$event->sender->id,$event->sender->getAttributes());
    }
    public function pushStopped(Event $event) {
        Journal::newEvent('Push', 'PushStopped',$event->sender->id,$event->sender->getAttributes());
    }
    public function pushDeleted(Event $event) {
        Journal::newEvent('Push', 'PushDeleted',$event->sender->id,$event->sender->getAttributes());
    }
    public function pushRestored(Event $event) {
        Journal::newEvent('Push', 'PushRestored',$event->sender->id,$event->sender->getAttributes());
    }




}