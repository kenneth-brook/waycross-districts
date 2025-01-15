<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0" />
    <title>City of Waycross</title>
    <meta name="format-detection" content="telephone=no" />
    <meta name="Keywords" content="" />
    <meta description="Description"
        content="The Mayor and City Council of Waycross, Georgia set policy and ordinances for Waycross." />
    <?php include "includes/header-code.txt"; ?>
</head>

<body>
    <div id="page" class="site page-mayor-city-commission">
        <?php $header_image = "images/headers/city-hall.jpg"; ?>
        <?php include "includes/header.php"; ?>
        <div class="header-contact d-none cushycms" title="Header address"><div>
            <p>Phone: <strong>912-287-2944</strong></p>
        </div>
    </div>

    <main id="content" class="site-content clearfix" role="main">
        <div class="container">
            <div class="content-header">
                <div class="content-header-right">
                    <?php include "includes/header-buttons.php"; ?>
                </div>
                    <div class="content-header-left cushycms" title="Page header"><h2 class="main-title large mt-xl-2 mb-6">District Map</h2>
                </div>
            </div>
            < ----- MAP GOES HERE ----- >
        </div>
    </main>
    <?php include "includes/footer.php"; ?>
    <?php include "includes/footer-code.txt"; ?>

    <script>
        const openBtns = document.querySelectorAll('.openBtn');
        const popups = document.querySelectorAll('.popup');
        const closeBtns = document.querySelectorAll('.closeBtn');

        openBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const target = btn.getAttribute('data-target');
                document.getElementById(target).style.display = 'block';
                overlay.style.display = 'block';
            });
        });

        closeBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const target = btn.getAttribute('data-target');
                document.getElementById(target).style.display = 'none';
                overlay.style.display = 'none';
            });
        });
    </script>
</body>
</html>