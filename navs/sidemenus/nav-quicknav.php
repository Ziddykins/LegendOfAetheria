<?php
    use Game\Account\Enums\Privileges;
    use Game\Character\Enums\FriendStatus;

    $new_mail = check_mail('unread');
    $data = get_friend_counts(null, false);
    $new_friend_reqs = $data[1]['REQUEST_RECV'] ?? 0;

?>

<div class="d-flex pb-2 shadow-sm justify-content-evenly w-100">
    <span data-bs-toggle="tooltip" data-bs-title="Expand All" class="nav-icon material-symbols-sharp mt-3 border" style="cursor: pointer;" onclick=expand_all()>
        unfold_more
    </span>

    <a href="/game?page=profile&sub=account">
        <span class="nav-icon material-symbols-sharp mt-3 border" style="cursor: pointer;"  data-bs-toggle="tooltip" data-bs-title="Account">
            person
        </span></a>

    <?php if ($account->get_privileges()->value > Privileges::ADMINISTRATOR->value): ?>
        <a href="/admini/strator/dashboard">
            <span class="material-symbols-sharp text-warning mt-3 border" data-bs-toggle="tooltip" data-bs-title="Administrator Panel">
                shield_person
            </span></a>
    <?php endif; ?>
    
    <a href="/game?page=profile&sub=character">
        <span class="nav-icon material-symbols-sharp mt-3 border" style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-title="Character Profile">
            demography
        </span></a>
    
    <span data-bs-toggle="tooltip" data-bs-title="Collapse All" class="nav-icon material-symbols-sharp mt-3 border" style="cursor: pointer;" onclick=collapse_all()>
        unfold_less
    </span>

    <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
        <span class="nav-icon mt-3 border material-symbols-outlined" data-bs-toggle="tooltip" data-bs-title="Hide Menu">
            menu_open
        </span></a>
</div>