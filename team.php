<?php
/* Template Name: team page */

get_header();
?>

<div class="main">
    <div class="top-section">
        <div class="container">
            <div class="left-section">
                <div class="title">
                    <h2>
                        The </br> Team
                    </h2>
                </div>
            </div>
            <div class="right-section">
                <p>
                    The Zervos team comprises innovative, resourceful, and forward-thinking professionals.
                </p>
            </div>

        </div>
    </div>
    <div class="team-section">
        <div class="container">
            <div class="left-section">

                    <h6>
                        Legal Team
                    </h6>
                    <?php do_shortcode('[team_shortcode]'); ?>

            </div>
            <div class="right-section">
                <img src="<?php echo site_url() . '/wp-content/uploads/2025/03/Zervos_Lawyers_Imagery_26_Our_Team-scaled-1.jpg'; ?>">
            </div>
        </div>
    </div>
</div>


<?php
get_footer();
?>
