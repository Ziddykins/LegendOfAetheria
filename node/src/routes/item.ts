import express from 'express';
import { promises as fs } from 'fs';
import type { Request, Response } from 'express';
import type { Engine } from '@ai-rpg-engine/core';
import { saveEngine } from '../utils/save.js';

export default function itemRoutes(engine: Engine) {
	const router = express.Router();

	router.get('/item/:itemType/:itemId', async (req: Request, res: Response) => {
		const itemType = req.params.itemType;
		const itemId   = parseInt(req.params.itemId as string);

		console.log(`itemType: ${itemType}\nitemId: ${itemId}\n`);
		const schemaFile = 'src/schema/items.json';
		const itemsSchema = await fs.readFile(schemaFile, 'utf8');
		const cat_items = JSON.parse(itemsSchema).Items[itemType as string];
		let item;

		for (let i of cat_items) {
			if (i.itemId == itemId) {
				item = i;
			}
		}

		if (!item) {
			res.status(400).json({error: "item not found"});
		} else {
			res.json(item);
		}

		try {
			console.log(JSON.stringify(item));
			saveEngine(engine);
		} catch (err: unknown) {
			res.status(500).json({ error: err instanceof Error ? err.message : String(err) });
		}
	});

	return router;
}
