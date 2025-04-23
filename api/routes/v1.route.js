const express = require('express');
const v1 = require('../services/v1');
const router = new express.Router();
 
router.get('/account/:accountID', async (req, res, next) => {
  let options = { 
    "accountID": req.params.accountID,
    
  };


  try {
    const result = await v1.getAccountAccountID(options);
    res.status(result.status || 200).send(result.data);
  }
  catch (err) {
    return res.status(500).send({
      error: err || 'Something went wrong.'
    });
  }
});
 
router.post('/auth/refresh', async (req, res, next) => {
  let options = { 
  };


  try {
    const result = await v1.postAuthRefresh(options);
    res.status(result.status || 200).send(result.data);
  }
  catch (err) {
    return res.status(500).send({
      error: err || 'Something went wrong.'
    });
  }
});
 
router.get('/characters', async (req, res, next) => {
  let options = { 
  };


  try {
    const result = await v1.getCharacters(options);
    res.status(result.status || 200).send(result.data);
  }
  catch (err) {
    return res.status(500).send({
      error: err || 'Something went wrong.'
    });
  }
});
 
router.post('/characters', async (req, res, next) => {
  let options = { 
  };

  options.postCharactersInlineReqJson = req.body;

  try {
    const result = await v1.postCharacters(options);
    res.status(result.status || 200).send(result.data);
  }
  catch (err) {
    return res.status(500).send({
      error: err || 'Something went wrong.'
    });
  }
});
 
router.get('/characters/:characterId', async (req, res, next) => {
  let options = { 
    "characterId": req.params.characterId,
  };


  try {
    const result = await v1.getCharactersCharacterId(options);
    res.status(result.status || 200).send(result.data);
  }
  catch (err) {
    return res.status(500).send({
      error: err || 'Something went wrong.'
    });
  }
});
 
router.get('/characters/:characterId/bank', async (req, res, next) => {
  let options = { 
    "characterId": req.params.characterId,
  };


  try {
    const result = await v1.getCharactersCharacterIdBank(options);
    res.status(result.status || 200).send(result.data);
  }
  catch (err) {
    return res.status(500).send({
      error: err || 'Something went wrong.'
    });
  }
});
 
router.post('/characters/:characterId/bank', async (req, res, next) => {
  let options = { 
    "characterId": req.params.characterId,
  };

  options.postCharactersCharacterIdBankInlineReqJson = req.body;

  try {
    const result = await v1.postCharactersCharacterIdBank(options);
    res.status(result.status || 200).send(result.data);
  }
  catch (err) {
    return res.status(500).send({
      error: err || 'Something went wrong.'
    });
  }
});
 
router.post('/characters/:characterId/battle', async (req, res, next) => {
  let options = { 
    "characterId": req.params.characterId,
  };

  options.postCharactersCharacterIdBattleInlineReqJson = req.body;

  try {
    const result = await v1.postCharactersCharacterIdBattle(options);
    res.status(result.status || 200).send(result.data);
  }
  catch (err) {
    return res.status(500).send({
      error: err || 'Something went wrong.'
    });
  }
});
 
router.get('/characters/:characterId/familiar', async (req, res, next) => {
  let options = { 
    "characterId": req.params.characterId,
  };


  try {
    const result = await v1.getCharactersCharacterIdFamiliar(options);
    res.status(result.status || 200).send(result.data);
  }
  catch (err) {
    return res.status(500).send({
      error: err || 'Something went wrong.'
    });
  }
});
 
router.get('/characters/:characterId/friends', async (req, res, next) => {
  let options = { 
    "characterId": req.params.characterId,
    "status": req.query.status,
  };


  try {
    const result = await v1.getCharactersCharacterIdFriends(options);
    res.status(result.status || 200).send(result.data);
  }
  catch (err) {
    return res.status(500).send({
      error: err || 'Something went wrong.'
    });
  }
});
 
router.post('/characters/:characterId/friends', async (req, res, next) => {
  let options = { 
    "characterId": req.params.characterId,
  };

  options.postCharactersCharacterIdFriendsInlineReqJson = req.body;

  try {
    const result = await v1.postCharactersCharacterIdFriends(options);
    res.status(result.status || 200).send(result.data);
  }
  catch (err) {
    return res.status(500).send({
      error: err || 'Something went wrong.'
    });
  }
});
 
