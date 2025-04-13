<?php

namespace OpenAI\NPC\Tutorial\Frank;

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
    public function generateTutorial(array $links): string {
        $categories = [];

        // Group links by their 'sub' parameter
        foreach ($links as $link) {
            $parsedUrl = parse_url($link);
            parse_str($parsedUrl['query'], $queryParams);
            $sub = $queryParams['sub'] ?? 'Uncategorized';
            $categories[$sub][] = $link;
        }

        $tutorial = "<div id='tutorial-container' style='font-family: Arial, sans-serif; line-height: 1.6;'>\n";
        $tutorial .= "<h1 style='color: #333;'>Interactive Tutorial</h1>\n";

        $tutorial .= "<div id='sections'>\n";
        foreach ($categories as $category => $links) {
            $tutorial .= "<div class='section' data-category='$category' style='display: none;'>\n";
            $tutorial .= "<h2 style='color: #555; text-transform: capitalize;'>$category</h2>\n";

            foreach ($links as $link) {
                $parsedUrl = parse_url($link);
                parse_str($parsedUrl['query'], $queryParams);
                $title = ucfirst($queryParams['page'] ?? 'Unknown Page');
                $tutorial .= "<div style='margin-bottom: 10px;'>\n";
                $tutorial .= "<p style='color: #666;'>Learn more about <strong>$title</strong>:</p>\n";
                $tutorial .= "<a href='$link' style='color: #1a73e8; text-decoration: none;'>Read More</a>\n";
                $tutorial .= "</div>\n";
            }

            $tutorial .= "</div>\n";
        }
        $tutorial .= "</div>\n";

        // Add navigation buttons
        $tutorial .= "<div id='navigation' style='margin-top: 20px;'>\n";
        $tutorial .= "<button id='prev-btn' style='display: none; padding: 10px 20px; margin-right: 10px;'>Previous</button>\n";
        $tutorial .= "<button id='next-btn' style='padding: 10px 20px;'>Next</button>\n";
        $tutorial .= "</div>\n";

        // Add JavaScript for interactivity
        $tutorial .= "<script>
            (function() {
                const sections = document.querySelectorAll('.section');
                const prevBtn = document.getElementById('prev-btn');
                const nextBtn = document.getElementById('next-btn');
                let currentIndex = 0;

                function updateSections() {
                    sections.forEach((section, index) => {
                        section.style.display = index === currentIndex ? 'block' : 'none';
                    });
                    prevBtn.style.display = currentIndex > 0 ? 'inline-block' : 'none';
                    nextBtn.style.display = currentIndex < sections.length - 1 ? 'inline-block' : 'none';
                }

                prevBtn.addEventListener('click', () => {
                    if (currentIndex > 0) {
                        currentIndex--;
                        updateSections();
                    }
                });

                nextBtn.addEventListener('click', () => {
                    if (currentIndex < sections.length - 1) {
                        currentIndex++;
                        updateSections();
                    }
                });

                updateSections();
            })();
        </script>\n";

        $tutorial .= "</div>\n";
        return $tutorial;
    }
}