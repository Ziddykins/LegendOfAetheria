document.querySelectorAll("a[id^='env-id-']").forEach((envelope) => {
    envelope.addEventListener('dblclick', (e) => {
        let id   = envelope.id.split('-')[2];
        let sub   = document.getElementById(`env-sub-${id}`).innerText;
        let from  = document.getElementById(`env-from-${id}`).innerText;
        let to    = loa.u_email;
        let frag  = document.getElementById(`env-frag-${id}`).innerText;
        let edate = document.getElementById(`env-date-${id}`).innerText;

        let env = {
            id: id,
            sub: sub,
            from: from,
            to: to,
            frag: frag,
            edate: edate
        };

        generate_mail_modal(env);

        console.log(document.getElementById(`${id}-modal`));
    });
});

function close_click() {
    document.querySelectorAll("*[id$='field']").forEach((ele) => {
        ele.value = "";
    });

    document.getElementById("list-mail-inbox").click();
}

function send_click() {
    let recipient_field = document.getElementById("to-field");
    let subject_field   = document.getElementById("subject-field");
    let message_field   = document.getElementById("message-field");
    let important_field = document.getElementById("important-field");

    let message_obj = {
        to: recipient_field.value,
        subject: subject_field.value,
        message: message_field.value,
        important: important_field.checked,
        s_aid: loa.u_aid,
        s_cid: loa.u_cid,
        s_sid: loa.u_sid,
        s_csrf: loa.u_csrf
    };

    let msg_str =  JSON.stringify(message_obj);

    fetch('/mail', {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        method: 'POST',
        body: msg_str
    })
    .then((response) => {
        return response.json();
    }).then((data) => {
        document.getElementById("status").textContent = `Status: ${data.mail_status}`;
    }).catch((error) => {
        document.getElementById("status").textContent = `Status: ${error.mail_status}`;
    });

    recipient_field.value = "";
    subject_field.value = "";
    message_field.value = "";
    
}

function generate_mail_modal(env) {
    let env_body = `<div class="container border border-secondary">
                <div class="row mb-3">
                    <div class="col text-end">
                        <label for="to-field" class="col-form-label">To:</label>
                    </div>

                    <div class="col-sm-10 pe-5">
                        <input id="to-field" name="to-field" type="text" class="form-control disabled" value="${env.to}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col text-end">
                        <label for="to-field" class="col-form-label">From:</label>
                    </div>

                    <div class="col-sm-10 pe-5">
                        <input id="from-field" name="from-field" type="text" class="form-control disabled" value="${env.from}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col text-end">
                        <label for="subject-field" class="col-form-label disabled">Subject:</label>
                    </div>

                    <div class="col-sm-10 pe-5">
                        <input id="subject-field" name="subject-field" type="text" class="form-control disabled" value="${env.sub}">
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col text-end">
                        <label for="message-field" class="col-form-label">Message:</label>
                    </div>

                    <div class="col-sm-10 pe-5">
                        <textarea id="message-field" name="message-field" rows="5" class="form-control mb-3" value="${env.frag}"></textarea>
                        <div class="d-grid gap-1">
                            <button id="send-mail" name="send-mail" class="btn btn-primary">Send Mail</button>
                            <button id="cancel-mail" name="cancel-mail" class="btn btn-secondary" onclick=close_click()>Close</button>
                        </div>
                    </div>
                </div>
            </div>`;

    let env_modal = `<div class="modal fade" id="${env.id}-modal" tabindex="-1" aria-hidden="true" style="z-index: 1044;">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-secondary text-bg-secondary">
                                <h1 class="modal-title fs-5">Message</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                ${env_body}
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary">Close</button>
                            </div>
                        </div>
                    </div>
                </div>`;
    return env_modal;
}
    