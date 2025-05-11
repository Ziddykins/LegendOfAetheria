const btnGoldwithdraw = document.getElementById('gold-withdraw');
const btnGoldDeposit = document.getElementById('gold-deposit');
const btnSpindelswithdraw = document.getElementById('spindels-withdraw');
const btnSpindelsDeposit = document.getElementById('spindels-deposit');
const submits = [ btnGoldwithdraw, btnGoldDeposit, btnSpindelsDeposit, btnSpindelswithdraw ];

submits.forEach((btn) => {
    btn.addEventListener('click', (e) => {
        const valGoldInput  = document.getElementById('gold-amount');
        const valGoldOnHand = document.getElementById('gold-on-hand');
        const valGoldInBank = document.getElementById('banked-gold');

        const valSpindelsInput  = document.getElementById('spindels-amount');
        const valSpindelsOnHand = document.getElementById('spindels-on-hand');
        const valSpindelsInBank = document.getElementById('banked-spindels');


        e.preventDefault();
        e.stopPropagation();
        
        let which = null;
        let action = null;
        let amount = null;
        let onhand = null;
        let inbank = null;
        let eleUpdate = null;

        if (e.target.id.match(/gold/)) {
            which  = 'gold';
            amount = parseFloat(valGoldInput.value);
            onhand = parseFloat(valGoldOnHand.textContent);
            inbank = parseFloat(valGoldInBank.textContent);
        } else if (e.target.id.match(/spindel/)) {
            which  = 'spindels';
            amount = parseFloat(valSpindelsInput.value);
            onhand = parseFloat(valSpindelsOnHand.textContent);
            inbank = parseFloat(valSpindelsInBank.textContent);
        }

        if (e.target.id.match(/deposit/)) {
            action = 'deposit';
        } else if (e.target.id.match(/withdraw/)) {
            action = 'withdraw';
        }


        console.log(`which: ${which} - action: ${action} - amount: ${amount} - onhand: ${onhand} - inbank: ${inbank} - e.target.id: ${e.target.id} e.target.innerText: ${e.target.innerText}`);

        if (which && action && amount) {
            let message = `The amount of ${which} you've chosen to ${action} is invalid - Min: 1, Max: `;
            let error = 0;
            message += action === 'deposit' ? onhand : inbank;

            if (action === 'deposit' && (amount <= 0 || amount >= onhand)) {
                error = 1;
            } else if (action === 'withdraw' && (amount <= 0 || amount >= inbank)) {
                error = 1;
            }
        }

        let data = {
            target: 'bank',
            payload: {
                'which': which,
                'action': action,
                'amount': amount,
            }
        };

        return fetch('/fetch', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        }).then((response) => response.json())
        .then((reply) => {
            console.log(reply);
            if (reply.status === 'success') {
                action === 'withdraw' ? (action = 'withdrawn') : (action = 'deposited');
                ToastManager.create({ id: `success-${action}`, type: 'success', icon: 'bi-check', header: 'Success', message: `${amount} ${which} successfully ${action}` });

                if (which === 'gold') {
                    if (action === 'deposit') {
                        valGoldOnHand.textContent = onhand - amount;
                        valGoldInBank.textContent = inbank + amount;
                    } else if (action === 'withdraw') {
                        valGoldOnHand.textContent = onhand + amount;
                        valGoldInBank.textContent = inbank - amount;
                    }
                } else if (which === 'spindels') {
                    if (action === 'deposit') {
                        valSpindelsOnHand.textContent = onhand - amount;
                        valSpindelsInBank.textContent = inbank + amount;
                    } else if (action === 'withdraw') {
                        valSpindelsnHand.textContent = onhand + amount;
                        valSpindelsInBank.textContent = inbank - amount;
                    }
                }
            } else {
                ToastManager.create({ id: 'invalid-amount', type: 'danger', icon: 'bi-cash-coin', header: 'Invalid Amount', message: reply.message });
            }
            valGoldInput.value = '';
            valSpindelsInput.value = '';
        });
    });
});