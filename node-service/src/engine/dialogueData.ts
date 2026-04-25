export function getDialogueDefinitions() {
	let def = {
		id: 'sage_intro',
		ownerId: 'question-sage',
		startNodeId: 'start',
		entryNodeId: 'start',
		speakers: ['Question Sage'],


		nodes: {
			start: {
				text: "Welcome. You come seeking quests. Little do you know, you've been on one the minute you walked through that door.",
				choices: [
					{
						text: '...huh?',
						nextNodeId: 'clueless',
						type: {
							color: 'primary',
							icon: 'emoji-astonished-fill'
						}
					},
					{
						text: 'Shut it, old man! Out with the quests or die.',
						nextNodeId: 'rude',
						type: {
							color: 'danger',
							icon: 'emoji-angry-fill'
						}
					},
				],
			},

			clueless: {
				text: 'Oh, nothing. Here, drink this quest-enabling potion.',
				choices: [
					{
						text: 'You got it, sport-o!',
						nextNodeId: 'end_power',
						type: {
							color: 'success',
							icon: 'emoji-sunglasses-fill'
						}
					},
					{
						text: "I ain't quaffin' a thing, later creep-o",
						nextNodeId: 'reverse_1',
						type: {
							color: 'warning',
							icon: 'emoji-neutral-fill'
						}
					},
				],
			},

			rude: {
				text: 'You fool... You foolish FOOL! You know what? I don\'t even wanna give you the only potion in the world that enables quests. Go live your questless life without quests, there, No-Quests. I\'ll just be over here, on a quest, questin\' it up.',
				choices: [
					{
						text: 'Wait, the only one? Gimme it or die, old man!',
						nextNodeId: 'reverse_1',
						type: {
							color: 'warning',
							icon: 'bi-award-fill'
						}
					},
					{
						text: 'Well, can you sweeten the deal. Toss in another potuon maybe?',
						nextNodeId: 'bargain',
						type: {
							color: 'success',
							icon: 'bi-flask-florence-fill'
						}
					}
				]
			},

			end_power: {
				text: 'Good, good. Now we play the waiting game... +2 STR.',
				effects: [
					{
						type: 'modifyStat',
						targetId: 'hero',
						stat: 'strength',
						amount: 2,
					},
				],
				end: true,
			},

			bargain: {
				text: 'Hrmph... Let me check the back room. Ah, yes, fine. Here.',
				effects: [
					{
						type: 'modifyStat',
						targetId: 'hero',
						stat: 'might',
						amount: 2,
					},
				],
			},
		},
	}

	return def;
}
