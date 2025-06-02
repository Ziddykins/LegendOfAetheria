<?php
    declare(strict_types = 1);
        use Game\Account\Account;
    use Game\Character\Character;
    use Game\Character\Stats;
    use Game\Inventory\Inventory;
    use Game\System\Tabulator;
    use Game\System\Tabulator\TableFromObject;
    use Game\System\Tabulator\Enums\DataType;

    require_once "../../bootstrap.php";

    if (check_session()) {
        $account = new Account($_SESSION['email']);
        $character = new Character($account->get_id(), $_SESSION['character-id']);
        $character->load();
    } else {
        header('Location: /?no_login');
    }

    function clsprop_to_tblcol($property) {
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

    
    require_once ADMIN_WEBROOT . '/system/functions.php';
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
                                <h3 class="mb-0">Characters</h3>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-end">
                                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                                    <li class="breadcrumb-item"><a href="<?php echo ADMIN_WEBROOT . '/dashboard'; ?>">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Characters</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="app-content">
                    <div class="container-fluid">
                        <div id="characters-tbl">
                        <?php

                            $refl = new ReflectionClass($character);
                            $props = $refl->getProperties();

                            $columns_data  = [];
                            
                            $characters = $db->execute_query("SELECT * FROM `tbl_characters`")->fetch_all(MYSQLI_ASSOC);
                            $row_data = [];
                            $col_data = [];

                            array_push($col_data, '{title:"Manage", field:"manage", formatter:"html"}');
                            
                            foreach ($props as $prop) {
                                $conv_prop = clsprop_to_tblcol($prop->name);
                                $col = "{title:\"{$conv_prop}\", field:\"{$conv_prop}\"} ";
                                array_push($col_data, $col);
                            }

                            foreach ($characters as $character) {
                                $char = new Character($character['account_id'], $character['id']);
                                $char->load();
                                $tb = new TableFromObject($char);
                                echo $tb->objectToJson($char, DataType::HEADER_DATA);
                                $character['manage'] ='<div class="text-center"><a href="./accounts/?action=ban&account_id=' . $character['id'] . '"><i class="bi bi-ban-fill text-danger fw-bold fs-6 me-2 opacity-50"></i></a><a href="./accounts/?action=message"><i class="bi bi-chat-text-fill text-primary fw-bold fs-6 me-2 opacity-50"></i></a><a href="./accounts/?action=save"><i class="bi bi-floppy-fill text-success fw-bold fs-6 me-2 opacity-50"></i></a></span>'; 
                                $stats     = new Stats($character['id']);
                                $stats     = $stats->propRestore($character['stats']);
                                $refl = new ReflectionClass($stats);

                                $props = $refl->getProperties();
                                $popup = "<div style='background-color: #000;'><span class='fw-bold text-center' style='font-size:1.2em;'>Stats for " . $character['name'] . ":</span><br />";

                                $i=0;
                                foreach ($props as $prop) {
                                    if (++$i % 3 == 0) {
                                        $popup .= "</div><div class='d-flex'>";
                                    }
                                    $str = 'get_' . $prop->name;
                                    
                                    $popup .= "<span class='fw-bold'>" . $prop->name . ": " .  $stats->$str() . " - </span>";
                                }
                                $popup .= "</div></div>";
                            }
                            $json_act = json_encode($character);
                            $json_act = preg_replace('/"(.*?) ":("?.*?"?),/', '$1:$2, ', $json_act);
                            $json_act = preg_replace('/, "(.*?)":/', ', $1:', $json_act);
                            $json_act = str_replace("null", '"null"', $json_act);
                            array_push($row_data, $json_act);

                            function sanitize_for_table($row_obj): string {
                                $json_act = json_encode($row_obj);
                                $json_act = preg_replace('/"(.*?) ":("?.*?"?),/', '$1:$2, ', $json_act);
                                $json_act = preg_replace('/, "(.*?)":/', ', $1:', $json_act);
                                $json_act = str_replace("null", '"null"', $json_act);
                                
                                array_push($row_data, $json_act);
                                return $json_act;
                            }
                        ?>

                    </div>
                </>
            </main>
        </div>
        <script>
            var rowPopupFormatter = function(e, row, onRendered){
                console.log(e.explicitOriginalTarget.attr('tabulator-field'));
                console.log(row.getCells());
                var data = row.getData();
                if (row.getIndex() != 'stats') {
                    return;
                }

                container = document.createElement("div"),
                contents = "<?php echo $popup; ?>";
                container.innerHTML = contents;
                return container;
            };

            var tbl_data = [<?php echo join(",", $row_data); ?>];
            var table = new Tabulator("#characters-tbl", {
                height:"70.0vh",
                layout:"fitColumnsData",
                rowClickPopup:rowPopupFormatter,
                rowHeight: Math.round(visualViewport.height / 20, 0),
                columns: [<?php echo join(",", $col_data); ?>],
                data: tbl_data,
            });
        </script>
    </body>
</html>