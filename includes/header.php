<?php
    $image_src = isset($header_image) ? $header_image : '';
    $image = $image_src ? sprintf('<img src="%s" alt="">', $image_src) : '';
?>

<!-- <div class="covid19">
<a href="arpa-business-grant-application.php">ARPA: Business Grant Application</a> 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
<a href="arpa-homeowner-grant-application.php">ARPA: Homeowner Grant Application</a> 

</div> -->

<header id="masthead" class="site-header clearfix" role="banner">
    <div class="site-header-main">
        <div class="container">
            <div class="site-header-left">
                <div class="logo-wrapper">
                    <h1 class="site-logo"><a href="./">City of Waycross</a></h1>
                </div>
            </div>
            <div class="btn-header">
            <div class="btn-list">
                <div class="btn-left">
                    <a href="https://esgengineering.maps.arcgis.com/apps/dashboards/8bce5c2a1c8c4ef0a62a7b664e641c6c" target="_blank" class="btn-way">District Dashboard </a>
                </div>
                <div class="btn-right">
                    <a href="https://apps.apple.com/us/app/mywaycrossga/id6504728091?platform=iphone" target="_blank" class="btn-way">MyWaycrossGA App
                    iOS </a>
                    <a href="https://play.google.com/store/apps/details?id=com.civicapps.waycrossga" target="_blank" class="btn-way">MyWaycrossGA App
                    Android</a>
                    <img src="images/app phone.png" alt="" class="phone-icon"> 
                </div>
            </div>
        </div>

            <div class="site-header-right">
                <button class="sidenav-button" type="button" data-target="mobile-navigation"><span
                        class="sidenav-button-bars"></span></button>

                <div class="top-right">
                    <!-- <p class="today">< ?php echo date('\<\b\>l\<\/\b\> - m/d/Y'); ?></p> -->
                    <?php include "searchform.php"; ?>
                </div>

                <nav id="access" class="site-navigation">
                    <?php include "navigation.php"; ?>
                </nav>
                <div style="display: flex; justify-content: flex-end;">
                    <a href="https://www.facebook.com/people/City-of-Waycross-Government/100069328174806/"
                        class="social" target="_blank"><img src="./assets/svg/social-icon1.svg" alt=""></a>
                    <a style="margin-left: 15px;" href="https://www.instagram.com/waycrosscity/" class="social"
                        target="_blank"><img src="./assets/svg/social-icon3.svg" alt=""></a>
                        <a style="margin-left: 15px;" href="https://x.com/CityWaycross417" class="social"
                        target="_blank"><img src="./assets/svg/icons8-twitter-30.png" alt=""></a>
                </div>
            </div>
        </div>
    </div>
    <div class="site-header-image">
        <div class="container">
            <div class="image-area"></div>
            <?php echo $image; ?>
        </div>
    </div>
    <div class="site-header-background"></div>
</header>



<nav id="mobile-navigation" class="sidenav">
    <?php include "searchform.php"; ?>
    <nav class="mobile-navigation">
        <?php include "navigation.php"; ?>
    </nav>
</nav>