router.get('/characters/:characterId/inventory', async (req, res, next) => {
  let options = { 
    "characterId": req.params.characterId,
  };


  try {
    const result = await v1.getCharactersCharacterIdInventory(options);
    res.status(result.status || 200).send(result.data);
  }
  catch (err) {
    return res.status(500).send({
      error: err || 'Something went wrong.'
    });
  }
});
 
router.get('/characters/:characterId/quests', async (req, res, next) => {
  let options = { 
    "characterId": req.params.characterId,
    "status": req.query.status,
  };


  try {
    const result = await v1.getCharactersCharacterIdQuests(options);
    res.status(result.status || 200).send(result.data);
  }
  catch (err) {
    return res.status(500).send({
      error: err || 'Something went wrong.'
    });
  }
});
 
router.get('/locations', async (req, res, next) => {
  let options = { 
    "floor": req.query.floor,
  };


  try {
    const result = await v1.getLocations(options);
    res.status(result.status || 200).send(result.data);
  }
  catch (err) {
    return res.status(500).send({
      error: err || 'Something went wrong.'
    });
  }
});
 
router.post('/locations/:locationId/travel', async (req, res, next) => {
  let options = { 
    "locationId": req.params.locationId,
  };

  options.postLocationsLocationIdTravelInlineReqJson = req.body;

  try {
    const result = await v1.postLocationsLocationIdTravel(options);
    res.status(result.status || 200).send(result.data);
  }
  catch (err) {
    return res.status(500).send({
      error: err || 'Something went wrong.'
    });
  }
});
 
router.get('/mail', async (req, res, next) => {
  let options = { 
    "folder": req.query.folder,
    "limit": req.query.limit,
    "page": req.query.page,
  };


  try {
    const result = await v1.getMail(options);
    res.status(result.status || 200).send(result.data);
  }
  catch (err) {
    return res.status(500).send({
      error: err || 'Something went wrong.'
    });
  }
});
 
router.post('/mail', async (req, res, next) => {
  let options = { 
  };

  options.postMailInlineReqJson = req.body;

  try {
    const result = await v1.postMail(options);
    res.status(result.status || 200).send(result.data);
  }
  catch (err) {
    return res.status(500).send({
      error: err || 'Something went wrong.'
    });
  }
});
 
router.get('/mail/:mailId', async (req, res, next) => {
  let options = { 
    "mailId": req.params.mailId,
  };


  try {
    const result = await v1.getMailMailId(options);
    res.status(result.status || 200).send(result.data);
  }
  catch (err) {
    return res.status(500).send({
      error: err || 'Something went wrong.'
    });
  }
});
 
router.delete('/mail/:mailId', async (req, res, next) => {
  let options = { 
    "mailId": req.params.mailId,
  };


  try {
    const result = await v1.deleteMailMailId(options);
    res.status(result.status || 200).send(result.data);
  }
  catch (err) {
    return res.status(500).send({
      error: err || 'Something went wrong.'
    });
  }
});
 
router.get('/market/listings', async (req, res, next) => {
  let options = { 
    "maxPrice": req.query.maxPrice,
    "minLevel": req.query.minLevel,
    "rarity": req.query.rarity,
    "type": req.query.type,
  };


  try {
    const result = await v1.getMarketListings(options);
    res.status(result.status || 200).send(result.data);
  }
  catch (err) {
    return res.status(500).send({
      error: err || 'Something went wrong.'
    });
  }
});
 
router.post('/market/listings', async (req, res, next) => {
  let options = { 
  };

  options.postMarketListingsInlineReqJson = req.body;

  try {
    const result = await v1.postMarketListings(options);
    res.status(result.status || 200).send(result.data);
  }
  catch (err) {
    return res.status(500).send({
      error: err || 'Something went wrong.'
    });
  }
});
 
router.post('/market/listings/:listingId/purchase', async (req, res, next) => {
  let options = { 
    "listingId": req.params.listingId,
  };


  try {
    const result = await v1.postMarketListingsListingIdPurchase(options);
    res.status(result.status || 200).send(result.data);
  }
  catch (err) {
    return res.status(500).send({
      error: err || 'Something went wrong.'
    });
  }
});

module.exports = router;