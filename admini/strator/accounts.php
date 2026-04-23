<?php
declare(strict_types = 1);

use Game\Account\Account;
use Game\Account\Enums\Privileges;
use Game\Character\Character;
use Game\Inventory\Gem;
use Game\Monster\Pool;
use Game\Traits\PropConvert;

require_once "../../system/constants.php";
;
require_once "../../system/functions.php";

if (check_session()) {
    $account = new Account($_SESSION['email']);
    $character = new Character($account->get_id(), $_SESSION['character-id']);
    $character->load();

    if (isset($_POST['action'])) {
        $action     = $_POST['action'];
        $account_id = null;

        if (isset($_POST['account_id'])) {
            $account_id = $_POST['account-id'];
        }

        if ($account_id == $_SESSION['account-id']) {

        }

        switch ($action) {
            case 'ban':
                $sql_query = "UPDATE {$t['accounts']} SET `banned` = true WHERE `id` = ?";
                $db->execute_query($sql_query, [ $account_id ]);
                $sql_query = "INSERT INTO {$t['banned']} (`expires`, `reason, `account_id`) VALUES (?, ?, ?)";
                $db->execute_query($sql_query, [ "NOW() + INTERVAL 1 DAY", "Admin Ban", $account_id ]);
                break;
            case 'delete':
                break;
            default:
                break;
        }
    }
} else {
    header('Location: /?no_login');
}

function clsprop_to_tblcol($property): string|null {
    $property = preg_replace('/[^a-zA-Z_1-3]/', '', $property);
    $out = null;
    $check_double = null;

    for ($i=0; $i<strlen($property); $i++) {
        if ($i == 0 && ctype_upper($property[$i])) {
            $out .= strtolower($property[$i]);
             continue;
        }
        if (isset($property[$i+1])) {
            $check_double = $property[$i] . $property[$i + 1];
        } else {
            $check_double = $property[$i];
        }

        if (ctype_upper($check_double)) {
            if (array_search($check_double, ['HP', 'MP', 'EP', 'ID']) !== false) {
                $out .= '_' . strtolower($check_double);
                $i++;
                continue;
            }
        }

        if (ctype_upper($property[$i])) {
            $let = strtolower($property[$i]);
            $out .= "_$let";
            continue;
        }
        $out .= $property[$i];
    }
    return $out;
}
?>

<?php include WEBROOT . '/html/opener.html'; ?>
<head>
    <?php include ADMIN_WEBROOT . '/html/headers.html'; ?>
    <script src="js/tabulator.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="css/tabulator.min.css">
    
</head>

<body class="main-font layout-fixed sidebar-expand-lg bg-body-tertiary" data-bs-theme="dark">
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">

                <?php include ADMIN_WEBROOT . '/components/nav-links.html'; ?>

                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                            <i class="bi bi-search"></i>
                        </a>
                    </li>

                    <?php include ADMIN_WEBROOT . '/components/messages.html'; ?>

                    <?php include ADMIN_WEBROOT . '/components/notifications.html'; ?>

                    <li class="nav-item">
                        <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                            <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                            <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none;"></i>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick=flipmode();>
                            <i class="bi bi-cloud-sun"></i>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-envelope-open"></i>
                        </a>
                    </li>

                    <?php include ADMIN_WEBROOT . '/components/usermenu.html'; ?>
                </ul>
            </div>
        </nav>

        <?php include ADMIN_WEBROOT . '/components/sidebar.html'; ?>

        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Accounts</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                                <li class="breadcrumb-item"><a href="<?php echo ADMIN_WEBROOT . '/dashboard'; ?>">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Accounts</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div id="accounts-tbl">
                        

                    <?php
                        $refl = new ReflectionClass($account);
                        $props = $refl->getProperties();
                        $columns_data  = [];
                        
                        $accounts = $db->execute_query("SELECT * FROM `tbl_accounts` ORDER BY `privileges` DESC")->fetch_all(MYSQLI_ASSOC);
                        $row_data = [];
                        $col_data = [];

                        array_push($col_data, '{title:"Manage", field:"manage", formatter:"html"}');
                        
                        foreach ($props as $prop) {
                            $conv_prop = clsprop_to_tblcol($prop->name);

                            $col = "{title:\"{$conv_prop}\", field:\"{$conv_prop}\", cellEdited:function(cell) {cell.getData();} ";

                            if (array_search($conv_prop, ["banned", "muted", "verified"])) {
                                $col .= ', formatter:"toggle", formatterParams:{size:16,onValue:"True",offValue:"False",onTruthy:true,onColor:"#6ea8fe",offColor:"grey",clickable:true}';
                            }

                            if ($conv_prop == 'logged_on') {
                                $col .= ", editor:\"input\"";
                            }

                            $col .= '}';
                            //$col .= 'width:125}';
                            array_push($col_data, $col);
                        }

                        foreach ($accounts as $account) {
                            if ($account['id'] === $_SESSION['account-id']) {
                                $account['manage'] = '<div class="text-center">-----</div>';
                            } else {
                                $account['manage'] = '<div class="text-center' . $ad . '"><a href="./accounts/?action=delete&account_id=' . $account['id'] . '"><i class="bi bi-eraser-fill fw-bold fs-6 me-2" styke="text-color: #fc4903;"></i></a><a href="./accounts/?action=ban&account_id=' . $account['id'] . '"><i class="bi bi-ban-fill fw-bold fs-6 me-2 text-danger"></i></a><a href="./accounts/?action=message"><i class="bi bi-chat-text-fill text-primary fw-bold fs-6 me-2 opacity-50"></i></a><a href="./accounts/?action=save"><i class="bi bi-floppy-fill text-success fw-bold fs-6 me-2 opacity-50"></i></a></span>'; 
                            }
                            
                            unset($account['focused_slot']);
                            unset($account['acceptable_doubles']);
                            
                            $json_act = json_encode($account);
                            $json_act = preg_replace('/"(.*?) ":("?.*?"?),/', '$1:$2, ', $json_act);
                            $json_act = preg_replace('/, "(.*?)":/', ', $1:', $json_act);
                            $json_act = str_replace("null", '"null"', $json_act);
                            array_push($row_data, $json_act);
                        }
                        

                        
                    ?>

                </div>
            </>
        </main>
    </div>
    <script>
        

        var tbl_data = [<?php echo join(",", $row_data); ?>];
        var table = new Tabulator("#accounts-tbl", {
            height:"70.0vh",
            layout:"fitColumnsData",
            rowHeight: Math.round(visualViewport.height / 20, 0),
            groupBy: "privileges",
            columns: [<?php echo join(",", $col_data); ?>],
            data: tbl_data,
        });
    </script>
</body>
</html>