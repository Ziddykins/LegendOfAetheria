<?php

namespace Game\OpenAI\NPC\Tutorial;

class Frank {
    private $accountID;
    private $characterID;
    private $tutorialStep;

    public function __construct($accountID, $characterID) {
        $this->accountID = $accountID;
        $this->characterID = $characterID;
        $this->tutorialStep = 0;
    }

    /**
     * Generates an interactive tutorial based on a list of links.
     *
     * @param array $links An array of URLs.
     * @return string The formatted interactive tutorial.
     */
    public function renderInteractiveTutorial() {
    }

}