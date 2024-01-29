<?php
session_start();
try {
    require './config.php';
    require '../TableDocumentation.php';
    $table_documentation = new \Taf\TableDocumentation($table_name);
    $taf_config->check_documentation_auth();
} catch (\Throwable $th) {
    echo "<h1>" . $th->getMessage() . "</h1>";
}
?>
<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>JantTaf</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="../taf_assets/assets/images/favicon.ico" />

    <!-- Library / Plugin Css Build -->
    <link href="../taf_assets/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./taf_assets/assets/css/core/libs.min.css" />
    <link href="../taf_assets/css/custom.ace.css" rel="stylesheet">

    <!-- Aos Animation Css -->
    <link rel="stylesheet" href="../taf_assets/assets/vendor/aos/dist/aos.css" />

    <!-- Hope Ui Design System Css -->
    <link rel="stylesheet" href="../taf_assets/assets/css/hope-ui.min.css?v=2.0.0" />

    <!-- Custom Css -->
    <link rel="stylesheet" href="../taf_assets/assets/css/custom.min.css?v=2.0.0" />

    <!-- Dark Css -->
    <link rel="stylesheet" href="../taf_assets/assets/css/dark.min.css" />

    <!-- Customizer Css -->
    <link rel="stylesheet" href="../taf_assets/assets/css/customizer.min.css" />

    <!-- RTL Css -->
    <link rel="stylesheet" href="../taf_assets/assets/css/rtl.min.css" />


</head>

