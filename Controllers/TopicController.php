<?php

require_once(__DIR__ . '/../Models/Topic.php');

class TopicController {
    private $topics = [];

    public function getTopics() {
        return $this->topics;
    }

    public function getTopic($name) {
        foreach ($this->getTopics() as $topic) {
            if ($topic->getName() === $name) {
                return $topic;
            }
        }

        $topic = $this->createTopic($name);

        array_push($this->topics, $topic);

        return $topic;
    }

    private function createTopic($name) {
        $topic = new Topic;

        $topic->setName($name);
        
        sleep(5); // seems to need time to process on Hubspots end

        return $topic->save();
    }
}