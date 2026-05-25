<?php
   use Game\Monster\Enums\MonsterScope;
   use Game\Monster\Monster;

   require_once "functions.php";

   $character->stats->set_ep(10000);
   [$mon_name, $mon_avatar, $mon_hp, $mon_maxHP, $mon_mp, $mon_maxMP, $mon_str, $mon_int, $mon_def] = [null, null, null, null, null, null, null, null, null];
   $monster = $character->get_monster();
   $mon_loaded = 0;

   if ($monster != null && $monster != "") {
      $mon_loaded = 1;
   }

   if ($mon_loaded && $monster->stats->get_hp() <= 0) {
      $monster = null;
      $mon_loaded = 0;
   }



   if (isset($_POST['hunt-new-monster']) && $_POST['hunt-new-monster'] == 1) {
      check_csrf($_POST['csrf-token']);
      if (!$mon_loaded) {
         $monster = new Monster(MonsterScope::PERSONAL);

         $monster->new();
         $monster->load(MonsterScope::PERSONAL);
         $monster->random_monster($character->get_level());
         $character->set_monster($monster);
         $mon_loaded = 1;
      }
   }

   if (isset($_POST['flee-monster']) && $_POST['flee-monster'] == 1) {
      check_csrf($_POST['csrf-token']);
      if ($mon_loaded) {
         $character->set_monster(monster: null);
      }
   }
   if ($mon_loaded) {
      $mon_name  = $monster->get_name();
      $mon_hp    = $monster->stats->get_hp();
      $mon_maxHP = $monster->stats->get_maxHP();
      $mon_mp    = $monster->stats->get_mp();
      $mon_maxMP = $monster->stats->get_maxMP();
      $mon_str   = $monster->stats->get_str();
      $mon_int   = $monster->stats->get_int();
      $mon_def   = $monster->stats->get_def();
      $mon_dl    = $monster->get_dropLevel();
      $mon_avatar = '/img/enemies/' . str_replace(' ', '', $monster->get_name()) . '.png';
   }
?>
<script>
window.__AETHERIA_CONFIG__ = {
    player: {
        name: '<?php echo $character->get_name(); ?>',
        level: <?php echo $character->get_level(); ?>,
        stats: {
            hp: <?php echo $character->stats->get_hp(); ?>,
            maxHP: <?php echo $character->stats->get_maxHP(); ?>,
            mp: <?php echo $character->stats->get_mp(); ?>,
            maxMP: <?php echo $character->stats->get_maxMP(); ?>,
            ep: <?php echo $character->stats->get_ep(); ?>,
            maxEP: <?php echo $character->stats->get_maxEP(); ?>,
            str: <?php echo $character->stats->get_str(); ?>,
            def: <?php echo $character->stats->get_def(); ?>,
        }
    },

    <?php if ($mon_loaded): ?>
    monster: {
        name: '<?php echo $mon_name; ?>',
        level: <?php echo $mon_level; ?>,
        stats: {
            hp: <?php echo $mon_hp; ?>,
            maxHP: <?php echo $mon_maxHP; ?>,
            mp: <?php echo $mon_mp; ?>,
            maxMP: <?php echo $mon_maxMP; ?>,
            ep: <?php echo $mon_ep; ?>,
            maxEP: <?php echo $mon_maxEP; ?>,
            str: <?php echo $mon_str; ?>,
            def: <?php echo $mon_def; ?>,
        }
    },
    <?php endif; ?>

    csrfToken: loa.u_csrf,
    apiEndpoint: '/battle'
};
</script>

<div id="root"></div>

<script type="module" src="/battle/assets/index.js"></script>
<link rel="stylesheet" href="/battle/assets/index.css">