<body class="">
    <!-- loader Start -->
    <div id="loading">
        <div class="loader simple-loader">
            <div class="loader-body"></div>
        </div>
    </div>
    <!-- loader END -->

    <aside class="sidebar sidebar-default sidebar-white sidebar-base navs-rounded-all ">
        <div class="sidebar-header d-flex align-items-center justify-content-start">
            <a href="../taf.php" class="navbar-brand">
                <!--Logo start-->
                <!--logo End-->

                <!--Logo start-->
                <div class="logo-main">
                    <div class="logo-normal">
                        <img class="text-primary icon-30" src="../taf_assets/assets/images/logo.jpg" alt="">
                    </div>
                    <div class="logo-mini">
                        <img class="text-primary icon-30" src="../taf_assets/assets/images/logo.jpg" alt="">
                    </div>
                </div>
                <!--logo End-->




                <h4 class="logo-title">JantTaf</h4>
            </a>
            <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
                <i class="icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.25 12.2744L19.25 12.2744" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M10.2998 18.2988L4.2498 12.2748L10.2998 6.24976" stroke="currentColor"
                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </i>
            </div>
        </div>
        <div class="sidebar-body pt-0 data-scrollbar">
            <div class="sidebar-list">
                <!-- Sidebar Menu Start -->
                <ul class="navbar-nav iq-main-menu" id="sidebar-menu">
                    <li class="nav-item static-item">
                        <a class="nav-link static-item disabled" href="#" tabindex="-1">
                            <span class="default-icon">Home</span>
                            <span class="mini-icon">-</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="../taf.php">
                            <i class="icon">
                                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                                    class="icon-20">
                                    <path opacity="0.4"
                                        d="M16.0756 2H19.4616C20.8639 2 22.0001 3.14585 22.0001 4.55996V7.97452C22.0001 9.38864 20.8639 10.5345 19.4616 10.5345H16.0756C14.6734 10.5345 13.5371 9.38864 13.5371 7.97452V4.55996C13.5371 3.14585 14.6734 2 16.0756 2Z"
                                        fill="currentColor"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M4.53852 2H7.92449C9.32676 2 10.463 3.14585 10.463 4.55996V7.97452C10.463 9.38864 9.32676 10.5345 7.92449 10.5345H4.53852C3.13626 10.5345 2 9.38864 2 7.97452V4.55996C2 3.14585 3.13626 2 4.53852 2ZM4.53852 13.4655H7.92449C9.32676 13.4655 10.463 14.6114 10.463 16.0255V19.44C10.463 20.8532 9.32676 22 7.92449 22H4.53852C3.13626 22 2 20.8532 2 19.44V16.0255C2 14.6114 3.13626 13.4655 4.53852 13.4655ZM19.4615 13.4655H16.0755C14.6732 13.4655 13.537 14.6114 13.537 16.0255V19.44C13.537 20.8532 14.6732 22 16.0755 22H19.4615C20.8637 22 22 20.8532 22 19.44V16.0255C22 14.6114 20.8637 13.4655 19.4615 13.4655Z"
                                        fill="currentColor"></path>
                                </svg>
                            </i>
                            <span class="item-name">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <hr class="hr-horizontal">
                    </li>
                    <li class="nav-item static-item">
                        <a class="nav-link static-item disabled" href="#" tabindex="-1">
                            <span class="default-icon">Pages</span>
                            <span class="mini-icon">-</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#sidebar-table" role="button"
                            aria-expanded="false" aria-controls="sidebar-table">
                            <i class="icon">
                                <svg class="icon-20" xmlns="http://www.w3.org/2000/svg" width="20" viewBox="0 0 24 24"
                                    fill="none">
                                    <path
                                        d="M2 5C2 4.44772 2.44772 4 3 4H8.66667H21C21.5523 4 22 4.44772 22 5V8H15.3333H8.66667H2V5Z"
                                        fill="currentColor" stroke="currentColor" />
                                    <path
                                        d="M6 8H2V11M6 8V20M6 8H14M6 20H3C2.44772 20 2 19.5523 2 19V11M6 20H14M14 8H22V11M14 8V20M14 20H21C21.5523 20 22 19.5523 22 19V11M2 11H22M2 14H22M2 17H22M10 8V20M18 8V20"
                                        stroke="currentColor" />
                                </svg>
                            </i>
                            <span class="item-name">Table</span>
                            <i class="right-icon">
                                <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </i>
                        </a>
                        <ul class="sub-nav collapse" id="sidebar-table" data-bs-parent="#sidebar-menu">
                            <?php
                            $dir = '../';
                            $files = scandir($dir);
                            foreach ($taf_config->tables as $key => $table) {
                                if (array_search($table, $files)) {
                                    echo "<li class='nav-item'>
                <a class='nav-link' href='./$table'>
                    <i class='icon'>
                        <svg class='icon-10' xmlns='http://www.w3.org/2000/svg' width='10' viewBox='0 0 24 24' fill='currentColor'>
                            <g>
                                <circle cx='12' cy='12' r='8' fill='currentColor'></circle>
                            </g>
                        </svg>
                    </i>
                    <i class='sidenav-mini-icon'> D </i>
                    <span class='item-name'>$table</span>
                </a>
            </li>";
                                }
                            }
                            ?>

                        </ul>
                    </li>
                </ul>
                <!-- Sidebar Menu End -->
            </div>
        </div>
        <div class="sidebar-footer"></div>
    </aside>
    <main class="main-content">
        <div class="position-relative iq-banner">
            <!--Nav Start-->
            <nav class="nav navbar navbar-expand-lg navbar-light iq-navbar fixed-top">
                <div class="container-fluid navbar-inner">
                    <a href="../taf.php" class="navbar-brand">
                        <!--Logo start-->
                        <!--logo End-->

                        <!--Logo start-->
                        <div class="logo-main">
                            <div class="logo-normal">
                                <img class="text-primary icon-30" src="../taf_assets/assets/images/logo.jpg" alt="">
                            </div>
                            <div class="logo-mini">
                                <img class="text-primary icon-30" src="../taf_assets/assets/images/logo.jpg" alt="">
                            </div>
                        </div>
                        <!--logo End-->




                        <h4 class="logo-title">JantTaf</h4>
                    </a>
                    <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
                        <i class="icon">
                            <svg width="20px" class="icon-20" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z" />
                            </svg>
                        </i>
                    </div>
                    <div class="input-group search-input">
                        <span class="input-group-text" id="search-input">
                            <svg class="icon-18" width="18" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <circle cx="11.7669" cy="11.7666" r="8.98856" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round"></circle>
                                <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </span>
                    </div>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon">
                            <span class="mt-2 navbar-toggler-bar bar1"></span>
                            <span class="navbar-toggler-bar bar2"></span>
                            <span class="navbar-toggler-bar bar3"></span>
                        </span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="mb-2 navbar-nav ms-auto align-items-center navbar-list mb-lg-0">
                            <li class="nav-item dropdown">
                                <a class="py-0 nav-link d-flex align-items-center" href="https://h24code.com"
                                    id="navbarDropdown" role="button">
                                    Faire un don
                                    <!-- <div class="caption ms-3 d-none d-md-block ">
                        <h6 class="mb-0 caption-title">Austin Robertson</h6>
                        <p class="mb-0 caption-sub-title">Marketing Administrator</p>
                    </div> -->
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="py-0 nav-link d-flex align-items-center" href="../login.php"
                                    id="navbarDropdown" role="button">
                                    <button type="button" class="btn btn-outline-warning">Se deconnecter</button>
                                    <!-- <div class="caption ms-3 d-none d-md-block ">
                        <h6 class="mb-0 caption-title">Austin Robertson</h6>
                        <p class="mb-0 caption-sub-title">Marketing Administrator</p>
                    </div> -->
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <!--Nav End-->
        </div>
        <br>
        <br>
        <br>
        <br>
        <br>
        <div class="conatiner-fluid content-inner mt-n5 py-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card rounded">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h3 class="mb-2">Description de la table
                                        <span class="text-danger">
                                            <?= $table_documentation->table_name ?>
                                        </span>
                                    </h3>
                                    <ol class="list-group list-group-numbered ms-3 mt-3">
                                        <?php
                                        try {
                                            if (count($table_documentation->table_descriptions["les_colonnes"]) > 0) {
                                                foreach ($table_documentation->table_descriptions["les_colonnes"] as $key => $value) {
                                                    // echo "<li class=\"\">" . $value["Field"] . " <span class=\"text-primary\">" . $value["explications"] . "</span></li>";
                                                    echo "<li class='list-group-item d-flex justify-content-between align-items-start'>
                                                        <div class='ms-2 me-auto'>
                                                            <div class='fw-bold'>" . $value['Field'] . "</div>" .
                                                        $value["explications"] .
                                                        "</div>
                                                    </li>";

                                                }
                                            } else {
                                                foreach ($table_documentation->description as $key => $value) {
                                                    // echo "<li class=\"\">" . $value . "</li>";
                                                    echo "<li class='list-group-item d-flex justify-content-between align-items-start'>
                                                        <div class='ms-2 me-auto'>
                                                            <div class='fw-bold'>" . $value . "</div>" .
                                                        "</div>
                                                    </li>";
                                                }
                                            }
                                        } catch (\Throwable $th) {
                                            echo "<li>" . $th->getMessage() . "</li>";
                                        }
                                        ?>
                                    </ol>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm-12">
                                    <h3 class="mb-2">Explorez les actions possibles pour la table
                                    </h3>
                                        <?php
                                        $dir = './';
                                        $files = scandir($dir);
                                        foreach ($files as $key => $value) {
                                            if ($value != "." && $value != ".." && $value != "index.php" && $value != "config.php" && pathinfo($value, PATHINFO_EXTENSION) === 'php') {
                                                $action = pathinfo($value, PATHINFO_FILENAME);

                                                if ($action == "add") {
                                                    echo "<div class='ms-3 mt-3'>" . $table_documentation->add() . "</div>";
                                                }
                                                if ($action == "get") {
                                                    echo "<div class='ms-3 mt-3'>" . $table_documentation->get() . "</div>";
                                                }
                                                if ($action == "edit") {
                                                    echo "<div class='ms-3 mt-3'>" . $table_documentation->edit() . "</div>";
                                                }
                                                if ($action == "delete") {
                                                    echo "<div class='ms-3 mt-3'>" . $table_documentation->delete() . "</div>";
                                                }
                                            }
                                        }
                                        ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- Footer Section Start -->
        <footer class="footer">
            <div class="footer-body">
                <ul class="left-panel list-inline mb-0 p-0">
                    <li class="list-inline-item"><a href="../dashboard/extra/privacy-policy.html">Privacy Policy</a>
                    </li>
                    <li class="list-inline-item"><a href="../dashboard/extra/terms-of-service.html">Terms of Use</a>
                    </li>
                </ul>
                <div class="right-panel">
                    ©
                    <script>document.write(new Date().getFullYear())</script> Hope UI, Made with
                    <span class="">
                        <svg class="icon-15" width="15" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M15.85 2.50065C16.481 2.50065 17.111 2.58965 17.71 2.79065C21.401 3.99065 22.731 8.04065 21.62 11.5806C20.99 13.3896 19.96 15.0406 18.611 16.3896C16.68 18.2596 14.561 19.9196 12.28 21.3496L12.03 21.5006L11.77 21.3396C9.48102 19.9196 7.35002 18.2596 5.40102 16.3796C4.06102 15.0306 3.03002 13.3896 2.39002 11.5806C1.26002 8.04065 2.59002 3.99065 6.32102 2.76965C6.61102 2.66965 6.91002 2.59965 7.21002 2.56065H7.33002C7.61102 2.51965 7.89002 2.50065 8.17002 2.50065H8.28002C8.91002 2.51965 9.52002 2.62965 10.111 2.83065H10.17C10.21 2.84965 10.24 2.87065 10.26 2.88965C10.481 2.96065 10.69 3.04065 10.89 3.15065L11.27 3.32065C11.3618 3.36962 11.4649 3.44445 11.554 3.50912C11.6104 3.55009 11.6612 3.58699 11.7 3.61065C11.7163 3.62028 11.7329 3.62996 11.7496 3.63972C11.8354 3.68977 11.9247 3.74191 12 3.79965C13.111 2.95065 14.46 2.49065 15.85 2.50065ZM18.51 9.70065C18.92 9.68965 19.27 9.36065 19.3 8.93965V8.82065C19.33 7.41965 18.481 6.15065 17.19 5.66065C16.78 5.51965 16.33 5.74065 16.18 6.16065C16.04 6.58065 16.26 7.04065 16.68 7.18965C17.321 7.42965 17.75 8.06065 17.75 8.75965V8.79065C17.731 9.01965 17.8 9.24065 17.94 9.41065C18.08 9.58065 18.29 9.67965 18.51 9.70065Z"
                                fill="currentColor"></path>
                        </svg>
                    </span> by <a href="https://iqonic.design/">IQONIC Design</a>.
                </div>
            </div>
        </footer>
        <!-- Footer Section End -->
    </main>

    <!-- Library Bundle Script -->
    <script src="../taf_assets/assets/js/core/libs.min.js"></script>

    <!-- External Library Bundle Script -->
    <script src="../taf_assets/assets/js/core/external.min.js"></script>

    <!-- Widgetchart Script -->
    <script src="../taf_assets/assets/js/charts/widgetcharts.js"></script>

    <!-- mapchart Script -->
    <script src="../taf_assets/assets/js/charts/vectore-chart.js"></script>
    <script src="./taf_assets/assets/js/charts/dashboard.js"></script>

    <!-- fslightbox Script -->
    <script src="../taf_assets/assets/js/plugins/fslightbox.js"></script>

    <!-- Settings Script -->
    <script src="../taf_assets/assets/js/plugins/setting.js"></script>

    <!-- Slider-tab Script -->
    <script src="../taf_assets/assets/js/plugins/slider-tabs.js"></script>

    <!-- Form Wizard Script -->
    <script src="../taf_assets/assets/js/plugins/form-wizard.js"></script>

    <!-- AOS Animation Plugin-->
    <script src="../taf_assets/assets/vendor/aos/dist/aos.js"></script>

    <!-- App Script -->
    <script src="../taf_assets/assets/js/hope-ui.js" defer></script>
    <script src="../taf_assets/bootstrap.bundle.min.js"></script>
    <script src="../taf_assets/ace.js" type="text/javascript" charset="utf-8"></script>
    <script src="../taf_assets/ace.beautify.js"></script>
    <script src="../taf_assets/js/custom.ace.js"></script>
</body>

</html>