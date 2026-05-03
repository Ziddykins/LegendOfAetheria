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
                id: string;
                speaker: string;
                text: string;
                choices: {
                    id: string;
                    text: string;
                    nextNodeId: string;
                    type: {
                        color: string;
                        icon: string;
                    };
                }[];
            };
            clueless: {
                id: string;
                speaker: string;
                text: string;
                choices: {
                    id: string;
                    text: string;
                    nextNodeId: string;
                    type: {
                        color: string;
                        icon: string;
                    };
                }[];
            };
            rude: {
                id: string;
                speaker: string;
                text: string;
                choices: {
                    id: string;
                    text: string;
                    nextNodeId: string;
                    type: {
                        color: string;
                        icon: string;
                    };
                }[];
            };
            end_power: {
                id: string;
                speaker: string;
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
                id: string;
                speaker: string;
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
};
export declare function createGameEngine(): Promise<Engine>;
//# sourceMappingURL=engine.d.ts.map