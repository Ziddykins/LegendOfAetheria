

function tgl_active (e) {                                                                                                                                                    
      document.querySelectorAll('i[class$="diamond-fill"]').forEach(function(e) {                                                                                              
          e.classList.remove('bi-diamond-fill');                                                                                                                               
          e.classList.add('bi-diamond');                                                                                                                                       
      });                                                                                                                                                                      
      e.childNodes[1].classList.add('bi-diamond-fill');                                                                                                                        
      e.childNodes[1].classList.remove('bi-diamond');                                                                                                                          
  };

