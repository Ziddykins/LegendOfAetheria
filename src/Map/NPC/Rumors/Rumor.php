<?php
    namespace Game\Map\NPC\Rumors;

    class Rumor {
        private int $id;
        private string $content;
        private string $sourceNPC;
        private bool $isActive;

        /* Rumors have a chance to "campfire"; this transforms them into
           a rumor which is vastly different than the original */
        private bool $isCampfired;

        public function __construct(int $id, string $content, string $sourceNPC, array $relatedLocations = [], bool $isActive = true) {
            $this->id = $id;
            $this->content = $content;
            $this->sourceNPC = $sourceNPC;
            $this->isActive = $isActive;
            $this->isCampfired = false;
        }
    }