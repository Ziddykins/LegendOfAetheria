export default {
  /**
  * Retrieve an account by its ID.
  * @param options.accountID The ID of the account to retrieve. 

  */
    getAccountAccountID: async (options) => {
        let sql = "SELECT * FROM tbl_accounts WHERE `email` = ?";
        const con = mysql.createConnection({
            host: process.env.SQLHOST,
            user: process.env.SQLUSER,
            password: process.env.SQLPASS,
            database: process.env.SQLDB
        });

                await new Promise((resolve, reject) => {
            con.connect(err => {
                if (err) reject(err);
                resolve();
            });
        });
        con.query(sql, [email], (err, results, fields) => {

        });

        var data = {
            "banned": "<boolean>",
            "charSlot1": "<string>",
            "charSlot2": "<string>",
            "charSlot3": "<string>",
            "credits": "<integer>",
            "dateRegistered": "<date-time>",
            "eggsOwned": "<integer>",
            "eggsSeen": "<integer>",
            "email": "<string>",
            "failedLogins": "<integer>",
            "focusedSlot": "<string>",
            "id": "<integer>",
            "ipAddress": "<string>",
            "ipLock": "<boolean>",
            "ipLockAddr": "<string>",
            "lastLogin": "<date-time>",
            "loggedIn": "<boolean>",
            "loggedOn": "<boolean>",
            "muted": "<boolean>",
            "password": "<string>",
            "privileges": "<string>",
            "sessionID": "<string>",
            "settings": "<object>",
            "verificationCode": "<string>",
            "verified": "<boolean>",
        },
        status = '200';

        return {
            status: status,
            data: data
        };  
    },

  /**
  * Get a new JWT token using an existing valid token  */
  postAuthRefresh: async (options) => {
    var data = {
        "expires": "<date-time>",
        "token": "<string>",
      },
      status = '200';

    return {
      status: status,
      data: data
    };  
  },

  /**
  * Get all characters for the authenticated account  */
  getCharacters: async (options) => {
    var data = [{
        "accountID": "<integer>",
        "alignment": "<integer>",
        "avatar": "<string>",
        "dateCreated": "<date-time>",
        "description": "<string>",
        "exp": "<integer>",
        "floor": "<integer>",
        "gold": "<integer>",
        "id": "<integer>",
        "inventory": "<Inventory>",
        "lastAction": "<date-time>",
        "level": "<integer>",
        "location": "<string>",
        "monster": "<Monster>",
        "name": "<string>",
        "race": "<string>",
        "stats": "<Stats>",
        "x": "<integer>",
        "y": "<integer>",
      }],
      status = '200';

    return {
      status: status,
      data: data
    };  
  },

  /**
  * Create a new character

  * @param options.postCharactersInlineReqJson.def
  * @param options.postCharactersInlineReqJson.int
  * @param options.postCharactersInlineReqJson.name requiredCharacter name
  * @param options.postCharactersInlineReqJson.race requiredAvailable character races
  * @param options.postCharactersInlineReqJson.str

  */
  postCharacters: async (options) => {
    var data = {
        "accountID": "<integer>",
        "alignment": "<integer>",
        "avatar": "<string>",
        "dateCreated": "<date-time>",
        "description": "<string>",
        "exp": "<integer>",
        "floor": "<integer>",
        "gold": "<integer>",
        "id": "<integer>",
        "inventory": "<Inventory>",
        "lastAction": "<date-time>",
        "level": "<integer>",
        "location": "<string>",
        "monster": "<Monster>",
        "name": "<string>",
        "race": "<string>",
        "stats": "<Stats>",
        "x": "<integer>",
        "y": "<integer>",
      },
      status = '201';

    return {
      status: status,
      data: data
    };  
  },

  /**
  * Get character details by ID
  * @param options.characterId  

  */
  getCharactersCharacterId: async (options) => {
    var data = {
        "accountID": "<integer>",
        "alignment": "<integer>",
        "avatar": "<string>",
        "dateCreated": "<date-time>",
        "description": "<string>",
        "exp": "<integer>",
        "floor": "<integer>",
        "gold": "<integer>",
        "id": "<integer>",
        "inventory": "<Inventory>",
        "lastAction": "<date-time>",
        "level": "<integer>",
        "location": "<string>",
        "monster": "<Monster>",
        "name": "<string>",
        "race": "<string>",
        "stats": "<Stats>",
        "x": "<integer>",
        "y": "<integer>",
      },
      status = '200';

    return {
      status: status,
      data: data
    };  
  },

  /**
  * Get character&apos;s bank account details
  * @param options.characterId  

  */
  getCharactersCharacterIdBank: async (options) => {

                                
                
    var data = {
        "balance": "<integer>",
        "characterId": "<integer>",
        "transactions": "<array>",
      },
      status = '200';

    return {
      status: status,
      data: data
    };  
  },

  /**
  * Deposit or withdraw gold from bank
  * @param options.characterId  
  * @param options.postCharactersCharacterIdBankInlineReqJson.action requiredType of transaction
  * @param options.postCharactersCharacterIdBankInlineReqJson.amount requiredAmount of gold

  */
  postCharactersCharacterIdBank: async (options) => {

                                
                
    var data = {
        "balance": "<integer>",
        "characterId": "<integer>",
        "transactions": "<array>",
      },
      status = '200';

    return {
      status: status,
      data: data
    };  
  },

  /**
  * Initiate a battle with a monster
  * @param options.characterId  
  * @param options.postCharactersCharacterIdBattleInlineReqJson.monsterId requiredID of the monster to battle
  * @param options.postCharactersCharacterIdBattleInlineReqJson.scopeScope of monster visibility and interaction:
- GLOBAL: Available for everyone to attack with shared rewards
- ZONE: Restricted to specific map areas with local player contribution
- PERSONAL: Only visible and attackable by individual player
- NONE: No specific scope assigned

  */
  postCharactersCharacterIdBattle: async (options) => {

                                
                
    var data = {
        "enemy": "<Monster>",
        "player": "<Character>",
        "turn": "<Turn>",
      },
      status = '200';

    return {
      status: status,
      data: data
    };  
  },

  /**
  * Get the familiar associated with a character
  * @param options.characterId  

  */
  getCharactersCharacterIdFamiliar: async (options) => {

                                
                
    var data = {
        "avatar": "<string>",
        "characterID": "<integer>",
        "experience": "<integer>",
        "id": "<integer>",
        "level": "<integer>",
        "name": "<string>",
        "nextLevel": "<integer>",
        "stats": "<FamiliarStats>",
      },
      status = '200';

    return {
      status: status,
      data: data
    };  
  },

  /**
  * Get list of friends for a character
  * @param options.characterId    * @param options.status Filter by friend status 

  */
  getCharactersCharacterIdFriends: async (options) => {

                                
                
    var data = {
        "friendId": "<integer>",
        "name": "<string>",
        "online": "<boolean>",
        "status": "<FriendStatus>",
      },
      status = '200';

    return {
      status: status,
      data: data
    };  
  },

  /**
  * Send a friend request to another character
  * @param options.characterId  
  * @param options.postCharactersCharacterIdFriendsInlineReqJson.targetCharacterId requiredID of the character to send friend request to

  */
  postCharactersCharacterIdFriends: async (options) => {

                                
                
    var data = {},
      status = '201';

    return {
      status: status,
      data: data
    };  
  },

  /**
  * Get the inventory for a character
  * @param options.characterId  

  */
  getCharactersCharacterIdInventory: async (options) => {

                                
                
    var data = {
        "currentWeight": "<integer>",
        "id": "<integer>",
        "maxWeight": "<integer>",
        "nextAvailableSlot": "<integer>",
        "slotCount": "<integer>",
        "slots": "<array>",
      },
      status = '200';

    return {
      status: status,
      data: data
    };  
  },

  /**
  * Get active and completed quests for a character
  * @param options.characterId    * @param options.status  

  */
  getCharactersCharacterIdQuests: async (options) => {

                                
                
    var data = [{
        "description": "<string>",
        "id": "<integer>",
        "progress": "<object>",
        "requirements": "<object>",
        "rewards": "<object>",
        "status": "<string>",
        "title": "<string>",
      }],
      status = '200';

    return {
      status: status,
      data: data
    };  
  },

  /**
  * Get list of available locations/maps
  * @param options.floor  

  */
  getLocations: async (options) => {

                                
                
    var data = [{
        "connectedLocations": "<array>",
        "coordinates": "<object>",
        "description": "<string>",
        "floor": "<integer>",
        "id": "<integer>",
        "monsters": "<array>",
        "name": "<string>",
        "npcs": "<array>",
        "requirements": "<object>",
        "weather": "<string>",
      }],
      status = '200';

    return {
      status: status,
      data: data
    };  
  },

  /**
  * Move character to a new location
  * @param options.locationId  
  * @param options.postLocationsLocationIdTravelInlineReqJson.characterId requiredID of character traveling

  */
  postLocationsLocationIdTravel: async (options) => {

                                
                
    var data = {
        "connectedLocations": "<array>",
        "coordinates": "<object>",
        "description": "<string>",
        "floor": "<integer>",
        "id": "<integer>",
        "monsters": "<array>",
        "name": "<string>",
        "npcs": "<array>",
        "requirements": "<object>",
        "weather": "<string>",
      },
      status = '200';

    return {
      status: status,
      data: data
    };  
  },

  /**
  * Get mail messages for the authenticated account
  * @param options.folder    * @param options.limit    * @param options.page  

  */
  getMail: async (options) => {

                                
                
    var data = [{
        "date": "<date-time>",
        "folder": "<FolderType>",
        "mail_id": "<integer>",
        "message": "<string>",
        "recipient": "<string>",
        "sender": "<string>",
        "status": "<EnvelopeStatus>",
        "subject": "<string>",
      }],
      status = '200';

    return {
      status: status,
      data: data
    };  
  },

  /**
  * Send a new mail message

  * @param options.postMailInlineReqJson.important
  * @param options.postMailInlineReqJson.message required
  * @param options.postMailInlineReqJson.recipient requiredRecipient&apos;s email
  * @param options.postMailInlineReqJson.subject required

  */
  postMail: async (options) => {

                                
                
    var data = {
        "date": "<date-time>",
        "folder": "<FolderType>",
        "mail_id": "<integer>",
        "message": "<string>",
        "recipient": "<string>",
        "sender": "<string>",
        "status": "<EnvelopeStatus>",
        "subject": "<string>",
      },
      status = '201';

    return {
      status: status,
      data: data
    };  
  },

  /**
  * Get a specific mail message by ID
  * @param options.mailId  

  */
  getMailMailId: async (options) => {

                                
                
    var data = {
        "date": "<date-time>",
        "folder": "<FolderType>",
        "mail_id": "<integer>",
        "message": "<string>",
        "recipient": "<string>",
        "sender": "<string>",
        "status": "<EnvelopeStatus>",
        "subject": "<string>",
      },
      status = '200';

    return {
      status: status,
      data: data
    };  
  },

  /**
  * Move a mail message to the deleted folder
  * @param options.mailId  

  */
  deleteMailMailId: async (options) => {

                                
                
    var data = {},
      status = '204';

    return {
      status: status,
      data: data
    };  
  },

  /**
  * Get current market listings with optional filters
  * @param options.maxPrice    * @param options.minLevel    * @param options.rarity    * @param options.type  

  */
  getMarketListings: async (options) => {

                                
                
    var data = {
        "listings": "<array>",
        "total": "<integer>",
      },
      status = '200';

    return {
      status: status,
      data: data
    };  
  },

  /**
  * Create a new market listing

  * @param options.postMarketListingsInlineReqJson.durationListing duration in hours
  * @param options.postMarketListingsInlineReqJson.itemId requiredID of item to sell
  * @param options.postMarketListingsInlineReqJson.price requiredAsking price in gold

  */
  postMarketListings: async (options) => {

                                
                
    var data = {
        "expires": "<date-time>",
        "id": "<integer>",
        "item": "<Item>",
        "listed": "<date-time>",
        "price": "<integer>",
        "sellerId": "<integer>",
      },
      status = '201';

    return {
      status: status,
      data: data
    };  
  },

  /**
  * Purchase an item from the market
  * @param options.listingId  

  */
  postMarketListingsListingIdPurchase: async (options) => {

                                
                
    var data = {
        "buyer": "<string>",
        "item": "<Item>",
        "price": "<integer>",
        "seller": "<string>",
        "timestamp": "<date-time>",
        "transactionId": "<integer>",
      },
      status = '200';

    return {
      status: status,
      data: data
    };  
  },
};
