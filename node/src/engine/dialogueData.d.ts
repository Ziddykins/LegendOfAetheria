export declare function getDialogueDefinitions(): {
    id: string;
    ownerId: string;
    startNodeId: string;
    entryNodeId: string;
    speakers: string[];
    nodes: {
        start: {
            text: string;
            choices: {
                text: string;
                nextNodeId: string;
                type: {
                    color: string;
                    icon: string;
                };
            }[];
        };
        clueless: {
            text: string;
            choices: {
                text: string;
                nextNodeId: string;
                type: {
                    color: string;
                    icon: string;
                };
            }[];
        };
        rude: {
            text: string;
            choices: {
                text: string;
                nextNodeId: string;
                type: {
                    color: string;
                    icon: string;
                };
            }[];
        };
        end_power: {
            text: string;
            effects: {
                type: string;
                targetId: string;
                stat: string;
                amount: number;
            }[];
            end: boolean;
        };
        bargain: {
            text: string;
            effects: {
                type: string;
                targetId: string;
                stat: string;
                amount: number;
            }[];
        };
    };
};
//# sourceMappingURL=dialogueData.d.ts.map