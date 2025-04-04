<?php
    use Game\Account\Enums\Privileges;
    use Game\Character\Enums\FriendStatus;

    
    $new_mail = check_mail('unread');
    $new_friend_reqs = get_friend_counts(FriendStatus::REQUEST_RECV);

?>

<div class="d-flex pe-2 pb-2 ps-2 shadow-sm justify-content-evenly" style="width: 90%;">
    <div class="text-center">
        <span data-bs-toggle="tooltip" data-bs-title="Expand All" class="nav-icon material-symbols-sharp mt-3 border" style="cursor: pointer;" onclick=expand_all()>
            unfold_more
        </span>

        <a href="/game?page=account">
            <span class="nav-icon material-symbols-sharp mt-3 border" style="cursor: pointer;"  data-bs-toggle="tooltip" data-bs-title="Account">
                person
            </span></a>

        <?php if ($account->get_privileges()->value > Privileges::MODERATOR->value): ?>
            <a href="/admini/strator/dashboard">
                <span class="material-symbols-sharp text-warning mt-3 border" data-bs-toggle="tooltip" data-bs-title="Administrator Panel">
                    shield_person
                </span></a>
        <?php endif; ?>
        
        <a href="/game?page=profile">
            <span class="nav-icon material-symbols-sharp mt-3 border" style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-title="Character Profile">
                demography
            </span></a>
        
        <span data-bs-toggle="tooltip" data-bs-title="Collapse All" class="nav-icon material-symbols-sharp mt-3 border" style="cursor: pointer;" onclick=collapse_all()>
            unfold_less
        </span>
    </div>
</div>