function gen_toast(id, type, icon, header_txt, message) {
  let toast_div = document.createElement('div');
  let toast_badge_text = type[0].toUpperCase() + type.slice(1);
  toast_badge_text = toast_badge_text.replace('Danger', 'Error');
  
  toast_div.id = id;
  toast_div.classList.add('toast');
  toast_div.ariaLive = 'assertive';
  toast_div.ariaAtomic = 'true';
  toast_div.role = 'alert';

  toast_div.innerHTML = `<div class="toast-header bg-${type}">
                              <span class="badge me-auto">
                                  <i class="bi ${icon} rounded me-2 text-black"></i>
                                  <strong>${header_txt}</strong>
                              </span>
                              <div class="text-white"><small>${new Date().toLocaleTimeString()}</small></div>
                              
                              <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                          </div>
                          <div class="toast-body">
                              ${message}
                          </div>
                      </div>`;

  document.getElementById('toast-container').append(toast_div);

  let toast = document.querySelector(`#toast-container #${id}`);
  let toast_bubble = bootstrap.Toast.getOrCreateInstance(toast);
  toast_bubble.show();
};