const express = require('express');
const { createGameEngine, getDialogueMap, saveEngine, getItemById } = require('../services/loa');

module.exports = async function createLoaRouter() {
  const engine = await createGameEngine();
  const dialogueMap = getDialogueMap();
  const router = express.Router();

  router.use((req, res, next) => {
    console.debug(`===REQ===\nURL: ${req.originalUrl}\n${JSON.stringify(req.body)}\n===`);
    next();
  });

  router.post('/talk/:npcId/:nodeId?', async (req, res) => {
    const npcId = req.params.npcId;
    const nodeId = req.params.nodeId;

    const dialogue = Object.values(dialogueMap).find(d => d.ownerId === npcId);
    if (!dialogue) {
      return res.json({ text: 'NPC has nothing to say.' });
    }

    if (typeof nodeId !== 'undefined' && nodeId !== 'reset') {
      dialogue.startNodeId = nodeId;
    }

    engine.store.state.dialogueCore = {
      activeDialogueId: dialogue.id,
      activeNodeId: dialogue.startNodeId,
      npcId,
    };

    const node = dialogue.nodes[dialogue.startNodeId];
    res.json({ text: node.text, choices: node.choices || [] });
  });

  router.post('/choice', async (req, res) => {
    const { choiceIndex } = req.body;
    const state = engine.store.state;
    const ds = state.dialogueCore;

    if (!ds) {
      return res.json({ error: 'No active dialogue' });
    }

    const dialogue = dialogueMap[ds.activeDialogueId];
    const node = dialogue.nodes[ds.activeNodeId];
    const choice = node.choices?.[choiceIndex];

    if (!choice) {
      return res.json({ error: 'Invalid choice' });
    }

    if (choice.effects) {
      for (const effect of choice.effects) {
        if (effect.type === 'modifyStat') {
          const target = state.entities[effect.targetId];
          if (target) {
            target.stats[effect.stat] += effect.amount;
          }
        }
      }
    }

    ds.activeNodeId = choice.nextNodeId;
    const nextNode = dialogue.nodes[ds.activeNodeId];

    if (nextNode?.end) {
      state.dialogueCore = null;
    }

    await saveEngine(engine);
    res.json({
      text: nextNode?.text || '',
      choices: nextNode?.choices || [],
      effects: nextNode?.effects || [],
      end: !!nextNode?.end,
    });
  });

  router.post('/attack/:targetId', async (req, res) => {
    try {
      engine.submitAction('attack', { targetIds: [req.params.targetId] });
      await saveEngine(engine);
      res.json(engine.store.state);
    } catch (err) {
      res.status(500).json({ error: err instanceof Error ? err.message : String(err) });
    }
  });

  router.get('/item/:itemType/:itemId', async (req, res) => {
    try {
      const item = getItemById(req.params.itemType, parseInt(req.params.itemId, 10));
      if (!item) {
        return res.status(400).json({ error: 'item not found' });
      }
      await saveEngine(engine);
      res.json(item);
    } catch (err) {
      res.status(500).json({ error: err instanceof Error ? err.message : String(err) });
    }
  });

  router.post('/state', async (req, res) => {
    try {
      res.json(engine.store.state);
    } catch (err) {
      res.status(500).json({ error: err instanceof Error ? err.message : String(err) });
    }
  });

  return router;
};