<?php
    use Game\Account\Enums\Privileges;
?>
<div class="text-center ms-n4">
    <span class="nav-icon material-symbols-sharp mt-3 border" style="cursor: pointer;" onclick=expand_all()>unfold_more</span>

    <?php if (Privileges::name_to_enum($account->get_privileges())->value > Privileges::MODERATOR->value): ?>
        <a href="/admini/strator/dashboard"><span class="material-symbols-sharp text-warning mt-3 border">shield_person</span></a>
    <?php endif; ?>

    <span class="nav-icon material-symbols-sharp mt-3 border" style="cursor: pointer;" onclick=collapse_all()>unfold_less</span>
</div>