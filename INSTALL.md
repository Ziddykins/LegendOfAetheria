                ...                                                         ..                                   
            .zf"` `"tu                                                    dF          
           x88      '8N.                                      u.    u.   '88bu.        
           888k     d88&      .u         uL          .u     x@88k u@88c. '*88888bu     
           8888N.  @888F   ud8888.   .ue888Nc..   ud8888.  ^"8888""8888"   ^"*8888N     
           `88888 9888%  :888'8888. d88E`"888E` :888'8888.   8888  888R   beWE "888L   
             %888 "88F   d888 '88%" 888E  888E  d888 '88%"   8888  888R   888E  888E
              8"   "*h=~ 8888.+"    888E  888E  8888.+"      8888  888R   888E  888E 
            z8Weu        8888L      888E  888E  8888L        8888  888R   888E  888F  
           ""88888i.   Z '8888c. .+ 888& .888E  '8888c. .+  "*88*" 8888" .888N..888     
          "   "8888888*   "88888%   *888" 888&   "88888%      ""   'Y"    `"888*""       
                ^"**""      "YP'     `"   "888E    "YP'                      ""             
                                            .dWi   `
                                     4888~  J8%                                                  
                                       ^"===*"`                    
                                                      ,
                                                     oec:  
                                             u.     @88888  
                                       ...ue888b    8"*88%  
                                       888R Y888r   8b.     
                                       888R I888>  u888888> 
                                       888R I888>   8888R   
                                       888R I888>   8888P   
     ▖▖ ▄ ▄ ▄  ▖ ▄▖▄▖              u8888cJ888   *888>   
     ▙▌ ▌▌▌▌▙▘   ▄▌▙▖               "*888*P"     4888    
      ▌ ▙▘▙▘▙▘   ▙▖▄                'Y"        '888    
                                                      88R   
                                                      88E
                                                      88>     
                                                      48     
                                                      '8
                                                      `

     ,..,,._                                  s                                           .                
  .zf"``"db96689b                            :8     .uef^"                               @88>              
 x88      '8NYdb895                         .88    :d88E                     .u    .     %8P               
 888k     d888F8888R               .u      :888ooo `888E            .u     .d88B :@8c     .          u     
 8888N.  @888F  Y1888R          ud8888.  -*8888888  888E .z8k    ud8888.  ="8888f8888r  .@88u     us888u.  
 `88888,9888%    1888R        :888'8888.   8888     888E~?888L :888'8888.   4888>'88"  ''888E` .@88 "8888" 
   %88899882      d888R       d888 '88%"   8888     888E  888E d888 '88%"   4888> '      888E  9888  9888  
     89988886ooood988B88      8888.+"      8888     888E  888E 8888.+"      4888>        888E  9888  9888  
      Y8W00        4888R      8888L       .8888Lu=  888E  888E 8888L       .d888L .+     888E  9888  9888  
      98888        YT888B     '8888c. .+  ^%888*    888E  888E '8888c. .+  ^"8888*"      888&  9888  9888  
     88880         Y888% ,*    "88888%      'Y"    m888N= 888>  "88888%       "Y"        R888" "888*""888" 
    d9YPR          M88%"        "YP'               `Y"   888     "YP'                    ""    ^Y"   ^Y'  
   dmq88Y,_        qp"        



Debian/Ubuntu/Alpine
--------------------
cd install

** if ubuntu **
sudo bash install/scripts/sury_setup_ubnt.sh

* note: Plucky doesn't seem to be supported yet by Sury PPA. Use this if you run into trusted issues.
sed -i 's/Components: main/Components: main\nTrusted: yes/' /etc/apt/sources.list.d/ondrej-ubuntu-php-plucky.sources

** if debian **
sudo bash install/scripts/sury_setup_deb.sh


sudo bash install/scripts/perldeps.sh
mv config.ini.default config.ini
cd install
chmod +x AutoInstaller.pl
sudo ./AutoInstaller.pl



Windows
--------------------
Soon


Mac
--------------------
Probably do-able, but not tested.
If you want to try, please let me know how it goes.