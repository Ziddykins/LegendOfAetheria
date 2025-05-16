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
        // Define tutorial steps
        $steps = [
            [
                'npcImage' => '/img/tutorial/frank-intro.png',
                'focusSelector' => '#main-content',
                'message' => "Hey there, I'm Frank! Welcome to the world of LOA. Don't worry, I only bite when debugging. Let's get started! (Step 1/5)",
                'clickFirst' => false,
                'left' => '20%',
                'top' => '80%'
            ],
            [
                'npcImage' => '/img/tutorial/frank-pointing.png',
                'focusSelector' => '#sidebar',
                'message' => "This is your main menu. It's like a Swiss Army knife, but with fewer sharp edges. (Step 2/5)",
                'clickFirst' => false,
            ],
            [
                'npcImage'      => '/img/tutorial/frank-pointing.png',
                'focusSelector' => '#character-anchor',
                'message' => "Here's your character panel. Looking good! (Step 3/5) Did you know I used to be an adventurer like you, until I took a semicolon to the knee?",
                'clickFirst' => true,
            ],
            [
                'npcImage'      => '/img/tutorial/frank-pointing.png',
                'focusSelector' => '#hunt-anchor',
                'message' => "Ready for battle? Click here to fight powerful monsters, collect the rarest loot, and progress through the ranks. (Step 4/5)",
                'clickFirst'    => true,
            ],
            [
                'npcImage'      => '/img/tutorial/frank-pointing.png',
                'focusSelector' => '#mail-anchor',
                'message' => "Check your mail here. I promise, no spam. Unless you count my jokes. (Step 5/5) Good luck, hero!",
                'clickFirst'    => true,
            ],
        ];

        // Return as JSON for frontend to consume
        return json_encode(['steps' => $steps]);
    }

}