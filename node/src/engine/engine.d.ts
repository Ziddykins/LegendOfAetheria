import { Engine } from '@ai-rpg-engine/core';
export declare function getDialogueMap(): {
    [k: string]: {
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
                    itemId: number;
                    itemType: string;
                }[];
                end: boolean;
            };
            bargain: {
                text: string;
                effects: {
                    type: string;
                    targetId: string;
                    itemId: number;
                    itemType: string;
                }[];
                end: boolean;
            };
        };
    };
};
export declare function createGameEngine(): Promise<Engine>;
//# sourceMappingURL=engine.d.ts.map